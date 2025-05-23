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

use core_form\filetypes_util;
/**
 * Class for featured tool block.
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

        // Set up renderer.
        $output = $this->page->get_renderer('block_featured_tool');

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // Checks if user is an admin/manager.
        $isallowed = false;
        $sitecontext = context_system::instance();
        if (has_capability('moodle/site:manageblocks', $sitecontext)) {
            $isallowed = true;
        } else { // If user is not an admin/manager, checks if user is a teacher in a course.
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
            $this->content->items = [];
            $this->content->icons = [];
            $this->content->footer = '';

            $filteropt = new stdClass;
            $filteropt->overflowdiv = true;
            $filteropt->noclean = true;

            if (!empty($this->config->text)) {
                // Only blocks with text in them should be in config->text at this point.
                $max = count($this->config->text);
                $randint = random_int(0, $max - 1);
                // Selects a random block based on the random int.
                $selectedblock = $this->config->text[$randint]['content'];
                // Grabs that block's subtitle.
                $selectedblocksubtitle = $this->config->subtitle[$randint]['content'];

                $selectedblock =
                        file_rewrite_pluginfile_urls($selectedblock, 'pluginfile.php', $sitecontext->id, 'block_featured_tool',
                                ('content' . $randint), null);
                // Stores the pluginfile link back into the respective config->text position.
                $format = $this->config->format;

                $fs = get_file_storage();
                $files = $fs->get_area_files($sitecontext->id, 'block_featured_tool', ('thumbnail' . $randint), false, 'filename',
                        false);
                // Tries to serve the thumbnail if it exists.
                if (count($files)) {
                    // There should only ever be one file in the filearea.
                    $file = reset($files);
                    // Creates a pluginfile URL for the thumbnail, since it comes from a file picker.
                    $selectedblockthumbnail =
                            moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                                    null, $file->get_filepath(), $file->get_filename());
                }

                // Creates the data array expected by the featuredcontent template.
                $data = [
                        "subtitle" => $selectedblocksubtitle,
                        "thumbnail" => $selectedblockthumbnail ?? '',
                        "editorhtml" => format_text($selectedblock, $format, $filteropt),

                ];
                $featuredblock = new \block_featured_tool\output\featured_block(
                    $sitecontext->id, $data['thumbnail'], $data['subtitle'], $data['editorhtml']);
                $this->content->text = $output->render($featuredblock);
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
     * Serialize and store config data.
     *
     * @param array|stdClass $data
     * @param bool $nolongerused
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    public function instance_config_save($data, $nolongerused = false) {

        $config = clone($data);

        $sitecontext = context_system::instance();
        $acceptedtypes = (new filetypes_util)->normalize_file_types('.jpg,.gif,.png');
        $thumbnailoptions = [
                'subdirs' => 0,
                'maxbytes' => 104857600,
                'areamaxbytes' => 104857600,
                'maxfiles' => 1,
                'accepted_types' => $acceptedtypes,
                'context' => $sitecontext,
                'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
        ];

        // Generates an array of the text fields.
        $data->text = [$data->text0, $data->text1, $data->text2];
        // Generates an array of the subtitles.
        $data->subtitle = [$data->subtitle0, $data->subtitle1, $data->subtitle2];
        // Generates an array of thumbnails.
        $data->thumbnail = [$data->thumbnail0, $data->thumbnail1, $data->thumbnail2];

        // Save only area files that have something in them and store them.
        $config->text = [];
        $config->subtitle = [];
        $config->thumbnail = [];
        foreach ($data->text as $idx => $text) {
            if (!empty($text) && !empty($text['text'])) {
                // Generates the key of where the text will be stored in the final text array.
                $key = count($config->text);
                // Move embedded files into a proper filearea and adjust HTML links to match.
                $config->text[$key] = [
                        'content' => file_save_draft_area_files($text['itemid'], $sitecontext->id,
                                'block_featured_tool', ('content' . $key), 0, ['subdirs' => true], $text['text']),
                        'idx' => $idx,
                ];
                // If a subtitle exists for this block, store it in the same index of the subtitle array.
                // Otherwise, it stores a default subtitle.
                $config->subtitle[$key] = [
                        'content' => !empty($data->subtitle[$idx]) ? $data->subtitle[$idx] : "Announcement",
                        'idx' => $idx,
                ];
                // If a thumbnail exists for this block, move the thumbnail into a proper filearea and adjust HTML link to match.
                if (!empty($data->thumbnail[$idx])) {
                    $thumbnail = $data->thumbnail[$idx];
                    // If a thumbnail exists for this block, store it in the same index of the thumbnail array.
                    $config->thumbnail[$key] = [
                            'content' => $thumbnail,
                            'idx' => $idx,
                    ];
                    file_save_draft_area_files($thumbnail, $sitecontext->id,
                            'block_featured_tool', ('thumbnail' . $key), 0, $thumbnailoptions);
                }
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
        return [
                'all' => false,
                'my' => true,
        ];
    }
}
