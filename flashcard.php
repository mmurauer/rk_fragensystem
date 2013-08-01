<?php

require_once dirname(__FILE__) . '/inc.php';
global $CFG, $DB;

$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
$id = required_param('id', PARAM_INT);

$url = '/blocks/rk_fragesystem/flashcard.php?courseid=' . $courseid.'&id='.$id;
$PAGE->set_url($url);

$context = get_context_instance(CONTEXT_SYSTEM);
$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);

if (!$course = $DB->get_record("course", array("id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}
if(!block_rk_fragesystem_check_kartei_owner($id))
	print_error('No permission to do this');

block_rk_fragesystem_print_header("flashcards");
$cat = optional_param('action', 'view', PARAM_ALPHA);

$thispageurl = new moodle_url($url);
$thispageurl->param('courseid', $courseid);
$thispageurl->param('id', $id);
$thispageurl->param('cat', $cat);

if (optional_param('add', false, PARAM_BOOL)) { /// Add selected questions to the current quiz
    $rawdata = (array) data_submitted();
    foreach ($rawdata as $key => $value) {    // Parse input for question ids
        if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
            $key = $matches[1];
			block_rk_fragesystem_add_kartei_category($key, $id);
        }
    }
    $significantchangemade = true;
}
if (($delete = optional_param('delete', false, PARAM_INT)) !== false) { /// Delete Questions from a certan Category
	block_rk_fragesystem_delete_kartei_category($delete, $id);
}

if($cat == "edit") {


$categories = block_rk_fragesystem_list_question_categories_new($coursecontext);
echo '<table width="100%">';
echo '<tr><td valign="top" width="50%">';
echo '<div class="generalbox quizquestions box">';
echo '<h2 class="main">Kategorien in der Kartei</h2>';
//block_rk_fragesystem_print_quiz_questions($quiz, $thispageurl);
$karteicategories = block_rk_fragesystem_get_kartei_categories($id);
block_rk_fragesystem_print_kartei_categories($karteicategories, $courseid, $id);
echo '</div>';
echo '</td><td valign="top"  width="50%">';
echo '<div class="generalbox quizquestions box">';
echo '<h2 class="main">Kategorie-Übersicht</h2>';

block_rk_fragesystem_print_category_checkbox($categories, $courseid, $id, $karteicategories);
echo '</div>';
echo '</td></tr>';
echo '<tr><td></td><td>';
echo '</div></td></tr>';
echo '</table>';


$OUTPUT->footer($course);
} else {
	echo '<div class="generalbox flashcards box">
<h2 class="main">Karteiübersicht</h2>';
	
	$qanzahl = block_rk_fragesystem_count_kartei_questions($id);
	//legt genauigkeit für prozentrechnung fest
	bcscale(3);
	
	
	echo '<table width="50%" class="generaltable quizattemptsummary boxaligncenter" border="0" cellpadding="2" cellspacing="0">';

	for($i=1;$i<7;$i++) {
		$level = block_rk_fragesystem_get_kartei_level($id,$i);
		$prozent = bcdiv($level, $qanzahl) * 100;
		
		$url = $CFG->wwwroot . '/blocks/rk_fragesystem/level.php?id='.$id.'&courseid='.$courseid.'&level=';
		echo '<tr>';
			if($level > 0)
				echo '<td><a href="'.$url.$i.'">Level '.$i.'</a>:</td><td>'.$level.'</td><td width="5%">('.$prozent.'%)</td>';
			else
				echo '<td>Level '.$i.':</td><td>'.$level.'</td><td width="5%">('.$prozent.'%)</td>';
		echo '</tr>';
	}
	echo '<td>Anzahl der Fragen gesamt:</td><td colspan="2">'.$qanzahl.'</td>';
	echo '</table>';
	echo '<form method="post" action="flashcard.php?action=edit&courseid=' . $courseid . '&id='.$id.'">';
    echo '<input type="submit" value="Fragen hinzuf&uuml;gen">';
    echo '</form>';
	echo '</div>';

}
?>
