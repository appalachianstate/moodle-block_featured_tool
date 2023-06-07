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
 * Block featured_module is defined here.
 *
 * @package     block_featured_module
 * @copyright   2023 Derek Wilson <wilsondc5@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_featured_module extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_featured_module');
        // Initialize the configuration
        $this->config = get_config('block_featured_module');
    }

    function content_is_trusted() {
        global $SCRIPT;

        if (!$context = context::instance_by_id($this->instance->parentcontextid, IGNORE_MISSING)) {
            return false;
        }
        //find out if this block is on the profile page
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                // this is exception - page is completely private, nobody else may see content there
                // that is why we allow JS here
                return true;
            } else {
                // no JS on public personal pages, it would be a big security issue
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // Retrieve the block configuration settings
        $blockConfig = get_config('block_featured_module');

        // Retrieve the file manager setting
        $featuredMediaSetting = $blockConfig->featuredmedia;

        // Get the context instance
        $context = $this->page->context;

        // Retrieve the uploaded files
        $fileArea = 'featuredmedia';
        $component = 'block_featured_module';
        $itemid = $this->instance->id;

        // Save the uploaded files to the file area
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, $component, $fileArea, $itemid);
        $fs->create_file_from_storedfile(
                [
                        'contextid' => $context->id,
                        'component' => $component,
                        'filearea' => $fileArea,
                        'itemid' => $itemid,
                ],
                $featuredMediaSetting
        );

        // Retrieve the files in the file area
        $files = $fs->get_area_files($context->id, $component, $fileArea, $itemid);

        // Process the retrieved files
        foreach ($files as $file) {
            $fileUrl = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
            );

            // Append file information to the block content
            $this->content->text .= '<a href="' . $fileUrl . '">' . $file->get_filename() . '</a><br>';
        }

        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_featured_module');
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
