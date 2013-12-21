<p><textarea rows="3" cols="40" name="<?php echo empty($choice->id)?'answers[]':'answer'.$choice->id?>"><?php echo stripslashes(@$choice->choice)?></textarea> <?php _e('Points:', 'chained')?> <input type="text" size="4" name="<?php echo empty($choice->id)?'points[]':'points'.$choice->id?>" value="<?php echo @$choice->points?>"> <input type="checkbox" name="<?php echo empty($choice->id)?'is_correct[]':'is_correct'.$choice->id?>" value="1" <?php if(!empty($choice->is_correct)) echo 'checked'?>> <?php _e('Correct answer','chained')?> | <?php _e('When selected go to:', 'chained')?> <select name="<?php echo empty($choice->id)?'goto[]':'goto'.$choice->id?>">
				<option value="next"><?php _e('Next question','chained')?></option>
				<option value="finalize" <?php if(!empty($choice->goto) and $choice->goto =='finalize') echo 'selected'?>><?php _e('Finalize quiz','chained')?></option>
				<?php if(sizeof($other_questions)):?>
					<option disabled><?php _e('- Select question -', 'chained')?></option>
					<?php foreach($other_questions as $other_question):?>
						<option value="<?php echo $other_question->id?>" <?php if(!empty($choice->id) and $choice->goto == $other_question->id) echo 'selected'?>><?php echo $other_question->title?></option>
					<?php endforeach;?>
				<?php endif;?>
</select> 
<?php if(!empty($choice->id)):?>
	<input type="checkbox" name="dels[]" value="<?php echo $choice->id?>"> <?php _e('Delete this choice', 'chained')?>
<?php endif;?></p>