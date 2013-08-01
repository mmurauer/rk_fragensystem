<?php
require_once dirname(__FILE__).'/inc.php';

$courseid = required_param('courseid',  PARAM_INT);
redirect('mytests.php?courseid='.$courseid);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);

require_capability('block/rk_fragesystem:use', $context);

if (! $course = get_record("course", "id", $courseid) ) {
	 print_error("invalidinstance","rk_fragesystem");
}

block_rk_fragesystem_print_header("info");

?>
