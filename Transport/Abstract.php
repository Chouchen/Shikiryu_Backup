<?php

abstract class Shikiryu_Backup_Transport_Abstract
{

    protected $backup;
    protected $config;

    public function __construct(Shikiryu_Backup_Abstract $backup)
    {
        $config         = parse_ini_file(dirname(__FILE__).'/../config/config.ini');
        $classname      = get_class($this);
        $type           = substr($classname, strrpos($classname, '_')+1);
        $this->config   = $config[ucfirst(strtolower($type))];
        $this->backup   = $backup;
    }

    /**
     * @return bool
     */
    public abstract function send();
}