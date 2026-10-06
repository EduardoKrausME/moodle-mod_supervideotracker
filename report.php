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
 * Consolidated teacher report for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

use mod_supervideotracker\local\progress_service;
use mod_supervideotracker\local\report_service;

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('supervideotracker', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/supervideotracker:viewreports', $context);

$PAGE->set_url('/mod/supervideotracker/report.php', ['id' => $cm->id]);
$PAGE->set_title(get_string('reportmatrix', 'supervideotracker'));
$PAGE->set_heading(format_string($course->fullname));

$event = \mod_supervideotracker\event\report_viewed::create([
    'objectid' => $activity->id,
    'context' => $context,
]);
$event->trigger();

$items = array_values($DB->get_records(
    'supervideotracker_items',
    ['activityid' => $activity->id, 'active' => 1],
    'position ASC, id ASC'
));
$users = report_service::users($cm, $context);
$userids = array_map('intval', array_keys($users));
$matrix = progress_service::load_matrix($activity, $context, $items, $userids);

$counts = ['notstarted' => 0, 'inprogress' => 0, 'completed' => 0, 'overdue' => 0];
$rows = [];
foreach ($users as $user) {
    $userprogress = $matrix[(int)$user->id] ?? [];
    $summary = progress_service::calculate($activity, $items, $userprogress);
    $cells = [];

    foreach ($items as $item) {
        $row = $userprogress[(int)$item->id] ?? null;
        $status = progress_service::status($item, $row, true);
        if (isset($counts[$status])) {
            $counts[$status]++;
        }
        $cells[] = [
            'percent' => $row ? (int)$row->percent : 0,
            'status' => get_string($status, 'supervideotracker'),
            'statuskey' => $status,
        ];
    }

    $rows[] = [
        'fullname' => fullname($user),
        'cells' => $cells,
        'overall' => (int)round($summary['overall']),
        'detailsurl' => (new moodle_url('/mod/supervideotracker/student.php', [
            'id' => $cm->id,
            'userid' => $user->id,
        ]))->out(false),
    ];
}

$data = [
    'name' => format_string($activity->name),
    'hasitems' => (bool)$items,
    'hasusers' => (bool)$users,
    'items' => array_map(static fn(stdClass $item): array => [
        'title' => format_string($item->title),
    ], $items),
    'rows' => $rows,
    'notstarted' => $counts['notstarted'],
    'inprogress' => $counts['inprogress'],
    'completed' => $counts['completed'],
    'overdue' => $counts['overdue'],
    'exporturl' => (new moodle_url('/mod/supervideotracker/export.php', ['id' => $cm->id]))->out(false),
    'backurl' => (new moodle_url('/mod/supervideotracker/view.php', ['id' => $cm->id]))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reportmatrix', 'supervideotracker'));
groups_print_activity_menu($cm, $PAGE->url);
echo $OUTPUT->render_from_template('mod_supervideotracker/report', $data);
echo $OUTPUT->footer();
