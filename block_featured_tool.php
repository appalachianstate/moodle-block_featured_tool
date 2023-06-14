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
 * Form for editing featured tool block instances.
 *
 * @package   block_featured_tool
 * @copyright 2023 Derek Wilson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_featured_tool extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_featured_tool');
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('my' => true);
    }

    function specialization() {
        $sitecontext = context_system::instance();
        if (isset($this->config->title)) {
            $this->title = $this->title = format_string($this->config->title, true, ['context' => $sitecontext]);
        } else {
            $this->title = get_string('newfeaturedtoolblock', 'block_featured_tool');
        }
    }

    function get_content() {
        global $CFG, $USER;

        require_once($CFG->libdir . '/filelib.php');

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
            if ($this->content !== null) {
                return $this->content;
            }

            $filteropt = new stdClass;
            $filteropt->overflowdiv = true;
            if ($this->content_is_trusted()) {
                // fancy html allowed only on course, category and system blocks.
                $filteropt->noclean = true;
            }

            $this->content = new stdClass;
            $this->content->footer = '';

            $sitecontext = context_system::instance();

            $fs = get_file_storage();
            $files = $fs->get_area_files($sitecontext->id, 'block_featured_tool', 'content', false, 'filename', false);

            // If files are already in the file area, load them
            if (count($files)) {
                $file = reset($files);
                $this->config->text = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                        null, $file->get_filepath(), $file->get_filename());
                // Default to FORMAT_HTML which is what will have been used before the
                // editor was properly implemented for the block.
                $format = FORMAT_HTML;
                // Check to see if the format has been properly set on the config
                if (isset($this->config->format)) {
                    $format = $this->config->format;
                }
                #$this->content->text = html_writer::img($url, get_string('featuredtool', 'block_featured_tool'), ['class' => 'featuredmedia']);
                $this->content->text = format_text($this->config->text, $format, $filteropt);
            // If files are just being added, rewrite from the draftfile
            } elseif (isset($this->content->text)) {
                // rewrite url
                $this->config->text = file_rewrite_pluginfile_urls($this->config->text, 'pluginfile.php', $sitecontext->id,
                        'block_featured_tool', 'content', null);
                // Default to FORMAT_HTML which is what will have been used before the
                // editor was properly implemented for the block.
                $format = FORMAT_HTML;
                // Check to see if the format has been properly set on the config
                if (isset($this->config->format)) {
                    $format = $this->config->format;
                }
                $this->content->text = format_text($this->config->text, $format, $filteropt);
            // Don't show anything if there is nothing to show
            } else {
                $this->content->text = '';
            }
        }
        else {
            return '';
        }

        unset($filteropt); // memory footprint

        return $this->content;
    }

    public function get_content_for_external($output) {
        global $CFG, $USER;
        require_once($CFG->libdir . '/externallib.php');

        $isallowed = false;
        $courses = enrol_get_all_users_courses($USER->id, true);
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            if (has_capability('moodle/course:manageactivities', $context)) {
                $isallowed = true;
                break;
            }
        }

        $bc = '';

        if ($isallowed) {
            $bc = new stdClass;
            $bc->title = null;
            $bc->content = '';
            $bc->contenformat = FORMAT_MOODLE;
            $bc->footer = '';
            $bc->files = [];

            if (!$this->hide_header()) {
                $bc->title = $this->title;
            }

            if (isset($this->config->text)) {
                $filteropt = new stdClass;
                if ($this->content_is_trusted()) {
                    // Fancy html allowed only on course, category and system blocks.
                    $filteropt->noclean = true;
                }

                $format = FORMAT_HTML;
                // Check to see if the format has been properly set on the config.
                if (isset($this->config->format)) {
                    $format = $this->config->format;
                }
                $sitecontext = context_system::instance();
                list($bc->content, $bc->contentformat) =
                        external_format_text($this->config->text, $format, $sitecontext->id, 'block_featured_tool', 'content', null,
                                $filteropt);
                $bc->files = external_util::get_area_files($sitecontext->id, 'block_featured_tool', 'content', false, false);

            }
        }
        return $bc;
    }


    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $DB;

        $config = clone($data);
        $sitecontext = context_system::instance();
        // Move embedded files into a proper filearea and adjust HTML links to match
        $config->text = file_save_draft_area_files($data->text['itemid'], $sitecontext->id, 'block_featured_tool', 'content', 0, array('subdirs'=>true), $data->text['text']);
        $config->format = $data->text['format'];

        parent::instance_config_save($config, $nolongerused);
    }

    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_featured_tool');
        return true;
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
     * Add custom html attributes to aid with theming and styling
     *
     * @return array
     */
    function html_attributes() {
        global $CFG;

        $attributes = parent::html_attributes();

        if (!empty($CFG->block_featured_tool_allowcssclasses)) {
            if (!empty($this->config->classes)) {
                $attributes['class'] .= ' '.$this->config->classes;
            }
        }

        return $attributes;
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        global $CFG;

        // Return all settings for all users since it is safe (no private keys, etc..).
        $instanceconfigs = !empty($this->config) ? $this->config : new stdClass();
        $pluginconfigs = (object) ['allowcssclasses' => $CFG->block_featured_tool_allowcssclasses];

        return (object) [
                'instance' => $instanceconfigs,
                'plugin' => $pluginconfigs,
        ];
    }
}
