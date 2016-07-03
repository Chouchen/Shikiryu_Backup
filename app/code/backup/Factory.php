<?php

namespace Shikiryu\Backup\Backup;

class Factory
{
    /**
     * @param array $config
     *
     * @return BackupAbstract
     */
    public static function build(array $config)
    {
        $class = __NAMESPACE__.'\\'.array_keys($config)[0];
        if (class_exists($class)) {
            return new $class(array_values($config)[0]);
        }
        return null;
    }
}
