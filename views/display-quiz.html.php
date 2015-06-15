<?php if(!empty($first_load)):?><div class="chained-quiz" id="chained-quiz-div-<?php echo $quiz->id?>"><?php endif;?>
<form method="post" id="chained-quiz-form-<?php echo $quiz->id?>">
	<div class="chained-quiz-area" id="chained-quiz-wrap-<?php echo $quiz->id?>">
		<?php if(!empty($quiz->email_user) and !is_user_logged_in()):?>
			<div class="chained-quiz-email">
				<p><label><?php _e('Your email address:', 'chained');?></label> <input type="text" name="chained_email" value="<?php echo @$_POST['chained_email']?>"></p>
			</div>
		<?php endif;?> 
		<div class="chained-quiz-question">
			<?php echo $_question->display_question($question);?>
		</div>
		
		<div class="chained-quiz-choices">
				<?php echo $_question->display_choices($question, $choices);?>
		</div>
		
		<div class="chained-quiz-action">
			<input type="button" id="chained-quiz-action-<?php echo $quiz->id?>" value="<?php _e('Go Ahead', 'chained')?>" onclick="chainedQuiz.goon(<?php echo $quiz->id?>, '<?php echo admin_url('admin-ajax.php')?>');" disabled="true">
		</div>
	</div>
	<input type="hidden" name="question_id" value="<?php echo $question->id?>">
	<input type="hidden" name="quiz_id" value="<?php echo $quiz->id?>">
	<input type="hidden" name="question_type" value="<?php echo $question->qtype?>">
	<input type="hidden" name="points" value="0">
</form>
<?php if(!empty($first_load)):?>
</div>
<script type="text/javascript" >
jQuery(function(){
	chainedQuiz.initializeQuestion(<?php echo $quiz->id?>);	
});
</script><?php endif;?>