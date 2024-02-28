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
 * @module      block_featured_tool/openmodal
 * @author      2024 Derek Wilson <wilsondc5@appstate.edu>
 * @copyright   (c) 2024 Appalachian State University, Boone, NC
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Modal from "core/modal_cancel";

const Selectors = {
    triggerButtons: ".featuredtoolButton[type='button']",
};


/**
 * Register the event listeners for this contextid.
 *
 * @param {Object} params
 */
const registerEventListeners = (params) => {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.triggerButtons)) {
            showModal(params);
        }
    });
};

/**
 * Display the modal for this contextId.
 *
 * @param {Object} params Parameters to pass to the modal.
 */
const showModal = async(params) => {
    const modal = await Modal.create(params);

    return modal.show();
};

/**
 * Set up modal for the featured tool.
 *
 * @param {Object} params The context id to set up for, title shown at the top of the modal, and body of the modal.
 */
export const init = async(params) => {
    registerEventListeners(params);
};