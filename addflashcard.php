<?php

require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/flashcard_edit_form.php';

global $DB;

$courseid = required_param('courseid',  PARAM_INT);
$action = optional_param("action", "", PARAM_ALPHA);
$confirm = optional_param("confirm", "", PARAM_BOOL);

$url = '/blocks/rk_fragesystem/addflashcard.php?courseid=' . $courseid;
$PAGE->set_url($url);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);

$id = optional_param('id', 0, PARAM_INT);
$returnurl = $CFG->wwwroot.'/blocks/rk_fragesystem/flashcards.php?courseid='.$courseid;
$finishurl = $CFG->wwwroot.'/blocks/rk_fragesystem/flashcard.php?courseid='.$courseid.'&action=edit&id=';


if ($id) {
	if(!block_rk_fragesystem_check_kartei_owner($id))
		error('No permission to do this');
	$existing = $DB->get_record('block_rk_user_kartei', array('id'=> $id));
} else {
    $existing = false;
}
if (!$course = $DB->get_record("course", array("id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}

// delete item
if ($action == 'delete') {
	if (!$existing) {
		print_error("notfound");
	}
	if (data_submitted() && $confirm && confirm_sesskey()) {
            block_rk_fragesystem_delete_flashcard($existing);
		redirect($returnurl);
	} else {
		$buttoncontinue = 'addflashcard.php?id='.$id.'&action=delete&confirm=1&session='.sesskey().'&courseid='.$courseid;
		$buttoncancel = 'flashcards.php?courseid='.$courseid;
		
		block_rk_fragesystem_print_header("flashcards");
		echo '<br />';
		echo $OUTPUT->confirm(get_string("deleteconfirmflashcard", "block_rk_fragesystem"), $buttoncontinue, $buttoncancel);
		echo $OUTPUT->footer();
		die;
	}
}

$editform = new block_rk_fragesystem_flashcard_edit_form($_SERVER['REQUEST_URI'].'&courseid='.$courseid);

if ($editform->is_cancelled()){
	redirect($returnurl);
} else if ($editform->no_submit_button_pressed()) {
	die("nosubmitbutton");
	//no_submit_button_actions($editform, $sitecontext);
} else if ($fromform = $editform->get_data()){
	switch ($action) {
		case 'add':
			$cmid = block_rk_fragesystem_add_flashcard($fromform,$courseid);
			$id = $cmid->id;
		break;

		case 'edit':
			if (!$existing) {
				print_error("bookmarknotfound", "block_exabis_eportfolio");
			}
            $existing->title = $fromform->title;
			//$cmid = block_rk_fragesystem_edit_flashcard($existing);
			block_rk_fragesystem_edit_flashcard($existing);
		break;

		default:
			print_error("unknownaction", "block_exabis_eportfolio");
	}
	
	redirect($finishurl.$id);
}

block_rk_fragesystem_print_header("flashcards");

if($existing)
    $editform->set_data ($existing);
$editform->display();

echo $OUTPUT->footer();
?>
