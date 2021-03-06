<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This page prints a summary of a quiz attempt before it is submitted.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(__FILE__) . '/inc.php';
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$attemptid = required_param('attempt', PARAM_INT); // The attempt to summarise.

$PAGE->set_url('/mod/quiz/summary.php', array('attempt' => $attemptid));

$attemptobj = quiz_attempt::create($attemptid);

// Check login.
require_login($attemptobj->get_course(), false);

// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    if ($attemptobj->has_capability('mod/quiz:viewreports')) {
        redirect($attemptobj->review_url(null));
    } else {
        throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
    }
}

// Check capabilites.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
}

if ($attemptobj->is_preview_user()) {
    navigation_node::override_active_url($attemptobj->start_attempt_url());
}

// Check access.
$accessmanager = $attemptobj->get_access_manager(time());
$accessmanager->setup_attempt_page($PAGE);
$output = $PAGE->get_renderer('block_rk_fragesystem');
$messages = $accessmanager->prevent_access();
if (!$attemptobj->is_preview_user() && $messages) {
    print_error('attempterror', 'quiz', $attemptobj->view_url(),
            $output->access_messages($messages));
}
if ($accessmanager->is_preflight_check_required($attemptobj->get_attemptid())) {
    redirect($attemptobj->start_attempt_url(null, $page));
}

$displayoptions = $attemptobj->get_display_options(false);

// If the attempt is now overdue, or abandoned, deal with that.
$attemptobj->handle_if_time_expired(time(), true);

// If the attempt is already closed, redirect them to the review page.
if ($attemptobj->is_finished()) {
    redirect($attemptobj->review_url());
}

block_rk_fragesystem_print_header("mytests");
// Display the page.
echo $output->summary_page($attemptobj, $displayoptions);
