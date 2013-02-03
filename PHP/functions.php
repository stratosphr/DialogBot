<?php

set_time_limit(0);

function isKnownVerb($verb){
	 if(is_string($verb)){
		  $known_verbs = file('Data/verbs.txt', FILE_IGNORE_NEW_LINES);
		  return in_array($verb, $known_verbs);
	 }
	 return false;
}

function isKnownWord($word){
	 if(is_string($word)){
		  $known_words = file('Data/known_words.txt', FILE_IGNORE_NEW_LINES);
		  return in_array($word, $known_words);
	 }
	 return false;
}

function isVerb($verb){
	 if(is_string($verb)){
		  $conjugations = file('Data/verbs_conjugations.txt');
		  foreach($conjugations as $conjugation){
			   $conjugated_verbs = explode(' ', $conjugation);
			   if(in_array($verb, $conjugated_verbs)) return true;
		  }
	 }
	 return false;
}

function getVerbTemplate($verb){
	 if(isKnownVerb($verb)){
		  $dom = new DomDocument();
		  $dom->load('Data/verbs_templates.xml');
		  $infinitives = $dom->getElementsByTagName('i');
		  $templates = $dom->getElementsByTagName('t');
		  foreach($infinitives as $key => $infinitive){
			   $infinitive = $infinitive->nodeValue;
			   if($infinitive == $verb) return $templates->item($key)->nodeValue;
		  }
	 }
	 return false; // Template was not found
}

function getVerbPrefix($verb){
	 if($template = getVerbTemplate($verb)){
		  $template_length = mb_strlen($template, 'UTF-8');
		  $prefix_length = mb_strpos($template, ':', 0, 'UTF-8') + 1;
		  $suffix_length = $template_length - $prefix_length;
		  return mb_substr($verb, 0, -$suffix_length, 'UTF-8');
	 }
	 return false; // Prefix was not found
}

function getVerbConjugation($verb){
	 $conjugations = file('Data/verbs_conjugations.txt', FILE_IGNORE_NEW_LINES);
	 foreach($conjugations as $conjugation){
		  $words = explode(' ', $conjugation);
		  $infinitive = $words[0];
		  if($infinitive == $verb) return $conjugation;
	 }
	 return false; // Conjugation was not found
}

function getVerbInfinitives($verb){
	 $infinitives = false;
	 if(is_string($verb)){
		  $conjugations = file('Data/verbs_conjugations.txt', FILE_IGNORE_NEW_LINES);
		  foreach($conjugations as $conjugation){
			   $words = explode(' ', $conjugation);
			   $exists = in_array($verb, $words);
			   if($exists === true) $infinitives[] = $words[0];
		  }
	 }
	 return $infinitives;
}

function getVERBS($message){
	 $words = explode(' ', $message);
	 $verbs = false;
	 $index = 0;
	 foreach($words as $word){
		  if($infinitives = getVerbInfinitives($word)){
			   foreach($infinitives as $infinitive){
					$verbs[$index][] = $infinitive;
					$verbs[$index][] = $word;
					++$index;
			   }
		  }
	 }
	 return $verbs;
}

function spellCheck($message){
	 $known_words = file('Data/known_words.txt', FILE_IGNORE_NEW_LINES);
	 $words = explode(' ', $message);
	 $check = "";
	 $correction = array();
	 $corrected = false;
	 $best_percentage = 0;
	 $best_correction = false;
	 foreach($words as $word){
		  if(isKnownWord($word)) $correction[] = $word;
		  else{
			   foreach($known_words as $known_word){
					$metaphone = levenshtein(metaphone($word), metaphone($known_word), 0, 0, 1);
					similar_text('a'.$word.'a', 'a'.$known_word.'a', $percentage);
					$levenshtein = levenshtein($word, $known_word, 2, 1, 1);
					if($metaphone == 0 && $percentage >= 75 && $levenshtein < 4){
						 if($percentage > $best_percentage){
							  $corrected = true;
							  $best_correction = $known_word;
							  $best_percentage = $percentage;
						 }
					}
			   }
			   if(!$corrected) $correction[] = $word;
			   else $correction[] = $best_correction;
			   $corrected = false;
			   $best_correction = '';
			   $best_percentage = '';
		  }
	 }
	 return implode(' ', $correction);
}

function getSUBJECTS($message){
	 $words = explode(' ', $message);
	 $subjects = false;
	 foreach($words as $key => $word){
		  if($key != 0 && isVerb($word) && !isVerb($words[$key - 1])) $subjects[] = $words[$key - 1];
		  else if($key == 0 && isVerb($word) && isset($words[$key + 1])) $subjects[] = $words[$key + 1];
	 }
	 return $subjects;
}

/*
 * Main function that returns all known data
 * about the message to the ajax function
 */
	 /*TODO : ASSERT IS QUESTION BEFORE THE REST : we must modify "vais je parler" by "je vais parler QUEST"
	 /*TODO : PROBLEM WITH "je te demande" : subject will be "te" and should be "je". Try to replace with : "je demande à toi*/
	 /*TODO : HANDLING "je veux simplement aider" : subject is not found because of adverb simplement*/
	 /*TODO : HANDLING "ne ... pas", "n'... pas" = NEGATION */
	 /*TODO : HANDLING "rien qu'", "rien que", "ne ... que" = uniquement*/
	 /*TODO : REMOVE NOUNS FROM MESSAGE: "je laisse la place" could result on "laisser placer"*/
	 /*TODO : REMOVE SUBJECTS FROM MESSAGE ! : "tu me suis" could result on "taire être suivre" */

if(
	 isset($_GET['message']) && !empty($_GET['message']) &&
	 isset($_GET['use_spellchecker']) && !empty($_GET['use_spellchecker']) &&
	 isset($_GET['display_verbs']) && !empty($_GET['display_verbs']) &&
	 isset($_GET['display_subjects']) && !empty($_GET['display_subjects']) &&
	 isset($_GET['display_questions']) && !empty($_GET['display_questions']) &&
	 isset($_GET['display_negations']) && !empty($_GET['display_negations'])
){
	 $message = ($_GET['use_spellchecker'] == 'true')?spellCheck($_GET['message']):$_GET['message'];

	 echo $message . "~";

	 $subjects_to_string = '';
	 if($subjects = getSUBJECTS($message)){
		  foreach($subjects as $subject)
			   $subjects_to_string .= "|" . $subject . "|";
	 }
	 if($_GET['display_subjects'] == 'true') echo $subjects_to_string . "~";

	 $verbs_to_string = '';
	 if($verbs = getVERBS($message)){
		  foreach($verbs as $verb)
			   $verbs_to_string .= "|" . $verb[1] . " => " . $verb[0] . "|";
	 }
	 if($_GET['display_verbs'] == 'true') echo $verbs_to_string . "~";

}

?>
