<?php
// This file is part of Moodle - http://moodle.org/.

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
