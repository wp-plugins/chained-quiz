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
			ORDER BY $ob $dir LIMIT $offset, 50", $quiz->id));
			
		$count = $wpdb->get_var("SELECT FOUND_ROWS()"); 	
		
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