<?php

include_once('Includes/all_includes.php');

/*
 * Testing and storing received data
 */

// Reset bot
if(isset($_GET['reset']) && $_GET['reset'] == 'true'){
    unlink(DATA_SAVED);
}

// Ajout d'un mot au dictionnaire
if(isset($_GET['add_words'])){
    $words_to_add = explode(' ', $_GET['add_words']);
    foreach($words_to_add as $word_to_add){
        file_put_contents(DATA_KNOWN_WORDS, $word_to_add . PHP_EOL, FILE_APPEND);
    }
}

// Message de l'utilisateur et option "use_spellchecker"
$use_spellchecker = (isset($_GET['use_spellchecker']) && $_GET['use_spellchecker'] === 'true') ? true : false;
$message = (isset($_GET['message'])) ? $_GET['message'] : '';

// Initialisation ou chargement de la discussion
$discussion = new Discussion();
$discussion->load();

////////////////////////////////////////////////////////////////////////////////////////////////////////
// Analyseur de message (TokenTalk)
$message_analyzer = new MessageAnalyzer($message, $use_spellchecker);

// Objet trouvant une réponse à partir de la discussion courante ainsi que de l'analyseur de message
$answerer = new Answerer($discussion, $message_analyzer);
echo 'answer:'.$answerer->getAnswer().'#'.$message_analyzer;
////////////////////////////////////////////////////////////////////////////////////////////////////////

// Sauvegarde de la discussion en cours
$discussion->save();

?>
