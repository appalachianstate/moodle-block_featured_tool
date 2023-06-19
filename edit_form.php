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
    protected function specific_definition($mform) {
        global $USER;

        $isallowed = false;
        $courses = enrol_get_all_users_courses($USER->id, true);
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            if (has_capability('moodle/site:configview', $context)) {
                $isallowed = true;
                break;
            }
        }

        if ($isallowed) {
            // Section header title.
            $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

            $editoroptions = array(
                    'maxfiles' => EDITOR_UNLIMITED_FILES,
                    'noclean' => true,
                    'trusttext' => false,
                    'context' => $this->block->context,
            );
            $mform->addElement('editor', 'config_text', get_string('featured_tool:media', 'block_featured_tool'), null,
                    $editoroptions);
            $mform->setType('config_text', PARAM_RAW); // XSS is prevented when printing the block contents and serving files
        }
    }

    /** Loads in existing data as form defaults.
     * Usually new entry defaults are stored directly in form definition (new entry form);
     * this function is used to load in data where values already exist and data is being edited (edit entry form).
     *
     * @param $defaults
     * @return void
     */
    function set_data($defaults) {
        global $DB;

        $draftid_editor = file_get_submitted_draft_itemid('config_text');

        // If there is text in the block's config_text, load it
        if (!empty($this->block->config) && !empty($this->block->config->text)) {
            $text = $this->block->config->text;
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
        } else {
            $text = '';
        }

        // Loads any already added files to the feature tool block's draft editor
        $defaults->config_text['text'] =
                file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_featured_tool', 'content', 0,
                        array('subdirs' => true), $currenttext);
        $defaults->config_text['itemid'] = $draftid_editor;
        $defaults->config_text['format'] = $this->block->config->format ?? FORMAT_MOODLE;

        // have to delete text here, otherwise parent::set_data will empty content
        // of editor
        unset($this->block->config->text);
        parent::set_data($defaults);
        // restore $text
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->text = $text;
    }
}
