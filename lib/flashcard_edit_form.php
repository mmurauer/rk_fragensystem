<?php

require_once $CFG->libdir.'/formslib.php';
require_once $CFG->libdir.'/filelib.php';


class block_rk_fragesystem_flashcard_edit_form extends moodleform {

    function definition() {

        global $COURSE, $CFG;
        $mform = & $this->_form;
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'title', get_string('name'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('title', PARAM_TEXT);
        } else {
            $mform->setType('title', PARAM_CLEAN);
        }
        $mform->addRule('title', null, 'required', null, 'client');

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
