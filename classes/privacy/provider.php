<?php
// This file is part of Moodle - http://moodle.org/.

namespace mod_supervideotracker\privacy;

use core_privacy\local\metadata\null_provider;

/**
 * Super Video Tracker stores no user-specific data of its own.
 */
final class provider implements null_provider {
    /**
     * Explains where playback data is stored.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
