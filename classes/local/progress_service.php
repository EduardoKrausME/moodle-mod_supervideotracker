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
 * Implementation for classes/local/progress_service.php.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_supervideotracker\local;

use context_module;
use local_video_bridge\media\config as media_config;
use local_video_bridge\progress\manager as bridge_progress_manager;
use stdClass;

/**
 * Derives package progress from Video Bridge data without duplicating it.
 */
final class progress_service {
    /**
     * Returns the media configuration for one item.
     *
     * @param stdClass $item Item record.
     * @return media_config
     */
    public static function media(stdClass $item): media_config {
        return new media_config(
            (string)$item->source,
            (string)$item->sourceconfig,
            (int)$item->id
        );
    }

    /**
     * Loads progress for several users and media items with one bridge query.
     *
     * @param stdClass $activity Activity.
     * @param context_module $context Module context.
     * @param array $items Item records.
     * @param array $userids User ids.
     * @return array [userid][itemid] => progress|null.
     */
    public static function load_matrix(
        stdClass $activity,
        context_module $context,
        array $items,
        array $userids
    ): array {
        $hashes = [];
        $itembyhash = [];
        foreach ($items as $item) {
            $hash = self::media($item)->get_mediahash();
            $hashes[] = $hash;
            $itembyhash[$hash] = (int)$item->id;
        }

        $raw = bridge_progress_manager::get_progress_bulk(
            $context->id,
            'mod_supervideotracker',
            (int)$activity->id,
            $hashes,
            $userids
        );

        $matrix = [];
        foreach ($userids as $userid) {
            $userid = (int)$userid;
            foreach ($items as $item) {
                $matrix[$userid][(int)$item->id] = null;
            }
        }
        foreach ($raw as $userid => $records) {
            foreach ($records as $hash => $record) {
                if (isset($itembyhash[$hash])) {
                    $matrix[(int)$userid][$itembyhash[$hash]] = $record;
                }
            }
        }
        return $matrix;
    }

    /**
     * Returns one user's progress records indexed by item id.
     *
     * @param stdClass $activity Activity.
     * @param context_module $context Module context.
     * @param array $items Items.
     * @param int $userid User id.
     * @return array
     */
    public static function load_user(
        stdClass $activity,
        context_module $context,
        array $items,
        int $userid
    ): array {
        return self::load_matrix($activity, $context, $items, [$userid])[$userid] ?? [];
    }

    /**
     * Calculates simple and weighted package progress.
     *
     * Optional videos do not reduce the compliance package percentage.
     *
     * @param stdClass $activity Activity.
     * @param array $items Item records.
     * @param array $progress Progress indexed by item id.
     * @return array
     */
    public static function calculate(stdClass $activity, array $items, array $progress): array {
        $required = array_values(array_filter($items, static function(stdClass $item): bool {
            return !empty($item->active) && !empty($item->required);
        }));

        $sum = 0.0;
        $weightedsum = 0.0;
        $weights = 0.0;
        $completed = 0;
        $allcompleted = true;

        foreach ($required as $item) {
            $row = $progress[(int)$item->id] ?? null;
            $percent = $row ? max(0.0, min(100.0, (float)$row->percent)) : 0.0;
            $sum += $percent;
            $weight = max(0.0001, (float)$item->weight);
            $weightedsum += $percent * $weight;
            $weights += $weight;

            if ($percent >= (float)$item->minpercent) {
                $completed++;
            } else {
                $allcompleted = false;
            }
        }

        $simple = $required ? $sum / count($required) : 0.0;
        $weighted = $weights > 0 ? $weightedsum / $weights : 0.0;
        $selected = ($activity->progressmode ?? 'simple') === 'weighted' ? $weighted : $simple;

        return [
            'simple' => round($simple, 2),
            'weighted' => round($weighted, 2),
            'overall' => round($selected, 2),
            'requiredcount' => count($required),
            'requiredcompleted' => $completed,
            'allrequiredcompleted' => $required ? $allcompleted : false,
        ];
    }

    /**
     * Returns whether one item is currently available to a learner.
     *
     * @param stdClass $item Item.
     * @param int|null $now Current timestamp.
     * @return bool
     */
    public static function is_available(stdClass $item, ?int $now = null): bool {
        $now ??= time();
        if (empty($item->active)) {
            return false;
        }
        if (!empty($item->availablefrom) && $now < (int)$item->availablefrom) {
            return false;
        }
        if (!empty($item->availableuntil) && $now > (int)$item->availableuntil) {
            return false;
        }
        return true;
    }

    /**
     * Derives learner-facing and report status.
     *
     * @param stdClass $item Item.
     * @param stdClass|null $progress Bridge progress.
     * @param bool $report Whether overdue should be distinguished.
     * @param int|null $now Current timestamp.
     * @return string Status key.
     */
    public static function status(
        stdClass $item,
        ?stdClass $progress,
        bool $report = false,
        ?int $now = null
    ): string {
        $now ??= time();
        $percent = $progress ? (float)$progress->percent : 0.0;
        if ($report && !empty($item->active) && !empty($item->availableuntil)
                && $now > (int)$item->availableuntil && $percent < (float)$item->minpercent) {
            return 'overdue';
        }
        if (!self::is_available($item, $now)) {
            return 'unavailable';
        }
        if ($percent <= 0) {
            return 'notstarted';
        }
        if ($percent >= (float)$item->minpercent) {
            return 'completed';
        }
        return 'inprogress';
    }

    /**
     * Tests the configured Moodle completion rule.
     *
     * @param stdClass $activity Activity.
     * @param context_module $context Module context.
     * @param int $userid User id.
     * @return bool
     */
    public static function is_activity_complete(
        stdClass $activity,
        context_module $context,
        int $userid
    ): bool {
        global $DB;

        $items = array_values($DB->get_records(
            'supervideotracker_items',
            ['activityid' => $activity->id, 'active' => 1],
            'position ASC, id ASC'
        ));
        $progress = self::load_user($activity, $context, $items, $userid);
        $summary = self::calculate($activity, $items, $progress);

        switch ((string)$activity->completionmode) {
            case 'count':
                $completed = 0;
                foreach ($items as $item) {
                    $row = $progress[(int)$item->id] ?? null;
                    if ($row && (float)$row->percent >= (float)$item->minpercent) {
                        $completed++;
                    }
                }
                return $completed >= max(1, (int)$activity->completioncount);

            case 'weighted':
                return $summary['weighted'] >= max(1, min(100, (int)$activity->completionpercent));

            case 'allrequired':
            default:
                return (bool)$summary['allrequiredcompleted'];
        }
    }
}
