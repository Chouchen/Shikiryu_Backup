<?php

namespace Shikiryu\Backup\Backup;

class BackupAbstract
{

    protected $_filesToBackup;
    protected $_streamsToBackup;

    /**
     * @param array $config
     */
    function __construct($config = array())
    {
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
        $this->_filesToBackup = array();
        $this->_streamsToBackup = array();
    }

    /**
     * Magic setter method
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
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
        foreach ($this->_filesToBackup as $file => $name) {
            $nameA = explode('.', $name);
            $nameA[] = end($nameA);
            $nameA[count($nameA) - 2] = date($format);
            $name = implode('.', $nameA);
            $tmpFiles[$file] = $name;
        }
        $this->_filesToBackup = $tmpFiles;

        $tmpStream = array();
        foreach ($this->_streamsToBackup as $name => $stream) {
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
        foreach ($this->_filesToBackup as $file => $name) {
            $nameA = explode('.', $name);
            $nameA[] = end($nameA);
            $nameA[count($nameA) - 2] = date($format);
            $name = implode('.', $nameA);
            $tmpFiles[$file] = $name;
        }
        $this->_filesToBackup = $tmpFiles;

        $tmpStream = array();
        foreach ($this->_streamsToBackup as $name => $stream) {
            $tmpStream[$name . '-' . date($format)] = $stream;
        }
        $this->_streamsToBackup = $tmpStream;

        return $this;
    }

    function backupToDropbox()
    {

    }

    /**
     * @param $folder
     */
    function backupToFolder($folder)
    {
        if (!empty($folder)) {
            $folder = sprintf('%s/', rtrim($folder, '/'));
        }
//        if($folder != '')
//        {
//            if(substr($folder, 0, -1) != '/')
//                $folder .= '/';
//        }
        foreach ($this->_filesToBackup as $file => $name) {
            copy($file, $folder . $name);
        }
        foreach ($this->_streamsToBackup as $name => $file) {
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
        foreach ($this->_filesToBackup as $file => $name) {
            if (filesize($file) < $fs) {
                return false;
            }
        }
        foreach ($this->_streamsToBackup as $name => $file) {
            if (mb_strlen($file, 'utf-8') < $fs) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

}

?>
