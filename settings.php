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
 * Plugin administration pages are defined here.
 *
 * @package     block_featured_module
 * @category    admin
 * @copyright   2023 Derek Wilson <wilsondc5@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('block_featured_module_settings', new lang_string('pluginname', 'block_featured_module'));
    require_once('./myform.php');

    //Instantiate simplehtml_form
    $mform = new myform();
    // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
    if ($ADMIN->fulltree) {
        $maxbytes = 0;
        $maxfiles = 20;
        $mform->addElement('header', 'featuredmediaheader', get_string('featuredmedia', 'block_featured_module'));
        $mform->addElement('filemanager', 'featuredmedia', get_string('featuredmedia', 'block_featured_module'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => $maxfiles));
        $mform->addHelpButton('featuredmedia', 'featuredmedia', 'block_featured_module');
        $settings->add($mform);


    }
}
