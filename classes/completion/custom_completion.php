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
 * Implementation for classes/completion/custom_completion.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_supervideotracker\completion;

use core_completion\activity_custom_completion;
use mod_supervideotracker\local\progress_service;

/**
 * Moodle custom completion integration.
 */
final class custom_completion extends activity_custom_completion {
    /**
     * Returns state for the package tracking rule.
     *
     * @param string $rule Rule name.
     * @return int Completion state.
     */
    public function get_state(string $rule): int {
        global $DB;

        if ($rule !== 'completiontracked') {
            return COMPLETION_INCOMPLETE;
        }
        $activity = $DB->get_record('supervideotracker', ['id' => $this->cm->instance], '*', MUST_EXIST);
        if (empty($activity->completiontracked)) {
            return COMPLETION_INCOMPLETE;
        }

        return progress_service::is_activity_complete(
            $activity,
            \context_module::instance($this->cm->id),
            (int)$this->userid
        ) ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Lists the custom rule.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return ['completiontracked'];
    }

    /**
     * Returns descriptions shown by Moodle.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        return ['completiontracked' => get_string('completiondescription', 'supervideotracker')];
    }

    /**
     * Returns completion rule display order.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return ['completiontracked'];
    }
}
