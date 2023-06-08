<?php
// This file is part of the block_featured_module plugin.
//
// Class definition for the myform form.

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class my_form extends moodleform {
    // Add elements to form.
    public function definition() {
        $mform = $this->_form;

        $maxbytes = 0;
        $maxfiles = 20;

        $mform->addElement('header', 'featuredmediaheader', get_string('featuredmedia', 'block_featured_module'));
        #$mform->addElement('filemanager', 'featuredmedia', get_string('featuredmedia', 'block_featured_module'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => $maxfiles));
        #$mform->addHelpButton('featuredmedia', 'featuredmedia', 'block_featured_module');
    }

    // Custom validation should be added here.
    public function validation($data, $files) {
        return [];
    }
}