<?php
class ChainedQuizQuizzes {
	static function manage() {
 		$action = empty($_GET['action']) ? 'list' : $_GET['action']; 
		switch($action) {
			case 'add':
				self :: add_quiz();
			break;
			case 'edit': 
				self :: edit_quiz();
			break;
			case 'list':
			default:
				self :: list_quizzes();	 
			break;
		}
	} // end manage()
	
	static function add_quiz() {
		$_quiz = new ChainedQuizQuiz();
		
		if(!empty($_POST['ok'])) {
			try {
				$qid = $_quiz->add($_POST);			
				chained_redirect("admin.php?page=chainedquiz_results&quiz_id=".$qid);
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		$output = __('Congratulations, you completed the <span>quiz!</span>
<h2>{{result-title}}</h2>
{{result-text}}

You achieved {{points}} points from {{questions}} questions.', 'chained');
		include(CHAINED_PATH.'/views/quiz.html.php');
	} // end add_quiz
	
	static function edit_quiz() {
		global $wpdb;
		$_quiz = new ChainedQuizQuiz();
		
		if(!empty($_POST['ok'])) {
			try {
				$_quiz->save($_POST, $_GET['id']);			
				chained_redirect("admin.php?page=chained_quizzes");
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		// select the quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $_GET['id']));
	   $output = stripslashes($quiz->output); 
		include(CHAINED_PATH.'/views/quiz.html.php');
	} // end edit_quiz
	
	// list and delete quizzes
	static function list_quizzes() {
		global $wpdb;
		$_quiz = new ChainedQuizQuiz();
		
		if(!empty($_GET['del'])) {
			$_quiz->delete($_GET['id']);
			chained_redirect("admin.php?page=chained_quizzes");
		}
		
		// select quizzes
		$quizzes = $wpdb->get_results("SELECT tQ.*, COUNT(tC.id) as submissions 
			FROM ".CHAINED_QUIZZES." tQ LEFT JOIN ".CHAINED_COMPLETED." tC ON tC.quiz_id = tQ.id AND tC.not_empty=1
			GROUP BY tQ.id ORDER BY tQ.id DESC");
		
		// now select all posts that have watu shortcode in them
		$posts=$wpdb->get_results("SELECT * FROM {$wpdb->posts} 
		WHERE post_content LIKE '%[chained-quiz %]%' AND post_title!=''
		AND post_status='publish' ORDER BY post_date DESC");	
		
		// match posts to exams
		foreach($quizzes as $cnt=>$quiz) {
			foreach($posts as $post) {
				if(strstr($post->post_content,"[chained-quiz ".$quiz->id."]")) {
					$quizzes[$cnt]->post=$post;			
					break;
				}
			}
		}
		include(CHAINED_PATH."/views/chained-quizzes.html.php");
	} // end list_quizzes	
	
	// displays a quiz
	static function display($quiz_id) {
	   global $wpdb, $user_ID;
	   $_question = new ChainedQuizQuestion();
	   
	   // select the quiz
	   $quiz = $wpdb -> get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $quiz_id));
	   if(empty($quiz->id)) die(__('Quiz not found', 'chained'));
	   
	   // completion ID already created?
		if(empty($_SESSION['chained_completion_id'])) {			
			$wpdb->query( $wpdb->prepare("INSERT INTO ".CHAINED_COMPLETED." SET
		 		quiz_id = %d, datetime = NOW(), ip = %s, user_id = %d",
		 		$quiz->id, $_SERVER['REMOTE_ADDR'], $user_ID));
		 	$_SESSION['chained_completion_id'] = $wpdb->insert_id;	
		}
	   
		 // select the first question
		 $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d
		 	ORDER BY sort_order, id LIMIT 1", $quiz->id));
		 
		 // select possible answers
		 $choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." 
		 	WHERE quiz_id=%d AND question_id=%d ORDER BY id", $quiz->id, $question->id));
		 			 	
		 $first_load = true;			 	
		 include(CHAINED_PATH."/views/display-quiz.html.php");
	}

	// answer a question or complete the quiz
	static function answer_question() {
		global $wpdb, $user_ID;
		$_quiz = new ChainedQuizQuiz();
		$_question = new ChainedQuizQuestion();
		
		// select quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $_POST['quiz_id']));
		
		// select question
		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE id=%d", $_POST['question_id']));
		
		// prepare $answer var		
		$answer = ($question->qtype == 'checkbox') ? @$_POST['answers'] : @$_POST['answer'];
		if(empty($answer)) $answer = 0;
				
		// calculate points
		$points = $_question->calculate_points($question, $answer);
		echo $points."|CHAINEDQUIZ|";
		
		// figure out next question
		$next_question = $_question->next($question, $answer);
		
		// store the answer
		if(!empty($_SESSION['chained_completion_id'])) {
			if(is_array($answer)) $answer = implode(",", $answer);

			// make sure to avoid duplicates and only update the answer if it already exists
			$exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".CHAINED_USER_ANSWERS."
				WHERE quiz_id=%d AND completion_id=%d AND question_id=%d", 
				$quiz->id, $_SESSION['chained_completion_id'], $question->id));			
			
			if($exists) {
				$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_USER_ANSWERS." SET
					answer=%s, points=%f WHERE quiz_id=%d AND completion_id=%d AND question_id=%d", 
					$answer, $points, $quiz->id, $_SESSION['chained_completion_id'], $question->id));
			}
			else {				
				$wpdb->query($wpdb->prepare("INSERT INTO ".CHAINED_USER_ANSWERS." SET
					quiz_id=%d, completion_id=%d, question_id=%d, answer=%s, points=%f",
					$quiz->id, $_SESSION['chained_completion_id'], $question->id, $answer, $points));
			}		
			
			// update the "completed" record as non empty
			$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_COMPLETED." SET not_empty=1 WHERE id=%d", $_SESSION['chained_completion_id']));
		}
		
		if(!empty($next_question->id)) {
			$question = $next_question;
			$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." 
		 	WHERE quiz_id=%d AND question_id=%d ORDER BY id", $quiz->id, $question->id));
			include(CHAINED_PATH."/views/display-quiz.html.php");
		}
		else {
			 // add to points
			 $points += $_POST['points'];
			 echo $_quiz->finalize($quiz, $points); // if none, submit the quiz
		}	 		
	}
}