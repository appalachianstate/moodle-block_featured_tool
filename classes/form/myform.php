<?php
namespace block_featured_module\classes\form;

// This file is part of the block_featured_module plugin.
//
// Class definition for the myform form.

// moodleform is defined in formslib.php
require_once($CFG->libdir . '/formslib.php');

class myform extends \moodleform {
    // Add elements to form.
    public function definition() {
        $mform = $this->_form;

        // Add elements to your form.
        $mform->addElement('text', 'email', get_string('email'));

        // Set type of element.
        $mform->setType('email', PARAM_NOTAGS);

        // Default value.
        $mform->setDefault('email', 'Please enter email');
    }

    // Custom validation should be added here.
    public function validation($data, $files) {
        return [];
    }
}