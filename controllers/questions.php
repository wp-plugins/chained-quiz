<?php
class ChainedQuizQuestions {
	static function manage() {
 		$action = empty($_GET['action']) ? 'list' : $_GET['action']; 
		switch($action) {
			case 'add':
				self :: add_question();
			break;
			case 'edit': 
				self :: edit_question();
			break;
			case 'list':
			default:
				self :: list_questions();	 
			break;
		}
	} // end manage()
	
	static function add_question() {
		global $wpdb;
		$_question = new ChainedQuizQuestion();
		
		if(!empty($_POST['ok'])) {
			try {
				$_POST['quiz_id'] = $_GET['quiz_id'];
				$qid = $_question->add($_POST);		
				$_question->save_choices($_POST, $qid);	
				chained_redirect("admin.php?page=chainedquiz_questions&quiz_id=".$_GET['quiz_id']);
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $_GET['quiz_id']));
		
		// select other questions for the go-to dropdown
		$other_questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d ORDER BY title", $quiz->id));
		
		include(CHAINED_PATH.'/views/question.html.php');
	} // end add_question
	
	static function edit_question() {
		global $wpdb;
		$_question = new ChainedQuizQuestion();
		
		if(!empty($_POST['ok'])) {
			try {
				$_question->save($_POST, $_GET['id']);
				$_question->save_choices($_POST, $_GET['id']);
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		// select the quiz and question		
		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE id=%d", $_GET['id']));
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $question->quiz_id));

		// select question choices
		$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." WHERE question_id=%d ORDER BY id ", $question->id));	
		
		// select other questions for the go-to dropdown
		$other_questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." 
			WHERE quiz_id=%d AND id!=%d ORDER BY title", $quiz->id, $question->id));	
		
		include(CHAINED_PATH.'/views/question.html.php');
	} // end edit_quiz
	
	// list and delete questions
	static function list_questions() {
		global $wpdb;
		$_question = new ChainedQuizQuestion();
		
		if(!empty($_GET['del'])) {
			$_question->delete($_GET['id']);			
		}
		
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $_GET['quiz_id']));
		$questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d ORDER BY id", $_GET['quiz_id']));
		include(CHAINED_PATH."/views/questions.html.php");
	} // end list_quizzes	
}