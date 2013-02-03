<!DOCTYPE HTML>
<html>

<head>
	<meta charset="UTF-8"/>
	<title>DialogBot v1.0</title>
	<script src="JS/jquery.js"></script>
	<!--<meta http-equiv="refresh" content="0.3">-->
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
</head>

<body>

	<h1>DialogBot v1.0</h1>
	<form action="index.php" method="post">
		<div class="field">
			 <textarea cols="80" id="textarea_chat" rows="14"></textarea>
		</div>
		<div class="field">
			 <input id="input_message" type="text"/>
			 <input id="input_submit" type="submit" value="Send"/>
		</div>
		<fieldset>
		<legend>Options</legend>
				<div class="field">
						<input id="input_use_spellchecker" type="checkbox" value="use_spellchecker">
						<label for="input_use_spellchecker">Use spellchecker</label>
				</div>
				<div class="field">
						<input id="input_display_verbs" type="checkbox" value="display_verbs"/>
						<label for="input_display_verbs">Display verbs data</label>
				</div>
				<div class="field">
						<input id="input_display_subjects" type="checkbox" value="display_subjects"/>
						<label for="input_display_subjects">Display subjects data</label>
				</div>
				<div class="field">
						<input id="input_display_questions" type="checkbox" value="display_question"/>
						<label for="input_display_questions">Display questions data</label>
				</div>
				<div class="field">
						<input id="input_display_negation" type="checkbox" value="display_negation"/>
						<label for="input_display_negation">Display negations data</label>
				</div>
		</fieldset>
		<input id="input_clear" type="button" value="Clear chat"/>
		<input id="input_reset_bot" type="button" value="Reset bot"/>
	</form>

	 <script src="JS/script.js" type="text/javascript"></script>

</body>

</html>
