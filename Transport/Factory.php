<?php

class Shikiryu_Backup_Transport_Factory
{
    /**
     * @param $type
     * @param Shikiryu_Backup_Abstract $backup
     *
     * @return bool
     */
    public static function getAndSend($type, Shikiryu_Backup_Abstract $backup, $args = null) {
        $class = sprintf('Shikiryu_Backup_Transport_%s', ucfirst($type));
        if (class_exists($class)) {
            /* @var $instance Shikiryu_Backup_Transport_Abstract */
            $instance = new $class($backup, $args);
            return $instance->send();
        }
        return false;
    }
}