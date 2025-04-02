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
 * Open modal button AMD module.
 *
 * @module      block_featured_tool/linktracker
 * @author      2025 Lina Brown <brownli2@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function() {
            // Capture link clicks in your block's DOM
            $('a.trackable').on('click', function(event) {
                // Send AJAX request to track the click
                var linkname = $(this).data('name');
                // Make the AJAX call
                ajax.call([{
                    methodname: 'track_link_clicks',
                    args: {
                        linkname: linkname,
                        contextid: M.cfg.contextid  // Send context (e.g., course or block context)
                    },
                    success: function(response) {
                        console.log('Link click logged successfully');
                        // Optionally, redirect the user or perform other actions
                        window.location.href = $(this).attr('href');  // Redirect to the link
                    }
                }]);
            });
        }
    };
});
