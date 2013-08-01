<?php

require_once dirname(__FILE__) . '/inc.php';
require_once("lib/editlib.php");
require_once($CFG->libdir.'/questionlib.php');
require_once($CFG->dirroot . '/question/previewlib.php');

$qid = required_param('qid',  PARAM_INT);
$courseid = required_param('courseid',  PARAM_INT);
require_login($courseid);

$url = '/blocks/rk_fragesystem/detailquestion.php?qid=' . $qid.'&courseid='.$courseid;
$PAGE->set_url($url);

global $DB, $OUTPUT;

$context = get_context_instance(CONTEXT_SYSTEM);

if (!$course = $DB->get_record("course", array("id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}
if (!$question = $DB->get_record("question",array("id"=>$qid)))
	print_error("no question found");

$strpreview = "Vorschau: ".format_string($question->name);
//$headtags = question_get_editing_head_contributions($question);
//$headtags = get_html_head_contributions($questionlist, $questions, $states[$historylength]);
//print_header($strpreview, '', '', '', $headtags);
//print_heading($strpreview);

	//wird nicht angezeigt
	
/*
$OUTPUT->heading($strpreview);
$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
$answers = block_rk_fragesystem_get_question_answers($question->id);
echo '<div id="q'.$question->id.'" class="que multichoice clearfix">';
	echo '<div class="content">';
	echo '<div class="ablock clearfix">';
		echo '<div class="prompt"> Antwort(en) wählen: </div>';
		echo '<table class="answer">';
			$bg="r0";
			foreach($answers as $answer) {
				echo '<tr class="'.$bg.'">';
				if($bg=="r0")
					$bg="r1";
				elseif($bg=="r1")
					$bg="r0";
				
				if($answer->fraction > 0)
					$selected = "checked disabled ";
				else
					$selected = " disabled ";
					
				if($answer->feedback)
					$feedback = ' ('.$answer->feedback.')';
				else
					$feedback = "";
					
				echo '<td><input name="a'.$answer->id.'" value="'.$answer->id.'" type="checkbox" '.$selected.'></td>';
				if($answer->fraction > 0) echo '<td class="c1 text correct">'.$answer->answer.'<img alt="falsch" src="'.$CFG->wwwroot.'/pix/i/tick_green_small.gif">'.$feedback.'</td>';
				else echo '<td>'.$answer->answer.'<img alt="falsch" src="'.$CFG->wwwroot.'/pix/i/cross_red_small.gif">'.$feedback.'</td>';
				
				echo '</tr>';
			}
		echo '</table>';
	echo '</div></div>';
echo '</div>';

*/

$quba = question_engine::make_questions_usage_by_activity('block_rk_fragesystem', context_user::instance($USER->id));
$question = question_bank::load_question($qid);
$options = new question_preview_options($question);
$options->set_from_request();
$options->readonly = true;
$quba->set_preferred_behaviour($options->behaviour);
$slot = $quba->add_question($question, $options->maxmark);

$quba->start_question($slot);

// Start output.
$title = get_string('previewquestion', 'question', format_string($question->name));
$headtags = question_engine::initialise_js() . $quba->render_question_head_html($slot);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// Output the question.

$correctresponse = $quba->get_correct_response($slot);
if (!is_null($correctresponse)) {
	$quba->process_action($slot, $correctresponse);
}
	
echo $quba->render_question($slot, $options);

$PAGE->requires->js_init_call('M.core_question_preview.init', null, false, array(
		'name' => 'core_question_preview',
		'fullpath' => '/question/preview.js',
		'requires' => array('base', 'dom', 'event-delegate', 'event-key', 'core_question_engine'),
		'strings' => array(
				array('closepreview', 'question'),
		)));

