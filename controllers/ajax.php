<?php
// handle all ajax
function chainedquiz_ajax() {
	$action = empty($_POST['chainedquiz_action']) ? 'answer' : $_POST['chainedquiz_action'];
	
	switch($action) {
		// answer a question or quiz
		case 'answer':
		default:
			echo ChainedQuizQuizzes :: answer_question();
		break;
	}

	exit;
}