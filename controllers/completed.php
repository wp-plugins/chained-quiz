<?php
class ChainedQuizCompleted {
	static function manage() {
		global $wpdb;
		
		// select quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $_GET['quiz_id']));
		$ob = empty($_GET['ob']) ? 'tC.id' : $_GET['ob'];
		$dir = empty($_GET['dir'])  ? 'desc' : $_GET['dir'];
		
		// select completed records, paginate by 50
		$offset = empty($_GET['offset']) ? 0 : $_GET['offset'];
		$records = $wpdb->get_results( $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS tC.*, tU.user_nicename as user_nicename, tR.title as result_title
			FROM ".CHAINED_COMPLETED." tC LEFT JOIN ".CHAINED_RESULTS." tR ON tR.id = tC.result_id
			LEFT JOIN {$wpdb->users} tU ON tU.ID = tC.user_id
			WHERE tC.quiz_id=%d
			ORDER BY $ob $dir LIMIT $offset, 25", $quiz->id));
			
		$count = $wpdb->get_var("SELECT FOUND_ROWS()"); 	
		
		// select all the given answers in these records
		$rids = array(0);
		foreach($records as $record) $rids[] = $record->id;		
		$answers = $wpdb->get_results( "SELECT tA.answer as answer, tA.points as points, tQ.question as question,
			tA.completion_id as completion_id 
			FROM ".CHAINED_USER_ANSWERS." tA JOIN ".CHAINED_QUESTIONS." tQ
			ON tQ.id = tA.question_id
			WHERE tA.completion_id IN (" .implode(',', $rids). ") ORDER BY tA.id" ); 
			
		// now for the answers we need to match the textual values of what the user has answered
		$aids = array(0);
		foreach($answers as $answer) {
			$ids = explode(',', $answer->answer);
			
			foreach($ids as $id) {
				if(!empty($id) and !in_array($id, $aids)) $aids[] = $id;
			}
		}	
		
		$choices = $wpdb->get_results("SELECT id, choice FROM ".CHAINED_CHOICES." WHERE id IN (" . implode(',', $aids) . ")");
		
		// now do the match
		foreach($answers as $cnt => $answer) {
			$ids = explode(',', $answer->answer);
			$answer_text = '';
			
			foreach($ids as $id) {
				foreach($choices as $choice) {
					if($choice->id == $id) {
						if(!empty($answer_text)) $answer_text .= ", ";
						$answer_text .= stripslashes($choice->choice);
					}
				} // end foreach choice
			} // end foreach id
			
			$answers[$cnt]->answer_text = $answer_text;
		} // end foreach answer
		
		// now match the answers to records
		foreach($records as $cnt=>$record) {
			$record_answers = array();
			
			foreach($answers as $answer) {
				if($record->id == $answer->completion_id) $record_answers[] = $answer;
			}
			
			$records[$cnt] -> details = $record_answers;
		}
		
		$dateformat = get_option('date_format');
		$timeformat = get_option('time_format');
			
		include(CHAINED_PATH."/views/completed.html.php");
	} // end manage
	
	// defines whether to sort by ASC or DESC
	static function define_dir($col, $ob, $dir) {		
		if($ob != $col) return $dir;
		
		// else reverse
		if($dir == 'asc') return 'desc';
		else return 'asc'; 
	}
}