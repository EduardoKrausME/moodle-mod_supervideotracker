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
 * Video catalog management page for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$itemid = optional_param('itemid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$cm = get_coursemodule_from_id('supervideotracker', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/supervideotracker:manageitems', $context);

$PAGE->set_url('/mod/supervideotracker/manage.php', ['id' => $cm->id]);
$PAGE->set_title(get_string('managevideos', 'supervideotracker'));
$PAGE->set_heading(format_string($course->fullname));

$normalize = static function() use ($DB, $activity): void {
    $position = 1;
    foreach ($DB->get_records('supervideotracker_items', ['activityid' => $activity->id], 'position ASC, id ASC') as $row) {
        if ((int)$row->position !== $position) {
            $DB->set_field('supervideotracker_items', 'position', $position, ['id' => $row->id]);
        }
        $position++;
    }
};

if ($action && $itemid) {
    require_sesskey();
    $item = $DB->get_record('supervideotracker_items', [
        'id' => $itemid,
        'activityid' => $activity->id,
    ], '*', MUST_EXIST);

    if ($action === 'delete') {
        if (!$confirm) {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('deletevideo', 'supervideotracker'));
            $yes = new moodle_url($PAGE->url, [
                'action' => 'delete',
                'itemid' => $item->id,
                'confirm' => 1,
                'sesskey' => sesskey(),
            ]);
            echo $OUTPUT->confirm(
                get_string('confirmdelete', 'supervideotracker', format_string($item->title)),
                $yes,
                $PAGE->url
            );
            echo $OUTPUT->footer();
            exit;
        }

        $mediahash = \mod_supervideotracker\local\progress_service::media($item)->get_mediahash();
        \local_video_bridge\progress\manager::delete_consumer_media(
            $context->id,
            'mod_supervideotracker',
            (int)$activity->id,
            $mediahash
        );
        (new \local_video_bridge\source\manager())->delete_files_for_media($context, (int)$item->id);
        (new \local_video_bridge\caption\manager())->delete_files_for_media($context, (int)$item->id);
        $DB->delete_records('supervideotracker_items', ['id' => $item->id]);
        $normalize();
        redirect($PAGE->url);
    }

    if (in_array($action, ['up', 'down'], true)) {
        $operator = $action === 'up' ? '<' : '>';
        $order = $action === 'up' ? 'position DESC' : 'position ASC';
        $other = $DB->get_record_sql(
            "SELECT *
               FROM {supervideotracker_items}
              WHERE activityid = :activityid
                AND position {$operator} :position
           ORDER BY {$order}",
            ['activityid' => $activity->id, 'position' => $item->position],
            IGNORE_MULTIPLE
        );
        if ($other) {
            $oldposition = (int)$item->position;
            $DB->set_field('supervideotracker_items', 'position', (int)$other->position, ['id' => $item->id]);
            $DB->set_field('supervideotracker_items', 'position', $oldposition, ['id' => $other->id]);
        }
        $normalize();
        redirect($PAGE->url);
    }
}

$items = $DB->get_records('supervideotracker_items', ['activityid' => $activity->id], 'position ASC, id ASC');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managevideos', 'supervideotracker'));

$buttons = [];
$buttons[] = $OUTPUT->single_button(
    new moodle_url('/mod/supervideotracker/edititem.php', ['id' => $cm->id]),
    get_string('addvideo', 'supervideotracker'),
    'get'
);
$buttons[] = html_writer::link(
    new moodle_url('/mod/supervideotracker/view.php', ['id' => $cm->id]),
    get_string('backtopackage', 'supervideotracker'),
    ['class' => 'btn btn-secondary']
);
echo html_writer::div(implode(' ', $buttons), 'mb-3');

if (!$items) {
    echo $OUTPUT->notification(get_string('novideos', 'supervideotracker'), 'info');
    echo $OUTPUT->footer();
    exit;
}

$table = new html_table();
$table->head = [
    '#',
    get_string('title', 'supervideotracker'),
    get_string('source', 'supervideotracker'),
    get_string('required', 'supervideotracker'),
    get_string('minpercent', 'supervideotracker'),
    get_string('weight', 'supervideotracker'),
    get_string('active', 'supervideotracker'),
    get_string('actions', 'supervideotracker'),
];

foreach ($items as $item) {
    $actions = [];
    $actions[] = $OUTPUT->action_icon(
        new moodle_url('/mod/supervideotracker/edititem.php', ['id' => $cm->id, 'itemid' => $item->id]),
        new pix_icon('t/edit', get_string('editvideo', 'supervideotracker'))
    );
    $actions[] = $OUTPUT->action_icon(
        new moodle_url($PAGE->url, ['action' => 'up', 'itemid' => $item->id, 'sesskey' => sesskey()]),
        new pix_icon('t/up', get_string('moveup', 'supervideotracker'))
    );
    $actions[] = $OUTPUT->action_icon(
        new moodle_url($PAGE->url, ['action' => 'down', 'itemid' => $item->id, 'sesskey' => sesskey()]),
        new pix_icon('t/down', get_string('movedown', 'supervideotracker'))
    );
    $actions[] = $OUTPUT->action_icon(
        new moodle_url($PAGE->url, ['action' => 'delete', 'itemid' => $item->id, 'sesskey' => sesskey()]),
        new pix_icon('t/delete', get_string('deletevideo', 'supervideotracker'))
    );

    $table->data[] = [
        (int)$item->position,
        format_string($item->title),
        s($item->source),
        $item->required ? get_string('yes') : get_string('no'),
        (int)$item->minpercent . '%',
        format_float((float)$item->weight, 2),
        $item->active ? get_string('yes') : get_string('no'),
        implode(' ', $actions),
    ];
}
echo html_writer::table($table);
echo $OUTPUT->footer();
