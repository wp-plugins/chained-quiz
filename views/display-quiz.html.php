<?php if(!empty($first_load)):?><div class="chained-quiz" id="chained-quiz-div-<?php echo $quiz->id?>"><?php endif;?>
<form method="post" id="chained-quiz-form-<?php echo $quiz->id?>">
	<div class="chained-quiz-area">
		<div class="chained-quiz-question">
			<?php echo $_question->display_question($question);?>
		</div>
		
		<div class="chained-quiz-choices">
				<?php echo $_question->display_choices($question, $choices);?>
		</div>
		
		<div class="chained-quiz-action">
			<input type="button" value="<?php _e('Go Ahead', 'chained')?>" onclick="chainedQuiz.goon(<?php echo $quiz->id?>, '<?php echo admin_url('admin-ajax.php')?>');">
		</div>
	</div>
	<input type="hidden" name="question_id" value="<?php echo $question->id?>">
	<input type="hidden" name="quiz_id" value="<?php echo $quiz->id?>">
	<input type="hidden" name="question_type" value="<?php echo $question->qtype?>">
	<input type="hidden" name="points" value="0">
</form>
<?php if(!empty($first_load)):?></div><?php endif;?>