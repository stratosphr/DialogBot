function refreshChat(list_messages){
	 var chat = $("#textarea_chat");
	 chat.val("");
	 for(var i=0; i<list_messages.length; i++){
		  if(i == 0) chat.val("You : " + list_messages[i]);
		  else if(i % 2 == 0) chat.val(chat.val() + "\n\nYou : " + list_messages[i]);
		  else chat.val(chat.val() + "\n\nBot : " + list_messages[i]);
	 }
}

if(typeof String.prototype.startsWith != 'function'){
	 String.prototype.startsWith = function(str){
		  return this.lastIndexOf(str, 0) === 0;
	 }
}

$(document).ready(function(){

	 var list_messages = new Array();

	 $("#input_message").focus();

	 $("#input_submit").click(function(event){
		  event.preventDefault();

		  var chat = $("#textarea_chat");
		  var new_message = $("#input_message").val().replace(/^[ ]+/, "").replace(/[ ][ ]+/g, " ").replace(/[ ]*$/, "");
		  var use_spellchecker = $("#input_use_spellchecker").is(':checked');
		  var display_verbs = $("#input_display_verbs").is(':checked');
		  var display_subjects = $("#input_display_subjects").is(':checked');
		  var display_questions = $("#input_display_questions").is(':checked');
		  var display_negations = $("#input_display_negations").is(':checked');

		  if(new_message != ""){
			   list_messages.push(new_message);

			   $.get("PHP/functions.php", {
					message : new_message, 
					use_spellchecker : use_spellchecker, 
					display_verbs : display_verbs,
					display_subjects : display_subjects, 
					display_questions : display_questions, 
					display_negations : display_negations
			   }, function(data){
					var analyze = data.split("#");
					var answer;
					var verbs;
					var subjects;
					var questions;
					var negations;
					for(i=0; i<analyze.length; i++){
						 if(analyze[i].startsWith('answer')) answer = analyze[i].substring(7);
						 else if(analyze[i].startsWith('verbs')) verbs = analyze[i].substring(6);
						 else if(analyze[i].startsWith('subjects')) subjects = analyze[i].substring(9);
						 else if(analyze[i].startsWith('questions')) questions = analyze[i].substring(10);
						 else if(analyze[i].startsWith('negations')) negations = analyze[i].substring(10);
					}
					list_messages.push(answer + " # " + verbs + " # " + subjects + " # " + questions + " # " + negations);
			   }).done(function(){
					refreshChat(list_messages);
					chat.animate({scrollTop : chat[0].scrollHeight - chat.height()}, 300);
					$("#input_message").val("");
			   });

		  }
		  $("#input_message").focus();

	 });

	 $("#input_add_words").click(function(event){
		  event.preventDefault();
		  var clean_add_words = $("#input_add_words_to_dictionary").val().replace(/^[ ]+/, "").replace(/[ ][ ]+/g, " ").replace(/[ ]*$/, "");
		  $.get("PHP/functions.php", {
			   add_words : clean_add_words
		  });
		  $("#input_add_words_to_dictionary").val("");
		  $("#input_message").focus();
	 });

	 $("#fieldset_options").click(function(event){
		  if(event.target.id != 'input_add_words_to_dictionary') $("#input_message").focus();
	 });

	 $("#input_clear").click(function(event){
		  event.preventDefault();
		  list_messages.splice(0, list_messages.length);
		  refreshChat(list_messages);
		  $("#input_message").val("");
		  $("#input_message").focus();
	 });


	 $("#input_message").keydown(function(event){
		  if(event.which == 38){ // Up arrow was pressed
			   event.preventDefault();
			   if(list_messages.length > 0){
					var chat = $("#textarea_chat");
					last_message = list_messages[list_messages.length - 2];
					$("#input_message").val(last_message);
					list_messages.pop();
					list_messages.pop();
					refreshChat(list_messages);
			   }
		  }else if(event.altKey && event.which == 76){
			   event.preventDefault();
			   $("#input_clear").click();
		  }
	 });

});
