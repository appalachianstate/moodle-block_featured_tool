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
 * Functions for featured_tool block instances.
 *
 * @package     block_featured_tool
 * @copyright   2023 Derek Wilson <wilsondc5@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing featured tool block instances.
 *
 * @copyright 2023 Derek Wilson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   block_featured_tool
 * @category  files
 * @param stdClass $course course object
 * @param stdClass $birecordorcm block instance record
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether force download
 * @param array $options additional options affecting the file serving
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function block_featured_tool_pluginfile($course, $birecordorcm, $context, $filearea, $args, $forcedownload,
        array $options = array()) {

    $fs = get_file_storage();

    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $sitecontext = context_system::instance();
    $file = $fs->get_file($sitecontext->id, 'block_featured_tool', $filearea, 0, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        send_file_not_found();
    }

    // NOTE: it would be nice to have file revisions here, for now rely on standard file lifetime,
    // Do not lower it because the files are displayed very often.
    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}
