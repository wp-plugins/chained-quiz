<div class="wrap">
	<h1><?php printf(__('Manage Questions in %s', 'chained'), $quiz->title);?> </h1>
	
	<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to quizzes', 'chained')?></a>
		| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results', 'chained')?></a>
		| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Quiz', 'chained')?></a>
	</p>
	
	<p><a href="admin.php?page=chainedquiz_questions&action=add&quiz_id=<?php echo $quiz->id?>"><?php _e('Click here to add new question', 'chained')?></a></p>
	<?php if(sizeof($questions)):?>
		<table class="widefat">
			<tr><th><?php _e('ID', 'chained')?></th><th><?php _e('Question', 'chained')?></th><th><?php _e('Type', 'chained')?></th>
				<th><?php _e('Edit / Delete', 'chained')?></th></tr>
			<?php foreach($questions as $question):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>"><td><?php echo $question->id?></td><td><?php echo stripslashes($question->title)?></td>
				<td><?php echo $question->qtype?></td><td><a href="admin.php?page=chainedquiz_questions&action=edit&id=<?php echo $question->id?>"><?php _e('Edit', 'chained')?></a> | <a href="#" onclick="chainedConfirmDelete(<?php echo $question->id?>);return false;"><?php _e('Delete', 'chained')?></a></td></tr>
			<?php endforeach;?>	
		</table>
	<?php endif;?>
</div>

<script type="text/javascript" >
function chainedConfirmDelete(qid) {
	if(confirm("<?php _e('Are you sure?', 'chained')?>")) {
		window.location = 'admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>&del=1&id='+qid;
	}
}
</script>