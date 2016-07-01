<?php

namespace Shikiryu\Backup\Transport;


use Shikiryu\Backup\Backup\BackupAbstract;

class Folder extends TransportAbstract
{
	/** @var string */
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
     * @throws \Exception
     */
    public function send()
    {
        foreach ($this->backup->getFilesToBackup() as $file => $name) {
            if (copy($file, $this->folder . $name) === false) {
                throw new \Exception(sprintf('Copy of %s in %s failed', $name, $this->folder));
            };
        }
        foreach ($this->backup->getStreamsToBackup() as $name => $file) {
            if (count(explode('.', $name)) < 2) {
                $name = 'backup' . $name . '.txt';
            }
            if (file_put_contents($this->folder . $name, $file) === false) {
                throw new \Exception(sprintf('Saving of %s in %s failed', $name, $this->folder));
            }
        }
        return true;
    }
}