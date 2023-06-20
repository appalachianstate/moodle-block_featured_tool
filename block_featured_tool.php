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
        global $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

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

            $this->content = new stdClass();
            $this->content->items = array();
            $this->content->icons = array();
            $this->content->footer = '';

            $filteropt = new stdClass;
            $filteropt->overflowdiv = true;
            $filteropt->noclean = true;

            if (!empty($this->config->text1)) {
                $sitecontext = context_system::instance();
                $this->config->text1 = file_rewrite_pluginfile_urls($this->config->text1, 'pluginfile.php', $sitecontext->id, 'block_featured_tool', 'content', null);
                $format = FORMAT_HTML;
                $this->content->text = format_text($this->config->text1, $format, $filteropt);
            } else {
                $text = '';
                $this->content->text = $text;
            }
        } else {
            return '';
        }

        return $this->content;
    }

    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {

        $config = clone($data);
        $sitecontext = context_system::instance();
        // Move embedded files into a proper filearea for all three editors and adjust HTML links to match
        $config->text1 = file_save_draft_area_files($data->text1['itemid'], $sitecontext->id, 'block_featured_tool', 'content', 0, array('subdirs'=>true), $data->text1['text']);
        $config->format1 = $data->text1['format'];

        $config->text2 = file_save_draft_area_files($data->text2['itemid'], $sitecontext->id, 'block_featured_tool', 'content', 0, array('subdirs'=>true), $data->text2['text']);
        $config->format2 = $data->text2['format'];

        $config->text3 = file_save_draft_area_files($data->text3['itemid'], $sitecontext->id, 'block_featured_tool', 'content', 0, array('subdirs'=>true), $data->text3['text']);
        $config->format3 = $data->text3['format'];

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
