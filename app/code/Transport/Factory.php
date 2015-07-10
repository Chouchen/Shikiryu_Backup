<?php

namespace Shikiryu\Backup\Transport;

class Factory
{
    /**
     * @param $type
     * @param Shikiryu_Backup_Abstract $backup
     *
     * @return bool
     */
    public static function build(array $config)
    {
        $class = array_keys($config)[0];
        if (class_exists($class)) {
            /* @var $instance BackupAbstract */
            return new $class(array_values($config));
        }
        return null;
    }
}