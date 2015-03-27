<?php
class ChainedQuizQuiz {
	function add($vars) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".CHAINED_QUIZZES." SET
			title=%s, output=%s", $vars['title'], $vars['output']));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return $wpdb->insert_id;	
	} // end add
	
	function save($vars, $id) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUIZZES." SET
			title=%s, output=%s WHERE id=%d", $vars['title'], $vars['output'], $id));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}
	
	function delete($id) {
		global $wpdb;
		
		// delete questions
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d", $id));
		
		// delete choices
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_CHOICES." WHERE quiz_id=%d", $id));
		
		// delete completed records
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_COMPLETED." WHERE quiz_id=%d", $id));
		
		// delete the quiz
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_QUIZZES." WHERE id=%d", $id));
	}

	function finalize($quiz, $points) {		
	    global $wpdb, $user_ID;
	    
	    $user_id = empty($user_ID) ? 0 : $user_ID;
	    
		 $_result = new ChainedQuizResult();
		 // calculate result
		 $result = $_result->calculate($quiz, $points);
		 
		 // get final screen and replace vars
		 $output = stripslashes($quiz->output);
		 $output = str_replace('{{result-title}}', @$result->title, $output);
		 $output = str_replace('{{result-text}}', stripslashes(@$result->description), $output);
		 $output = str_replace('{{points}}', $points, $output);
		 $output = str_replace('{{questions}}', $_POST['total_questions'], $output);
		 
		 $output = do_shortcode($output);
		 $output = wpautop($output);
		
		 // now insert in completed
		 if(!empty($_SESSION['chained_completion_id'])) {
		 	$wpdb->query( $wpdb->prepare("UPDATE ".CHAINED_COMPLETED." SET
		 		quiz_id = %d, points = %d, result_id = %d, datetime = NOW(), ip = %s, user_id = %d, 
		 		snapshot = %s WHERE id=%d",
		 		$quiz->id, $points, @$result->id, $_SERVER['REMOTE_ADDR'], $user_id, $output, $_SESSION['chained_completion_id']));
		 	unset($_SESSION['chained_completion_id']);	
		 }	 
		 else {
		 	// normally this shouldn't happen, but just in case
		 	$wpdb->query( $wpdb->prepare("INSERT INTO ".CHAINED_COMPLETED." SET
		 		quiz_id = %d, points = %d, result_id = %d, datetime = NOW(), ip = %s, user_id = %d, snapshot = %s",
		 		$quiz->id, $points, @$result->id, $_SERVER['REMOTE_ADDR'], $user_id, $output));
		 }
		 
		 // if the result needs to redirect, replace the output with the redirect URL
		 if(!empty($result->redirect_url)) $output = "[CHAINED_REDIRECT]".$result->redirect_url;
		 
		 return $output;
  }
}