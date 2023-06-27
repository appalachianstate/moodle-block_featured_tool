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
                // Grabs that block's subtitle
                $selectedBlockSubtitle = $this->config->subtitle[$randInt];

                $selectedBlock = file_rewrite_pluginfile_urls($selectedBlock, 'pluginfile.php', $sitecontext->id, 'block_featured_tool', ('content' . $randInt), null);
                // Stores the pluginfile link back into the respective config->text position
                $this->config->text[$randInt] = $selectedBlock;
                $format = $this->config->format;

                $fs = get_file_storage();
                $files = $fs->get_area_files($sitecontext->id, 'block_featured_tool', ('thumbnail'. $randInt), false, 'filename', false);
                // Tries to serve the thumbnail if it exists
                if (count($files)) {
                    // There should only ever be one file in the filearea
                    $file = reset($files);
                    // Creates a pluginfile URL for the thumbnail, since it comes from a file picker
                    $selectedBlockThumbnail = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                            null, $file->get_filepath(), $file->get_filename());
                }
                // Creates the data array expected by the featuredcontent template
                $data = array(
                    "subtitle" => $selectedBlockSubtitle,
                    "thumbnail" => $selectedBlockThumbnail ?? '',
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

        // Generates an array of the text fields
        $data->text = array($data->text1, $data->text2, $data->text3);
        // Generates an array of the subtitles
        $data->subtitle = array($data->subtitle1, $data->subtitle2, $data->subtitle3);
        // Generates an array of thumbnails
        $data->thumbnail = array($data->thumbnail1, $data->thumbnail2, $data->thumbnail3);

        // Save only area files that have something in them and store them
        $config->text = array();
        $config->subtitle = array();
        foreach ($data->text as $idx => $text) {
            if (!empty($text) && !empty($text['text'])) {
                // Generates the key of where the text will be stored in the final text array
                $key = sizeof($config->text);
                // Move embedded files into a proper filearea and adjust HTML links to match
                $config->text[$key] = file_save_draft_area_files($text['itemid'], $sitecontext->id,
                        'block_featured_tool', ('content' . $key), 0, array('subdirs'=>true), $text['text']);
                $test[$idx] = ${'config_text1' . ($idx+1)};
                // If a subtitle exists for this block, store it in the same index of the subtitle array
                // Otherwise, it stores a default subtitle
                $config->subtitle[$key] = !empty($data->subtitle[$idx]) ? $data->subtitle[$idx] : "Announcement";
                // If a thumbnail exists for this block, move the thumbnail into a proper filearea and adjust HTML link to match
                if (!empty($data->thumbnail[$idx])) {
                    $thumbnail = $data->thumbnail[$idx];
                    file_save_draft_area_files($thumbnail, $sitecontext->id,
                    'block_featured_tool', ('thumbnail' . $key), 0, $thumbnailoptions);
                }
            }
        }
        $config->format = FORMAT_HTML;

        print_object($test);
        #print_object($data);

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