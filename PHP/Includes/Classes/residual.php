<?php

class Residual{

    private $text;

    public static $VERY_NEGATIVE = -3;
    public static $NEGATIVE = -1;
    public static $NEUTRAL = 0;
    public static $POSITIVE = 1;
    public static $VERY_POSITIVE = 3;

    public function __construct($text){
        $this->setText($text);
    }

    public function getScore(){
        $known_adjectives = file(DATA_ADJECTIVES, FILE_IGNORE_NEW_LINES);
        $message_analyzer = new MessageAnalyzer($this->text);
        $adjectives = $message_analyzer->getAdjectives();
        $known_adjectives_values = array();

        foreach($known_adjectives as $known_adjective){
            $adjective_and_value = explode(' ', $known_adjective);
            $known_adjective = $adjective_and_value[0];
            $adjective_value = $adjective_and_value[1];
            $known_adjectives_values[$known_adjective] = 0;
            $known_adjectives_values[$known_adjective] += mb_substr_count($adjective_value, '+');
            $known_adjectives_values[$known_adjective] -= mb_substr_count($adjective_value, '-');
        }

        $sum_adjectives_value = 0;
        foreach($adjectives as $adjective)
            $sum_adjectives_value += $known_adjectives_values[$adjective];

        if($sum_adjectives_value >= 3) return self::$VERY_POSITIVE;
        if($sum_adjectives_value >= 1) return self::$POSITIVE;
        if($sum_adjectives_value <= -3) return self::$VERY_NEGATIVE;
        if($sum_adjectives_value <= -1) return self::$NEGATIVE;
        if($sum_adjectives_value == 0) return self::$NEUTRAL;
    }

    public function setText($text){
        if(is_string($text)) $this->text = $text;
        else $this->text = '';
    }

    public function __toString(){
        return $this->text;
    }

}

?>
