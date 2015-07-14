<div class="wrap">
	<h1><?php printf(__('Add/Edit Question in "%s"', 'chained'), $quiz->title)?></h1>
	
	<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to quizzes', 'chained')?></a> | <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Back to questions', 'chained')?></a>
		| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results', 'chained')?></a>
		| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Quiz', 'chained')?></a>
	</p>
	
	<form method="post" onsubmit="return chainedQuizValidate(this);">
		<p><label><?php _e('Question title', 'chained')?></label> <input type="text" name="title" size="40" value="<?php echo @$question->title?>"></p>
		<p><label><?php _e('Question contents', 'chained')?></label> <?php echo wp_editor(stripslashes(@$question->question), 'question')?></p>
		<p><label><?php _e('Question type:', 'chained')?></label> <select name="qtype" onchange="this.value == 'radio' ? jQuery('#chainedAutoContinue').show() : jQuery('#chainedAutoContinue').hide();">
			<option value="radio" <?php if(!empty($question->id) and $question->qtype == 'radio') echo 'selected'?>><?php _e('Radio buttons (one possible answer)','chained')?></option>
			<option value="checkbox" <?php if(!empty($question->id) and $question->qtype == 'checkbox') echo 'selected'?>><?php _e('Checkboxes (multiple possible answers)','chained')?></option>
			<option value="text" <?php if(!empty($question->id) and $question->qtype == 'text') echo 'selected'?>><?php _e('Text box (open-end, essay question)','chained')?></option>
		</select>
		
		<span id="chainedAutoContinue" style="display:<?php echo (empty($question->id) or $question->qtype == 'radio') ? 'inline' : 'none';?>"><input type="checkbox" name="autocontinue" value="1" <?php if(!empty($question->autocontinue)) echo 'checked'?>> <?php _e('Automatically continue to the next question when a choice is selected', 'chained')?></span> </p>
		
		<h3><?php _e('Choices/Answers for this question', 'chained')?></h3>
		
		<p> <input type="button" value="<?php _e('Add more rows', 'chained')?>" onclick="chainedQuizAddChoice();" class="button"></p>
		
		<div id="answerRows">
			<?php if(!empty($choices) and sizeof($choices)):
				foreach($choices as $choice):
					include(CHAINED_PATH."/views/choice.html.php");
				endforeach;
			endif;
			unset($choice);
			include(CHAINED_PATH."/views/choice.html.php");?>
		</div>
		
		<p><input type="submit" value="<?php _e('Save question and answers','chained')?>" class="button-primary"></p>
		<input type="hidden" name="ok" value="1">
		<input type="hidden" name="quiz_id" value="<?php echo $quiz->id?>">
	</form>
</div>

<script type="text/javascript" >
var numChoices = 1;
function chainedQuizAddChoice() {
	html = '<?php ob_start();
	include(CHAINED_PATH."/views/choice.html.php");
	$content = ob_get_clean();	
	$content = str_replace("\n", '', $content);
	echo $content; ?>';
	
	// the correct checkbox value
	numChoices++;
	html = html.replace('name="is_correct[]" value="1"', 'name="is_correct[]" value="'+numChoices+'"');
	
	jQuery('#answerRows').append(html);
}

function chainedQuizValidate(frm) {
	if(frm.title.value == '') {
		alert("<?php _e('Please enter question title', 'chained')?>");
		frm.title.focus();
		return false;
	}
	
	return true;
}
</script>