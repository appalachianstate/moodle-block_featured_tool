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
 * Featured Tool Block external API
 * @package     block_featured_tool
 * @copyright   2025 Lina Brown <brownli2@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . "/externallib.php");

/**
 * Class containing methods for sending click data to DB.
 */
class block_featured_tool_external extends \core_external\external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters link_name
     */
    public static function get_clicked_link_parameters() {
        return new external_function_parameters(
            ['link_name' => new external_value(PARAM_TEXT, 'Name of clicked link')]
        );
    }
    /**
     * Returns success or failure message from the DB operation
     * @param string  $linkname is name of the clicked link from the data-name attribute
     * @return array $message
     */
    public static function get_clicked_link($linkname) {
        global $DB, $USER;
        $linkname = trim($linkname);
        try {
            $record = new stdClass();
            $record->user_id = $USER->id;
            $record->link_name = $linkname;
            $record->time_clicked = time();
            $DB->insert_record('block_featured_tool_link_clicks', $record);
            $message = "Link click record added successfully!";
        } catch (Exception $e) {
            $message = "Error: Link click record insertion failed. " . $e->getMessage();
        }
        return [
            'message' => $message,
        ];
    }
    /**
     * Returns description of method result value
     * @return external_single_structure
     */
    public static function get_clicked_link_returns(): external_single_structure {
        return new external_single_structure([
            'message' => new external_value(PARAM_TEXT, 'The message body'),
        ]);
    }
}
