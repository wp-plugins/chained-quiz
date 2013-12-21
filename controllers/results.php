<?php
// results or "grades"
class ChainedQuizResults {
	static function manage() {
		global $wpdb;
 		$_result = new ChainedQuizResult();
		
 		// select quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $_GET['quiz_id']));
 		
 		if(!empty($_POST['add'])) {
 			try {
 				$_POST['quiz_id'] = $quiz->id;
 				$_result->add($_POST);
 				chained_redirect("admin.php?page=chainedquiz_results&quiz_id=".$quiz->id);
 			}
 			catch(Exception $e) {
 				$error = __('The result was not added', 'chained');
 			}
 		}
 		
 		if(!empty($_POST['save'])) {
 			try {
 				$_POST['description'] = $_POST['description'.$_POST['id']];
 				$_result->save($_POST, $_POST['id']);
 			}
 			catch(Exception $e) {
 				$error = __('The result was not saved', 'chained');
 			}
 		}
 		
 		if(!empty($_POST['del'])) {
 			try {
 				$_result->delete($_POST['id']);
 			}
 			catch(Exception $e) {
 				$error = __('The result was not deleted', 'chained');
 			}
 		}
 		
 		// select results
 		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS." WHERE quiz_id=%d ORDER BY id", $quiz->id));
 		include(CHAINED_PATH."/views/results.html.php");
	} // end manage()
}