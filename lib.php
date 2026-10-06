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
 * Library callbacks for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_video_bridge\progress\manager as bridge_progress_manager;

/**
 * Library callbacks for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Declares supported Moodle features.
 *
 * @param string $feature Feature constant.
 * @return mixed
 */
function supervideotracker_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
        case FEATURE_COMPLETION_HAS_RULES:
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Creates an activity instance.
 *
 * @param stdClass $data Submitted form data.
 * @param moodleform|null $mform Form instance.
 * @return int Instance id.
 */
function supervideotracker_add_instance(stdClass $data, ?moodleform $mform = null): int {
    global $DB;

    $now = time();
    $data->timecreated = $now;
    $data->timemodified = $now;
    return (int)$DB->insert_record('supervideotracker', $data);
}

/**
 * Updates an activity instance.
 *
 * @param stdClass $data Submitted form data.
 * @param moodleform|null $mform Form instance.
 * @return bool
 */
function supervideotracker_update_instance(stdClass $data, ?moodleform $mform = null): bool {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();
    return $DB->update_record('supervideotracker', $data);
}

/**
 * Deletes an activity and bridge-owned media state.
 *
 * @param int $id Instance id.
 * @return bool
 */
function supervideotracker_delete_instance(int $id): bool {
    global $DB;

    $activity = $DB->get_record('supervideotracker', ['id' => $id]);
    if (!$activity) {
        return false;
    }

    $cm = get_coursemodule_from_instance('supervideotracker', $id, $activity->course, false);
    if ($cm) {
        $context = context_module::instance($cm->id);
        $source = new \local_video_bridge\source\manager();
        $captions = new \local_video_bridge\caption\manager();

        foreach ($DB->get_records('supervideotracker_items', ['activityid' => $id]) as $item) {
            $source->delete_files_for_media($context, (int)$item->id);
            $captions->delete_files_for_media($context, (int)$item->id);
        }
        bridge_progress_manager::delete_consumer($context->id, 'mod_supervideotracker', $id);
    }

    $DB->delete_records('supervideotracker_items', ['activityid' => $id]);
    $DB->delete_records('supervideotracker', ['id' => $id]);
    return true;
}

/**
 * Returns the current package completion state for legacy callers.
 *
 * @param stdClass $course Course.
 * @param stdClass $cm Course module.
 * @param int $userid User id.
 * @param int $type Completion type.
 * @return bool
 */
function supervideotracker_get_completion_state($course, $cm, $userid, $type): bool {
    global $DB;

    $activity = $DB->get_record('supervideotracker', ['id' => $cm->instance], '*', MUST_EXIST);
    return \mod_supervideotracker\local\progress_service::is_activity_complete(
        $activity,
        context_module::instance($cm->id),
        (int)$userid
    );
}
