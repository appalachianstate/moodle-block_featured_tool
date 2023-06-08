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

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {

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

        require_once('myform.php');
        //Instantiate simplehtml_form
        $mform = new my_form();
        if ($mform->is_cancelled()) {
            // If there is a cancel element on the form, and it was pressed,
            // then the `is_cancelled()` function will return true.
            // You can handle the cancel operation here.
        } else if ($fromform = $mform->get_data()) {
            // When the form is submitted, and the data is successfully validated,
            // the `get_data()` function will return the data posted in the form.
        } else {
            // This branch is executed if the form is submitted but the data doesn't
            // validate and the form should be redisplayed or on the first display of the form.

            // Set anydefault data (if any).
            $mform->set_data($toform);

            // Display the form.
            $this->content->text = $mform->render();
        }
        //if (!empty($this->config->text)) {
        //    $this->content->text = $this->config->text;
        //} else {
        //    $text = 'This is where featured posts will go.';
        //    $this->content->text = $text;
        //}

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
