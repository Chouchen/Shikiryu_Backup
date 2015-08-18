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

    protected function preBuild()
    {
        // TODO: Implement preBuild() method.
    }

    protected function postBuild()
    {
        // TODO: Implement postBuild() method.
    }

    protected function build()
    {
        // TODO: Implement build() method.
    }
}