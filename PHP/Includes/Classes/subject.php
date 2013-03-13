<?php

class Subject{

    private $text;

    public function __construct($text){
        $this->setText($text);
    }

    public function getText(){
        return $this->text;
    }

    public function getTokenTalk(){
        $token_talk_subjects = file(DATA_SUBJECTS, FILE_IGNORE_NEW_LINES);
        foreach($token_talk_subjects as $token_talk_subject){
            $words = explode(' ', $token_talk_subject);
            if(in_array($this->text, $words)) return $words[0];
        }
    }

    public function setText($text){
        if(is_string($text)) $this->text = $text;
        else $this->text = '';
    }

}

?>
