<?php

require_once dirname(__FILE__) . '/inc.php';
require_once("../../mod/quiz/locallib.php");

global $USER, $DB;

$courseid = required_param('courseid', PARAM_INT);
$id = required_param('id', PARAM_INT);
$attempt = optional_param('attempt',0, PARAM_INT);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
$quiz = $DB->get_record('quiz',array('id'=>$id));

if(!block_rk_fragesystem_check_owner($id))
	error('No permission to do this');
	
if (!$course = $DB->get_record("course", array("id" => $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}
$url = '/blocks/rk_fragesystem/reviews.php?courseid=' . $courseid."&id=".$id;
$PAGE->set_url($url);

block_rk_fragesystem_print_header("mytests");

?>
<div class="generalbox mytests box">
<h2 class="main">Ergebnisse für <?php echo $quiz->name; ?></h2>
<?php

if($attempt != 0)
    block_rk_fragesystem_print_attempt_result($id,$attempt);
else
    block_rk_fragesystem_print_quiz_review($id);
?>
</div>