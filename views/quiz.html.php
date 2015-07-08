<div class="wrap">
	<h1><?php _e('Add/Edit Chained Quiz', 'chained')?></h1>
	
	<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to quizzes', 'chained')?></a>
	<?php if(!empty($quiz->id)):?>
		| <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Questions', 'chained')?></a>
		| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results / Outcomes', 'chained')?></a>
	<?php endif;?></p>
	
	<form method="post" onsubmit="return validateChainedQuiz(this);">
		<p><label><?php _e('Quiz Title', 'chained')?></label> <input type="text" name="title" size="60" value="<?php echo @$quiz->title?>"></p>
		
		<p><label><?php _e('Final Output', 'chained')?></label> <?php echo wp_editor($output, 'output')?></p>
		
		<p><?php _e('This is the content that is shown to the user after they complete the quiz. The following variables can be used:', 'chained')?></p>
		
		<ul>
			<li>{{result-title}} <?php _e('- The result (grade) title', 'chained')?></li>
			<li>{{result-text}} <?php _e('- The result (grade) text/description', 'chained')?></li>
			<li>{{points}} <?php _e('- Points collected', 'chained')?></li>
			<li>{{questions}} <?php _e('- The number of total questions answered', 'chained')?></li>
			<!-- (let's leave this for the next version) li>{{correct}} <?php _e('- The number of correctly answered questions', 'chained')?></li-->
		</ul>	
		
		<p><input type="checkbox" name="email_admin" value="1" <?php if(!empty($quiz->email_admin)) echo 'checked'?>> <?php _e('Send me email when user completes this quiz. It will be delivered to the email address from your main WP Settings page.', 'chained');?></p>
			<p><input type="checkbox" name="email_user" value="1" <?php if(!empty($quiz->email_user)) echo 'checked'?>> <?php _e('Send email to user with their result. If the user is not logged in visitor an optional "Enter email" field will automatically appear above the quiz.', 'chained');?></p>
		
		<p><input type="submit" value="<?php _e('Save Quiz', 'chained')?>" class="button-primary"></p>
		<input type="hidden" name="ok" value="1">
	</form>
</div>

<script type="text/javascript" >
function validateChainedQuiz(frm) {
	if(frm.title.value == '') {
		alert("<?php _e('Title is required', 'chained')?>");
		frm.title.focus();
		return false;
	}
	
	return true;
}
</script>