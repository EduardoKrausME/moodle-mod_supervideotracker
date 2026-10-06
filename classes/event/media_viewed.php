<?php
// This file is part of Moodle - http://moodle.org/.

namespace mod_supervideotracker\event;

/**
 * Tracked media viewed.
 */
final class media_viewed extends \core\event\base {
    /**
     * Initializes event metadata.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'supervideotracker_items';
    }

    /** @return string Event name. */
    public static function get_name(): string {
        return get_string('eventmediaviewed', 'supervideotracker');
    }

    /** @return string Description. */
    public function get_description(): string {
        return 'The user with id ' . $this->userid . ' triggered media_viewed in Super Video Tracker.';
    }
}
