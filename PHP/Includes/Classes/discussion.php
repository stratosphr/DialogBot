<?php

class Discussion extends SavableAndLoadable{

    private $bot;
    private $user;

    public function __construct(){
        parent::__construct('discussion');
        $this->setBot(new Bot());
        $this->setUser(new User());
    }

    public function getBot(){
        return $this->bot;
    }

    public function getUser(){
        return $this->user;
    }

    public function initialize(){
        if($this->load() == null) $this->save();
    }

    public function setBot($bot){
        if(is_a($bot, 'Bot')) $this->bot = $bot;
        else $this->bot = new Bot();
    }

    public function setUser($user){
        if(is_a($user, 'User') && $user->hasName()) $this->user = $user;
        else if(is_a($user, 'User')){
            $loaded_user = $user->load();
            if($loaded_user != null) $this->user = $loaded_user;
            else $this->user = new User();
        }else $this->user = new User();
    }

    public function save(){
        parent::save();
        $this->user->save();
        $this->bot->save();
    }

}

?>
