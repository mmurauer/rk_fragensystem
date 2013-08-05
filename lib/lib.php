<?php
/**
 * Print moodle header
 * @param string $item_identifier translation-id for this page
 * @param string $sub_item_identifier translation-id for second level if needed
 */
function block_rk_fragesystem_print_header($item_identifier, $sub_item_identifier = null, $courseid=0, $id=0) {

	global $CFG, $COURSE, $OUTPUT;

	$strbookmarks = get_string("blocktitle", "block_rk_fragesystem");

	// navigationspfad
	$navlinks = array();
	$navlinks[] = array('name' => $strbookmarks, 'link' => "view.php?courseid=" . $COURSE->id, 'type' => 'title');
	$nav_item_identifier = $item_identifier;

	$icon = $item_identifier;
	$currenttab = $item_identifier;

	// haupttabs
	$tabs = array();
	$tabs[] = new tabobject('mytests', $CFG->wwwroot . '/blocks/rk_fragesystem/mytests.php?courseid=' . $COURSE->id, get_string("mytests", "block_rk_fragesystem"), '', true);
	$tabs[] = new tabobject('flashcards', $CFG->wwwroot . '/blocks/rk_fragesystem/flashcards.php?courseid=' . $COURSE->id, get_string("flashcards", "block_rk_fragesystem"), '', true);
	$tabs[] = new tabobject('myquestions', $CFG->wwwroot . '/blocks/rk_fragesystem/myquestions.php?courseid=' . $COURSE->id, get_string("myquestions", "block_rk_fragesystem"), '', true);
	$tabs[] = new tabobject('allquestions', $CFG->wwwroot . '/blocks/rk_fragesystem/allquestions.php?courseid=' . $COURSE->id, "Globale Fragen", '', true);

	$tabs_sub = array();
	$activetabsubs = Array();

	//$tabs_sub = array();
	// $activetabsubs = Array();

	//    if (strpos($item_identifier, 'mytestsedit') === 0) {
	//        $item_identifier = "mytests";
	//        $activetabsubs[] = $item_identifier;
	//        $currenttab = 'mytests';
	//
	//        // untermen� tabs hinzuf�gen
	//        $tabs_sub['updatetest'] = new tabobject('updatetest', s($CFG->wwwroot . '/blocks/rk_fragesystem/view_items.php?courseid=' . $COURSE->id),
	//                        get_string("bookmarksall", "block_exabis_eportfolio"), '', true);
	//        $tabs_sub['exporttest'] = new tabobject('bookmarkslinks', s($CFG->wwwroot . '/blocks/exabis_eportfolio/view_items.php?courseid=' . $COURSE->id . '&type=link'),
	//                        get_string("bookmarkslinks", "block_exabis_eportfolio"), '', true);
	//        $tabs_sub['dotest'] = new tabobject('bookmarksfiles', s($CFG->wwwroot . '/blocks/exabis_eportfolio/view_items.php?courseid=' . $COURSE->id . '&type=file'),
	//                        get_string("bookmarksfiles", "block_exabis_eportfolio"), '', true);
	//
	//        if ($sub_item_identifier) {
	//            $navlinks[] = array('name' => get_string($item_identifier, "block_rk_fragesystem"), 'link' => $tabs_sub[$item_identifier]->link, 'type' => 'misc');
	//
	//            $nav_item_identifier = $sub_item_identifier;
	//        }
	//    }



	$item_name = get_string($nav_item_identifier, "block_rk_fragesystem");
	if ($item_name[0] == '[')
		$item_name = get_string($nav_item_identifier);
	$navlinks[] = array('name' => $item_name, 'link' => null, 'type' => 'misc');

	$navigation = build_navigation($navlinks);
	if ($sub_item_identifier != null)
		$strupdatemodule = block_rk_fragesystem_update_quiz_button($id, $courseid);
	else
		$strupdatemodule="";
	print_header_simple($item_name, '', $navigation, "", "", true, $strupdatemodule);

	// header //changed to $OUTPUT!!
	if ($sub_item_identifier != null && $item_identifier == "mytests")
		$OUTPUT->heading($strbookmarks . ': ' . $item_name . ' - ' . $sub_item_identifier);
	else
		$OUTPUT->heading($strbookmarks . ': ' . $item_name);

	print_tabs(array($tabs, $tabs_sub), $currenttab, null, $activetabsubs);
}

function block_rk_fragesystem_update_quiz_button($id, $courseid) {
	global $CFG, $USER;

	return "<form method=\"get\" action=\"$CFG->wwwroot/blocks/rk_fragesystem/addtest.php?courseid=" . $courseid . "&id=" . $id . "&action=edit\">" . //hack to allow edit on framed resources
			"<div>" .
			"<input type=\"hidden\" name=\"courseid\" value=\"$courseid\"/>" .
			"<input type=\"hidden\" name=\"id\" value=\"$id\"/>" .
			"<input type=\"hidden\" name=\"action\" value=\"edit\"/>" .
			"<input type=\"submit\" value=\"Test aktualisieren\" /></div></form>";
}

function block_rk_fragesystem_flashcard_list($courseid) {
	global $CFG, $COURSE, $USER, $DB;

	if (!$course = $DB->get_record('course', array('id'=> $courseid))) {
		notify('Course not found!');
		return;
	}

	$flashcards = $DB->get_records('block_rk_user_kartei', array('userid'=> $USER->id));
	echo '<table width="50%" class="generaltable quizattemptsummary boxaligncenter" border="0" cellpadding="2" cellspacing="0">';
	echo '<tr>
	<th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Name</th>
	<th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Aktionen</th>
	</tr>';
	if (!$flashcards) {
		echo '<tr><td colspan="2">Keine Karteien vorhanden</td></tr>';
	} else {
		foreach ($flashcards as $flashcard) {

			echo '<tr align="center"><td valign="top">';
			echo '<a href="'.$CFG->wwwroot.'/blocks/rk_fragesystem/flashcard.php?courseid='.$COURSE->id.'&id='.$flashcard->id.'">'.$flashcard->title.'</a>';
			echo '</td><td>';
			echo "<a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/addflashcard.php?courseid=" . $COURSE->id . "&action=edit&id=" . $flashcard->id . "'><img src=\"pix/pencil.png\" alt='Edit' title='Editieren' /></a>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/addflashcard.php?courseid=" . $COURSE->id . "&action=delete&id=" . $flashcard->id . "'><img src=\"pix/cross.png\" alt='Delete' title='L�schen'/></a>";
			echo '</td></tr>';
		}
	}
	//echo '<tr><td>';
	//echo '</td></tr>';
	echo '</table>';
	echo '<form method="post" action="addflashcard.php?action=add&courseid=' . $courseid . '">';
	echo '<input type="submit" value=" Neue Kartei erstellen">';
	echo '</form>';
}
function block_rk_fragesystem_check_owner($quizid) {
	global $USER, $DB;

	$quizowner = $DB->get_record('block_rk_user_quizes',array('quizid'=>$quizid, 'createdby'=>$USER->id));
	if(!$quizowner)
		return false;
	else
		return true;
}
function block_rk_fragesystem_check_kartei_owner($id) {
	global $USER, $DB;

	$quizowner = $DB->get_record('block_rk_user_kartei',array('id'=>$id, 'userid'=>$USER->id));
	if(!$quizowner)
		return false;
	else
		return true;
}
function block_rk_fragesystem_mytest_list($courseid) {

	global $CFG, $COURSE, $USER, $DB;

	if (!$course = $DB->get_record('course', array('id' => $courseid))) {
		notify('Course not found!');
		return;
	}
	$mod = $DB->get_record('modules', array('name'=>'quiz'));
	$quizes = $DB->get_records('block_rk_user_quizes', array('createdby'=> $USER->id));
	echo '<table width="50%" class="generaltable quizattemptsummary boxaligncenter" border="0" cellpadding="2" cellspacing="0">';
	echo '<tr>
	<th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Name</th>
	<th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Aktionen</th>
	<th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Export</th>
	</tr>';
	if (!$quizes) {
		echo '<tr><td colspan="3">Keine Tests vorhanden</td></tr>';
	} else {
		foreach ($quizes as $quiz) {
			$quizinstance = $DB->get_record('quiz', array('id'=> $quiz->quizid));
			$cm = $DB->get_record('course_modules', array('module'=> $mod->id, 'instance'=> $quiz->quizid));
			echo '<tr align="center"><td valign="top">';
			echo '<a href="'.$CFG->wwwroot.'/blocks/rk_fragesystem/startattempt.php?courseid='.$COURSE->id.'&id='.$cm->id.'">'.$quizinstance->name.'</a>';
			echo '</td><td>';
			echo "<a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/edit.php?courseid=" . $COURSE->id . "&action=edit&cmid=" . $cm->id . "'><img width=\"16px\" height=\"16px\" src=\"pix/pencil.png\" alt='Edit' title='Editieren' /></a>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/addtest.php?courseid=" . $COURSE->id . "&action=delete&id=" . $quizinstance->id . "'><img width=\"16px\" height=\"16px\" src=\"pix/cross.png\" alt='Delete' title='L�schen'/></a>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/mytests.php?courseid=" . $COURSE->id . "&copyid=" . $quizinstance->id . "'><img src=\"$CFG->wwwroot/pix/i/backup.gif\" alt='Kopieren' title='Kopieren' /></a>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/reviews.php?courseid=" . $COURSE->id . "&id=" . $quizinstance->id . "'><img src=\"$CFG->wwwroot/pix/i/grades.gif\" alt='Review' title='Ergebnisse' /></a>";
			echo "</td><td>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/printtest.php?courseid=" . $COURSE->id . "&id=" . $quizinstance->id . "' target='_blank'><img src=\"pix/pdf_red.gif\" alt='Print' title='Drucken' /></a>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/printtest.php?courseid=" . $COURSE->id . "&id=" . $quizinstance->id . "&correct=1' target='_blank'><img src=\"pix/pdf.gif\" alt='Print' title='Korrekturtest drucken' /></a>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/exportcsv.php?courseid=" . $COURSE->id . "&id=" . $quizinstance->id . "' target='_blank'><img width=\"16px\" height=\"16px\" src=\"pix/icon-csv.gif\" alt='csv' title='Csv Export'/></a>";
			echo " <a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/exportcsv.php?correct=1&courseid=" . $COURSE->id . "&id=" . $quizinstance->id . "' target='_blank'><img width=\"16px\" height=\"16px\" src=\"pix/icon-csv korrekt.png\" alt='csv' title='Csv Export'/></a>";
			echo '</td></tr>';
		}
	}
	//echo '<tr><td>';
	//echo '</td></tr>';
	echo '</table>';
	echo '<form method="post" action="addtest.php?action=add&courseid=' . $courseid . '">';
	echo '<input type="submit" value=" Neuen Test erstellen ">';
	echo '</form>';
}

function block_rk_fragesystem_print_quiz_review($id, $attempt=0) {
	global $DB;
	$quiz = $DB->get_record('quiz', array('id'=> $id));

	echo '<div align="center">';
	echo '<table width="50%" "align="center" id="attempts" class="flexible generaltable generalbox" cellspacing="0">';
	echo '<tr>';
	echo '<th class="header" align="left" scope="col" style="white-space:nowrap;">Versuch</th>';
	echo '<th class="header" align="left" scope="col" style="white-space:nowrap;">Datum</th>';
	echo '<th class="header" align="left" scope="col" style="white-space:nowrap;">Punkte/' . substr_count($quiz->questions, ",") . '</th>';
	echo '</tr>';
	block_rk_fragesystem_print_attempt($id, $type = "best");
	block_rk_fragesystem_print_attempt($id, $type = "worst");
	block_rk_fragesystem_print_attempt($id, $type = "latest");
	echo '</table>';
	echo '</div>';
}
function block_rk_fragesystem_copy_quiz($copyid) {
	global $COURSE, $DB;

	if(!block_rk_fragesystem_check_owner($copyid))
		error('No permission to do this');

	$copyquiz = $DB->get_record('quiz',array('id'=>$copyid));
	$copyquiz->questions = str_replace(",0","",$copyquiz->questions);
	$questions = explode(",",$copyquiz->questions);
	shuffle($questions);
	$copyquiz->questions = implode(",0,",$questions);
	$copyquiz->questions .= ",0";
	$copyquiz->name = "Kopie von ".$copyquiz->name;

	block_rk_fragesystem_add_test($copyquiz,$COURSE->id,true);
}
function block_rk_fragesystem_print_attempt($quizid, $type="best") {
	global $CFG, $COURSE;

	if ($type == "best") {
		$desc = "Bester Versuch";
		$attempt = block_rk_fragesystem_get_quiz_attempts($quizid, 'max');
	}if ($type == "worst") {
		$desc = "Schlechtester Versuch";
		$attempt = block_rk_fragesystem_get_quiz_attempts($quizid, 'min');
	}if ($type == "latest") {
		$desc = "Letzter Versuch";
		$attempt = block_rk_fragesystem_get_quiz_attempts($quizid, 'latest');
	}

	if($attempt == false) {
		echo '<tr><td colspan="2">'.$desc.':</td><td>Noch kein Ergebnis vorhanden.</td></tr>';
	}
	else {
		echo '<tr>';
		echo "<td><a href='" . $CFG->wwwroot . "/blocks/rk_fragesystem/review.php?courseid=" . $COURSE->id . "&id=" . $quizid . "&attempt=" . $attempt->id . "'><img src=\"$CFG->wwwroot/pix/i/search.gif\" alt='see attempt' /></a>" . $desc . "</td>";
		echo '<td>' . date("D M j G:i Y", $attempt->timefinish) . '</td>';
		echo '<td>' . $attempt->sumgrades . '</td>';
		echo '</tr>';
	}
}

function block_rk_fragesystem_get_quiz_attempts($quizid, $option='max') {
	global $USER, $CFG, $DB;
	if ($option == "latest")
		$attempt = $DB->get_record_sql('select * from {quiz_attempts} where quiz=' . $quizid . ' AND userid=' . $USER->id . ' and timefinish=(SELECT max(timefinish) FROM {quiz_attempts} WHERE userid=' . $USER->id . ' and quiz=' . $quizid . ') limit 0,1');
	else
		$attempt = $DB->get_record_sql('select * from {quiz_attempts} where quiz=' . $quizid . ' AND userid=' . $USER->id . ' and sumgrades=(SELECT ' . $option . '(sumgrades) FROM {quiz_attempts} WHERE userid=' . $USER->id . ' and quiz=' . $quizid . ' and timefinish > 0) order by timefinish desc limit 0,1');

	return $attempt;
}

function block_rk_fragesystem_print_attempt_result($quizid, $attemptid) {

	global $CFG,$USER,$COURSE, $DB;

	$quiz = $DB->get_record('quiz', array('id'=> $quizid));
	$attempt = $DB->get_record('quiz_attempts', array('id'=> $attemptid));

	$sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance" .
			"  FROM {$CFG->prefix}question q," .
			"       {$CFG->prefix}quiz_question_instances i" .
			" WHERE i.quiz = '$quiz->id' AND q.id = i.question" .
			"   AND q.id IN ($attempt->layout)";
	if (!$questions = $DB->get_records_sql($sql)) {
		error('No questions found');
	}
	// Load the question type specific information
	if (!get_question_options($questions)) {
		error('Could not load question options');
	}

	/// Print summary table about the whole attempt.
	/// First we assemble all the rows that are appopriate to the current situation in
	/// an array, then later we only output the table if there are any rows to show.
	$rows = array();
	if ($attempt->userid <> $USER->id) {
		$student = $DB->get_record('user', array('id'=> $attempt->userid));
		$picture = print_user_picture($student, $course->id, $student->picture, false, true);
		$rows[] = '<tr><th scope="row" class="cell">' . $picture . '</th><td class="cell"><a href="' .
				$CFG->wwwroot . '/user/view.php?id=' . $student->id . '&amp;course=' . $course->id . '">' .
				fullname($student, true) . '</a></td></tr>';
	}
	/// Timing information.
	$rows[] = '<tr><th scope="row" class="cell">' . get_string('startedon', 'quiz') .
	'</th><td class="cell">' . userdate($attempt->timestart) . '</td></tr>';
	if ($attempt->timefinish) {
		$rows[] = '<tr><th scope="row" class="cell">' . get_string('completedon', 'quiz') . '</th><td class="cell">' .
				userdate($attempt->timefinish) . '</td></tr>';
	}

	/// Show scores (if the user is allowed to see scores at the moment).
	$grade = quiz_rescale_grade($attempt->sumgrades, $quiz);

	if ($quiz->grade and $quiz->sumgrades) {

		/// Show raw marks only if they are different from the grade (like on the view page.
		if ($quiz->grade != $quiz->sumgrades) {
			$a = new stdClass;
			$a->grade = round($attempt->sumgrades, $CFG->quiz_decimalpoints);
			$a->maxgrade = $quiz->sumgrades;
			$rows[] = '<tr><th scope="row" class="cell">' . get_string('marks', 'quiz') . '</th><td class="cell">' .
					get_string('outofshort', 'quiz', $a) . '</td></tr>';
		}

	}



	/// Now output the summary table, if there are any rows to be shown.
	if (!empty($rows)) {
		echo '<table class="generaltable generalbox quizreviewsummary"><tbody>', "\n";
		echo implode("\n", $rows);
		echo "\n</tbody></table>\n";
	}

	$cm = get_coursemodule_from_instance("quiz", $quiz->id, $COURSE->id);
	$options = new stdClass();
	$options->responses = true;
	$options->scores = true;
	$options->feedback = true;
	$options->correct_responses = true;
	$options->solutions = false;
	$options->generalfeedback = true;
	$options->overallfeedback = true;
	$options->quizstate = QUIZ_STATE_TEACHERACCESS;

	/// Print all the questions
	$quiz->thispageurl = $CFG->wwwroot . '/mod/quiz/review.php?attempt=' . $attempt->id;
	$quiz->cmid = $cm->id;
	$pagelist = quiz_questions_in_quiz($attempt->layout);
	$states = get_question_states($questions, $quiz, $attempt);
	$pagequestions = explode(',', $pagelist);
	$number = quiz_first_questionnumber($attempt->layout, $pagelist);
	foreach ($pagequestions as $i) {
		if (!isset($questions[$i])) {
			print_simple_box_start('center', '90%');
			echo '<strong><font size="+1">' . $number . '</font></strong><br />';
			notify(get_string('errormissingquestion', 'quiz', $i));
			print_simple_box_end();
			$number++; // Just guessing that the missing question would have lenght 1
			continue;
		}
		$options->validation = QUESTION_EVENTVALIDATE === $states[$i]->event;
		$options->history = 'all';
		// Print the question
		print_question($questions[$i], $states[$i], $number, $quiz, $options);

		$number += $questions[$i]->length;
	}
	//    $i=1;
	//    $states = get_question_states($questions, $quiz, $attempt);
	//    $pagelist = quiz_questions_in_quiz($attempt->layout);
	//    $pagequestions = explode(',', $pagelist);
	//
	////    foreach($questions as $question) {
	////        block_rk_fragesystem_print_question($question,$quiz,$i);
	////        $i++;
	////    }
	//
	//    foreach ($pagequestions as $i) {
	//        $correctanswers = block_rk_fragesystem_get_correct_responses($questions[$i], $state[$i]);
	//        print_r($correctanswers);
	//    }


}

function block_rk_fragesystem_print_question($question,$quiz,$count) {
	//Unterscheidung Typ
	if($question->qtype == "multichoice") {
		echo '<div id="q'.$question->id.'" class="que multichoice clearfix">';
		echo '<div class="info">';
		echo '<span class="no">'.$count.'</span>';
		echo '</div>';
		echo '<div class="content">';
		echo '<div class="qtext">'.$question->questiontext.'</div>';
		echo '<div class="ablock clearfix">';
		echo '<div class="prompt"> Answer: </div>';
		echo '<table class="answer">';
		echo '<tbody>';

		echo '</tbody';
		echo '</table>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}
/**
 * Update item in the database
 */
function block_rk_fragesystem_edit_test($post) {
	global $DB;
	$post->timemodified = time();

	$DB->update_record('quiz', $post);
	$cm = get_coursemodule_from_instance('quiz', $post->id);
	return $cm->id;
}
function block_rk_fragesystem_edit_flashcard($post) {
	global $DB;
	$DB->update_record('block_rk_user_kartei', $post);
	//return $cm->id;
}

/**
 * Write a new item into database
 */
function block_rk_fragesystem_add_test($post, $courseid, $copy = false) {
	global $CFG, $USER, $DB;

	if(!$copy) {
		$post->userid = $USER->id;
		$post->timemodified = time();
		$post->course = $courseid;
		$post->shufflequestions = 1;
		$post->shuffleanswers = 1;
		$post->questionsperpage = 1;
		$post->questions = "";
		$post->subnet = "";
		$post->password = "";
		$post->preferredbehaviour= "deferredfeedback";
		$post->reviewattempt = 69904;
		$post->reviewcorrectness = 4368;
		$post->reviewmarks = 4368;
		$post->reviewspecificfeedback = 4368;
		$post->reviewgeneralfeedback = 4368;
		$post->reviewrightanswer = 4368;
		$post->reviewoverallfeedback = 4368;
	}

	$post->id = $DB->insert_record('quiz', $post);

	$data = new stdClass();
	$data->createdby = $USER->id;
	$data->quizid = $post->id;
	$DB->insert_record('block_rk_user_quizes', $data);

	// Eintrag in course_modules Tabelle
	$mod = $DB->get_record('modules', array('name'=> 'quiz'));
	$cm = new stdClass();
	$cm->course = $courseid;
	$cm->instance = $post->id;
	$cm->visible = 1;
	$cm->module = $mod->id;
	$cmid = $DB->insert_record('course_modules', $cm);

	return $cmid;
}
function block_rk_fragesystem_add_flashcard($post, $courseid) {
	global $CFG, $USER, $DB;

	$post->userid = $USER->id;

	$post->id = $DB->insert_record('block_rk_user_kartei', $post);

	return $post;
}
/**
 * Delete item from database
 */
function block_rk_fragesystem_delete_test($post) {
	global $DB;
	$status = $DB->delete_records('quiz', array('id'=> $post->id));
	$status2 = $DB->delete_records('block_rk_user_quizes', array('quizid'=> $post->id));

	$mod = $DB->get_record('modules', array('name'=> 'quiz'));
	$status3 = $DB->delete_records('course_modules', array('instance'=> $post->id, 'module'=> $mod->id));
	if (!$status) {
		print_error('deleteposterror', 'block_exabis_eportfolio', $returnurl);
	}
}
function block_rk_fragesystem_delete_flashcard($post) {
	global $DB;
	$status = $DB->delete_records('block_rk_user_kartei', array('id'=> $post->id));
	//fragen_kartei tab l�schen
	$status2= $DB->delete_records('block_rk_user_kartei_frage ', array('karteiid'=> $post->id));
	if (!$status) {
		print_error('deleteposterror', 'block_exabis_eportfolio', $returnurl);
	}
}
function block_rk_fragesystem_list_question_categories($thiscontext) {
	global $DB;
	$pcontextids = get_parent_contexts($thiscontext);

	$contexts = array($thiscontext);
	$cat = "";

	foreach ($pcontextids as $pcontextid) {
		$cur = get_context_instance_by_id($pcontextid);

		$contexts[] = $cur;
		/*if ($cur->contextlevel != 40)
		 $contexts[] = $cur;
		if ($cur->contextlevel == 40)
			$cat = $cur;*/
	}

	$contexts = array_reverse($contexts);
	$categories = array();

	/*foreach ($contexts as $context) {
	 $cats = get_record('question_categories', 'contextid', $context->id, 'parent', 0);

	$categories = array_merge($categories,block_rk_fragesystem_get_sub_categories($cats));

	}*/

	foreach ($contexts as $context) {
		$cats = $DB->get_records('question_categories', array('contextid'=> $context->id));

		$categories = array_merge($categories,block_rk_fragesystem_get_sub_categories($cats));

		foreach($cats as $cat) {

			$anz = $DB->count_records_select('question', "category = $cat->id AND hidden = 0 AND qtype IN ('multichoice','truefalse','shortanswer')");
			$cat->name = $cat->name . '(' . $anz . ')';

			$categories[] = $cat;
		}
	}

	return $categories;
}
function block_rk_fragesystem_list_question_categories_new($thiscontext, $courseid = null) {
	global $COURSE, $CFG, $USER, $DB;

	$pcontextids = get_parent_contexts($thiscontext);
	$contexts = array($thiscontext);
	$cat = "";

	foreach ($pcontextids as $pcontextid) {
		$cur = get_context_instance_by_id($pcontextid);

		$contexts[] = $cur;
		/*if ($cur->contextlevel != 40)
		 $contexts[] = $cur;
		if ($cur->contextlevel == 40)
			$cat = $cur;*/
	}

	$contexts = array_reverse($contexts);
	$categories = array();

	foreach ($contexts as $context) {

		if($context->instanceid == $COURSE->id && $context->contextlevel == 50)
			continue;
			
		$cats = $DB->get_records('question_categories', array('contextid'=> $context->id, 'parent'=> 0));

		foreach($cats as $cat) {
			$subcategories = block_rk_fragesystem_get_sub_categories($cat);
			if($subcategories == null) {
				$cat->count = $DB->count_records('question', array('category'=>$cat->id));
	
			} else
				$cat->count = 0;
	
			$cat->level = 0;
			$categories[] = $cat;
			$categories = array_merge($categories,$subcategories);
		}

	}

	//Kategorie "Eigene Fragen" berücksichtigen
	$coursecontext = $DB->get_record('context', array('contextlevel'=> 50, 'instanceid'=> $COURSE->id));

	if($eigenefragen = $DB->get_records('question_categories', array('contextid'=> $coursecontext->id))) {
		foreach($eigenefragen as $eigenefrage) {
			$eigenefrage->count = $DB->count_records('question',array('category'=>$eigenefrage->id, 'createdby'=> $USER->id));
			if(!in_array($eigenefrage,$categories))
				$categories[] = $eigenefrage;
		}
	}

	return $categories;
}
function block_rk_fragesystem_get_sub_categories($parent, $level = 1) {
	global $USER, $CFG, $DB;
	$subcategories = array();

	$categories = $DB->get_records('question_categories', array('parent'=> $parent->id), 'sortorder');

	foreach($categories as $category) {

		if($DB->get_records('question_categories', array('parent'=>$category->id))) {

			$category->count = 0;
			$subcategories[] = $category;
			$subs = block_rk_fragesystem_get_sub_categories($category,$level+1);

			$subcategories = array_merge($subcategories, $subs);
		}
		else {
			/* $category->count = count_records('question', 'category', $category->id, 'qtype', 'multichoice' ); */
			$category->count = $DB->count_records_select('question', "category = $category->id AND hidden = 0 AND qtype IN ('multichoice','truefalse','shortanswer')");
			$subcategories[] = $category;
		}
		$category->level = $level;
	}

	return $subcategories;
}
function block_rk_fragesystem_print_category_dropdown($categories, $courseid, $cmid, $current) {

	foreach ($categories as $category) {
		var_dump($category);
		$selected = "";
		if ($category->id == $current)
			$selected = "selected";

		if($cmid == "watch")
			echo '<option ' . $selected . ' value="allquestions.php?courseid=' . $courseid . '&cat=' . $category->id . '">';
		else
			echo '<option ' . $selected . ' value="edit.php?courseid=' . $courseid . '&cmid=' . $cmid . '&cat=' . $category->id . '">';
		
		if(!isset($category->level))
			$category->level = 0;
		
		for($i=0;$i<$category->level;$i++)
		echo '&nbsp;';
			
		echo $category->name . ' ('.$category->count.')</option>';
	}
}

function block_rk_fragesystem_count_kartei_questions($karteiid) {
	global $DB;
	return $DB->count_records_select('block_rk_user_kartei_frage','karteiid = '.$karteiid);
}
function block_rk_fragesystem_get_kartei_level($karteiid, $level) {
	global $DB;
	return $DB->count_records_select('block_rk_user_kartei_frage','karteiid = '.$karteiid.' AND level = '.$level);
}
function block_rk_fragesystem_get_kartei_level_question($karteiid,$level,$max) {
	global $CFG, $DB;

	$random = rand(1,$max);

	$levelquestions = $DB->get_records_select('block_rk_user_kartei_frage', 'karteiid='.$karteiid.' AND level='.$level);
	$i=1;
	$levelquestion = null;

	foreach($levelquestions as $current) {

		if($i == $random) $levelquestion = $current;
		$i++;
	}

	$question = $DB->get_record('question',array('id'=>$levelquestion->frageid));
	if($question->qtype == "multichoice")
		return $question;
	else
		block_rk_fragesystem_get_kartei_level_question($karteiid,$level,$max);
}
function block_rk_fragesystem_get_question_answers($questionid) {
	global $DB;
	$answers = $DB->get_records('question_answers',array('question'=>$questionid));
	return $answers;
}
function block_rk_fragesystem_check_correct_question_answers($questionid,$responses) {
	global $CFG, $DB;

	$answers = $DB->get_records_select('question_answers', "question = $questionid AND fraction>0");

	return $answers;
}
function block_rk_fragesystem_process_question_answer($questionid,$karteiid,$correct) {
	global $DB;
	$qinstance = $DB->get_record('block_rk_user_kartei_frage',array('frageid'=>$questionid,'karteiid'=>$karteiid));
	if($correct) {
		if($qinstance->level < 6)
			$qinstance->level = $qinstance->level + 1;
	} else {
		if($qinstance->level > 1)
			$qinstance->level = $qinstance->level - 1;
	}
	$DB->update_record('block_rk_user_kartei_frage',$qinstance);
}
function block_rk_fragesystem_get_kartei_categories($karteiid) {
	global $CFG,$USER,$COURSE, $DB;

	$sql = "SELECT qc.id, qc.name, count(q.id) as anz FROM ".$CFG->prefix."question q, ".$CFG->prefix."question_categories qc, ".$CFG->prefix."block_rk_user_kartei_frage k WHERE k.karteiid=".$karteiid." AND q.id=k.frageid AND q.category = qc.id group by qc.id";
	$cats = $DB->get_records_sql($sql);

	return $cats;
}
function block_rk_fragesystem_print_kartei_categories($categories, $courseid, $id) {
	global $CFG;

	$returnurl = $CFG->wwwroot . '/blocks/rk_fragesystem/flashcard.php?courseid=' . $courseid . '&id=' . $id . '&action=edit&delete=';

	echo "<form method=\"post\" action=\"flashcard.php\">";
	echo '<fieldset class="invisiblefieldset" style="display: block;">';
	echo "<table width='100%'>";
	echo "<th align=\"left\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">Kategorie</th>";
	echo "<th align=\"left\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">Aktion</th>";
	echo "</tr>";
	foreach($categories as $category) {
		echo "<tr>";
		echo "<td>".$category->name."(".$category->anz.")</td>";
		echo "<td><a title=\"L�schen\" href=\"" . $returnurl. $category->id . "\">
		<img src=\"$CFG->wwwroot/pix/t/removeright.gif\" class=\"iconsmall\" alt=\"L�schen\" /></a></td>";
		echo "</tr>";
	}
	echo "</table></fieldset></form>";
}
function block_rk_fragesystem_print_category_checkbox($categories, $courseid, $id, $karteicategories) {
	global $CFG, $DB;
	$returnurl = $CFG->wwwroot . '/blocks/rk_fragesystem/flashcard.php?courseid=' . $courseid . '&id=' . $id . '&action=edit';

	$coursecontext = $DB->get_record('context',array('contextlevel'=>50,'instanceid'=>$courseid));

	echo "<form method=\"post\" action=\"".$returnurl."\">";
	foreach($categories as $category) {

		$sql = "SELECT * FROM ".$CFG->prefix."question_categories
		WHERE contextid = ".$coursecontext->id." and id=(select min(id) from ".$CFG->prefix."question_categories where contextid = ".$coursecontext->id.")";
		$cat = $DB->get_record_sql($sql);

		if($category->id == $cat->id)
			$eigenefragen=true;
		else
			$eigenefragen=false;

		if($eigenefragen)
			continue;

		if(array_key_exists($category->id,$karteicategories)) continue;
		if(!isset($category->level))
			$category->level = 0;
		
		for($i=0;$i<$category->level;$i++)
			echo '&nbsp;';
		if($category->count>0)
			echo '<input type="checkbox" name="q'.$category->id.'" value="1">';
			
		echo $category->name.' ('.$category->count.')<br/>';
	}
	echo "<input type=\"submit\" name=\"add\" value=\"Ausgewählte Kategorien zur Kartei hinzufügen\" />";
	echo "</form>";
}
function block_rk_fragesystem_add_kartei_category($key, $kartei) {
	global $CFG, $DB;

	$questions = $DB->get_records('question',array('category'=>$key));

	foreach($questions as $question) {

		$entry = new stdClass;
		$entry->karteiid = $kartei;
		$entry->frageid = $question->id;
		$entry->level = 1;
		if($question->qtype == "multichoice")
			$DB->insert_record('block_rk_user_kartei_frage', $entry);

	}
}
function block_rk_fragesystem_delete_kartei_category($delete, $id) {
	global $CFG, $DB;

	$i= $DB->delete_records_select('block_rk_user_kartei_frage'," frageid IN (SELECT id FROM ".$CFG->prefix."question WHERE category=".$delete.") AND karteiid=".$id);
}
function block_rk_fragesystem_print_quiz_questions($quiz, $pageurl) {
	global $USER, $CFG, $QTYPES, $DB;
	$strorder = get_string("order");
	$strquestionname = get_string("questionname", "quiz");
	$strgrade = get_string("grade");
	$strremove = get_string('remove', 'quiz');
	$stredit = get_string("edit");
	$strview = get_string("view");
	$straction = get_string("action");
	$strmoveup = get_string("moveup");
	$strmovedown = get_string("movedown");
	$strsavegrades = get_string("savegrades", "quiz");
	$strtype = get_string("type", "quiz");
	$strpreview = get_string("preview", "quiz");

	if (!$quiz->questions) {
		echo "<p class=\"quizquestionlistcontrols\">";
		print_string("noquestions", "quiz");
		echo "</p>";
		return 0;
	}
	if (!$questions = $DB->get_records_sql("SELECT q.*,c.contextid
			FROM {$CFG->prefix}question q,
			{$CFG->prefix}question_categories c
			WHERE q.id in ($quiz->questions)
			AND q.category = c.id")) {
			echo "<p class=\"quizquestionlistcontrols\">";
			print_string("noquestions", "quiz");
			echo "</p>";
			return 0;
			}

			echo "<form method=\"post\" action=\"edit.php\">";
			echo '<fieldset class="invisiblefieldset" style="display: block;">';
			echo "<table width='100%'";
			echo "<tr><th colspan=\"3\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$strorder</th>";
			echo "<th align=\"left\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">Frage</th>";
			echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">Typ</th>";
			echo "<th align=\"center\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";
			echo "</tr>\n";

			$count = 0;
			$qno = 1;
			$sumgrade = 0;
			$order = explode(',', $quiz->questions);
			$lastindex = count($order) - 1;

			foreach ($order as $i => $qnum) {

				if ($qnum and empty($questions[$qnum])) {
					continue;
				}

				// If the questiontype is missing change the question type
				// if ($qnum and !array_key_exists($questions[$qnum]->qtype, question_bank)) {
				//   $questions[$qnum]->qtype = 'missingtype';
				//}

				// If the questiontype is missing change the question type
				if ($qnum && !array_key_exists($qnum, $questions)) {
					$fakequestion = new stdClass();
					$fakequestion->id = $qnum;
					$fakequestion->category = 0;
					$fakequestion->qtype = 'missingtype';
					$fakequestion->name = get_string('missingquestion', 'quiz');
					$fakequestion->questiontext = ' ';
					$fakequestion->questiontextformat = FORMAT_HTML;
					$fakequestion->length = 1;
					$questions[$qnum] = $fakequestion;
					$quiz->grades[$qnum] = 0;
				} else if ($qnum && !question_bank::qtype_exists($questions[$qnum]->qtype)) {
					$questions[$qnum]->qtype = 'missingtype';
				}

				if ($qnum == 0) { // This is a page break
					$count++;
					// missing </tr> here, if loop is broken, need to close the </tr>
					echo "</tr>";
					continue;
				}
				$question = $questions[$qnum];

				echo "<td>";
				if ($count != 0) {
					echo "<a title=\"$strmoveup\" href=\"" . $pageurl->out(true, array('up' => $count, 'sesskey'=>sesskey())) . "\"><img
					src=\"$CFG->wwwroot/pix/t/up.gif\" class=\"iconsmall\" alt=\"$strmoveup\" /></a>";
				}
				echo "</td>";
				echo "<td>";
				if ($count < $lastindex - 1) {
					echo "<a title=\"$strmovedown\" href=\"" . $pageurl->out(true, array('down' => $count, 'sesskey'=>sesskey())) . "\"><img
					src=\"$CFG->wwwroot/pix/t/down.gif\" class=\"iconsmall\" alt=\"$strmovedown\" /></a>";
				}
				echo "</td>";

				if (!$quiz->shufflequestions) {
					// Print and increment question number
					echo '<td>' . ($question->length ? $qno : '&nbsp;') . '</td>';
					$qno += $question->length;
				} else {
					echo '<td>&nbsp;</td>';
				}

				echo '<td>' . format_string($question->name) . '</td>';
				echo '<td  valign="center"; align="center">';
				echo print_question_icon($question);
				echo "</td>";
				echo '<td  valign="center"; align="center">';

				// $returnurl = $pageurl->out();
				//var_dump($pageurl->out());
				//TODO funktioniert nicht!!
				$returnurl = $CFG->dirroot.'/blocks/rk_fragesystem/edit.php';
				$questionparams = array('returnurl' => $returnurl, 'cmid' => $quiz->cmid, 'id' => $question->id);
				$questionurl = new moodle_url("$CFG->wwwroot/question/question.php", $questionparams);
				if (question_has_capability_on($question, 'edit', $question->category) || question_has_capability_on($question, 'move', $question->category)) {
					echo "<a title=\"$stredit\" href=\"" . $questionurl->out() . "\">
					<img src=\"$CFG->wwwroot/pix/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a>&nbsp;";
				} elseif (question_has_capability_on($question, 'view', $question->category)) {
					echo "<a title=\"$strview\" href=\"" . $questionurl->out(false, array('id' => $question->id)) . "\"><img
					src=\"$CFG->wwwroot/pix/i/info.gif\" alt=\"$strview\" /></a>&nbsp;";
				}
				echo "<a title=\"$strremove\" href=\"" . $pageurl->out(true, array('delete' => $question->id,'sesskey' => sesskey())) . "\"><img src=\"$CFG->wwwroot/pix/t/removeright.gif\" class=\"iconsmall\" alt=\"$strremove\" /></a>";


				echo "</td></tr>";
				$count++;
			}
			echo "</table></fieldset></form>";
}

function block_rk_fragesystem_print_question_list($cat, $courseid, $cmid, $search_results = null) {
	global $CFG, $USER, $DB;
	$strselectall = get_string("selectall", "quiz");
	$strselectnone = get_string("selectnone", "quiz");

	$coursecontext = $DB->get_record('context', array('contextlevel'=>50,'instanceid'=>$courseid));
	$sql = "SELECT * FROM ".$CFG->prefix."question_categories
	WHERE contextid = ".$coursecontext->id." and id=(select min(id) from ".$CFG->prefix."question_categories where contextid = ".$coursecontext->id.")";
	$category = $DB->get_record_sql($sql);

	if($cat == $category->id)
		$eigenefragen=true;
	else
		$eigenefragen=false;



	if ($search_results != null) {
		$questions = $search_results;
	} else
		$questions = $DB->get_records('question', array('category'=>$cat));


	$returnurl = $CFG->wwwroot . '/blocks/rk_fragesystem/edit.php?courseid=' . $courseid . '&cmid=' . $cmid . '&cat=' . $cat;
	$questionurl = new moodle_url("$CFG->wwwroot/question/question.php",
			array('returnurl' => $returnurl, 'courseid' => $courseid));

	echo '<form action="' . $returnurl . '" method="post">';
	echo '<table id="categoryquestions" style="width: 100%"><tr>';
	echo "<th style=\"white-space:nowrap; text-align: left; width:10%\" class=\"header\" scope=\"col\">Aktion</th>";

	echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">Frage</th>
	<th style=\"white-space:nowrap; text-align: right; width:10%\" class=\"header\" scope=\"col\">Typ</th>";
	echo "</tr>\n";
	foreach ($questions as $question) {
		if ($question->hidden == 1 OR $question->qtype != "multichoice" && $question->qtype != "truefalse" && $question->qtype != "shortanswer")
			continue;

		if(($eigenefragen && $question->createdby == $USER->id) || !$eigenefragen) {
			echo '<tr>';
			echo "<td style=\"white-space:nowrap; valign:top;\">";
			echo '<a href="edit.php?courseid=' . $courseid . '&cmid=' . $cmid . '&addquestion=' . $question->id . '&cat=' . $cat . '" title="Add to quiz">
			<img alt="Add to quiz" src="' . $CFG->wwwroot . '/pix/t/moveleft.gif"></a>&nbsp;&nbsp;';

			if (question_has_capability_on($question, 'edit', $question->category) || question_has_capability_on($question, 'move', $question->category)) {
				echo "<a title='edit' href=\"" . $questionurl->out(false, array('id' => $question->id)) . "\"><img
				src=\"$CFG->wwwroot/pix/t/edit.gif\" alt=\"edit\" /></a>&nbsp;";
			} elseif (question_has_capability_on($question, 'view', $question->category)) {
				echo "<a title=\"view\" href=\"" . $questionurl->out(false, array('id' => $question->id)) . "\"><img
				src=\"$CFG->wwwroot/pix/i/info.gif\" alt=\"view\" /></a>&nbsp;";
			}
			echo "<input title=\"select\" type=\"checkbox\" name=\"q$question->id\" value=\"1\" />";

			//        if (question_has_capability_on($question, 'edit', $question->category)) {
			//            // hide-feature
			//            if ($question->hidden) {
			//                echo "<a title=\"restore\" href=\"edit.php?" . $querystring . "&amp;unhide=$question->id&amp;sesskey=$USER->sesskey\"><img
			//                        src=\"$CFG->pixpath/t/restore.gif\" alt=\"restore\" /></a>";
			//            } else {
			//                echo "<a title=\"löschen\" href=\"edit.php?" . $querystring . "&amp;deleteselected=$question->id&amp;q$question->id=1\"><img
			//                        src=\"$CFG->pixpath/t/delete.gif\" alt=\"löschen\" /></a>";
			//            }
			//        }

			echo '</td>';
			echo '<td>' . $question->name . '</td>';
			echo '<td style="text-align: right;">';
			echo print_question_icon($question);
			echo '</td>';
			echo '</tr>';
		}

	}
	echo '</table>';
	echo '<br />';
	echo '<a href="javascript:select_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectall.'</a> /'.
			' <a href="javascript:deselect_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectnone.'</a><br />';
	echo '<br />';
	echo "<input type=\"submit\" name=\"add\" value=\"Ausgew&auml;hlte Fragen zum Test hinzuf&uuml;gen\" />";

	$cm = get_coursemodule_from_id('quiz', $cmid);
	$quiz = $DB->get_record("quiz", array("id"=> $cm->instance));
	$categorylist = question_categorylist($cat);
	$commaseperated = implode(",", $categorylist);
	$qcount = $DB->count_records_select('question', "category IN ($commaseperated) AND parent = '0' AND qtype IN ('multichoice','truefalse','shortanswer')");

	if(!empty($quiz->questions)){
		$sql = "SELECT count( q.id ) as anz
		FROM ".$CFG->prefix."quiz qz, ".$CFG->prefix."question q
		WHERE qz.id = ".$quiz->id."
		AND q.id
		IN ( ".$quiz->questions." )
		AND q.category =".$cat;

		$totalnumber = $DB->get_record_sql($sql);
	}else{
		$totalnumber = new stdClass();
		$totalnumber->anz = 0;
	}
	$category = $DB->get_record('question_categories', array('id'=> $cat));

	echo module_specific_controls(($qcount - $totalnumber->anz), 1, $category, $cmid);

	echo '</form>';
}

function block_rk_fragesystem_print_question_list_for_all($cat, $courseid) {

	global $CFG, $USER, $DB, $OUTPUT;

	$coursecontext = $DB->get_record('context', array('contextlevel'=>50,'instanceid'=>$courseid));
	$sql = "SELECT * FROM ".$CFG->prefix."question_categories
	WHERE contextid = ".$coursecontext->id." and id=(select min(id) from ".$CFG->prefix."question_categories where contextid = ".$coursecontext->id.")";
	$category = $DB->get_record_sql($sql);

	if($cat == $category->id)
		$eigenefragen=true;
	else
		$eigenefragen=false;

	$questions = $DB->get_records('question', array('category'=> $cat));


	$returnurl = $CFG->wwwroot . '/blocks/rk_fragesystem/allquestions.php?courseid=' . $courseid . '&cat=' . $cat;
	$questionurl = new moodle_url("$CFG->wwwroot/question/question.php",
			array('returnurl' => $returnurl, 'courseid' => $courseid));

	echo '<form action="' . $returnurl . '" method="post">';
	echo '<table id="categoryquestions" style="width: 100%"><tr>';
	echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">Frage</th>
	<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">Typ</th>";
	echo "</tr>\n";
	$detailurl = $CFG->wwwroot.'/blocks/rk_fragesystem/detailquestion.php?qid=';
	foreach ($questions as $question) {

		if(($eigenefragen && $question->createdby == $USER->id) || !$eigenefragen) {
			if ($question->hidden == 1 OR $question->qtype != "multichoice" && $question->qtype != "truefalse" && $question->qtype != "shortanswer")
				continue;

			$link = new moodle_url($detailurl.$question->id.'&courseid='.$courseid);
			$html = $OUTPUT->action_link($link, '<img src="'.$CFG->wwwroot.'/pix/t/preview.gif">', new popup_action('click', $link, 'watchdetail', array('height' => 400, 'width' => 1000)));

			echo '<tr>';echo '<td>';
			echo $html." ".$question->name;;
			echo '</td>';
			echo '<td>';
			echo print_question_icon($question);
			echo '</td>';
			echo '</tr>';
		}
	}
	echo '</table>';
	echo '</form>';
}

function block_rk_fragesystem_print_search_form($cat, $courseid, $cmid, $value='') {
	global $CFG;

	echo '<div class="generalbox quizquestions box"><h3>Volltextsuche</h3>';

	$returnurl = $CFG->wwwroot . '/blocks/rk_fragesystem/edit.php?courseid=' . $courseid . '&cmid=' . $cmid . '&cat=' . $cat;

	echo '<form action="' . $returnurl . '" method="post">';
	echo '<p>Suchbegriff:<br><input name="searchpattern" type="text" size="50" maxlength="50" value="' . $value . '">
	<input type="submit" value="Suchen"></p>';
	if ($value != '')
		echo '<a href="' . $returnurl . '">Suchbegriff zurücksetzen</a>';
	echo '</form>';
}

function block_rk_fragesystem_do_search($searchpattern, $categories) {
	global $DB;
	$implode_categories = array();
	foreach ($categories as $category) {
		$implode_categories[] = $category->id;
	}
	$catstring = implode(",", $implode_categories);

	$results = $DB->get_records_select('question', 'category IN (' . $catstring . ') AND name LIKE "%' . $searchpattern . '%"');
	return $results;
}

?>
