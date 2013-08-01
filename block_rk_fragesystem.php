<?php

class block_rk_fragesystem extends block_base {

    function init() {
        $this->title = get_string('blocktitle', 'block_rk_fragesystem');
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $CFG, $COURSE, $USER;

        $context = get_context_instance(CONTEXT_SYSTEM);

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;

        $this->content->footer = '';

        $this->content->text = '<a title="' . get_string('blocktitle', 'block_rk_fragesystem') . '" href="' . $CFG->wwwroot . '/blocks/rk_fragesystem/view.php?courseid=' . $COURSE->id . '">' . get_string('blocktitle', 'block_rk_fragesystem') . '</a>';
        //$this->content->icons[] = '<img src="' . $CFG->wwwroot . '/blocks/exabis_eportfolio/pix/categories.png" height="16" width="16" alt="' . get_string("mybookmarks", "block_exabis_eportfolio") . '" />';

        return $this->content;
    }

}