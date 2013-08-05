<?php

require_once dirname(__FILE__) . '/inc.php';

$courseid = required_param('courseid',  PARAM_INT);
$quizid = required_param('id',  PARAM_INT);
$correct = optional_param('correct', 0, PARAM_INT);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
$quiz = $DB->get_record('quiz', array('id' => $quizid));
	
if(!block_rk_fragesystem_check_owner($quizid))
	error('No permission to do this');
	
$sql = "SELECT q.*" .
        "  FROM {$CFG->prefix}question q" .
        " WHERE q.id IN ($quiz->questions)";
if (!$questions = $DB->get_records_sql($sql)) {
    error('No questions found');
}

header("Content-type: text/x-csv; charset=utf-8");
header("Content-Disposition: attachment; filename=".$quiz->name.".csv");
header("Pragma: no-cache");
header("Expires: 0");

foreach ($questions as $question) {
    $answers = $DB->get_records('question_answers', array('question' => $question->id));
    if ($answers) {
        echo utf8_decode($question->questiontext).";";
		foreach($answers as $answer) {
			echo utf8_decode($answer->answer).";";
			if($correct==1) {
				if($answer->fraction > 0)
					echo "t;";
				else
					echo "f;";
			}
		}
		echo "\n";
    }
}