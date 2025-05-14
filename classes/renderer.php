<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Main renderer for plugin.
 *
 * @package     block_featured_tool
 * @author      2024 Derek Wilson <wilsondc5@appstate.edu>
 * @copyright   (c) 2024 Appalachian State University, Boone, NC
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_featured_tool\output\featured_block;

/**
 * The core renderer
 *
 * @author      2024 Derek Wilson <wilsondc5@appstate.edu>
 * @copyright   (c) 2024 Appalachian State University, Boone, NC
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_featured_tool_renderer extends plugin_renderer_base {

    /**
     * Renders the read more button.
     *
     * @param featured_block $featuredblock Data to send to modal.
     * @return string HTML
     * @return string XHTML
     */
    public function render_featured_block(featured_block $featuredblock): string {
        $failbackurl = new moodle_url('#');
        $button = new single_button($failbackurl, get_string('cardbutton', 'block_featured_tool'));

        $attributes = [
            'type' => 'button',
            'disabled' => $button->disabled ? 'disabled' : null,
            'title'    => $button->tooltip,
            'class'    => 'btn btn-primary featuredtoolButton',
        ];

        if ($button->actions) {
            $id = html_writer::random_id('single_button');
            $attributes['id'] = $id;
            foreach ($button->actions as $action) {
                $this->add_action_handler($action, $id);
            }
        }
        $data = $featuredblock->get_data();
        $arguments = [
            'contextid' => $featuredblock->contextid,
            'title' => $data->subtitle,
            'body' => $data->editorhtml,
            'isVerticallyCentered' => true,
            'buttons' => [
                'cancel' => get_string('closemodal', 'block_featured_tool'),
            ],
        ];
        $this->page->requires->js_call_amd('block_featured_tool/openmodal', 'init', [$arguments]);
        // Load the link clicker javascript.
        $this->page->requires->js_call_amd('block_featured_tool/linktracker', 'init');

        // First create button.
        $buttonhtml = html_writer::tag('button', $button->label, $attributes);

        // Then generate HTML from the mustache file.
        $finalhtml = $this->render_from_template('block_featured_tool/featuredcontent',
            ['subtitle' => $data->subtitle, 'thumbnail' => $data->thumbnail, 'button' => $buttonhtml]);

        // And then return the HTML.
        return $finalhtml;
    }
}
