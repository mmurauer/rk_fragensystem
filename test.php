<?php

require_once dirname(__FILE__) . '/inc.php';
require_once("../../config.php");
require_once($CFG->dirroot . '/mod/quiz/editlib.php');

$courseid = required_param('courseid', PARAM_INT);
$action = optional_param("action", "", PARAM_ALPHA);
$confirm = optional_param("confirm", "", PARAM_BOOL);

require_login($courseid);
block_rk_fragesystem_print_header("mytests", 'nix', $courseid, 3456346);
$context = get_context_instance(CONTEXT_SYSTEM);

$courseid = required_param('courseid');
$coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
$categories = block_rk_fragesystem_list_question_categories_new($coursecontext);

foreach($categories as $category) {
	echo $category->name.'<br/>';
}
?>