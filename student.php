<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Implementation for student.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

use local_video_bridge\progress\manager as bridge_progress_manager;
use mod_supervideotracker\local\progress_service;
use mod_supervideotracker\local\report_service;

$id = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

$cm = get_coursemodule_from_id('supervideotracker', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/supervideotracker:viewreports', $context);
if (!report_service::can_view_user($cm, $context, $userid)) {
    throw new required_capability_exception($context, 'mod/supervideotracker:viewreports', 'nopermissions', '');
}

$user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);
$items = array_values($DB->get_records(
    'supervideotracker_items',
    ['activityid' => $activity->id, 'active' => 1],
    'position ASC, id ASC'
));
$progress = progress_service::load_user($activity, $context, $items, $userid);

$PAGE->set_url('/mod/supervideotracker/student.php', ['id' => $cm->id, 'userid' => $userid]);
$PAGE->set_title(get_string('studentdetails', 'supervideotracker'));
$PAGE->set_heading(format_string($course->fullname));

$details = [];
foreach ($items as $item) {
    $row = $progress[(int)$item->id] ?? null;
    $duration = $row ? (int)$row->duration : 0;
    $length = bridge_progress_manager::progress_length($duration);
    $watched = [];
    if ($row) {
        foreach ((json_decode((string)$row->map, true) ?: []) as $bucket) {
            $watched[(int)$bucket] = true;
        }
    }
    $buckets = [];
    for ($bucket = 1; $bucket <= $length; $bucket++) {
        $buckets[] = [
            'watched' => !empty($watched[$bucket]),
            'title' => $duration > 0
                ? format_time((int)floor(($bucket - 1) / max(1, $length) * $duration))
                : '',
        ];
    }

    $status = progress_service::report_status($item, $row);
    $details[] = [
        'title' => format_string($item->title),
        'percent' => $row ? (int)$row->percent : 0,
        'status' => get_string($status, 'supervideotracker'),
        'duration' => $duration ? format_time($duration) : '—',
        'currentposition' => $row ? format_time((int)$row->currenttime) : '—',
        'lastview' => $row ? userdate((int)$row->timemodified) : get_string('never', 'supervideotracker'),
        'hasmap' => (bool)$buckets,
        'buckets' => $buckets,
    ];
}

$summary = progress_service::calculate($activity, $items, $progress);
$data = [
    'fullname' => fullname($user),
    'overall' => (int)round($summary['overall']),
    'items' => $details,
    'hasitems' => (bool)$details,
    'backurl' => (new moodle_url('/mod/supervideotracker/report.php', ['id' => $cm->id]))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_supervideotracker/student', $data);
echo $OUTPUT->footer();
