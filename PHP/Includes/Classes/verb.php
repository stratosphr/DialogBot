<?php

class Verb{

    private $text;
    private $infinitive;

    public function __construct($text, $infinitive){
        $this->setText($text);
        $this->setInfinitive($infinitive);
    }

    public function getInfinitive(){
        return $this->infinitive;
    }

    public function getText(){
        return $this->text;
    }

    public function getTokenTalk(){
        return $this->getInfinitive();
    }

    public function setInfinitive($infinitive){
        if(is_string($infinitive)) $this->infinitive = $infinitive;
        else $this->infinitive = '';
    }

    public function setText($text){
        if(is_string($text)) $this->text = $text;
        else $this->text = '';
    }

}

?>
