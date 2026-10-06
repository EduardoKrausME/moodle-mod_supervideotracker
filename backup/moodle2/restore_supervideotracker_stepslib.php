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
 * Implementation for backup/moodle2/restore_supervideotracker_stepslib.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Restores activity configuration and remaps every media item id.
 */
class restore_supervideotracker_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines restore paths.
     *
     * @return array
     */
    protected function define_structure(): array {
        return $this->prepare_activity_structure([
            new restore_path_element('supervideotracker', '/activity/supervideotracker'),
            new restore_path_element('supervideotracker_item', '/activity/supervideotracker/items/item'),
        ]);
    }

    /**
     * Restores activity record.
     *
     * @param array $data Backup data.
     * @return void
     */
    protected function process_supervideotracker(array $data): void {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->id = $DB->insert_record('supervideotracker', $data);

        $this->apply_activity_instance($data->id);
        $this->set_mapping('supervideotracker', $oldid, $data->id, true);
    }

    /**
     * Restores one media item and maps old mediaid to the new record id.
     *
     * @param array $data Backup data.
     * @return void
     */
    protected function process_supervideotracker_item(array $data): void {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->activityid = $this->get_new_parentid('supervideotracker');
        $data->id = $DB->insert_record('supervideotracker_items', $data);

        $this->set_mapping('supervideotracker_item', $oldid, $data->id, true);
    }

    /**
     * Restores bridge-owned files using the remapped media item ids.
     *
     * @return void
     */
    protected function after_execute(): void {
        $this->add_related_files('local_video_bridge', 'video', 'supervideotracker_item');
        $this->add_related_files('local_video_bridge', 'caption', 'supervideotracker_item');
    }
}
