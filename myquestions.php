<?php

require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/qeditlib.php';
//require_once($CFG->dirroot.'/question/editlib.php');

global $DB, $COURSE;

$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);

$url = '/blocks/rk_fragesystem/myquestions.php?courseid=' . $courseid;
$PAGE->set_url($url);

$context = get_context_instance(CONTEXT_SYSTEM);

if (!$course = $DB->get_record("course", array("id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('questions', $url);

block_rk_fragesystem_print_header("myquestions");

$questionbank = new question_bank_view($contexts, $thispageurl, $COURSE, $cm);
$questionbank->process_actions();
echo '<table class="boxaligncenter" border="0" cellpadding="2" cellspacing="0">';
echo '<tr><td valign="top">';
$questionbank->display('questions', $pagevars['qpage'], $pagevars['qperpage'], $pagevars['cat'],  $pagevars['recurse'],  $pagevars['showhidden'], $pagevars['qbshowtext'],true);
echo '</td></tr>';
echo '</table>';

echo $OUTPUT->footer();
?>
