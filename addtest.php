<?php

require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/test_edit_form.php';

$courseid = required_param('courseid',  PARAM_INT);
$action = optional_param("action", "", PARAM_ALPHA);
$confirm = optional_param("confirm", "", PARAM_BOOL);

$url = '/blocks/rk_fragesystem/addtest.php?courseid=' . $courseid;
$PAGE->set_url($url);
global $OUTPUT;

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);

$returnurl = $CFG->wwwroot.'/blocks/rk_fragesystem/mytests.php?courseid='.$courseid;
$finishurl = $CFG->wwwroot.'/blocks/rk_fragesystem/edit.php?courseid='.$courseid.'&action=edit&cmid=';

$id = optional_param('id', 0, PARAM_INT);
if ($id) {
	if(!block_rk_fragesystem_check_owner($id))
		error('No permission to do this');
	$existing = $DB->get_record('quiz', array('id'=> $id));
} else {
    $existing = false;
}
if (!$course = $DB->get_record("course", array("id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}

// delete item
if ($action == 'delete') {

	if(!block_rk_fragesystem_check_owner($id))
		error('No permission to do this');
	
	if (!$existing) {
		print_error("notfound");
	}
	if (data_submitted() && $confirm && confirm_sesskey()) {
            block_rk_fragesystem_delete_test($existing);
		redirect($returnurl);
	} else {
		$optionsyes = array('id'=>$id, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey(), 'courseid'=>$courseid);
		$optionsno = array('courseid'=>$courseid);

		block_rk_fragesystem_print_header("mytests");
		// ev. noch eintrag anzeigen!!!
		//blog_print_entry($existing);
		echo '<br />';
		echo $OUTPUT->confirm(get_string("deleteconfirmtest", "block_rk_fragesystem"), new moodle_url("addtest.php",$optionsyes), new moodle_url("mytests.php",$optionsno));
		//notice_yesno(get_string("deleteconfirmtest", "block_rk_fragesystem"), 'addtest.php', 'mytests.php', $optionsyes, $optionsno, 'post', 'get');
		//print_footer();
		echo $OUTPUT->footer();
		die;
	}
}


$editform = new block_rk_fragesystem_test_edit_form($_SERVER['REQUEST_URI'].'&courseid='.$courseid);

if ($editform->is_cancelled()){
	redirect($returnurl);
} else if ($editform->no_submit_button_pressed()) {
	die("nosubmitbutton");
	//no_submit_button_actions($editform, $sitecontext);
} else if ($fromform = $editform->get_data()){
	switch ($action) {
		case 'add':
			$cmid = block_rk_fragesystem_add_test($fromform,$courseid);
		break;

		case 'edit':

			if (!$existing) {
				print_error("bookmarknotfound", "block_exabis_eportfolio");
			}
                        $existing->name = $fromform->name;
                        $existing->intro = $fromform->intro;
			$cmid = block_rk_fragesystem_edit_test($existing);
		break;

		default:
			print_error("unknownaction", "block_exabis_eportfolio");
	}

	redirect($finishurl.$cmid);
}
if($existing)
    $editform->set_data ($existing);
    
block_rk_fragesystem_print_header("mytests");
$editform->display();
echo $OUTPUT->footer();
?>
