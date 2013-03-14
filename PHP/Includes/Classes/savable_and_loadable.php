<?php

abstract class SavableAndLoadable{

    private $file;
    private $identifier;

    public function __construct($identifier, $file=null){
        $this->setFile($file);
        $this->setIdentifier($identifier);
    }

    public function getIdentifier(){
        return $this->identifier;
    }

    public function load(){
        if(file_exists($this->file)){
            $serialized_objects = file($this->file, FILE_IGNORE_NEW_LINES);
            foreach($serialized_objects as $serialized_object){
                if(startsWith($this->identifier.'=', $serialized_object)){
                    $serialized_object = explode('=', $serialized_object)[1];
                    return unserialize($serialized_object);
                }
            }
        }
        return null;
    }

    public function save(){
        if(file_exists($this->file)){
            $serialized_objects = file($this->file, FILE_IGNORE_NEW_LINES);
            $new_serialized_objects = array();
            foreach($serialized_objects as $serialized_object)
                if(!startsWith($this->identifier.'=', $serialized_object)) $new_serialized_objects[] = $serialized_object;
        }
        $new_serialized_objects[] = $this->identifier.'='.serialize($this);
        file_put_contents($this->file, implode(PHP_EOL, $new_serialized_objects));
    }

    public function setFile($file){
        if(is_string($file)) $this->file($file);
        else $this->file = DATA_SAVED;
    }

    public function setIdentifier($identifier){
        if(is_string($identifier)) $this->identifier = $identifier;
        else $this->identifier = '';
    }

}

?>
