<?php

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
 * External Web Service Template
 *
 * @package    Externallib
 * @author      2025 Lina Brown <brownli2@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir . "/externallib.php");

class block_featured_tool_external extends \core_external\external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_clicked_link_parameters(){
        return new external_function_parameters(
            array(
                'link_name'=> new external_value(PARAM_TEXT, 'Name of clicked link')
            )
        );
    }
    /**
     * Returns success or failutre message from the DB operation
     * @param string  link_name is name of the clicked link from the data-name attribute
     * @return array success message
     */
    public static function get_clicked_link($link_name) {
        global $DB, $USER;
        $link_name = trim($link_name);
        // Since we are storing link name in DB table, add extra check to make sure it's not a SQL statement
        if (strlen($link_name) > 50) {
            throw new moodle_exception('invalidlink', 'block_featured_tool', 'Link name is too long!');
        }
        try {
            $record = new stdClass();
            $record->user_id = $USER->id;
            $record->link_name = $link_name;
            $record->time_clicked = time();
            $DB->insert_record('block_featured_tool_link_clicks', $record);
            $message = "Link click record added successfully!";
        }
        catch (Exception $e) {
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