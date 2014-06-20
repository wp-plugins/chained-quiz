<div class="wrap">
	<h1><?php printf(__('Users who submitted quiz "%s"', 'chained'), $quiz->title)?></h1>
	<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to quizzes', 'chained')?></a> | <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage questions', 'chained')?></a>
		| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results', 'chained')?></a>
		| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Quiz', 'chained')?></a>
	</p>
	
	<?php if(sizeof($records)):?>
		<table class="widefat">
			<tr><th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=tC.id&dir=<?php echo self :: define_dir('tC.id', $ob, $dir);?>"><?php _e('Record ID','chained')?></a></th><th><?php _e('User name or IP','chained')?></th><th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=datetime&dir=<?php echo self :: define_dir('datetime', $ob, $dir);?>"><?php _e('Date/time','chained')?></a></th>
			<th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=points&dir=<?php echo self :: define_dir('points', $ob, $dir);?>"><?php _e('Points','chained')?></a></th><th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=result_title&dir=<?php echo self :: define_dir('result_title', $ob, $dir);?>"><?php _e('Result','chained')?></a></th></tr>
			<?php foreach($records as $record):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>">
				<td><?php echo $record->id?></td>
				<td><?php echo empty($record->user_id) ? $record->ip : $record->user_nicename?></td>
				<td><?php echo date($dateformat.' '.$timeformat, strtotime($record->datetime))?></td>
				<td><?php echo $record->points?></td><td><?php echo $record->result_title?></td></tr>
			<?php endforeach;?>
		</table>
		
		<p align="center"><?php if($offset > 0):?>
			<a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&offset=<?php echo ($offset - 50)?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>"><?php _e('previous page', 'chained')?></a>
		<?php endif;?> <?php if($count > ($offset + 50)):?>
			<a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&offset=<?php echo ($offset + 50)?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>"><?php _e('next page', 'chained')?></a> <?php endif;?></p>
	<?php else:?>
		<p><?php _e('No one has submitted this quiz yet.', 'chained')?></p>
	<?php endif;?>
</div>