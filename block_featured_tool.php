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
 * Block featured_tool is defined here.
 *
 * @package     block_featured_tool
 * @copyright   2023 Derek Wilson <wilsondc5@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_featured_tool extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_featured_tool');
    }

    /**
     * Returns the block contents.
     * Checks if user is enrolled in a course as a teacher before doing rendering the block.
     * 1. Global user ($USER), grab ID
     * 2. Get courses for the userID
     * 3. Get a list of course IDs that the user has enrollment in
     * 4. Loop through course ids and has_capability check with course ID (manageactivities)
     * 5. If true for any course, break and continue with displaying the block. Otherwise, just don't show the block (return "")
     * @return stdClass The block contents.
     */
    public function get_content() {

        global $USER, $CFG;

        require_once($CFG->libdir . '/filelib.php');

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
            if ($this->content !== null) {
                return $this->content;
            }

            if (empty($this->instance)) {
                $this->content = '';
                return $this->content;
            }

            $this->content = new stdClass();
            $this->content->items = array();
            $this->content->icons = array();
            $this->content->footer = '';

            $filteropt = new stdClass;
            $filteropt->overflowdiv = true;
            $filteropt->noclean = false;

            print_object($this->config);

            if (true) {
                // rewrite url
                #$this->config->text = file_rewrite_pluginfile_urls($this->config->text, 'pluginfile.php', $this->context->id, 'block_featured_tool', 'featuredmedia', NULL);
                $this->config->text = $this->config;
                // Default to FORMAT_HTML which is what will have been used before the
                // editor was properly implemented for the block.
                $format = FORMAT_HTML;
                // Check to see if the format has been properly set on the config
                if (isset($this->config->format)) {
                    $format = $this->config->format;
                }
                #$this->content->text = format_text($this->config->text, $format, $filteropt);
                $this->content->text = print_r($this->config->text);
            } else {
                // Shows up if there is no media to show.
                $text = 'Insert media in the Featured Tool for it to show up here.';
                $this->content->text = $text;
            }

            return $this->content;
        }

        return '';
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_featured_tool');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $CFG;

        $config = clone($data);

        $editoroptions = array(
                'subdirs' => 0,
                'maxbytes' => $CFG->maxbytes,
                'maxfiles' => EDITOR_UNLIMITED_FILES,
                'changeformat' => 1,
                'context' => $this->context,
                'noclean' => 1,
                'trusttext' => 0
        );

        $type = 'featuredmedia';
        $draftitemid = $data;

        print_object($data);
        $data->$type = $type;
        #$data->{$type . 'format'} = ${'temp_' . $type}['format'];

        if ($draftitemid) {
            #$config->text = file_save_draft_area_files($draftitemid, $this->context->id, 'block_featured_tool', $type, 0, $editoroptions, $data->$type);
        }
        $config->text = "Test text";
        // Move embedded files into a proper filearea and adjust HTML links to match
        #$config->text = file_save_draft_area_files($data->text['itemid'], $this->context->id, 'block_featured_tool', 'featuredmedia', 0, $editoroptions, $data->text['text']);
        $config->format = $data->text['format'];

        parent::instance_config_save($config, $nolongerused);
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return array(
            'my' => true,
        );
    }
}
