<?php

namespace Shikiryu\Backup\Backup;

class Folder extends BackupAbstract
{

    public function __construct(array $config = array())
    {
        parent::__construct($config);


    }

    /**
     * @return bool
     */
    public function isValid()
    {
        // TODO: Implement isValid() method.
    }
}