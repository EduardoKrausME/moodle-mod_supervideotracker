<?php
// This file is part of Moodle - http://moodle.org/.

require('../../config.php');
require_once($CFG->libdir . '/formslib.php');

use local_video_bridge\source\manager as source_manager;
use mod_supervideotracker\form\item_form;

$id = required_param('id', PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);

$cm = get_coursemodule_from_id('supervideotracker', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/supervideotracker:manageitems', $context);

$item = null;
if ($itemid) {
    $item = $DB->get_record('supervideotracker_items', [
        'id' => $itemid,
        'activityid' => $activity->id,
    ], '*', MUST_EXIST);
}

$PAGE->set_url('/mod/supervideotracker/edititem.php', ['id' => $cm->id, 'itemid' => $itemid]);
$PAGE->set_title(get_string($item ? 'editvideo' : 'addvideo', 'supervideotracker'));
$PAGE->set_heading(format_string($course->fullname));

$manager = new source_manager('source', 'sourceconfig', null);
$form = new item_form($context, $PAGE->url);

$defaults = $item ? (array)$item : [
    'id' => $cm->id,
    'itemid' => 0,
    'source' => (string)(array_key_first($manager->get_options(['tracking'])) ?? ''),
    'required' => 1,
    'minpercent' => 100,
    'weight' => 1,
    'active' => 1,
];
$defaults['id'] = $cm->id;
$defaults['itemid'] = $itemid;
if ($item) {
    $manager->prepare_form_data_for_media($defaults, $context, $itemid);
}
$form->set_data($defaults);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/supervideotracker/manage.php', ['id' => $cm->id]));
}

if ($data = $form->get_data()) {
    $previoussource = $item ? (string)$item->source : null;
    $manager->normalise_record($data);
    $now = time();

    if ($item) {
        $data->id = $item->id;
        $data->activityid = $activity->id;
        $data->position = $item->position;
        $data->timemodified = $now;
        $DB->update_record('supervideotracker_items', $data);
        $savedid = (int)$item->id;
    } else {
        $max = $DB->get_field_sql(
            'SELECT COALESCE(MAX(position), 0) FROM {supervideotracker_items} WHERE activityid = ?',
            [$activity->id]
        );
        $data->activityid = $activity->id;
        $data->position = (int)$max + 1;
        $data->timecreated = $now;
        $data->timemodified = $now;
        $savedid = (int)$DB->insert_record('supervideotracker_items', $data);
    }

    $manager->save_files_for_media($data, $context, $savedid, $previoussource);
    redirect(
        new moodle_url('/mod/supervideotracker/manage.php', ['id' => $cm->id]),
        get_string('changessaved')
    );
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string($item ? 'editvideo' : 'addvideo', 'supervideotracker'));
$form->display();
echo $OUTPUT->footer();
