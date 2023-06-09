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
 * Form for editing featured_tool block instances.
 *
 * @package     block_featured_tool
 * @copyright   2023 Derek Wilson <wilsondc5@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_featured_tool_edit_form extends block_edit_form {

    /**
     * Extends the configuration form for block_featured_tool.
     *
     * @param MoodleQuickForm $mform The form being built.
     */
    protected function specific_definition($mform): void {

        global $USER;

        $isallowed = false;
        $courses = enrol_get_all_users_courses($USER->id, true);
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            if (has_capability('moodle/course:manageactivities', $context)) {
                $isallowed = true;
                break;
            }
        }

        if ($isallowed) {
            $editoroptions = array(
                    'subdirs' => 0,
                    'maxbytes' => $this->maxbytes,
                    'maxfiles' => EDITOR_UNLIMITED_FILES,
                    'changeformat' => 1,
                    'context' => $this->block->context,
                    'noclean' => 1,
                    'trusttext' => 0
            );

            $mform->addElement('editor', 'featuredmedia', get_string('featuredtool', 'block_featured_tool'), null, $editoroptions);
            $mform->setType('featuredmedia', PARAM_RAW);
        }
    }
}
