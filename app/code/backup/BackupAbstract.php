<?php

namespace Shikiryu\Backup\Backup;

abstract class BackupAbstract
{

    protected $options;
    /** @var string[] */
    protected $_filesToBackup = [];
    /** @var string[] */
    protected $_streamsToBackup = [];

    /**
     * @param array $config
     */
    function __construct($config = array())
    {

        $this->options = !empty($config['options']) ? $config['options'] : [];
        unset($config['options']);

        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
        $this->init();
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
     * Check if all files got the minimum given size.
     *
     * @param int $file_size
     *
     * @return bool
     */
    public function checkMinimumFilesize($file_size)
    {
        foreach ($this->_filesToBackup as $file => $name) {
            if (filesize($file) < $file_size) {
                return false;
            }
        }
        foreach ($this->_streamsToBackup as $name => $file) {
            if (mb_strlen($file, 'utf-8') < $file_size) {
                return false;
            }
        }
        return true;
    }

    protected function init()
    {
        $this->preBuild();
        $this->build();
        $this->postBuild();
        $this->applyOptions();
    }

    abstract protected function preBuild();
    abstract protected function postBuild();
    abstract protected function build();
    abstract public function isValid();

    /**
     * @return $this
     */
    protected function applyOptions()
    {
        // TODO isValid here ?
        foreach($this->options as $name => $value)
        {
            $method = sprintf('setOption%s', ucfirst($name));
            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $value);
            }
        }

        return $this;
    }

    /**
     * Zip every backup files and streams into one zip
     * Enabled via options
     *
     * @return $this
     * @throws \Exception
     */
    protected function setOptionZip()
    {
        $zip = new \ZipArchive();
        $zip_name = sprintf('%s.zip', (!empty($this->options['name']) ? $this->options['name'] : time())); // Zip name
        if (touch(TEMP_DIR.$zip_name) === false) {
            throw new \Exception('Backup::Zip::Permission denied.');
        }
        if ($zip->open(TEMP_DIR.$zip_name, \ZIPARCHIVE::OVERWRITE)==TRUE) {
            foreach($this->_filesToBackup as $file => $name)
            {
                $zip->addFile($file, $name); // Adding files into zip
            }

            foreach($this->_streamsToBackup as $file => $name)
            {
                $zip->addFromString($file, $name); // Adding streams into zip
            }
            $zip->close();
        } else {
            throw new \Exception('Backup::Zip::Can\'t zip the given backup.');
        }

        $this->_filesToBackup   = [TEMP_DIR.$zip_name => $zip_name];
        $this->_streamsToBackup = [];

        return $this;
    }

    /**
     * Add the current date with the given format into the files names
     *
     * @param string $format
     *
     * @return $this
     */
    protected function setOptionAddDate($format = 'Ymd')
    {
        if ($format === true) {
            $format = 'Ymd';
        }
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
    protected function setOptionAddTime($format = 'his')
    {
        if ($format === true) {
            $format = 'his';
        }
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
     * @param $name
     * @throws \Exception
     */
    protected function setOptionName($name)
    {
        if (empty($this->options['zip'])) {
            throw new \Exception('name option is for zip only.');
        }
    }

}

?>
