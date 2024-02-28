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

namespace block_featured_tool\output;

use renderable;

/**
 * Core class to display featured tool content.
 *
 * @package     block_featured_tool
 * @author      2024 Derek Wilson <wilsondc5@appstate.edu>
 * @copyright   (c) 2024 Appalachian State University, Boone, NC
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class featured_block implements renderable {
    /**
     * @var int $contextid The context id for the featured_block instance.
     */
    public int $contextid;
    /**
     * @var \moodle_url $thumbnail The url to the thumbnail for the featured tool.
     */
    private \moodle_url $thumbnail;
    /**
     * @var string $subtitle The subtitle for the featured tool.
     */
    private string $subtitle;
    /**
     * @var string $editorhtml The body of the featured tool.
     */
    private string $editorhtml;

    /**
     * Constructs renderable block.
     *
     * @param int $contextid
     * @param \moodle_url $thumbnail
     * @param string $subtitle
     * @param string $editorhtml
     */
    public function __construct(int $contextid, \moodle_url $thumbnail, string $subtitle, string $editorhtml) {
        $this->contextid = $contextid;
        $this->thumbnail = $thumbnail;
        $this->subtitle = $subtitle;
        $this->editorhtml = $editorhtml;
    }

    /**
     * Convenience function to get all the mustache data for the block.
     *
     * @return object
     */
    public function get_data() {
        return (object) [
            'thumbnail' => $this->thumbnail,
            'subtitle' => $this->subtitle,
            'editorhtml' => $this->editorhtml,
        ];
    }
}
