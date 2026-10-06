<?php
// This file is part of Moodle - http://moodle.org/.

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
