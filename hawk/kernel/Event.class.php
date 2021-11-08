<?php

namespace hawk\kernel;

class Event {

    private $events;
    private static $instance;

    private function __construct()
    {
        $this->events = [];
    }

    public static function getInstance(){
        if(self::$instance == NULL){
            self::$instance = new Event();
        }
        return self::$instance;
    }

    public static function registerEvent(string $event, $callable){
        if(!array_key_exists($event, self::getInstance()->events)){
            self::getInstance()->events[$event] = [];
        }
        self::getInstance()->events[$event][] = $callable;
    }

    public static function trigger(string $event) : bool
    {
        if(!array_key_exists($event, self::getInstance()->events)){
            return FALSE;
        }
        foreach (self::getInstance()->events[$event] as $callable) {
            call_user_func($callable);
        }
        return TRUE;
    }

}
