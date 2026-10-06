<?php
// This file is part of Moodle - http://moodle.org/.

require('../../config.php');

use mod_supervideotracker\local\progress_service;

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('supervideotracker', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/supervideotracker:view', $context);

$PAGE->set_url('/mod/supervideotracker/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($activity->name));
$PAGE->set_heading(format_string($course->fullname));

$completion = new completion_info($course);
if ($completion->is_enabled($cm)) {
    $completion->set_module_viewed($cm);
}

$event = \mod_supervideotracker\event\course_module_viewed::create([
    'objectid' => $activity->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('supervideotracker', $activity);
$event->trigger();

$items = array_values($DB->get_records(
    'supervideotracker_items',
    ['activityid' => $activity->id, 'active' => 1],
    'position ASC, id ASC'
));
$progress = progress_service::load_user($activity, $context, $items, (int)$USER->id);
$summary = progress_service::calculate($activity, $items, $progress);

$cards = [];
foreach ($items as $item) {
    $row = $progress[(int)$item->id] ?? null;
    $status = progress_service::status($item, $row);
    $percent = $row ? (int)$row->percent : 0;
    $duration = $row && !empty($row->duration) ? format_time((int)$row->duration) : '—';
    $lastview = $row && !empty($row->timemodified) ? userdate((int)$row->timemodified) : get_string('never', 'supervideotracker');

    $cards[] = [
        'id' => (int)$item->id,
        'title' => format_string($item->title),
        'description' => s((string)$item->description),
        'duration' => $duration,
        'percent' => $percent,
        'status' => get_string($status, 'supervideotracker'),
        'statuskey' => $status,
        'lastview' => $lastview,
        'required' => !empty($item->required),
        'optional' => empty($item->required),
        'requirement' => $item->required
            ? get_string('required', 'supervideotracker') . ' · ' . (int)$item->minpercent . '%'
            : get_string('optional', 'supervideotracker'),
        'available' => progress_service::is_available($item),
        'url' => (new moodle_url('/mod/supervideotracker/item.php', [
            'id' => $cm->id,
            'item' => $item->id,
        ]))->out(false),
    ];
}

$data = [
    'name' => format_string($activity->name),
    'intro' => format_module_intro('supervideotracker', $activity, $cm->id),
    'hasintro' => trim((string)$activity->intro) !== '',
    'hasitems' => (bool)$cards,
    'items' => $cards,
    'overall' => (int)round($summary['overall']),
    'requiredcompleted' => $summary['requiredcompleted'],
    'requiredcount' => $summary['requiredcount'],
    'canmanage' => has_capability('mod/supervideotracker:manageitems', $context),
    'manageurl' => (new moodle_url('/mod/supervideotracker/manage.php', ['id' => $cm->id]))->out(false),
    'canreport' => has_capability('mod/supervideotracker:viewreports', $context),
    'reporturl' => (new moodle_url('/mod/supervideotracker/report.php', ['id' => $cm->id]))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_supervideotracker/view', $data);
echo $OUTPUT->footer();
