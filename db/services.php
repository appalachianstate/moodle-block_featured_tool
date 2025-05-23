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
 * External web service functions
 * @package     block_featured_tool
 * @copyright   2025 Appalachian State University
 * @author      2025 Lina Brown <brownli2@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_featured_tool_get_clicked_link' => [
        'classname'   => 'block_featured_tool_external',
            'methodname'  => 'get_clicked_link',
            'classpath'   => 'blocks/featured_tool/externallib.php',
            'description' => 'Logs when a user has clicked on a featured tool link',
            'type'        => 'write',
            'ajax'        => true,
            'loginrequired' => true,
            'capabilities' => '',
      ],
];

$services = [
    'Featured Tool Link Click Tracking Service' => [
        'functions' => [
            'block_featured_tool_get_clicked_link',
        ],
         'restrictedusers' => 0,
          'enabled' => 1,
      ],
];
