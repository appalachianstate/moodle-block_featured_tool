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
        // Initialize the configuration
        $this->config = get_config('block_featured_tool');
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

        global $USER, $DB;

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

            $id = optional_param('id', 0, PARAM_INT);
            if (!$media = $DB->get_record('featuredtool', $id)) {
                throw new moodle_exception('Requested media does not exist.');
            }

            if (get_config('block_featured_tool', 'featuredtool')) {
                $formatoptions = new stdClass();
                $formatoptions->noclean = false;
                $formatoptions->context = $this->context;

                if (!empty($media->featuredmedia)) {
                    echo html_writer::tag('h3', get_string('pluginname', 'block_featured_tool'));
                    $text = file_rewrite_pluginfile_urls($media->featuredmedia, 'pluginfile.php',
                            $context->id, 'block_featured_tool', 'featuredmedia', 0);
                    $text = format_text($text, $media->featuredmediaformat, $formatoptions);
                    $this->content->text = html_writer::tag('div', $text);
                }
            } else {
                // Grabs all the courses for the current user that are currently active
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
