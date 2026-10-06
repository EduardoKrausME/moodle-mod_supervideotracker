<?php
// This file is part of Moodle - http://moodle.org/.

require('../../config.php');

use local_video_bridge\media\config as media_config;
use local_video_bridge\progress\consumer;
use local_video_bridge\source\manager as source_manager;
use mod_supervideotracker\local\progress_service;

$id = required_param('id', PARAM_INT);
$itemid = required_param('item', PARAM_INT);

$cm = get_coursemodule_from_id('supervideotracker', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/supervideotracker:view', $context);

$item = $DB->get_record('supervideotracker_items', [
    'id' => $itemid,
    'activityid' => $activity->id,
], '*', MUST_EXIST);

$canmanage = has_capability('mod/supervideotracker:manageitems', $context);
if (!$canmanage && !progress_service::is_available($item)) {
    throw new moodle_exception('unavailable', 'supervideotracker');
}

$PAGE->set_url('/mod/supervideotracker/item.php', ['id' => $cm->id, 'item' => $item->id]);
$PAGE->set_title(format_string($item->title));
$PAGE->set_heading(format_string($course->fullname));

$media = new media_config((string)$item->source, (string)$item->sourceconfig, (int)$item->id);
$manager = new source_manager();
$player = $manager->get_player_config_for_media(
    $media,
    $context,
    new consumer('mod_supervideotracker', (int)$activity->id),
    \local_video_bridge\analytics::LEVEL_BASIC,
    ['tracking']
);

$clientconfig = $player;
unset($clientconfig['sourcetemplate']);
$sourcehtml = $OUTPUT->render_from_template($player['sourcetemplate'], ['player' => $player]);

$event = \mod_supervideotracker\event\media_viewed::create([
    'objectid' => $item->id,
    'context' => $context,
    'other' => ['activityid' => (int)$activity->id],
]);
$event->trigger();

$PAGE->requires->js_call_amd('mod_supervideotracker/player', 'init', [
    $clientconfig,
    'supervideotracker-player',
]);

$data = [
    'title' => format_string($item->title),
    'description' => s((string)$item->description),
    'sourcehtml' => $sourcehtml,
    'backurl' => (new moodle_url('/mod/supervideotracker/view.php', ['id' => $cm->id]))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_supervideotracker/item', $data);
echo $OUTPUT->footer();
