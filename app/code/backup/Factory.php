<?php

namespace Shikiryu\Backup\Backup;

class Factory
{
    /**
     * @param array $config
     * @return BackupAbstract
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