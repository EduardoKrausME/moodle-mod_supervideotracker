<?php
// This file is part of Moodle - http://moodle.org/.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Version metadata for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->component = 'mod_supervideotracker';
$plugin->version = 2026100600;
$plugin->release = '1.0.0';
$plugin->requires = 2024100700;
$plugin->maturity = MATURITY_BETA;
$plugin->dependencies = [
    'local_video_bridge' => 2026100604,
];
