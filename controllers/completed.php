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
		$limit_sql = empty($_GET['chained_export']) ? "LIMIT $offset, 25" : ""; 
		
		if(!empty($_GET['del'])) {
			$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_COMPLETED." WHERE id=%d", $_GET['del']));
		}		
		
		if(!empty($_POST['cleanup_all'])) {
			$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_COMPLETED." WHERE quiz_id=%d", $quiz->id));
			chained_redirect("admin.php?page=chainedquiz_list&quiz_id=".$quiz->id);	 
		}
		
		$records = $wpdb->get_results( $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS tC.*, tU.user_nicename as user_nicename, tR.title as result_title
			FROM ".CHAINED_COMPLETED." tC LEFT JOIN ".CHAINED_RESULTS." tR ON tR.id = tC.result_id
			LEFT JOIN {$wpdb->users} tU ON tU.ID = tC.user_id
			WHERE tC.quiz_id=%d AND tC.not_empty=1
			ORDER BY $ob $dir $limit_sql", $quiz->id));
			
		$count = $wpdb->get_var("SELECT FOUND_ROWS()"); 	
		
		// select all the given answers in these records
		$rids = array(0);
		foreach($records as $record) $rids[] = $record->id;		
		$answers = $wpdb->get_results( "SELECT tA.answer as answer, tA.points as points, tQ.question as question,
			tA.completion_id as completion_id, tQ.qtype as qtype 
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
			
			if($answer->qtype == 'text') $answer_text = $answer->answer;
			else { 
				foreach($ids as $id) {
					foreach($choices as $choice) {
						if($choice->id == $id) {
							if(!empty($answer_text)) $answer_text .= ", ";
							$answer_text .= stripslashes($choice->choice);
						}
					} // end foreach choice
				} // end foreach id
			} // end if not textarea	
			
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
		
		if(!empty($_GET['chained_export'])) {
			$newline=kiboko_define_newline();		
			
			$csv = "";
			$rows=array();
			$rows[]=__("Record ID", 'chained')."\t".__("User name or IP", 'chained')."\t".
				__("Date / time", 'chained')."\t".__("Points", 'chained')."\t".__("Record ID", 'chained');
			foreach($records as $record) {
				$row = $record->id . "\t" . (empty($record->user_id) ? $record->ip : $record->user_nicename) 
					. "\t" . date_i18n($dateformat.' '.$timeformat, strtotime($record->datetime)) 
					. "\t" . $record->points ."\t" . stripslashes($record->result_title);
				$rows[] = $row;		
			} // end foreach taking
			$csv=implode($newline,$rows);		
			
			$now = gmdate('D, d M Y H:i:s') . ' GMT';	
			$filename = 'quiz-'.$quiz->id.'-results.csv';	
			header('Content-Type: ' . kiboko_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Pragma: no-cache');
			echo $csv;
			exit;
		}	
			
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