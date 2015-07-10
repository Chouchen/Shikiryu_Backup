<?php

namespace Shikiryu\Backup\Transport;

use Shikiryu\Backup\Backup\BackupAbstract;

abstract class TransportAbstract
{

    protected $backup;
    protected $config;

    public function __construct(BackupAbstract $backup, array $config)
    {
        $this->config = $config;
        $this->backup = $backup;
    }

    /**
     * @return bool
     */
    public abstract function send();
}