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
 * Implementation for backup/moodle2/backup_supervideotracker_stepslib.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Backs up activity configuration and media catalog.
 */
class backup_supervideotracker_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines backup structure.
     *
     * Playback progress is intentionally not copied: it belongs to local_video_bridge.
     *
     * @return backup_nested_element
     */
    protected function define_structure(): backup_nested_element {
        $activity = new backup_nested_element('supervideotracker', ['id'], [
            'name', 'intro', 'introformat', 'progressmode', 'completiontracked',
            'completionmode', 'completioncount', 'completionpercent', 'timecreated', 'timemodified',
        ]);
        $items = new backup_nested_element('items');
        $item = new backup_nested_element('item', ['id'], [
            'title', 'description', 'source', 'sourceconfig', 'position', 'required',
            'minpercent', 'weight', 'availablefrom', 'availableuntil', 'active',
            'timecreated', 'timemodified',
        ]);

        $activity->add_child($items);
        $items->add_child($item);

        $activity->set_source_table('supervideotracker', ['id' => backup::VAR_ACTIVITYID]);
        $item->set_source_table('supervideotracker_items', ['activityid' => backup::VAR_PARENTID]);

        // Media files are owned by the bridge, but item ids are owned by this catalog.
        $item->annotate_files('local_video_bridge', 'video', 'id');
        $item->annotate_files('local_video_bridge', 'caption', 'id');

        return $this->prepare_activity_structure($activity);
    }
}
