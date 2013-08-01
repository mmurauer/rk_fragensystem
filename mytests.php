<?php

require_once dirname(__FILE__) . '/inc.php';
global $DB;
$courseid = required_param('courseid',  PARAM_INT);

$url = '/blocks/rk_fragesystem/mytests.php?courseid=' . $courseid;
$PAGE->set_url($url);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);

if (!$course = $DB->get_record('course',array('id'=>$courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}

block_rk_fragesystem_print_header("mytests");

$copyid = optional_param('copyid',0,PARAM_INT);
if($copyid) {
	block_rk_fragesystem_copy_quiz($copyid);
}
?>
<div class="generalbox mytests box">
<h2 class="main">Test&uuml;bersicht</h2>
<?php block_rk_fragesystem_mytest_list($courseid); ?>
</div>