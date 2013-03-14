<?php

class User extends SavableAndLoadable{

    private $name;
    private $attributes = array();

    public function __construct($name='user'){
        parent::__construct('user');
        $this->setName($name);
    }

    public function getAttribute($infinitive){
        if(isset($this->attributes[$infinitive])) return $this->attributes[$infinitive];
        else return array('');
    }

    public function hasName(){
        return $this->name != 'user';
    }

    public function setName($name){
        if(is_string($name)) $this->name = $name;
        else $this->name = 'user';
    }

    public function addAttribute($infinitive, $attribute){
        $this->attributes[$infinitive][] = $attribute;
    }

}

?>
