<?php

require_once dirname(__FILE__) . '/inc.php';
require_once($CFG->dirroot.'/question/editlib.php');


$courseid = required_param('courseid',  PARAM_INT);

$url = '/blocks/rk_fragesystem/allquestions.php?courseid=' . $courseid;
$PAGE->set_url($url);

//global $DB, $OUTPUT;

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);

if (!$course = $DB->get_record("course", array("id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}

block_rk_fragesystem_print_header("allquestions");

$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
$categories = block_rk_fragesystem_list_question_categories_new($coursecontext);

$cat = optional_param('cat',current($categories)->id, PARAM_INT);

echo '<div class="generalbox quizquestions box">';
echo '<h2 class="main">Fragenkatalog</h2>';


echo 'Kategorie: ';
echo '<form id="catmenu2" class="popupform" method="get">';
echo '<select id="catmenu2_jump" onchange="self.location=document.getElementById(\'catmenu2\').jump.options[document.getElementById(\'catmenu2\').jump.selectedIndex].value;" name="jump">';
block_rk_fragesystem_print_category_dropdown($categories, $courseid, "watch", $cat);
echo '</select></form>';
block_rk_fragesystem_print_question_list_for_all($cat, $courseid);
echo '</div>';

echo $OUTPUT->footer();
?>
