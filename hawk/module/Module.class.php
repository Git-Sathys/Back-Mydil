<?php

class Module extends JsonObject
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $description;
    /**
     * @var bool
     */
    public $activated;
    /**
     * @var array
     */
    public $load;

    /**
     * Module constructor.
     * @param string $name
     * @param string $description
     * @param bool $activated
     * @param array $load
     * @return Module
     */
    public static function create(string $name, string $description, bool $activated, array $load = []): Module
    {
        $module = new Module();
        $module->name = $name;
        $module->description = $description;
        $module->activated = $activated;
        return $module;
    }

}
