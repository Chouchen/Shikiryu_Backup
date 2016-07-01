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

	/**
	 * Function that can be used to initialize the backup
	 */
    protected function preBuild()
    {
        // TODO: Implement preBuild() method.
    }

	 /**
	 * Function that can be used after the backup
	 */
    protected function postBuild()
    {
        // TODO: Implement postBuild() method.
    }

	/**
	 * Mandatory function doing the backup
	 */
    protected function build()
    {
        // TODO: Implement build() method.
    }
}