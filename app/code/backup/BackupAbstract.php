<?php

namespace Shikiryu\Backup\Backup;

abstract class BackupAbstract
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
    public function addDate($format = 'Ymd')
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
    public function addTime($format = 'his')
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
    abstract public function isValid();

}

?>
