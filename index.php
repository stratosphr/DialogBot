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

	<h1>DialogBot</h1>
	<form action="index.php" method="post">
		<label for="input_message">Enter your message here :</label>
		<input id="input_message" type="text"/>
		<input id="input_submit" type="submit" value="Send"/>
		<textarea cols="80" id="textarea_chat" rows="14"></textarea>
		<input id="input_clear" type="button" value="Clear chat"/>
		<input id="input_reset_bot" type="button" value="Reset bot"/>
	</form>

	 <script src="JS/script.js" type="text/javascript"></script>

</body>

</html>
