<?php

set_time_limit(0);

function isKnownVerb($verb){
	 if(is_string($verb)){
		  $known_verbs = file('Data/verbs.txt', FILE_IGNORE_NEW_LINES);
		  foreach($known_verbs as $known_verb)
			   if($known_verb == $verb) return true;
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

if(isset($_GET['message']) && !empty($_GET['message'])){
	 $message = $_GET['message'];
	 echo "~MESSAGE~" . "bonjour~MESSAGE~";

	 /*TODO : REMOVE SUBJECTS FROM MESSAGE ! : "tu me suis" could result on "taire être suivre" */

	 if($verbs = getVERBS($message)){
		  foreach($verbs as $verb)
			   foreach($verb as $infinitive) echo $infinitive;
	 }

}

?>
