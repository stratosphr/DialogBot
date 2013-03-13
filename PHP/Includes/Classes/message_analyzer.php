<?php

class MessageAnalyzer{

    private $message;
    private $use_spellchecker = false;

    public function __construct($message, $use_spellchecker=false){
        $this->setMessage($message);
        $this->setUseSpellchecker($use_spellchecker);
    }

    public function __toString(){
        $to_string = 'verbs:';
        foreach($this->getVerbs() as $verb) $to_string .= ' '.$verb[0].' => '.$verb[1].' |';
        $to_string .= '#';
        $to_string .= 'subject:';
        $subject = $this->getSubject();
        $to_string .= ' '.$subject[0][0].' => '.$subject[0][1].' |';
        $to_string .= '#';
        $to_string .= 'question: '.$this->getQuestion().' #';
        $to_string .= 'negation: '.$this->getNegation().' #';
        $to_string .= 'adjectives: ';
        foreach($this->getAdjectives() as $adjective) $to_string .= ' '.$adjective.' |';
        $to_string .= '#';
        $toto = new Subject($subject[0][0]);
        return $to_string;
    }

    public function getAdjectives(){
        $adjectives = array();
        $known_adjectives = file(DATA_ADJECTIVES, FILE_IGNORE_NEW_LINES);
        $index = 0;
        $previous_pos = 0;
        $words = explode(' ', $this->message);
        foreach($words as $word){
            foreach($known_adjectives as $known_adjective){
                $known_adjective = preg_replace(array('#[+-=]+#', '# $#'), '', $known_adjective);
                if($word == $known_adjective) $adjectives[] = $known_adjective;
            }
        }

        return $adjectives;
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
        $first_subject = (isset($this->getSubject()[0][1])) ? $this->getSubject()[0][1] : null;

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

    public function getResidual(){
        $negative_words = file(DATA_NEGATIVE_WORDS, FILE_IGNORE_NEW_LINES);
        $subject = $this->getSubject()[0][1];
        $verbs = $this->getVerbs();
        $question = $this->getQuestion();
        $residual = array();

        //Removing negations
        $words = explode(' ', $this->message);
        $tmp_words = array();
        foreach($words as $key => $word){
            if($word != 'ne' && !in_array($word, $negative_words)) $tmp_words[] = $word;
        }
        $words = $tmp_words;

        $is_verb = false;
        foreach($words as $word){
            foreach($verbs as $verb)
                if($word == $verb[1]) $is_verb = true;
            if($word != $subject && !$is_verb && $word != $question) $residual[] = $word;
            $is_verb = false;
        }

        $residual = implode(' ', $residual);
        $toto = new Residual($residual);
        echo $toto->getScore();
        return new Residual($residual);

    }

    public function getSubject(){
        $negative_words = file(DATA_NEGATIVE_WORDS, FILE_IGNORE_NEW_LINES);
        $found_subject = array();
        $verbs = $this->getVerbs();

        //Removing negations
        $words = explode(' ', $this->message);
        $tmp_words = array();
        foreach($words as $key => $word){
            if($word != 'ne' && !in_array($word, $negative_words)) $tmp_words[] = $word;
        }
        $words = $tmp_words;

        // If there are any words in the message
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
                            if(in_array($words[2], $subjects_xtalk)) $found_subject[0] = array($subjects_xtalk[0], $words[2]);
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
                            if(in_array($words[1], $subjects_xtalk)) $found_subject[0] = array($subjects_xtalk[0], $words[1]);
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
                        if(in_array($words[0], $subjects_xtalk)) $found_subject[0] = array($subjects_xtalk[0], $words[0]);
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
                        if(in_array($words[1], $subjects_xtalk)) $found_subject[0] = array($subjects_xtalk[0], $words[1]);
                    }
                }
            }

            //Testing pattern 'SUBJECT * VERB'
            foreach($subjects as $line){
                $subjects_xtalk = explode(' ', $line);
                if(in_array($words[0], $subjects_xtalk)) $found_subject[0] = array($subjects_xtalk[0], $words[0]);
            }

        }

        if(count($found_subject) == 0) $found_subject[0] = array('OTHER', 'OTHER');

        return $found_subject;
    }

    public function getTokenTalk(){
        $token_talk = '';
        $subject = $this->getSubject()[0][0];
        $negation = $this->getNegation();
        $last_verb_index = count($this->getVerbs());
        if($last_verb_index > 0) $last_verb = $this->getVerbs()[$last_verb_index - 1][0];
        else $last_verb = '';
        $question = $this->getQuestion();
        $residual = $this->getResidual();
        $token_talk .= $question . ' ' . $subject . ' ' . $negation . ' ' . $last_verb . ' ' . $residual;

        return $token_talk;
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
        $match = array('#([[:blank:]]+|^)s([[:blank:]]+)il([[:blank:]]+|$)#', '#([[:blank:]]+|^)c([[:blank:]]+|$)#', '#([[:blank:]]+|^)d([[:blank:]]+|$)#', '#([[:blank:]]+|^)j([[:blank:]]+|$)#', '#([[:blank:]]+|^)l([[:blank:]]+|$)#', '#([[:blank:]]+|^)m([[:blank:]]+|$)#', '#([[:blank:]]+|^)n([[:blank:]]+|$)#', '#([[:blank:]]+|^)qu([[:blank:]]+|$)#', '#([[:blank:]]+|^)s([[:blank:]]+|$)#', '#([[:blank:]]+|^)t([[:blank:]]+|$)#', '#([[:blank:]]+|^)puisqu([[:blank:]]+|$)#', '#([[:blank:]]+|^)quoiqu([[:blank:]]+|$)#', '#([[:blank:]]+|^)jusqu([[:blank:]]+|$)#', '#([[:blank:]]+|^)lorsqu([[:blank:]]+|$)#');
        $replace = array(' si il ', ' cela ', ' de ', ' je ', ' le ', ' me ', ' ne ', ' que ', ' se ', ' te ', ' puisque ', ' quoique ', ' jusque ', 'lorsque');
        $message = preg_replace($match, $replace, $message); // Contractions
        $message = str_replace('est ce que', '__est_ce_que__', $message);
        $message = str_replace('aujourd hui', 'aujourd\'hui', $message);
        $message = str_replace('parce que', 'puisque', $message);
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
