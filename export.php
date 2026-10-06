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
 * Implementation for export.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');

use mod_supervideotracker\local\progress_service;
use mod_supervideotracker\local\report_service;

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('supervideotracker', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/supervideotracker:viewreports', $context);

$items = array_values($DB->get_records(
    'supervideotracker_items',
    ['activityid' => $activity->id, 'active' => 1],
    'position ASC, id ASC'
));
$users = report_service::users($cm, $context);
$userids = array_map('intval', array_keys($users));
$matrix = progress_service::load_matrix($activity, $context, $items, $userids);

$csv = new csv_export_writer();
$csv->set_filename(clean_filename(format_string($activity->name) . '-video-progress'));
$header = [get_string('learner', 'supervideotracker')];
foreach ($items as $item) {
    $header[] = format_string($item->title);
}
$header[] = get_string('overall', 'supervideotracker');
$csv->add_data($header);

foreach ($users as $user) {
    $userprogress = $matrix[(int)$user->id] ?? [];
    $summary = progress_service::calculate($activity, $items, $userprogress);
    $line = [fullname($user)];
    foreach ($items as $item) {
        $progress = $userprogress[(int)$item->id] ?? null;
        $status = progress_service::report_status($item, $progress);
        $line[] = ($progress ? (int)$progress->percent : 0) . '% - ' .
            get_string($status, 'supervideotracker');
    }
    $line[] = (int)round($summary['overall']) . '%';
    $csv->add_data($line);
}

$csv->download_file();
