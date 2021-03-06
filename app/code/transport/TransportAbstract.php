<?php

namespace Shikiryu\Backup\Transport;

use Shikiryu\Backup\Backup\BackupAbstract;

abstract class TransportAbstract
{

    /** @var BackupAbstract */
    protected $backup;
    /** @var array */
    protected $config;

    public function __construct(BackupAbstract $backup, array $config)
    {

        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
        $this->config = $config;
        $this->backup = $backup;
    }

    /**
     * @return bool
     */
    abstract public function send();
}
