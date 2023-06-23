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
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        global $USER, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // Checks if user is an admin/manager
        $isallowed = false;
        $sitecontext = context_system::instance();
        if (has_capability('moodle/site:manageblocks', $sitecontext)) {
            $isallowed = true;
        }
        // If user is not an admin/manager, checks if user is a teacher in a course
        else {
            $courses = enrol_get_all_users_courses($USER->id, true);
            foreach ($courses as $course) {
                $context = context_course::instance($course->id);
                if (has_capability('moodle/course:manageactivities', $context)) {
                    $isallowed = true;
                    break;
                }
            }
        }

        if ($isallowed) {

            $this->content = new stdClass();
            $this->content->items = array();
            $this->content->icons = array();
            $this->content->footer = '';

            $filteropt = new stdClass;
            $filteropt->overflowdiv = true;
            $filteropt->noclean = true;

            if (!empty($this->config->text)) {
                // Only blocks with text in them should be in config->text at this point
                $max = sizeof($this->config->text);
                $randInt = random_int(0, $max-1);
                // Selects a random block based on the random int
                $selectedBlock = $this->config->text[$randInt];

                $selectedBlock = file_rewrite_pluginfile_urls($selectedBlock, 'pluginfile.php', $sitecontext->id, 'block_featured_tool', ('content' . $randInt), null);
                $format = $this->config->format;

                $data = array(
                    "subtitle" => "Card subtitle",
                    "snippet" => format_text($selectedBlock, $format, $filteropt),
                    "editorhtml" => format_text($selectedBlock, $format, $filteropt),

                );
                $this->content->text = $OUTPUT->render_from_template('block_featured_tool/featuredcontent', $data);
            } else {
                $text = '';
                $this->content->text = $text;
            }
        } else {
            return '';
        }

        return $this->content->text;
    }

    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {

        $config = clone($data);

        $sitecontext = context_system::instance();
        // Generates an array of the text fields
        $data->text = array($data->text1, $data->text2, $data->text3);

        // Save only area files that have something in them and store them
        $config->text = array();
        foreach ($data->text as $text) {
            if (!empty($text) && !empty($text['text'])) {
                // Generates the key of where the text will be stored in the final text array
                $key = sizeof($config->text);
                // Move embedded files into a proper filearea and adjust HTML links to match
                $text = file_save_draft_area_files($text['itemid'], $sitecontext->id,
                        'block_featured_tool', ('content' . $key), 0, array('subdirs'=>true), $text['text']);
                $config->text[$key] = $text;
            }
        }
        $config->format = FORMAT_HTML;

        parent::instance_config_save($config, $nolongerused);
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