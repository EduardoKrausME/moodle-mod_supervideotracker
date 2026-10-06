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
 * Implementation for classes/local/report_service.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_supervideotracker\local;

use context_module;
use stdClass;

/**
 * Group-aware learner selection for teacher reports.
 */
final class report_service {
    /**
     * Returns learners visible in the current group selection.
     *
     * @param stdClass $cm Course module.
     * @param context_module $context Module context.
     * @return array User records indexed by id.
     */
    public static function users(stdClass $cm, context_module $context): array {
        global $USER;

        $groupid = groups_get_activity_group($cm, true);
        if ($groupid > 0) {
            return get_enrolled_users(
                $context,
                'mod/supervideotracker:view',
                $groupid,
                'u.id,u.firstname,u.lastname,u.email',
                'u.lastname ASC,u.firstname ASC'
            );
        }

        $users = get_enrolled_users(
            $context,
            'mod/supervideotracker:view',
            0,
            'u.id,u.firstname,u.lastname,u.email',
            'u.lastname ASC,u.firstname ASC'
        );

        $separate = groups_get_activity_groupmode($cm) === SEPARATEGROUPS;
        if (!$separate || has_capability('moodle/site:accessallgroups', $context, $USER->id)) {
            return $users;
        }

        $allowedids = [];
        foreach (array_keys(groups_get_activity_allowed_groups($cm, $USER->id)) as $groupid) {
            foreach (groups_get_members((int)$groupid, 'u.id') as $member) {
                $allowedids[(int)$member->id] = true;
            }
        }

        return array_filter(
            $users,
            static fn(stdClass $user): bool => isset($allowedids[(int)$user->id])
        );
    }

    /**
     * Checks whether the current teacher may inspect one learner.
     *
     * @param stdClass $cm Course module.
     * @param context_module $context Module context.
     * @param int $userid Learner id.
     * @return bool
     */
    public static function can_view_user(stdClass $cm, context_module $context, int $userid): bool {
        global $USER;

        if (!is_enrolled($context, $userid, 'mod/supervideotracker:view', true)) {
            return false;
        }
        if (groups_get_activity_groupmode($cm) !== SEPARATEGROUPS
                || has_capability('moodle/site:accessallgroups', $context, $USER->id)) {
            return true;
        }

        foreach (array_keys(groups_get_activity_allowed_groups($cm, $USER->id)) as $groupid) {
            if (groups_is_member((int)$groupid, $userid)) {
                return true;
            }
        }
        return false;
    }
}
