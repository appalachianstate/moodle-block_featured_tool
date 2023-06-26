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

        $acceptedtypes = (new \core_form\filetypes_util)->normalize_file_types('.jpg,.gif,.png');
        $thumbnailoptions = array(
                'subdirs' => 0,
                'maxbytes' => 1048576,
                'areamaxbytes' => 1048576,
                'maxfiles' => 1,
                'accepted_types' => $acceptedtypes,
                'context' => $sitecontext,
                'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
        );
        $editoroptions = array(
                'maxfiles' => EDITOR_UNLIMITED_FILES,
                'noclean' => true,
                'trusttext' => false,
                'context' => $sitecontext,
        );

        // Parameters for first featured tool
        $mform->addElement('text', 'config_subtitle1', get_string('featured_tool:subtitle', 'block_featured_tool'));
        $mform->setType('config_subtitle1', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail1', get_string('featured_tool:thumbnail', 'block_featured_tool'), null, $thumbnailoptions);

        $mform->addElement('editor', 'config_text1', get_string('featured_tool:media1', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text1', PARAM_RAW);

        // Parameters for second featured tool
        $mform->addElement('text', 'config_subtitle2', get_string('featured_tool:subtitle', 'block_featured_tool'));
        $mform->setType('config_subtitle2', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail2', get_string('featured_tool:thumbnail', 'block_featured_tool'), null, $thumbnailoptions);

        $mform->addElement('editor', 'config_text2', get_string('featured_tool:media2', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text2', PARAM_RAW);

        // Parameters for third featured tool
        $mform->addElement('text', 'config_subtitle3', get_string('featured_tool:subtitle', 'block_featured_tool'));
        $mform->setType('config_subtitle3', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail3', get_string('featured_tool:thumbnail', 'block_featured_tool'), null, $thumbnailoptions);

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

        $draftid_thumbnail1 = file_get_submitted_draft_itemid('config_thumbnail1');
        $draftid_thumbnail2 = file_get_submitted_draft_itemid('config_thumbnail2');
        $draftid_thumbnail3 = file_get_submitted_draft_itemid('config_thumbnail3');

        $sitecontext = context_system::instance();

        $acceptedtypes = (new \core_form\filetypes_util)->normalize_file_types('.jpg,.gif,.png');
        $thumbnailoptions = array(
                'subdirs' => 0,
                'maxbytes' => 1048576,
                'areamaxbytes' => 1048576,
                'maxfiles' => 1,
                'accepted_types' => $acceptedtypes,
                'context' => $sitecontext,
                'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
        );
        // If there is text in the block's config_text, load it in the respective text variable
        // Also, if there are any subtitles set, load them into respective subtitle variables
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
                // Remove the thumbnail from the config so that parent::set_data doesn't empty it.
                unset($this->block->config->text1);
            }
            // Loads any already added files to the second feature tool block's draft editor
            if (!empty($this->block->config->text2)) {
                $text2 = $this->block->config->text2;
                $defaults->config_text2['text'] =
                        file_prepare_draft_area($draftid_editor2, $sitecontext->id, 'block_featured_tool', 'content', 0,
                                array('subdirs' => true), $text2);
                $defaults->config_text2['itemid'] = $draftid_editor2;
                // Remove the thumbnail from the config so that parent::set_data doesn't empty it.
                unset($this->block->config->text2);
            }
            // Loads any already added files to the third feature tool block's draft editor
            if (!empty($this->block->config->text3)) {
                $text3 = $this->block->config->text3;
                $defaults->config_text3['text'] =
                        file_prepare_draft_area($draftid_editor3, $sitecontext->id, 'block_featured_tool', 'content', 0,
                                array('subdirs' => true), $text3);
                $defaults->config_text3['itemid'] = $draftid_editor3;
                // Remove the thumbnail from the config so that parent::set_data doesn't empty it.
                unset($this->block->config->text3);
            }
            // Loads the subtitle set for the first featured tool block if it exists
            if (!empty($this->block->config->subtitle1)) {
                $subtitle1 = $this->block->config->subtitle1;
                $defaults->config_subtitle1 = format_string($subtitle1, true, $this->page->context);
                // Remove the subtitle from the config so that parent::set_data doesn't set it.
                unset($this->block->config->subtitle1);
            }
            // Loads the subtitle set for the second featured tool block if it exists
            if (!empty($this->block->config->subtitle2)) {
                $subtitle2 = $this->block->config->subtitle2;
                $defaults->config_subtitle2 = format_string($subtitle2, true, $this->page->context);
                // Remove the subtitle from the config so that parent::set_data doesn't set it.
                unset($this->block->config->subtitle2);
            }
            // Loads the subtitle set for the third featured tool block if it exists
            if (!empty($this->block->config->subtitle3)) {
                $subtitle3 = $this->block->config->subtitle3;
                $defaults->config_subtitle3 = format_string($subtitle3, true, $this->page->context);
                // Remove the subtitle from the config so that parent::set_data doesn't set it.
                unset($this->block->config->subtitle3);
            }
            // Loads an already added thumbnail to the first feature tool block's file picker
            if (!empty($this->block->config->thumbnail1)) {
                $thumbnail1 = $this->block->config->thumbnail1;
                $defaults->config_thumbnail1['text'] =
                        file_prepare_draft_area($draftid_thumbnail1, $sitecontext->id, 'block_featured_tool', 'thumbnail', 0,
                                $thumbnailoptions, $thumbnail1);
                $defaults->config_thumbnail1['itemid'] = $draftid_thumbnail1;
                // Remove the thumbnail from the config so that parent::set_data doesn't set it.
                unset($this->block->config->thumbnail1);
            }
            // Loads an already added thumbnail to the second feature tool block's file picker
            if (!empty($this->block->config->thumbnail2)) {
                $thumbnail2 = $this->block->config->thumbnail2;
                $defaults->config_thumbnail2['text'] =
                        file_prepare_draft_area($draftid_thumbnail2, $sitecontext->id, 'block_featured_tool', 'thumbnail', 0,
                                $thumbnailoptions, $thumbnail2);
                $defaults->config_thumbnail2['itemid'] = $draftid_thumbnail2;
                // Remove the thumbnail from the config so that parent::set_data doesn't set it.
                unset($this->block->config->thumbnail2);
            }
            // Loads an already added thumbnail to the third feature tool block's file picker
            if (!empty($this->block->config->thumbnail3)) {
                $thumbnail3 = $this->block->config->thumbnail3;
                $defaults->config_thumbnail3['text'] =
                        file_prepare_draft_area($draftid_thumbnail3, $sitecontext->id, 'block_featured_tool', 'thumbnail', 0,
                                $thumbnailoptions, $thumbnail3);
                $defaults->config_thumbnail3['itemid'] = $draftid_thumbnail3;
                // Remove the thumbnail from the config so that parent::set_data doesn't set it.
                unset($this->block->config->thumbnail3);
            }
        }

        parent::set_data($defaults);
        // Restore variables
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }

        // Resets the preserved editor text variables
        if (isset($text1)) {
            $this->block->config->text1 = $text1;
        }
        if (isset($text2)) {
            $this->block->config->text2 = $text2;
        }
        if (isset($text3)) {
            $this->block->config->text3 = $text3;
        }

        // Resets the preserved subtitles
        if (isset($subtitle1)) {
            $this->block->config->subtitle1 = $subtitle1;
        }
        if (isset($subtitle2)) {
            $this->block->config->subtitle2 = $subtitle2;
        }
        if (isset($subtitle3)) {
            $this->block->config->subtitle3 = $subtitle3;
        }

        // Resets the preserved thumbnails
        if (isset($thumbnail1)) {
            $this->block->config->thumbnail1 = $thumbnail1;
        }
        if (isset($thumbnail2)) {
            $this->block->config->thumbnail2 = $thumbnail2;
        }
        if (isset($thumbnail3)) {
            $this->block->config->thumbnail3 = $thumbnail3;
        }
    }
}