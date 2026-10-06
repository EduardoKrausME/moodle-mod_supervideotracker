<?php
// This file is part of Moodle - http://moodle.org/.

namespace mod_supervideotracker;

use advanced_testcase;
use mod_supervideotracker\local\progress_service;

/**
 * Tests package progress and completion calculations.
 */
final class progress_service_test extends advanced_testcase {
    /**
     * Simple progress ignores optional media.
     *
     * @return void
     */
    public function test_simple_progress_uses_required_media_only(): void {
        $activity = (object)['progressmode' => 'simple'];
        $items = [
            (object)['id' => 1, 'active' => 1, 'required' => 1, 'weight' => 1, 'minpercent' => 80],
            (object)['id' => 2, 'active' => 1, 'required' => 1, 'weight' => 3, 'minpercent' => 80],
            (object)['id' => 3, 'active' => 1, 'required' => 0, 'weight' => 10, 'minpercent' => 100],
        ];
        $progress = [
            1 => (object)['percent' => 100],
            2 => (object)['percent' => 50],
            3 => (object)['percent' => 0],
        ];

        $result = progress_service::calculate($activity, $items, $progress);
        $this->assertSame(75.0, $result['simple']);
        $this->assertSame(62.5, $result['weighted']);
        $this->assertSame(75.0, $result['overall']);
        $this->assertSame(1, $result['requiredcompleted']);
        $this->assertFalse($result['allrequiredcompleted']);
    }

    /**
     * Weighted mode applies item weights.
     *
     * @return void
     */
    public function test_weighted_progress(): void {
        $activity = (object)['progressmode' => 'weighted'];
        $items = [
            (object)['id' => 1, 'active' => 1, 'required' => 1, 'weight' => 1, 'minpercent' => 100],
            (object)['id' => 2, 'active' => 1, 'required' => 1, 'weight' => 3, 'minpercent' => 100],
        ];
        $progress = [
            1 => (object)['percent' => 100],
            2 => (object)['percent' => 50],
        ];

        $result = progress_service::calculate($activity, $items, $progress);
        $this->assertSame(62.5, $result['weighted']);
        $this->assertSame(62.5, $result['overall']);
    }

    /**
     * Availability boundaries are deterministic.
     *
     * @return void
     */
    public function test_item_availability(): void {
        $item = (object)[
            'active' => 1,
            'availablefrom' => 100,
            'availableuntil' => 200,
        ];
        $this->assertFalse(progress_service::is_available($item, 99));
        $this->assertTrue(progress_service::is_available($item, 100));
        $this->assertTrue(progress_service::is_available($item, 200));
        $this->assertFalse(progress_service::is_available($item, 201));
    }
}
