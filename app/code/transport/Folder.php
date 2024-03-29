<?php

namespace Shikiryu\Backup\Transport;

use Exception;
use Shikiryu\Backup\Backup\BackupAbstract;

class Folder extends TransportAbstract
{
    /**
    * Folder to backup
    *
    * @var string
    */
    protected $folder;

    public function __construct(BackupAbstract $backup, array $config = array())
    {
        parent::__construct($backup, $config);

        if (!empty($this->folder)) {
            $this->folder = sprintf('%s/', rtrim($this->folder, '/'));
        }
    }

    /**
    * @return bool
    *
    * @throws Exception
    */
    public function send()
    {
        if ($this->backup->isDistant()) {
            $this->backup = $this->backup->retrieve();
        }
        if ($this->backup->isLocal()) {
            foreach ($this->backup->getFilesToBackup() as $file => $name) {
                if (copy($file, $this->folder . $name) === false) {
                    throw new Exception(sprintf('Copy of %s in %s failed', $name, $this->folder));
                }
            }
            foreach ($this->backup->getStreamsToBackup() as $name => $file) {
                if (substr_count($name, '.') + 1 < 2) {
                    $name = 'backup' . $name . '.txt';
                }
                if (file_put_contents($this->folder . $name, $file) === false) {
                    throw new Exception(sprintf('Saving of %s in %s failed', $name, $this->folder));
                }
            }
        }

        return true;
    }
}
