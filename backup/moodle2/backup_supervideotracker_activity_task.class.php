<?php
// This file is part of Moodle - http://moodle.org/.

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/supervideotracker/backup/moodle2/backup_supervideotracker_stepslib.php');

/**
 * Backup task for Super Video Tracker.
 */
class backup_supervideotracker_activity_task extends backup_activity_task {
    /** @return void */
    protected function define_my_settings(): void {
    }

    /** @return void */
    protected function define_my_steps(): void {
        $this->add_step(new backup_supervideotracker_activity_structure_step(
            'supervideotracker_structure',
            'supervideotracker.xml'
        ));
    }

    /**
     * No activity URLs are stored in user content.
     *
     * @param string $content Content.
     * @return string
     */
    public static function encode_content_links($content): string {
        return $content;
    }
}
