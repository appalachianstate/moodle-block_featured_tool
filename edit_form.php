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
                'maxbytes' => 104857600,
                'areamaxbytes' => 104857600,
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
        $mform->addElement('html', '
            <br/>
            <h4>First Featured Tool</h4>
        ');

        $mform->addElement('text', 'config_subtitle1', get_string('featured_tool:subtitle1', 'block_featured_tool'));
        $mform->setType('config_subtitle1', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail1', get_string('featured_tool:thumbnail1', 'block_featured_tool'), null, $thumbnailoptions);

        $mform->addElement('editor', 'config_text0', get_string('featured_tool:media1', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text0', PARAM_RAW);

        // Parameters for second featured tool
        $mform->addElement('html', '
            <hr/>
            <h4>Second Featured Tool</h4>
        ');

        $mform->addElement('text', 'config_subtitle2', get_string('featured_tool:subtitle2', 'block_featured_tool'));
        $mform->setType('config_subtitle2', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail2', get_string('featured_tool:thumbnail2', 'block_featured_tool'), null, $thumbnailoptions);

        $mform->addElement('editor', 'config_text1', get_string('featured_tool:media2', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text1', PARAM_RAW);

        // Parameters for third featured tool
        $mform->addElement('html', '
            <hr/>
            <h4>Third Featured Tool</h4>
        ');

        $mform->addElement('text', 'config_subtitle3', get_string('featured_tool:subtitle3', 'block_featured_tool'));
        $mform->setType('config_subtitle3', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail3', get_string('featured_tool:thumbnail3', 'block_featured_tool'), null, $thumbnailoptions);

        $mform->addElement('editor', 'config_text2', get_string('featured_tool:media3', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text2', PARAM_RAW);
    }

    /** Loads in existing data as form defaults.
     * Usually new entry defaults are stored directly in form definition (new entry form);
     * this function is used to load in data where values already exist and data is being edited (edit entry form).
     *
     * @param $defaults
     * @return void
     */
    function set_data($defaults) {

        $sitecontext = context_system::instance();

        $acceptedtypes = (new \core_form\filetypes_util)->normalize_file_types('.jpg,.gif,.png');
        $thumbnailoptions = array(
                'subdirs' => 0,
                'maxbytes' => 104857600,
                'areamaxbytes' => 104857600,
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
        // If there is text in the block's config_text, load it in the respective text variable
        // If there are any subtitles set, load them into respective subtitle variables
        // If there are any thumbnails uploaded, load them into the respective thumbnail variables
        if (!empty($this->block->config)) {
            foreach ($this->block->config->text as $idx => $text) {
                ${'text' . $idx} = $text;
                $config_text_num = 'config_text' . $idx;
                $draftid_editor = file_get_submitted_draft_itemid($config_text_num);

                $defaults->{$config_text_num}['text'] =
                        file_prepare_draft_area($draftid_editor, $sitecontext->id, 'block_featured_tool', ('content' . $idx), 0,
                                array('subdirs' => true), $text);
                $defaults->{$config_text_num}['itemid'] = $draftid_editor;
                $defaults->{$config_text_num}['format'] = FORMAT_HTML;
                print_object($defaults);
                // Remove the text from the config so that parent::set_data doesn't empty it.
                print_object($this->block->config);
                unset($this->block->config->${'text' . $idx});
                print_object($this->block->config);
            }
            // Loads any already added files to the first feature tool block's draft editor
            //if (!empty($this->block->config->text1)) {
            //    $text1 = $this->block->config->text1;
            //    $draftid_editor1 = file_get_submitted_draft_itemid('config_text0');
            //    $defaults->config_text0['text'] =
            //            file_prepare_draft_area($draftid_editor1, $sitecontext->id, 'block_featured_tool', 'content0', 0,
            //                    array('subdirs' => true), $text1);
            //    $defaults->config_text0['itemid'] = $draftid_editor1;
            //    $defaults->config_text0['format'] = FORMAT_HTML;
            //    print_object($defaults);
            //    // Remove the thumbnail from the config so that parent::set_data doesn't empty it.
            //    unset($this->block->config->text1);
            //}
            //// Loads any already added files to the second feature tool block's draft editor
            //if (!empty($this->block->config->text2)) {
            //    $text2 = $this->block->config->text2;
            //    $draftid_editor2 = file_get_submitted_draft_itemid('config_text1');
            //    $defaults->config_text1['text'] =
            //            file_prepare_draft_area($draftid_editor2, $sitecontext->id, 'block_featured_tool', 'content1', 0,
            //                    array('subdirs' => true), $text2);
            //    $defaults->config_text1['itemid'] = $draftid_editor2;
            //    $defaults->config_text1['format'] = FORMAT_HTML;
            //    print_object($defaults);
            //    // Remove the thumbnail from the config so that parent::set_data doesn't empty it.
            //    unset($this->block->config->text2);
            //}
            //// Loads any already added files to the third feature tool block's draft editor
            //if (!empty($this->block->config->text3)) {
            //    $text3 = $this->block->config->text3;
            //    $draftid_editor3 = file_get_submitted_draft_itemid('config_text2');
            //    $defaults->config_text2['text'] =
            //            file_prepare_draft_area($draftid_editor3, $sitecontext->id, 'block_featured_tool', 'content2', 0,
            //                    array('subdirs' => true), $text3);
            //    $defaults->config_text2['itemid'] = $draftid_editor3;
            //    $defaults->config_text2['format'] = FORMAT_HTML;
            //    print_object($defaults);
            //    // Remove the thumbnail from the config so that parent::set_data doesn't empty it.
            //    unset($this->block->config->text3);
            //}
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
                $draftid_thumbnail1 = file_get_submitted_draft_itemid('config_thumbnail1');
                file_prepare_draft_area($draftid_thumbnail1, $sitecontext->id, 'block_featured_tool', 'thumbnail', 0,
                        $thumbnailoptions, $thumbnail1);
                $defaults->config_thumbnail1 = $draftid_thumbnail1;
                // Remove the thumbnail from the config so that parent::set_data doesn't set it.
                unset($this->block->config->thumbnail1);
            }
            // Loads an already added thumbnail to the second feature tool block's file picker
            if (!empty($this->block->config->thumbnail2)) {
                $thumbnail2 = $this->block->config->thumbnail2;
                $draftid_thumbnail2 = file_get_submitted_draft_itemid('config_thumbnail2');
                file_prepare_draft_area($draftid_thumbnail2, $sitecontext->id, 'block_featured_tool', 'thumbnail', 0,
                        $thumbnailoptions, $thumbnail2);
                $defaults->config_thumbnail2 = $draftid_thumbnail2;
                // Remove the thumbnail from the config so that parent::set_data doesn't set it.
                unset($this->block->config->thumbnail2);
            }
            // Loads an already added thumbnail to the third feature tool block's file picker
            if (!empty($this->block->config->thumbnail3)) {
                $thumbnail3 = $this->block->config->thumbnail3;
                $draftid_thumbnail3 = file_get_submitted_draft_itemid('config_thumbnail3');
                file_prepare_draft_area($draftid_thumbnail3, $sitecontext->id, 'block_featured_tool', 'thumbnail', 0,
                        $thumbnailoptions, $thumbnail3);
                $defaults->config_thumbnail3 = $draftid_thumbnail3;
                // Remove the thumbnail from the config so that parent::set_data doesn't set it.
                unset($this->block->config->thumbnail3);
            }
        }

        print_object($defaults);
        parent::set_data($defaults);
        // Restore variables
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }

        // Resets the preserved editor text variables
        if (isset($text0)) {
            $this->block->config->text0 = $text0;
        }
        if (isset($text1)) {
            $this->block->config->text1 = $text1;
        }
        if (isset($text2)) {
            $this->block->config->text2 = $text2;
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
        print_object($this->block->config);
    }
}