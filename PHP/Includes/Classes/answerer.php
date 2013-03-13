<?php

class Answerer{

    private $message_analyzer;

    public function __construct($message_analyzer){
        $this->setMessageAnalyzer($message_analyzer);
    }

    public function setMessageAnalyzer($message_analyzer){
        if(is_a($message_analyzer, 'MessageAnalyzer')) $this->message_analyzer = $message_analyzer;
        else $this->message_analyzer = new MessageAnalyzer('', false);
    }

    public function getAnswer(){
        return $this->message_analyzer->getTokenTalk();
    }

}

?>
