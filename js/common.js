chainedQuiz = {};
chainedQuiz.points = 0; // initialize the points as 0
chainedQuiz.questions_answered = 0;

chainedQuiz.goon = function(quizID, url) {
	// submit the answer by ajax
	data = jQuery('#chained-quiz-form-'+quizID).serialize();
	data += '&action=chainedquiz_ajax';
	data += '&chainedquiz_action=answer';
	this.questions_answered++;
	data += '&total_questions=' + this.questions_answered;
	
	// console.log(data);
	jQuery.post(url, data, function(msg) {
		  parts = msg.split("|CHAINEDQUIZ|");
		  points = parseFloat(parts[0]);		  
		  chainedQuiz.points += points;		  
		  jQuery('#chained-quiz-div-'+quizID).html(parts[1]);
		  jQuery('#chained-quiz-form-' + quizID + ' input[name=points]').val(chainedQuiz.points);
	});
}