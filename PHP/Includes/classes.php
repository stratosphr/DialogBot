<?php

include_once('defines.php');

class MessageAnalyzer{

	 private $message;
	 private $use_spellchecker = false;

	 public function __construct($message, $use_spellchecker){
		  $this->setMessage($message);
		  $this->setUseSpellchecker($use_spellchecker);
	 }

	 public function __toString(){
		  $to_string = 'answer: '.$this->getAnswer().' #';
		  $to_string .= 'verbs:';
		  foreach($this->getVerbs() as $verb) $to_string .= ' '.$verb[0].' => '.$verb[1].' |';
		  $to_string .= '#';
		  $to_string .= 'subjects:';
		  foreach($this->getSubjects() as $subject) $to_string .= ' '.$subject[0].' => '.$subject[1].' |';
		  $to_string .= '#';
		  $to_string .= 'question: '.$this->getQuestion().' #';
		  $to_string .= 'negation: '.$this->getNegation().' #';
		  return $to_string;
	 }

	 public function getAnswer(){
		  return $this->message;
	 }

	 public function getNegation(){
		  $negative_words = file(DATA_NEGATIVE_WORDS, FILE_IGNORE_NEW_LINES);
		  $verbs = $this->getVerbs();
		  $words = explode(' ', $this->message);
		  $negation = 'NOT_NEGATION';
		  $index_DISCORDANTIAL = -1;
		  $index_FORCLUSIVE = -1;
		  $index_VERB = -1;
		  foreach($words as $key=>$word){
			   if($word == 'ne') $index_DISCORDANTIAL = $key;
			   else if(in_array($word, $negative_words)) $index_FORCLUSIVE = $key;
			   else{
					foreach($verbs as $verb)
						 if($word == $verb[1]) $index_VERB = $key;
			   }
			   if($index_DISCORDANTIAL != -1 && $index_FORCLUSIVE != -1 && $index_VERB != -1){
					if($index_DISCORDANTIAL < $index_VERB) $negation = 'NEGATION';
			   }
		  }
		  return $negation;
	 }

	 public function getMessage(){
		  return $this->message;
	 }

	 public function getQuestion(){
		  $interrogative_words = file(DATA_INTERROGATIVE_WORDS, FILE_IGNORE_NEW_LINES);
		  $words = explode(' ', $this->message);
		  $first_verb = (isset($this->getVerbs()[0][1])) ? $this->getVerbs()[0][1] : null;
		  $first_subject = (isset($this->getSubjects()[0][1])) ? $this->getSubjects()[0][1] : null;

		  // Testing patterns 'est ce que .*' and '.* ?'
		  $question = endsWith('?', $this->message) ? 'QUESTION' : 'NOT_QUESTION';

		  // Testing pattern '.* VERB SUBJECT .*'
		  $verb_found = false;
		  foreach($words as $key => $word){
			   if($word == $first_verb) $verb_found = true;
			   else if($word == $first_subject && $verb_found) $question = 'QUESTION';
		  }

		  // Testing pattern '.* INTERROGATVIE_WORD .*'
		  foreach($words as $word){
			   foreach($interrogative_words as $key=>$interrogative_word){
					if($word == $interrogative_word && $key % 2 == 0)
						 $question = $interrogative_words[$key + 1];
			   }
			   if($question != 'QUESTION' && $question != 'NOT_QUESTION') break;
		  }

		  return $question;
	 }

	 public function getSubjects(){
		  $negative_words = file(DATA_NEGATIVE_WORDS, FILE_IGNORE_NEW_LINES);
		  $found_subjects = array();
		  $verbs = $this->getVerbs();
		  $words = explode(' ', $this->message);
		  
		  //Removing negations and adjectives
		  foreach($words as $key => $word){
			   if($word == 'ne' || in_array($word, $negative_words)){
					unset($words[$key]);
					array_values($words);
					print_r($words); echo '<br />';
			   }
		  }

		  if(count($words > 0)){
			   $interrogative_words = file(DATA_INTERROGATIVE_WORDS, FILE_IGNORE_NEW_LINES);
			   $subjects = file(DATA_SUBJECTS, FILE_IGNORE_NEW_LINES);

			   // Testing pattern 'QUESTION VERB SUBJECT .*'
			   if(in_array($words[0], $interrogative_words)){
					if(isset($words[1]) && isset($words[2])){
						 $verb_found = false;
						 foreach($verbs as $verb)
							  if($words[1] == $verb[1]) $verb_found = true;
						 if($verb_found){
							  foreach($subjects as $line){
								   $subjects_xtalk = explode(' ', $line);
								   if(in_array($words[2], $subjects_xtalk)) $found_subjects[] = array($subjects_xtalk[0], $words[2]);
							  }
						 }
					}
			   }

			   // Testing pattern 'QUESTION SUBJECT VERB .*'
			   if(in_array($words[0], $interrogative_words)){
					if(isset($words[1]) && isset($words[2])){
						 $verb_found = false;
						 foreach($verbs as $verb)
							  if($words[2] == $verb[1]) $verb_found = true;
						 if($verb_found){
							  foreach($subjects as $line){
								   $subjects_xtalk = explode(' ', $line);
								   if(in_array($words[1], $subjects_xtalk)) $found_subjects[] = array($subjects_xtalk[0], $words[1]);
							  }
						 }
					}
			   }

			   // Testing pattern 'SUBJECT VERB .*'
			   if(isset($words[1])){
					$verb_found = false;
					foreach($verbs as $verb)
						 if($words[1] == $verb[1]) $verb_found = true;
					if($verb_found){
						 foreach($subjects as $line){
							  $subjects_xtalk = explode(' ', $line);
							  if(in_array($words[0], $subjects_xtalk)) $found_subjects[] = array($subjects_xtalk[0], $words[0]);
						 }
					}
			   }

			   // Testing pattern 'VERB SUBJECT .*'
			   if(isset($words[1])){
					$verb_found = false;
					foreach($verbs as $verb)
						 if($words[0] == $verb[1]) $verb_found = true;
					if($verb_found){
						 foreach($subjects as $line){
							  $subjects_xtalk = explode(' ', $line);
							  if(in_array($words[1], $subjects_xtalk)) $found_subjects[] = array($subjects_xtalk[0], $words[1]);
						 }
					}
			   }
		  }

		  if(count($found_subjects) == 0) $found_subjects[] = array('OTHER', 'OTHER');

		  return $found_subjects;
	 }

	 public function getUseSpellchecker(){
		  return $this->use_spellchecker;
	 }

	 public function getVerbs(){
		  $conjugated_verbs = file(DATA_CONJUGATED_VERBS, FILE_IGNORE_NEW_LINES);
		  $words = explode(' ', $this->message);
		  $verbs = array();
		  foreach($words as $word){
			   if($word != ''){
					foreach($conjugated_verbs as $conjugated_verb){
						 $conjugations = explode(' ', $conjugated_verb);
						 if(in_array($word, $conjugations)) $verbs[] = array($conjugations[0], $word);
					}
			   }
		  }
		  return $verbs;
	 }

	 public function setMessage($message){
		  if(is_string($message)){
			   if($this->use_spellchecker) $this->message = $this->spellcheck($this->normalize($message));
			   else $this->message = $this->normalize($message);
		  }
		  else $this->message = ERROR_MESSAGE_NOT_STRING;
	 }

	 public function setUseSpellchecker($use_spellchecker){
		  if(is_bool($use_spellchecker)){
			   $this->use_spellchecker = $use_spellchecker;
			   $this->setMessage($this->message);
		  }
		  else $this->use_spellchecker = false;
	 }

	 public function normalize($message){
		  $message = toLower($message);
		  $message = preg_replace('# *([,?;.:!]) *#', ' $1 ', $message);
		  $message = preg_replace(array('#\bt( |-)+(il|elle|on|ils|elles)\b#'), array(' $2'), $message);
		  $message = str_replace('-', ' ', $message); // Dashes
		  $message = str_replace('\'', ' ', $message); // Single quotes
		  $message = preg_replace(array('#([[:blank:]]+|^)c([[:blank:]]+|$)#', '#([[:blank:]]+|^)d([[:blank:]]+|$)#', '#([[:blank:]]+|^)j([[:blank:]]+|$)#', '#([[:blank:]]+|^)l([[:blank:]]+|$)#', '#([[:blank:]]+|^)m([[:blank:]]+|$)#', '#([[:blank:]]+|^)n([[:blank:]]+|$)#', '#([[:blank:]]+|^)qu([[:blank:]]+|$)#', '#([[:blank:]]+|^)s([[:blank:]]+|$)#', '#([[:blank:]]+|^)t([[:blank:]]+|$)#'), array(' cela ', ' de ', ' je ', ' le ', ' me ', ' ne ', ' que ', ' se ', ' te '), $message); // Contractions
		  $message = str_replace('est ce que', '__est_ce_que__', $message);
		  echo 'Normalized : #'.$message.'#<br />';
		  return $message;
	 }

	 public function spellcheck($message){
		  $known_words = file(DATA_KNOWN_WORDS, FILE_IGNORE_NEW_LINES);
		  $words = explode(' ', $message);
		  $check = '';
		  $correction = array();
		  $corrected = false;
		  $best_percentage = 0;
		  $best_correction = false;
		  foreach($words as $word){
			   if($word != ''){
					if(in_array($word, $known_words)) $correction[] = $word; // Word is known
					else{ // Word is unknown
						 foreach($known_words as $known_word){
							  $metaphone = levenshtein(metaphone($word), metaphone($known_word), 0, 0, 1);
							  similar_text('a'.$word.'a', 'a'.$known_word.'a', $percentage);
							  $levenshtein = levenshtein($word, $known_word, 2, 1, 1);

							  // The known word could potentially be the misspelled word
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
						 $best_correction = false;
						 $best_percentage = 0;
					}
			   }
		  }
		  $spellchecked_message = implode(' ', $correction);
		  echo 'Correction : '.$message.' => '.$spellchecked_message.'<br />';
		  return $spellchecked_message;
	 }

}

?>
