<?php
// This file is part of Moodle - http://moodle.org/.

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/supervideotracker/backup/moodle2/restore_supervideotracker_stepslib.php');

/**
 * Restore task for Super Video Tracker.
 */
class restore_supervideotracker_activity_task extends restore_activity_task {
    /** @return void */
    protected function define_my_settings(): void {
    }

    /** @return void */
    protected function define_my_steps(): void {
        $this->add_step(new restore_supervideotracker_activity_structure_step(
            'supervideotracker_structure',
            'supervideotracker.xml'
        ));
    }

    /** @return array */
    public static function define_decode_contents(): array {
        return [];
    }

    /** @return array */
    public static function define_decode_rules(): array {
        return [];
    }

    /** @return array */
    public static function define_restore_log_rules(): array {
        return [];
    }

    /** @return array */
    public static function define_restore_log_rules_for_course(): array {
        return [];
    }
}
