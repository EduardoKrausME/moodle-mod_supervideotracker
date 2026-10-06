<?php
// This file is part of Moodle - http://moodle.org/.

require('../../config.php');

$id = required_param('id', PARAM_INT);
$course = get_course($id);
require_course_login($course);

$PAGE->set_url('/mod/supervideotracker/index.php', ['id' => $course->id]);
$PAGE->set_title(get_string('modulenameplural', 'supervideotracker'));
$PAGE->set_heading(format_string($course->fullname));

$instances = get_all_instances_in_course('supervideotracker', $course);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'supervideotracker'));

if (!$instances) {
    echo $OUTPUT->notification(get_string('none'));
} else {
    $table = new html_table();
    $table->head = [get_string('name'), get_string('sectionname', 'format_' . $course->format)];
    foreach ($instances as $instance) {
        $table->data[] = [
            html_writer::link(
                new moodle_url('/mod/supervideotracker/view.php', ['id' => $instance->coursemodule]),
                format_string($instance->name)
            ),
            get_section_name($course, $instance->section),
        ];
    }
    echo html_writer::table($table);
}
echo $OUTPUT->footer();
