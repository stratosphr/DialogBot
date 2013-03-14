<?php

define('DATA_ADJECTIVES', 'Data/adjectives.dat');
define('DATA_CONJUGATED_VERBS', 'Data/conjugated_verbs.dat');
define('DATA_DETERMINERS', 'Data/determiners.dat');
define('DATA_FIRSTNAMES', 'Data/firstnames.dat');
define('DATA_INTERROGATIVE_WORDS', 'Data/interrogative_words.dat');
define('DATA_KNOWN_WORDS', 'Data/known_words.dat');
define('DATA_NEGATIVE_WORDS', 'Data/negative_words.dat');
define('DATA_PATTERNS', 'Data/patterns.dat');
define('DATA_SAVED', 'Data/serialized_objects.dat');
define('DATA_SUBJECTS', 'Data/subjects.dat');
define('DATA_GENERAL_ANSWERS', 'Data/general_answers.dat');

define('ERROR_MESSAGE_NOT_STRING', '__message__');

function startsWith($search, $str){
	 return !strncmp($search, $str, strlen($search));
}

function endsWith($search, $str){
	 return substr($str, -strlen($search)) === $search;
}

function stripAccents($str){
	 return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $str); 
}

function toLower($str){
	 $convert_from = array('À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý');
	 $convert_to = array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý');
	 return str_replace($convert_from, $convert_to, strtolower($str));
}

?>
