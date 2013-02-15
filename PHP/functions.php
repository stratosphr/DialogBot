<meta http-equiv="refresh" content="0.2">
<meta charset="UTF-8"/>
<?php

include_once('Includes/all_includes.php');

/*
 * Testing and storing received data
*/
$use_spellchecker = (isset($_GET['use_spellchecker']) && $_GET['use_spellchecker'] === 'true') ? true : false;
$message = (isset($_GET['message'])) ? $_GET['message'] : '';

$m = new MessageAnalyzer($message, $use_spellchecker);
echo str_replace('#', '#<br />', $m);

?>
