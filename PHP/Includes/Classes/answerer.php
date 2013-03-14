<?php

class Answerer{

    private $message_analyzer;

    public function __construct($discussion, $message_analyzer){
        $this->setDiscussion($discussion);
        $this->setMessageAnalyzer($message_analyzer);
    }

    public function getAnswer(){
        $token_talk = $this->message_analyzer->getTokenTalk();
        $nouns = $this->message_analyzer->getNouns();
        $adjectives = $this->message_analyzer->getAdjectives();
        $response = '';

        // Le nom de l'utilisateur n'est pas encore connu
        if(!$this->discussion->getUser()->hasName()){

            if($this->message_analyzer->isPresentation()){
                $user_firstname = '';
                $residual = $this->message_analyzer->getResidual();
                $user_firstname = implode('-', explode(' ', $residual));
                $this->discussion->setUser(new User($user_firstname));
                $this->discussion->getUser()->save();
                $this->discussion->getUser()->addAttribute('être', $user_firstname);
                $this->discussion->getUser()->addAttribute('appeler', $user_firstname);
                $this->discussion->getUser()->addAttribute('prénommer', $user_firstname);
                return 'Bonjour ' . $user_firstname . ', comment vas-tu ?';
            }else return 'Je voudrais simplement connaître votre prénom...';

        // On connait le nom de l'utilisateur et il parle de lui (mémorisation des données)
        }else if(startsWith('NOT_QUESTION USER', $token_talk)){
            $verbs = $this->message_analyzer->getVerbs();
            if(isset($verbs[count($verbs) - 1])){
                $verb = $verbs[count($verbs) - 1];
                foreach($nouns as $noun)
                    $this->discussion->getUser()->addAttribute($verb->getTokenTalk(), $noun);
                foreach($adjectives as $adjective)
                    $this->discussion->getUser()->addAttribute($verb->getTokenTalk(), $adjective);
            }

        // S'il s'agit d'une question sur l'utilisateur, on va chercher dans
        // la mémoire en fonction du verbe à l'infinitif
        }else{
            $tmp_token_talk = explode(' ', $token_talk);
            if(count($tmp_token_talk) >= 2){
                $verbs = $this->message_analyzer->getVerbs();
                if($tmp_token_talk[0] != 'NOT_QUESTION' && $tmp_token_talk[1] == 'USER'){
                    if(isset($verbs[count($verbs) - 1])){ // There is a verb
                        $infinitive = $verbs[count($verbs) - 1]->getTokenTalk();
                        $attributes = $this->discussion->getUser()->getAttribute($infinitive);
                        $response = 'Si je me souviens bien, Vous avez parlé de ' . $infinitive . ' ' . implode(', ', $attributes). '...';
                    }
                }
            }
        }

        // Si on arrive jusqu'ici, on cherche si on connait le pattern
        // et on le retourne le cas échéant un pattern correspondant
        $patterns = file(DATA_PATTERNS, FILE_IGNORE_NEW_LINES);
        foreach($patterns as $key=>$pattern){
            if($key % 2 == 0 && $token_talk == $pattern){
                if($response != '') return $patterns[$key + 1] . ' ' . $response;
                else return $patterns[$key + 1];
            }
        }

        if($response != '') return $response;
        if(count($nouns) > 0) return 'Pourquoi me parlez-vous de ' . $nouns[0] . ' ?';
        else if(count($adjectives) > 0) return 'Pourquoi spécialement "' . $adjectives[0] . '" ?';
        else{
            $various_topics = file(DATA_GENERAL_ANSWERS, FILE_IGNORE_NEW_LINES);
            $rand_answer = rand(0, count($various_topics) - 1);
            if(!startsWith('NOT_QUESTION', $this->message_analyzer->getTokenTalk())) return 'Je ne sais pas... ' . $various_topics[$rand_answer];
            else return $various_topics[$rand_answer];
        }


    }

    public function setDiscussion($discussion){
        if(is_a($discussion, 'Discussion')) $this->discussion = $discussion;
        else $this->discussion = new Discussion();
    }

    public function setMessageAnalyzer($message_analyzer){
        if(is_a($message_analyzer, 'MessageAnalyzer')) $this->message_analyzer = $message_analyzer;
        else $this->message_analyzer = new MessageAnalyzer('', false);
    }

}

?>
