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
 * Implementation for backup/moodle2/restore_supervideotracker_activity_task.class.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
