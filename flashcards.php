<?php
global $DB, $OUTPUT;
require_once dirname(__FILE__) . '/inc.php';

$courseid = required_param('courseid', PARAM_INT);

$url = '/blocks/rk_fragesystem/flashcards.php?courseid=' . $courseid;
$PAGE->set_url($url);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);


if (!$course = $DB->get_record("course", array("id"=> $courseid))) {
    print_error("invalidinstance", "rk_fragesystem");
}

block_rk_fragesystem_print_header("flashcards");

?>
<div class="generalbox flashcards box">
<h2 class="main">Kartei&uuml;bersicht</h2>
<?php block_rk_fragesystem_flashcard_list($courseid); ?>
</div>
<?php echo $OUTPUT->footer();?>