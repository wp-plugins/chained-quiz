<?php
// main model containing general config and UI functions
class ChainedQuiz {
   static function install($update = false) {
   	global $wpdb;	
   	$wpdb -> show_errors();
   	
   	if(!$update) self::init();
	  
	   // quizzes
   	if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_QUIZZES."'") != CHAINED_QUIZZES) {        
			$sql = "CREATE TABLE `" . CHAINED_QUIZZES . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `title` VARCHAR(255) NOT NULL DEFAULT '',
				  `output` TEXT				  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  // questions
   	if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_QUESTIONS."'") != CHAINED_QUESTIONS) {        
			$sql = "CREATE TABLE `" . CHAINED_QUESTIONS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `title` VARCHAR(255) NOT NULL DEFAULT '',
				  `question` TEXT,
				  `qtype` VARCHAR(20) NOT NULL DEFAULT '',
				  `rank` INT UNSIGNED NOT NULL DEFAULT 0			  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  } 
	  
	  // choices
     if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_CHOICES."'") != CHAINED_CHOICES) {        
			$sql = "CREATE TABLE `" . CHAINED_CHOICES . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `question_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `choice` TEXT,
				  `points` DECIMAL(4,2) NOT NULL DEFAULT '0.00',
				  `is_correct` TINYINT UNSIGNED NOT NULL DEFAULT 0,
				  `goto` VARCHAR(100) NOT NULL DEFAULT 'next'
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  } 
	  
	  // results
	  if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_RESULTS."'") != CHAINED_RESULTS) {        
			$sql = "CREATE TABLE `" . CHAINED_RESULTS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `points_bottom` DECIMAL(4,2) NOT NULL DEFAULT '0.00',
				  `points_top` DECIMAL(4,2) NOT NULL DEFAULT '0.00',
				  `title` VARCHAR(255) NOT NULL DEFAULT '',
				  `description` TEXT 
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  } 
	  
	  // completed quizzes	
	  if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_COMPLETED."'") != CHAINED_COMPLETED) {        
			$sql = "CREATE TABLE `" . CHAINED_COMPLETED . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `points` DECIMAL(4,2) NOT NULL DEFAULT '0.00',
				  `result_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `datetime` DATETIME,
				  `ip` VARCHAR(20) NOT NULL DEFAULT '',
				  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `snapshot` TEXT
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  } 	 
	  
	  // details of user answers
	  if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_USER_ANSWERS."'") != CHAINED_USER_ANSWERS) {        
			$sql = "CREATE TABLE `" . CHAINED_USER_ANSWERS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `completion_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `question_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `answer` TEXT,
				  `points` DECIMAL(4,2) NOT NULL DEFAULT '0.00'				  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  } 	 
	  
	  
	  chainedquiz_add_db_fields(array(
	  	  array("name" => 'autocontinue', 'type' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0')
	  ), CHAINED_QUESTIONS);
	  
	  update_option('chainedquiz_version', "0.59");
	  // exit;
   }
   
   // main menu
   static function menu() {
   	add_menu_page(__('Chained Quiz', 'chained'), __('Chained Quiz', 'chained'), "manage_options", "chained_quizzes", 
   		array('ChainedQuizQuizzes', "manage"));
   		
   	add_submenu_page(NULL, __('Chained Quiz Results', 'chained'), __('Chained Quiz Results', 'chained'), 'manage_options', 
   		'chainedquiz_results', array('ChainedQuizResults','manage'));	
   	add_submenu_page(NULL, __('Chained Quiz Questions', 'chained'), __('Chained Quiz Questions', 'chained'), 'manage_options', 
   		'chainedquiz_questions', array('ChainedQuizQuestions','manage'));	
   	add_submenu_page(NULL, __('Users Completed Quiz', 'chained'), __('Users Completed Quiz', 'chained'), 'manage_options', 
   		'chainedquiz_list', array('ChainedQuizCompleted','manage'));		
	}
	
	// CSS and JS
	static function scripts() {
		// CSS
		wp_register_style( 'chained-css', CHAINED_URL.'css/main.css?v=1');
	  wp_enqueue_style( 'chained-css' );
   
   	wp_enqueue_script('jquery');
	   
	   // Chained quiz's own Javascript
		wp_register_script(
				'chained-common',
				CHAINED_URL.'js/common.js',
				false,
				'0.1.0',
				false
		);
		wp_enqueue_script("chained-common");
		
		$translation_array = array('please_answer' => __('Please answer the question', 'chained'));
		wp_localize_script( 'chained-common', 'chained_i18n', $translation_array );	
	}
	
	// initialization
	static function init() {
		global $wpdb;
		load_plugin_textdomain( 'chained', false, CHAINED_RELATIVE_PATH."/languages/" );
		if (!session_id()) @session_start();
		
		// define table names 
		define( 'CHAINED_QUIZZES', $wpdb->prefix. "chained_quizzes");
		define( 'CHAINED_QUESTIONS', $wpdb->prefix. "chained_questions");
		define( 'CHAINED_CHOICES', $wpdb->prefix. "chained_choices");
		define( 'CHAINED_RESULTS', $wpdb->prefix. "chained_results");
		define( 'CHAINED_COMPLETED', $wpdb->prefix. "chained_completed");
		define( 'CHAINED_USER_ANSWERS', $wpdb->prefix. "chained_user_answers");
		
		define( 'CHAINED_VERSION', get_option('chained_version'));
				
		// shortcodes
		add_shortcode('chained-quiz', array("ChainedQuizShortcodes", "quiz"));		
		
		$version = get_option('chainedquiz_version');
		if($version < '0.59') self::install(true);
	}
			
	// manage general options
	static function options() {
		if(!empty($_POST['ok'])) {
			update_option('wphostel_currency', $_POST['currency']);
			update_option('wphostel_booking_mode', $_POST['booking_mode']);
			update_option('wphostel_email_options', array("do_email_admin"=>@$_POST['do_email_admin'], 
				"admin_email"=>$_POST['admin_email'], "do_email_user"=>@$_POST['do_email_user'], 
				"email_admin_subject"=>$_POST['email_admin_subject'], "email_admin_message"=>$_POST['email_admin_message'],
				"email_user_subject"=>$_POST['email_user_subject'], "email_user_message"=>$_POST['email_user_message']));
			update_option('wphostel_paypal', $_POST['paypal']);
			update_option('wphostel_booking_url', $_POST['booking_url']);		
		}		
		
		$currency = get_option('wphostel_currency');
		$currencies=array('USD'=>'$', "EUR"=>"&euro;", "GBP"=>"&pound;", "JPY"=>"&yen;", "AUD"=>"AUD",
		   "CAD"=>"CAD", "CHF"=>"CHF", "CZK"=>"CZK", "DKK"=>"DKK", "HKD"=>"HKD", "HUF"=>"HUF",
		   "ILS"=>"ILS", "MXN"=>"MXN", "NOK"=>"NOK", "NZD"=>"NZD", "PLN"=>"PLN", "SEK"=>"SEK",
		   "SGD"=>"SGD");
		   
		$booking_mode = get_option('wphostel_booking_mode');   
		$email_options = get_option('wphostel_email_options');
		$paypal = get_option('wphostel_paypal');
		   	
		require(CHAINED_PATH."/views/options.php");
	}	
	
	static function help() {
		require(CHAINED_PATH."/views/help.php");
	}	
}