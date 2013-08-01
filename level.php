<?php
require_once dirname(__FILE__) . '/inc.php';

$courseid = required_param('courseid', PARAM_INT);


require_login($courseid);
$id = required_param('id', PARAM_INT);
$level = required_param('level', PARAM_INT);
$qd = optional_param('qd',0,PARAM_INT);

$url = '/blocks/rk_fragesystem/level.php?id=' . $id.'&level='.$level;
$PAGE->set_url($url);

global $DB;

if(!block_rk_fragesystem_check_kartei_owner($id))
	error('No permission to do this');
		
$responses = array();
$rawdata = (array) data_submitted();
    foreach ($rawdata as $key => $value) {    // Parse input for question ids
        if (preg_match('!^a([0-9]+)$!', $key, $matches))
		$responses[] = $matches[1];
	}

$context = get_context_instance(CONTEXT_SYSTEM);
$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);

if (!$course = $DB->get_record("course",array( "id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
} if(!$kartei = $DB->get_record("block_rk_user_kartei",array("id"=>$id)))
	print_error("Ungültige Kartei!");
	
if($level < 1 || $level > 6)
	print_error("There is no level with id < 0 or > 6");
	
block_rk_fragesystem_print_header("flashcards");

$qanz = block_rk_fragesystem_get_kartei_level($id,$level);
if($qanz > 0) {
if(!$responses)
	$question = block_rk_fragesystem_get_kartei_level_question($id,$level,$qanz);
else
	$question =$DB->get_record('question',array('id'=>$qd));
$answers = block_rk_fragesystem_get_question_answers($question->id);

$correct=false;
if($responses) {
	$correctanswers = block_rk_fragesystem_check_correct_question_answers($question->id,$responses);

	if(count($correctanswers) == count($responses)) {
		$correct = true;
		foreach($responses as $response) {
			if(!array_key_exists($response,$correctanswers)) $correct=false;
		}
	}
	block_rk_fragesystem_process_question_answer($question->id,$id,$correct);
}

echo '<form method="post">';
echo '<input type="hidden" name="qd" value="'.$question->id.'">';
echo '<h2 class="main">'.$kartei->title.'</h2>';
echo 'Anzahl der restlichen Fragen: '.$qanz;
echo '<br/>';
echo '<div id="q'.$question->id.'" class="que multichoice clearfix">';
	echo '<div class="content"><div class="qtext">'.$question->questiontext.'</div>';
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
				
				$selected="";
				if($responses)
					$selected = "disabled ";
				if(in_array($answer->id,$responses))
					$selected = "checked disabled ";
					
				echo '<td><input name="a'.$answer->id.'" value="'.$answer->id.'" type="checkbox" '.$selected.'></td>';
				if(!$responses)echo '<td>'.$answer->answer.'</td>';
				else if($answer->fraction > 0) echo '<td class="c1 text correct">'.$answer->answer.'<img alt="falsch" src="'.$CFG->wwwroot.'/pix/i/tick_green_small.gif"></td>';
				else echo '<td>'.$answer->answer.'<img alt="falsch" src="'.$CFG->wwwroot.'/pix/i/cross_red_small.gif"></td>';
				echo '</tr>';
			}
		echo '</table>';
		if($responses) {
			if($correct)
				echo '<div class="c1 text correct">Richtig!</div>';
			else
				echo '<div class="c1 text incorrect">Falsch!</div>';
		}
	echo '</div></div>';
echo '</div>';
if(!$responses)
	echo '<input type="submit" value="Auswerten">';
else
	echo '<input type="submit" value="Nächste Frage">';
}
else {
	$returnurl = $CFG->wwwroot.'/blocks/rk_fragesystem/flashcard.php?courseid='.$courseid.'&id='.$id;
	echo "In diesem Level sind keine Fragen mehr vorhanden! <a href='".$returnurl."'>Hier geht's zurück.</a>";
}