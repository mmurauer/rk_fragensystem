<?php

// $Id: edit.php,v 1.107.2.13 2009/06/05 09:18:35 tjhunt Exp $
/**
 * Page to edit quizzes
 *
 * This page generally has two columns:
 * The right column lists all available questions in a chosen category and
 * allows them to be edited or more to be added. This column is only there if
 * the quiz does not already have student attempts
 * The left column lists all questions that have been added to the current quiz.
 * The lecturer can add questions from the right hand list to the quiz or remove them
 *
 * The script also processes a number of actions:
 * Actions affecting a quiz:
 * up and down  Changes the order of questions and page breaks
 * addquestion  Adds a single question to the quiz
 * add          Adds several selected questions to the quiz
 * addrandom    Adds a certain number of random questions to the quiz
 * repaginate   Re-paginates the quiz
 * delete       Removes a question from the quiz
 * savechanges  Saves the order and grades for questions in the quiz
 *
 * @author Martin Dougiamas and many others. This has recently been extensively
 *         rewritten by Gustav Delius and other members of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */
require_once dirname(__FILE__) . '/inc.php';
require_once("../../config.php");
//require_once($CFG->dirroot . '/mod/quiz/editlib.php');
require_once dirname(__FILE__) . '/lib/qeditlib.php';

define("DEFAULT_CAT", 505);

$courseid = required_param('courseid',  PARAM_INT);

$url = '/blocks/rk_fragesystem/edit.php?courseid=' . $courseid;
$PAGE->set_url($url);

require_login($courseid);

/**
 * Callback function called from question_list() function (which is called from showbank())
 * Displays action icon as first action for each question.
 */
function module_specific_actions($pageurl, $questionid, $cmid, $canuse) {
    global $CFG;
    if ($canuse) {
        // for RTL languages: switch right and left arrows /****/
        if (right_to_left ()) {
            $movearrow = 'removeright.gif';
        } else {
            $movearrow = 'moveleft.gif';
        }
        $straddtoquiz = get_string("addtoquiz", "quiz");
        $out = "<a title=\"$straddtoquiz\" href=\"edit.php?" . $pageurl->get_query_string() . "&amp;addquestion=$questionid&amp;sesskey=" . sesskey() . "\"><img
                  src=\"$CFG->wwwroot/pix/t/$movearrow\" alt=\"$straddtoquiz\" /></a>&nbsp;";
        return $out;
    } else {
        return '';
    }
}

/**
 * Callback function called from question_list() function (which is called from showbank())
 * Displays button in form with checkboxes for each question.
 */
function module_specific_buttons($cmid) {
    global $THEME;
    $straddtoquiz = get_string("addtoquiz", "quiz");
    $out = "<input type=\"submit\" name=\"add\" value=\"{$THEME->larrow} $straddtoquiz\" />\n";
    return $out;
}

/**
 * Callback function called from question_list() function (which is called from showbank())
 */
function module_specific_controls($totalnumber, $recurse, $category, $cmid) {
    global $CFG, $PAGE, $COURSE, $OUTPUT;
    $out = '';
    $catcontext = context::instance_by_id($category->contextid);
    //get_context_instance_by_id($category->contextid);

	$randomusablequestions = question_bank::get_qtype('random')->get_available_questions_from_category(
            $category->id, $recurse, '0');
    //$randomusablequestions = $QTYPES['random']->get_usable_questions_from_category(
      //              $category->id, $recurse, '0');

    $maxrand = $totalnumber;
    if ($maxrand > 0) {
        for ($i = 1; $i <= min(30, $maxrand); $i++) {
            $randomcount[$i] = $i;
        }
        for ($i = 40; $i <= min(100, $maxrand); $i += 10) {
            $randomcount[$i] = $i;
        }
        $out .= '<br />';
        $out .= html_writer::select($randomcount, 'random_quiz',array(1),null,array("onchange"=>"document.location.href='".$CFG->wwwroot.$PAGE->url."courseid=".$COURSE->id."&viewid='+this.value;"));
       // $out .= get_string('addrandom', 'quiz', choose_from_menu($randomcount, 'randomcount', '1', '', '', '', true));
        $out .= 'Zufallsfrage(n)';
        $out .= '<input type="hidden" name="recurse" value="' . $recurse . '" />';
        $out .= '<input type="hidden" name="categoryid" value="' . $category->id . '" />';
        $out .= '<input type="submit" name="addrandom" value="' . get_string('add') . '" />';
        $out .= $OUTPUT->help_icon('random', 'block_rk_fragesystem', false);
    }

    return $out;
}

global $DB, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', array('id'=> $courseid));

$cmid = required_param('cmid', PARAM_INT);
$cat = optional_param('cat', DEFAULT_CAT, PARAM_INT);
$searchpattern = optional_param('searchpattern', '', PARAM_ALPHA);
$thiscontext = get_context_instance(CONTEXT_MODULE, $cmid);
$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
$thispageurl = new moodle_url($url);
$thispageurl->param('courseid', $courseid);
$thispageurl->param('cmid', $cmid);
$thispageurl->param('cat', $cat);

list($quiz, $cm) = get_module_from_cmid($cmid);

if(!block_rk_fragesystem_check_owner($quiz->id))
	error('No permission to do this');
	
block_rk_fragesystem_print_header("mytests", $quiz->name, $courseid, $quiz->id);

if (($up = optional_param('up', false, PARAM_INT)) !== false) { /// Move the given question up a slot
    $questions = explode(",", $quiz->questions);
    if ($up > 0 and isset($questions[$up])) {
        $prevkey = ($questions[$up - 1] == 0) ? $up - 2 : $up - 1;
        $swap = $questions[$prevkey];
        $questions[$prevkey] = $questions[$up];
        $questions[$up] = $swap;
        $quiz->questions = implode(",", $questions);
        // Always have a page break at the end
        $quiz->questions = $quiz->questions . ',0';
        // Avoid duplicate page breaks
        $quiz->questions = str_replace(',0,0', ',0', $quiz->questions);
        if (!$DB->set_field('quiz', 'questions', $quiz->questions, array('id'=> $quiz->instance))) {
            error('Could not save question list');
        }
        $significantchangemade = true;
    }
}
if (($down = optional_param('down', false, PARAM_INT)) !== false) { /// Move the given question down a slot
    $questions = explode(",", $quiz->questions);
    if ($down < count($questions)) {
        $nextkey = ($questions[$down + 1] == 0) ? $down + 2 : $down + 1;
        $swap = $questions[$nextkey];
        $questions[$nextkey] = $questions[$down];
        $questions[$down] = $swap;
        $quiz->questions = implode(",", $questions);
        // Avoid duplicate page breaks
        $quiz->questions = str_replace(',0,0', ',0', $quiz->questions);
        if (!$DB->set_field('quiz', 'questions', $quiz->questions, array('id'=> $quiz->instance))) {
            error('Could not save question list');
        }
        $significantchangemade = true;
    }
}
if (($addquestion = optional_param('addquestion', 0, PARAM_INT))) { /// Add a single question to the current quiz
    quiz_add_quiz_question($addquestion, $quiz);
}
if (optional_param('add', false, PARAM_BOOL)) { /// Add selected questions to the current quiz
    $rawdata = (array) data_submitted();
    foreach ($rawdata as $key => $value) {    // Parse input for question ids
        if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
            $key = $matches[1];
            quiz_add_quiz_question($key, $quiz);
        }
    }
    $significantchangemade = true;
}
if (($delete = optional_param('delete', false, PARAM_INT)) !== false) { /// Remove a question from the quiz
    quiz_remove_question($quiz, $delete);
}
if (optional_param('addrandom', false, PARAM_BOOL)) { /// Add random questions to the quiz

    $randomcount = required_param('randomcount', PARAM_INT);

    $sql = "SELECT q.*
        FROM ".$CFG->prefix."quiz qz, ".$CFG->prefix."question q
        WHERE qz.id = ".$quiz->id;
        
	if($quiz->questions)
		$sql .= " AND q.id NOT IN ( ".$quiz->questions." )";
		
    $sql .= "
		AND q.category =".$cat."
        AND qtype IN ('multichoice','truefalse','shortanswer')";

    $possibleq = $DB->get_records_sql($sql);
	
    if(count($possibleq) < $randomcount)
        $randomcount = count($possibleq);

    shuffle($possibleq);
    for($i=0;$i<$randomcount;$i++) {
        quiz_add_quiz_question($possibleq[$i]->id, $quiz);
    }
    // Find existing random questions in this category that are not used by any quiz.
//    if ($existingquestions = get_records_sql(
//                    "SELECT * FROM " . $CFG->prefix . "question q
//                WHERE qtype = '" . RANDOM . "'
//                    AND category = $category->id
//                    AND " . sql_compare_text('questiontext') . " = '$recurse'
//                    AND NOT EXISTS (SELECT * FROM " . $CFG->prefix . "quiz_question_instances WHERE question = q.id)
//                ORDER BY id")) {
//        // Take as many of these as needed.
//        while (($existingquestion = array_shift($existingquestions)) and $randomcount > 0) {
//            quiz_add_quiz_question($existingquestion->id, $quiz);
//            $randomcount--;
//        }
//    }

    // If more are needed, create them.
//    if ($randomcount > 0) {
//        $form->questiontext = $recurse; // we use the questiontext field to store the info
//        // on whether to include questions in subcategories
//        $form->questiontextformat = 0;
//        $form->image = '';
//        $form->defaultgrade = 1;
//        $form->hidden = 1;
//        for ($i = 0; $i < $randomcount; $i++) {
//            $form->category = "$category->id,$category->contextid";
//            $form->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
//            $question = new stdClass;
//            $question->qtype = RANDOM;
//            $question = $QTYPES[RANDOM]->save_question($question, $form, $course);
//            if (!isset($question->id)) {
//                error('Could not insert new random question!');
//            }
//            quiz_add_quiz_question($question->id, $quiz);
//        }
//    }
    $significantchangemade = true;
}

$categories = block_rk_fragesystem_list_question_categories_new($coursecontext);
if($cat == DEFAULT_CAT)
	$cat = reset($categories)->id;

echo '<table width="100%">';
echo '<tr><td valign="top" width="50%">';
echo '<div class="generalbox quizquestions box">';
echo '<h2 class="main">Fragen im Quiz</h2>';
block_rk_fragesystem_print_quiz_questions($quiz, $thispageurl);
echo '</div>';

echo '</td><td valign="top"  width="50%">';
echo '<div class="generalbox quizquestions box">';
echo '<h2 class="main">Fragenkatalog</h2>';

if ($searchpattern != '') {
    $search_results = block_rk_fragesystem_do_search($searchpattern, $categories);
}
else
    $search_results = null;

if ($searchpattern != '') {
    if ($search_results != null)
        echo 'Suchergebnisse für: <b>' . $searchpattern . '</b><br/>';
    else
        echo 'Keine Suchergebnisse für <b>' . $searchpattern . '</b><br/>';
}

echo 'Kategorie: ';
echo '<form id="catmenu2" class="popupform" method="get">';
echo '<select id="catmenu2_jump" onchange="self.location=document.getElementById(\'catmenu2\').jump.options[document.getElementById(\'catmenu2\').jump.selectedIndex].value;" name="jump">';
block_rk_fragesystem_print_category_dropdown($categories, $courseid, $cmid, $cat);
echo '</select></form>';
block_rk_fragesystem_print_question_list($cat, $courseid, $cmid, $search_results);
echo '</div>';
echo '</td></tr>';
echo '<tr><td></td><td>';
block_rk_fragesystem_print_search_form($cat, $courseid, $cmid, $searchpattern);
echo '</div></td></tr>';
echo '</table>';


echo $OUTPUT->footer($course);
?>
