<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     block_featured_tool
 * @category    string
 * @copyright   2023 Derek Wilson <wilsondc5@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['featured_tool:header1'] = 'First Featured Tool';
$string['featured_tool:header2'] = 'Second Featured Tool';
$string['featured_tool:header3'] = 'Third Featured Tool';
$string['featured_tool:subtitle1'] = 'First featured tool subtitle';
$string['featured_tool:subtitle1_help'] = 'Enter a short title for the first featured tool. The subtitle will appear above the first featured tool\'s card on the dashboard.';
$string['featured_tool:subtitle2'] = 'Second featured tool subtitle';
$string['featured_tool:subtitle2_help'] = 'Enter a short title for the second featured tool. The subtitle will appear above the second featured tool\'s card on the dashboard.';
$string['featured_tool:subtitle3'] = 'Third featured tool subtitle';
$string['featured_tool:subtitle3_help'] = 'Enter a short title for the third featured tool. The subtitle will appear above the third featured tool\'s card on the dashboard.';
$string['featured_tool:thumbnail1'] = 'First featured tool thumbnail';
$string['featured_tool:thumbnail1_help'] = 'A descriptive image for the first featured tool. The thumbnail appears below the subtitle in the card for first featured tool.';
$string['featured_tool:thumbnail2'] = 'Second featured tool thumbnail';
$string['featured_tool:thumbnail2_help'] = 'A descriptive image for the second featured tool. The thumbnail appears below the subtitle in the card for second featured tool.';
$string['featured_tool:thumbnail3'] = 'Third featured tool thumbnail';
$string['featured_tool:thumbnail3_help'] = 'A descriptive image for the third featured tool. The thumbnail appears below the subtitle in the card for third featured tool.';
$string['featured_tool:media1'] = 'First featured tool content';
$string['featured_tool:media1_help'] = 'Full content to show to the user in the first featured tool. Displayed when user clicks on the first featured tool\'s button. Admins can enable link click tracking on
featured tool links by adding the following HTML attributes to the anchor tag of the link: data-action="trackable" and data-name=<name of link> through Tools > Source Code.';
$string['featured_tool:media2'] = 'Second featured tool content';
$string['featured_tool:media2_help'] = 'Full content to show to the user in the second featured tool. Displayed when user clicks on the second featured tool\'s button. Admins can enable link click tracking on
featured tool links by adding the following HTML attributes to the anchor tag of the link: data-action="trackable" and data-name=<name of link> through Tools > Source Code.';
$string['featured_tool:media3'] = 'Third featured tool content';
$string['featured_tool:media3_help'] = 'Full content to show to the user in the third featured tool. Displayed when user clicks on the third featured tool\'s button. Admins can enable link click tracking on
featured tool links by adding the following HTML attributes to the anchor tag of the link: data-action="trackable" and data-name=<name of link> through Tools > Source Code.';
$string['featured_tool:myaddinstance'] = 'Add a new featured tool block to Dashboard';
$string['featured_tool:addinstance'] = 'Add a new featured tool block';
$string['pluginname'] = 'Featured tool';
$string['cardbutton'] = 'Read more';
$string['closemodal'] = 'Close';
$string['lengtherror'] = 'data-name must be between 1 and 255 characters';
$string['missingdataattributeerror'] = 'Trackable links must have both data-name and data-action';
$string['nottrackableerror'] = 'data-action attribute must be set to "trackable"';
$string['notrackableandmissingerror'] = 'data-action must be set to "trackable" and must a have data-name';
