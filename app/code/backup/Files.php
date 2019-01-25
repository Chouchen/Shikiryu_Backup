<?php
namespace Shikiryu\Backup\Backup;

class Files extends BackupAbstract
{

    /**
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config = array())
    {
        if (!isset($config['files'])) {
            throw new \Exception('Files needs a "files" configuration.');
        }
        $filesToBackup = $config['files'];
        if (!empty($filesToBackup) && is_array($filesToBackup)) {
            $names = array_map("basename", $filesToBackup);
            $this->files_to_backup = array_combine($filesToBackup, $names);
        }
        parent::__construct($config);
    }

    /**
     * Check if the backup is valid
     *
     * @return bool
     *
     * @SuppressWarnings("unused")
     */
    public function isValid()
    {
        $result = true;
        foreach ($this->files_to_backup as $file => $name) {
            if (!file_exists($file)) {
                $result = false;
                break;
            }
        }
        return $result;
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
