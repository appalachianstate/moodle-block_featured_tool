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
 * Plugin functions.
 *
 * @package     block_featured_tool
 * @copyright   2023 Derek Wilson <wilsondc5@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/repository/lib.php');

/**
 * Add featured_tool instance.
 *
 * @param stdClass $data
 * @param block_featured_tool_edit_form $mform
 * @return int new featured_tool instance id
 */
function featured_tool_add_instance($data, $mform = null) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($mform) {
        $editoroptions = array(
                'subdirs' => 0,
                'maxbytes' => $CFG->maxbytes,
                'maxfiles' => -1,
                'changeformat' => 1,
                'context' => $CFG->context,
                'noclean' => 1,
                'trusttext' => 0
        );
        $formattedtypes = array('featuredmedia');
        foreach ($formattedtypes as $type) {
            ${'temp_' . $type} = $data->$type;
            $data->$type = ${'temp_' . $type}['text'];
            $data->{$type . 'format'} = ${'temp_' . $type}['format'];
        }
    }

    $data->id = $DB->insert_record('featuredtool', $data);

    // We need to use context now, so we need to make sure all needed info is already in db.
    #$DB->set_field('course_modules', 'instance', $data->id, array('id' => $data->coursemodule));

    if ($mform) {
        foreach ($formattedtypes as $type) {
            if (!empty(${'temp_' . $type}['itemid'])) {
                $draftitemid = ${'temp_' . $type}['itemid'];
                $data->$type = file_save_draft_area_files($draftitemid, $context->id, 'block_featured_tool', $type, 0, $editoroptions, $data->$type);
            }
        }
    }

    $DB->update_record('featuredtool', $data);

    return $data->id;
}

/**
 * Update featured_tool instance.
 *
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function featured_tool_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    $data->timemodified = time();
    $data->id = $data->instance;
    $data->revision++;

    $editoroptions = array(
            'subdirs' => 0,
            'maxbytes' => $CFG->maxbytes,
            'maxfiles' => -1,
            'changeformat' => 1,
            'context' => $CFG->context,
            'noclean' => 1,
            'trusttext' => 0
    );

    $formattedtypes = array('featuredmedia');
    foreach ($formattedtypes as $type) {
        ${'temp_' . $type} = $data->$type;
        $data->$type = ${'temp_' . $type}['text'];
        $data->{$type . 'format'} = ${'temp_' . $type}['format'];

        $draftitemid = ${'temp_' . $type}['itemid'];

        if ($draftitemid) {
            $data->$type = file_save_draft_area_files($draftitemid, $context->id, 'block_featured_tool', $type, 0, $editoroptions, $data->$type);
        }
    }

    $DB->update_record('featuredtool', $data);

    return true;
}
