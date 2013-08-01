<?php

require_once $CFG->libdir.'/formslib.php';
require_once $CFG->libdir.'/filelib.php';

require_once("$CFG->dirroot/mod/quiz/locallib.php");

class block_rk_fragesystem_test_edit_form extends moodleform {

    function definition() {

        global $COURSE, $CFG;
        $mform = & $this->_form;
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'intro', get_string("introduction", "quiz"));
        $mform->setType('intro', PARAM_RAW);
        $mform->addHelpButton('intro', 'intro', 'block_rk_fragesystem');

//        $mform->addElement('hidden', 'action');
//	$mform->setType('action', PARAM_ACTION);
//	$mform->setDefault('action', '');

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (count($errors) == 0) {
            return true;
        } else {
            return $errors;
        }
    }

}

?>
