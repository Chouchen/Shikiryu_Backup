<?php

namespace Shikiryu\Backup\Transport;

use Shikiryu\Backup\Backup\BackupAbstract;

class Factory
{
    /**
     *
     * @param BackupAbstract $backup
     * @param array $config
     *
     * @return TransportAbstract|null
     */
    public static function build(BackupAbstract $backup, array $config)
    {
        $class = __NAMESPACE__.'\\'.array_keys($config)[0];
        if (class_exists($class)) {
            return new $class($backup, array_values($config)[0]);
        }
        return null;
    }
}