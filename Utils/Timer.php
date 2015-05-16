<?php
namespace Utils;

class Timer {
    static private $timers = array();
    static private $completedTimers = array();
    static public $factor = 1000;   //ms by default
    
    static public function start($name) {
         static::$timers[$name] = microtime(true);
    }
    
    static public function end($name) {
        $ended = microtime(true);
        $started =  static::$timers[$name];
        unset(static::$timers[$name]);
        static::$completedTimers[$name] = array(
            'started' => $started,
            'ended' => $ended,
            'duration' => ($ended-$started) * static::$factor,
        );
    }
    
    static public function getTimes() {
        print_r(static::$completedTimers);
    }
}