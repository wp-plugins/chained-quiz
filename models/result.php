<?php
class ChainedQuizResult {
	function add($vars) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".CHAINED_RESULTS." SET
			quiz_id=%d, points_bottom=%s, points_top=%s, title=%s, description=%s, redirect_url=%s", 
			$vars['quiz_id'], $vars['points_bottom'], $vars['points_top'], $vars['title'], 
			$vars['description'], $vars['redirect_url']));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return $wpdb->insert_id;	
	} // end add
	
	function save($vars, $id) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".CHAINED_RESULTS." SET
		 points_bottom=%s, points_top=%s, title=%s, description=%s, redirect_url=%s WHERE id=%d", 
		$vars['points_bottom'], $vars['points_top'], $vars['title'], $vars['description'], 
		$vars['redirect_url'], $id));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}
	
	function delete($id) {
		global $wpdb;
		
		// delete result
		$result =$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_RESULTS." WHERE id=%d", $id));
		
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}

	// calculate result based on points collected
	function calculate($quiz, $points) {
		global $wpdb;
		
		// select all results order by best
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS." 
			WHERE quiz_id = %d ORDER BY points_bottom DESC", $quiz->id));
		foreach($results as $result) {
			if($result->points_bottom <= $points and $points <= $result->points_top) return $result;
    }	
    
    return null; // in case of nothing found
	}
}