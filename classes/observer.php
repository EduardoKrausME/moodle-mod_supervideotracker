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
 * Event observers for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_supervideotracker;

/**
 * Reacts to Video Bridge progress updates.
 */
final class observer {
    /**
     * Re-evaluates Moodle completion when bridge progress changes.
     *
     * @param \local_video_bridge\event\analytics_updated $event Progress event.
     * @return void
     */
    public static function video_progress_updated(
        \local_video_bridge\event\analytics_updated $event
    ): void {
        $data = $event->get_data();
        $other = $data['other'] ?? [];
        if (($other['component'] ?? '') !== 'mod_supervideotracker') {
            return;
        }

        $userid = (int)($data['relateduserid'] ?? 0);
        $activityid = (int)($other['itemid'] ?? 0);
        $context = $event->get_context();
        if (!$userid || !$activityid || !$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('supervideotracker', $context->instanceid, 0, false);
        if (!$cm || (int)$cm->instance !== $activityid) {
            return;
        }

        $course = get_course($cm->course);
        $completion = new \completion_info($course);
        if ($completion->is_enabled($cm)) {
            $completion->update_state($cm, COMPLETION_UNKNOWN, $userid);
        }
    }
}
