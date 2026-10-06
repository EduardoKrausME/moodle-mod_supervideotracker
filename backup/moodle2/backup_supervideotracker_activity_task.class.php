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
 * Implementation for backup/moodle2/backup_supervideotracker_activity_task.class.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
