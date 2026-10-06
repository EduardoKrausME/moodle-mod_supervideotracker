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
 * English language strings for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['actions'] = 'Actions';
$string['active'] = 'Active';
$string['addvideo'] = 'Add video';
$string['allgroups'] = 'All allowed groups';
$string['availability'] = 'Availability';
$string['availabilityerror'] = 'The end of the availability window must be after its start.';
$string['available'] = 'Available';
$string['availablefrom'] = 'Available from';
$string['availableuntil'] = 'Available until';
$string['backtopackage'] = 'Back to package';
$string['completed'] = 'Completed';
$string['completioncount'] = 'Required number of completed videos';
$string['completiondescription'] = 'Meet the configured video package requirement';
$string['completionmode'] = 'Completion rule';
$string['completionmode_allrequired'] = 'All required videos completed';
$string['completionmode_count'] = 'At least X videos completed';
$string['completionmode_weighted'] = 'Weighted package progress reaches X%';
$string['completionpercent'] = 'Required weighted progress';
$string['completiontracked'] = 'Require package tracking rule';
$string['completiontracked_desc'] = 'Complete the activity when the configured package rule is satisfied.';
$string['confirmdelete'] = 'Delete the video "{$a}"?';
$string['countgreaterthanitems'] = 'The completion count can be greater than the current number of videos; completion will remain pending until enough videos exist.';
$string['currentposition'] = 'Current position';
$string['deletevideo'] = 'Delete video';
$string['details'] = 'Details';
$string['duration'] = 'Duration';
$string['editvideo'] = 'Edit video';
$string['eventcoursemoduleviewed'] = 'Super Video Tracker viewed';
$string['eventmediaviewed'] = 'Tracked video viewed';
$string['eventreportviewed'] = 'Super Video Tracker report viewed';
$string['exportcsv'] = 'Export CSV';
$string['inprogress'] = 'In progress';
$string['invaliditem'] = 'The requested video does not belong to this activity.';
$string['lastview'] = 'Last view';
$string['learner'] = 'Learner';
$string['managevideos'] = 'Manage videos';
$string['minpercent'] = 'Minimum watched percentage';
$string['modulename'] = 'Super Video Tracker';
$string['modulenameplural'] = 'Super Video Trackers';
$string['movedown'] = 'Move down';
$string['moveup'] = 'Move up';
$string['name'] = 'Name';
$string['never'] = 'Never';
$string['notstarted'] = 'Not started';
$string['novideos'] = 'No videos have been added yet.';
$string['openvideo'] = 'Open video';
$string['optional'] = 'Optional';
$string['overall'] = 'Overall';
$string['overallprogress'] = 'Overall progress';
$string['overdue'] = 'Overdue';
$string['packageprogresshelp'] = 'Optional videos do not reduce package progress. Required active videos are used in both simple and weighted calculations.';
$string['percent'] = 'Percentage';
$string['percentageerror'] = 'Percentage must be between 1 and 100.';
$string['pluginadministration'] = 'Super Video Tracker administration';
$string['pluginname'] = 'Super Video Tracker';
$string['privacy:metadata'] = 'Super Video Tracker stores only package and media configuration. Playback progress is stored by local_video_bridge.';
$string['progressmode'] = 'Package progress calculation';
$string['progressmode_simple'] = 'Simple mean of required videos';
$string['progressmode_weighted'] = 'Weighted mean of required videos';
$string['report'] = 'Report';
$string['reportmatrix'] = 'Learner × video matrix';
$string['required'] = 'Required';
$string['resume'] = 'Resume from {$a}';
$string['shortdescription'] = 'Short description';
$string['source'] = 'Video source';
$string['sourcehelp'] = 'Only Video Bridge providers that guarantee tracking can be selected.';
$string['status'] = 'Status';
$string['studentdetails'] = 'Learner details';
$string['summary_completed'] = 'Completed';
$string['summary_inprogress'] = 'In progress';
$string['summary_notstarted'] = 'Not started';
$string['summary_overdue'] = 'Overdue';
$string['supervideotracker:addinstance'] = 'Add a Super Video Tracker activity';
$string['supervideotracker:manageitems'] = 'Manage tracked videos';
$string['supervideotracker:view'] = 'View Super Video Tracker';
$string['supervideotracker:viewreports'] = 'View consolidated video reports';
$string['title'] = 'Title';
$string['trackingrequired'] = 'The selected source does not provide reliable tracking.';
$string['unavailable'] = 'Unavailable';
$string['videos'] = 'Videos';
$string['viewingmap'] = 'Viewing map';
$string['weight'] = 'Weight';
$string['weighterror'] = 'Weight must be greater than zero.';
