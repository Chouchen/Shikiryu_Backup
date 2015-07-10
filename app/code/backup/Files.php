<?php
namespace Shikiryu\Backup\Backup;

class Files extends BackupAbstract
{

    /**
     * @param array $filesToBackup
     */
    function __construct($filesToBackup = array())
    {
        parent::__construct();
        if(!empty($filesToBackup) && is_array($filesToBackup)){
            $names = array_map("basename",$filesToBackup);
            $this->_filesToBackup = array_combine($filesToBackup,$names);
        }
    }

    /**
     *
     *
     * @param string[] $filestobackup a list of file path
     *
     * @return $this
     */
    function setFilePath($filesToBackup = array())
    {
        if(!empty($filesToBackup) && is_array($filesToBackup))
        {
            $names = array_map("basename",$filesToBackup);
            $this->_filesToBackup = array_combine($filesToBackup,$names);
        }
        return $this;
    }
}
?>
