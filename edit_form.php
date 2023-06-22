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

        $sitecontext = context_system::instance();
        require_capability('block/featured_tool:addinstance', $sitecontext);

        // Section header title.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $editoroptions = array(
                'maxfiles' => EDITOR_UNLIMITED_FILES,
                'noclean' => true,
                'trusttext' => false,
                'context' => $sitecontext,
        );
        $mform->addElement('editor', 'config_text1', get_string('featured_tool:media1', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text1', PARAM_RAW);

        $mform->addElement('editor', 'config_text2', get_string('featured_tool:media2', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text2', PARAM_RAW);

        $mform->addElement('editor', 'config_text3', get_string('featured_tool:media3', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text3', PARAM_RAW);
    }

    /** Loads in existing data as form defaults.
     * Usually new entry defaults are stored directly in form definition (new entry form);
     * this function is used to load in data where values already exist and data is being edited (edit entry form).
     *
     * @param $defaults
     * @return void
     */
    function set_data($defaults) {

        $draftid_editor1 = file_get_submitted_draft_itemid('config_text1');
        $draftid_editor2 = file_get_submitted_draft_itemid('config_text2');
        $draftid_editor3 = file_get_submitted_draft_itemid('config_text3');

        $sitecontext = context_system::instance();

        // If there is text in the block's config_text, load it in the respective text variable
        $text1 = '';
        $text2 = '';
        $text3 = '';
        if (!empty($this->block->config)) {
            // Loads any already added files to the first feature tool block's draft editor
            if (!empty($this->block->config->text1)) {
                $text1 = $this->block->config->text1;
                $defaults->config_text1['text'] =
                        file_prepare_draft_area($draftid_editor1, $sitecontext->id, 'block_featured_tool', 'content', 0,
                                array('subdirs' => true), $text1);
                $defaults->config_text1['itemid'] = $draftid_editor1;
            }
            // Loads any already added files to the second feature tool block's draft editor
            if (!empty($this->block->config->text2)) {
                $text2 = $this->block->config->text2;
                $defaults->config_text2['text'] =
                        file_prepare_draft_area($draftid_editor2, $sitecontext->id, 'block_featured_tool', 'content', 0,
                                array('subdirs' => true), $text2);
                $defaults->config_text2['itemid'] = $draftid_editor2;
            }
            // Loads any already added files to the third feature tool block's draft editor
            if (!empty($this->block->config->text3)) {
                $text3 = $this->block->config->text3;
                $defaults->config_text3['text'] =
                        file_prepare_draft_area($draftid_editor3, $sitecontext->id, 'block_featured_tool', 'content', 0,
                                array('subdirs' => true), $text3);
                $defaults->config_text3['itemid'] = $draftid_editor3;
            }
        }

        // have to delete text here, otherwise parent::set_data will empty content
        // of editor
        unset($this->block->config->text1);
        unset($this->block->config->text2);
        unset($this->block->config->text3);
        // restore $text
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }

        $this->block->config->text1 = $text1;
        $this->block->config->text2 = $text2;
        $this->block->config->text3 = $text3;

        parent::set_data($defaults);
    }
}