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

        $mform->addElement('text', 'config_subtitle0', get_string('featured_tool:subtitle1', 'block_featured_tool'));
        $mform->setType('config_subtitle0', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail0', get_string('featured_tool:thumbnail1', 'block_featured_tool'), null, $thumbnailoptions);

        $mform->addElement('editor', 'config_text0', get_string('featured_tool:media1', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text0', PARAM_RAW);

        // Parameters for second featured tool
        $mform->addElement('html', '
            <hr/>
            <h4>Second Featured Tool</h4>
        ');

        $mform->addElement('text', 'config_subtitle1', get_string('featured_tool:subtitle2', 'block_featured_tool'));
        $mform->setType('config_subtitle1', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail1', get_string('featured_tool:thumbnail2', 'block_featured_tool'), null, $thumbnailoptions);

        $mform->addElement('editor', 'config_text1', get_string('featured_tool:media2', 'block_featured_tool'), null,
                $editoroptions);
        $mform->setType('config_text1', PARAM_RAW);

        // Parameters for third featured tool
        $mform->addElement('html', '
            <hr/>
            <h4>Third Featured Tool</h4>
        ');

        $mform->addElement('text', 'config_subtitle2', get_string('featured_tool:subtitle3', 'block_featured_tool'));
        $mform->setType('config_subtitle2', PARAM_TEXT);

        $mform->addElement('filemanager', 'config_thumbnail2', get_string('featured_tool:thumbnail3', 'block_featured_tool'), null, $thumbnailoptions);

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

        // If there is text in the block's config_text, load it in the respective text variable
        // If there are any subtitles set, load them into respective subtitle variables
        if (!empty($this->block->config)) {
            // Loads any already added files to the respective feature tool block's draft editor
            foreach ($this->block->config->text as $index => $textInfo) {
                // Grabs the actual editor text
                $text = $textInfo['content'];
                // Grabs the canonical index set during saving
                $canonIdx = $textInfo['idx'];
                if (!empty($text) && $canonIdx === $index) {
                    $textKey = 'text' . $index;
                    ${'text' . $index} = $text;
                    $draftIdEditor = file_get_submitted_draft_itemid('config_' . $textKey);

                    $defaults->{'config_' . $textKey}['text'] =
                            file_prepare_draft_area($draftIdEditor, $sitecontext->id, 'block_featured_tool', 'content' . $index, 0,
                                    array('subdirs' => true), $text);
                    $defaults->{'config_' . $textKey}['itemid'] = $draftIdEditor;
                    $defaults->{'config_' . $textKey}['format'] = FORMAT_HTML;

                    // Remove the text from the config so that parent::set_data doesn't empty it.
                    unset($this->block->config->$textKey);
                }
            }
            // Loads the subtitle set for a respective featured tool block if it exists
            foreach ($this->block->config->subtitle as $index => $subtitleInfo) {
                // Grabs the actual subtitle text
                $subtitle = $subtitleInfo['content'];
                // Grabs the canonical index set during saving
                $canonIdx = $textInfo['idx'];
                if (!empty($subtitle) && $canonIdx === $index) {
                    $subKey = 'subtitle' . $index;
                    ${'subtitle' . $index} = $subtitle;
                    $defaults->{'config_' . $subKey} = format_string($subtitle, true, $this->page->context);
                    // Remove the subtitle from the config so that parent::set_data doesn't set it.
                    unset($this->block->config->$subKey);
                }
            }
        }

        parent::set_data($defaults);
        // Restore variables
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }

        // Resets the preserved editor text variables
        if (isset($text0)) {
            $this->block->config->text0 = $text0;
            unset($text0);
        }
        if (isset($text1)) {
            $this->block->config->text1 = $text1;
            unset($text1);
        }
        if (isset($text2)) {
            $this->block->config->text2 = $text2;
            unset($text2);
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
