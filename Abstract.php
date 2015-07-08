<?php

include_once dirname(__FILE__) . '/Transport/Email.php';
include_once dirname(__FILE__) . '/Transport/FTP.php';

class Shikiryu_Backup_Abstract
{

    protected $_filesToBackup;
    protected $_streamsToBackup;

    /**
     *
     */
    function __construct()
    {
        $this->_filesToBackup   = array();
        $this->_streamsToBackup = array();
    }

    /**
     * @return array
     */
    public function getFilesToBackup()
    {
        return $this->_filesToBackup;
    }

    /**
     * @return array
     */
    public function getStreamsToBackup()
    {
        return $this->_streamsToBackup;
    }

    /**
     * Add the current date with the given format into the files names
     *
     * @param string $format
     *
     * @return $this
     */
    function addDate($format = 'Ymd')
    {
        $tmpFiles = array();
        foreach($this->_filesToBackup as $file => $name)
        {
            $nameA = explode('.', $name);
            $nameA[] = end($nameA);
            $nameA[count($nameA)-2] = date($format);
            $name = implode('.', $nameA);
            $tmpFiles[$file] = $name;
        }
        $this->_filesToBackup = $tmpFiles;

        $tmpStream = array();
        foreach($this->_streamsToBackup as $name => $stream)
        {
            $tmpStream[$name . '-' . date($format)] = $stream;
        }
        $this->_streamsToBackup = $tmpStream;

        return $this;
    }

    /**
     * Add the current time with the given format into the files names
     *
     * @param string $format
     *
     * @return $this
     */
    function addTime($format = 'his')
    {
        $tmpFiles = array();
        foreach($this->_filesToBackup as $file => $name)
        {
            $nameA = explode('.', $name);
            $nameA[] = end($nameA);
            $nameA[count($nameA)-2] = date($format);
            $name = implode('.', $nameA);
            $tmpFiles[$file] = $name;
        }
        $this->_filesToBackup = $tmpFiles;

        $tmpStream = array();
        foreach($this->_streamsToBackup as $name => $stream)
        {
            $tmpStream[$name . '-' . date($format)] = $stream;
        }
        $this->_streamsToBackup = $tmpStream;

        return $this;
    }

    /**
     *
     * @param $name string
     * @param $args mixed
     *
     * @return bool
     */
    public function __call($name, $args)
    {
        if (substr($name,0,8) == 'backupTo') {
            $type = substr($name, 8);
            return Shikiryu_Backup_Transport_Factory::getAndSend($type, $this, $args);
        }
    }

    /*function backupToEmail($to, $from, $object, $mes)
    {
        $email = new Shikiryu_Backup_Email();
        $email->addTo($to)
                ->setFrom($from)
                ->setSubject($object)
                ->setMessage($mes)
                ->setFiles($this->_filesToBackup)
                ->setStreams($this->_streamsToBackup);
        return $email->send();
    }*/

    function backupToDropbox()
    {
        
    }

    function backupToFTP($adress, $login = '', $pwd = '', $path ='/')
    {
        $ftp = new Shikiryu_Backup_FTP($adress, $login, $pwd, $path);
        $ftp->setFiles($this->_filesToBackup)
                ->setStreams($this->_streamsToBackup)
                ->send();
    }

    /**
     * @param $folder
     */
    function backupToFolder($folder)
    {
        if (!empty($folder)) {
            $folder = sprintf('%s/',rtrim($folder, '/'));
        }
//        if($folder != '')
//        {
//            if(substr($folder, 0, -1) != '/')
//                $folder .= '/';
//        }
        foreach($this->_filesToBackup as $file => $name)
        {
            copy($file, $folder . $name);
        }
        foreach($this->_streamsToBackup as $name => $file)
        {
            if (count(explode('.', $name)) < 2) {
                $name = 'backup' . $name . '.txt';
            }
            file_put_contents($folder . $name, $file);
        }
    }

    /**
     * Check if all files got the minimum given size.
     *
     * @param int $fs
     *
     * @return bool
     */
	function checkMinimumFilesize($fs)
	{
		foreach($this->_filesToBackup as $file => $name)
        {
           if (filesize($file) < $fs) {
               return false;
           }
        }
        foreach($this->_streamsToBackup as $name => $file)
        {
            if (mb_strlen($file, 'utf-8') < $fs) {
                return false;
            }
        }
		return true;
	}

}

?>
