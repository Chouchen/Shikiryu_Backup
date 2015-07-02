<?php
include_once dirname(__FILE__).'/Abstract.php';

class Shikiryu_Backup_Files extends Shikiryu_Backup_Abstract
{

    /**
     * @param array $filesToBackup
     */
    function __construct($filesToBackup = array())
    {
        parent::__construct();
        if(!empty($filesToBackup) && is_array($filesToBackup)){
            $names = $filesToBackup;
            array_walk($names, array($this, '_getNames'));
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
    function setFilePath($filestobackup = array())
    {
        if(!empty($filestobackup) && is_array($filestobackup))
        {
            $names = $filestobackup;
            array_walk($names, 'basename');
            $this->_filesToBackup = array_combine($filestobackup,$names);
        }
        return $this;
    }
}
?>
