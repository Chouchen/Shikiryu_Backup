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
        if(!empty($filesToBackup) && is_array($filesToBackup)){
            $names = array_map("basename",$filesToBackup);
            $this->_filesToBackup = array_combine($filesToBackup,$names);
        }
        parent::__construct($config);
    }

    public function isValid()
    {
        $result = true;
        foreach ($this->_filesToBackup as $file => $name) {
            if (!file_exists($file)) {
                $result = false;
                break;
            }
        }
        return $result;
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
?>
