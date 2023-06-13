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
 * Form for editing featured tool block instances.
 *
 * @package   block_featured_tool
 * @copyright 2023 Derek Wilson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing featured tool block instances.
 *
 * @copyright 2023 Derek Wilson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_featured_tool_edit_form extends block_edit_form {
    /**
     * Extends the configuration form for block_featured_tool.
     *
     * @param MoodleQuickForm $mform The form being built.
     */
    protected function specific_definition($mform) {
        global $CFG, $USER;

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
            // Fields for editing featured tool block title and contents.
            $mform->addElement('header', 'configheader', get_string('editingblock', 'block_featured_tool'));

            $mform->addElement('text', 'config_title', get_string('featuredtoolconfigtitle', 'block_featured_tool'));
            $mform->setType('config_title', PARAM_TEXT);

            $editoroptions = array(
                    'maxfiles' => EDITOR_UNLIMITED_FILES,
                    'noclean' => true,
                    'trusttext' => false,
                    'context' => $this->block->context,
                    );
            $mform->addElement('editor', 'config_text', get_string('featuredtool', 'block_featured_tool'), null, $editoroptions);
            $mform->setType('config_text', PARAM_RAW); // XSS is prevented when printing the block contents and serving files
        }
    }

    /** Loads in existing data as form defaults.
     * Usually new entry defaults are stored directly in form definition (new entry form);
     * this function is used to load in data where values already exist and data is being edited (edit entry form).
     * @param $defaults
     * @return void
     */
    function set_data($defaults) {
        if (!empty($this->block->config) && !empty($this->block->config->text)) {
            $text = $this->block->config->text;
            $draftid_editor = file_get_submitted_draft_itemid('config_text');
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
            $sitecontext = context_system::instance();
            $defaults->config_text['text'] = file_prepare_draft_area($draftid_editor, $sitecontext->id, 'block_featured_tool', 'content', 0, array('subdirs'=>true), $currenttext);
            $defaults->config_text['itemid'] = $draftid_editor;
            $defaults->config_text['format'] = $this->block->config->format ?? FORMAT_MOODLE;
        } else {
            $text = '';
        }

        if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
            // If a title has been set but the user cannot edit it format it nicely
            $title = $this->block->config->title;
            $defaults->config_title = format_string($title, true, $this->page->context);
            // Remove the title from the config so that parent::set_data doesn't set it.
            unset($this->block->config->title);
        }

        // have to delete text here, otherwise parent::set_data will empty content
        // of editor
        unset($this->block->config->text);
        parent::set_data($defaults);
        // restore $text
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->text = $text;
        if (isset($title)) {
            // Reset the preserved title
            $this->block->config->title = $title;
        }
    }
}
