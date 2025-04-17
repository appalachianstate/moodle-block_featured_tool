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
 *
 * @module      block_featured_tool/linktracker
 * @author      2025 Lina Brown <brownli2@appstate.edu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// JS function to capture the name of the clicked link and send it off to get stored.
define("block_featured_tool/linktracker", ["core/ajax"], function (ajax) {
    let eventListenerAdded = false; // Flag to prevent one click from logging twice.
    const Selectors = {
        trackableLink: '[data-action="trackable"]',
    };
    const submitLinkData = (linkName) => {
        ajax.call([{
            methodname: 'block_featured_tool_get_clicked_link',
            args: { link_name: linkName },
        }]);
    };
    const getLinkName = () => {
        if (eventListenerAdded) {
            return;
        }
        document.addEventListener('click', e => {
            if (e.target.matches(Selectors.trackableLink)) {
                //e.preventDefault();
                const linkName = e.target.getAttribute('data-name');
                submitLinkData(linkName);
                setTimeout(() => {
                    window.location.href = link.href;
                }, 200)
            };
        })
        eventListenerAdded = true;
    };
    return {
        init: async () => {
            getLinkName();
        }
    };
});
    
/*   
Leaving this here in case we need to switch it later .
Moodle says to use ESM format but already using amd
    
    import call from "core/ajax";
    const Selectors = {
        trackableLink: '[data-action="trackable"]',
    };
    const submitLinkData = (linkName) => ajax.call([{
        methodname: 'block_featured_tool_getclickedlink',
        args: {link_name: linkName,},
    }])[0];
    const getLinkName = () => {
        document.addEventListener('click', e => {
            if (e.target.matches(Selectors.trackableLink)) {
                const linkName = e.target.getAttribute('data-name');
                window.alert(`Thank you for clicking on the ${linkName} link`);
                submitLinkData(linkName);
            }
        });
    };
    export const init = async() => {
        getLinkName();
    };
*/
