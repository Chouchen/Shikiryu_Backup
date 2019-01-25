<?php

namespace Shikiryu\Backup\Backup;

abstract class BackupAbstract
{
    /**
     * Options
     *
     * @var array
     */
    protected $options;
    /**
     * File path to backup
     *
     * @var string[]
     */
    protected $files_to_backup = [];
    /**
     * Streams to backup
     *
     * @var string[]
     */
    protected $streams_to_backup = [];

    /**
     * Constructor
     *
     * @param array $config array of options and parameters
     */
    public function __construct($config = array())
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
     * @param string $name  attribute name
     * @param mixed  $value attribute value
     *
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
        
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getFilesToBackup()
    {
        return $this->files_to_backup;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getStreamsToBackup()
    {
        return $this->streams_to_backup;
    }

    /**
     * Check if all files got the minimum given size.
     *
     * @param int $file_size file size
     *
     * @return bool
     */
    public function checkMinimumFilesize($file_size)
    {
        foreach ($this->files_to_backup as $file => $name) {
            if (filesize($file) < $file_size) {
                return false;
            }
        }
        foreach ($this->streams_to_backup as $name => $file) {
            if (mb_strlen($file, 'utf-8') < $file_size) {
                return false;
            }
        }
        return true;
    }

    /**
     * Initialize everything
     *
     * @return $this
     */
    protected function init()
    {
        $this->preBuild();
        $this->build();
        $this->postBuild();
        $this->applyOptions();
        
        return $this;
    }

    /**
     * Function that can be used to initialize the backup
     *
     * @return void
     */
    abstract protected function preBuild();

    /**
     * Function that can be used after the backup
     *
     * @return void
     */
    abstract protected function postBuild();

    /**
     * Mandatory function doing the backup
     *
     * @return void
     */
    abstract protected function build();

    /**
     * Check if the backup is valid
     *
     * @return bool
     */
    abstract public function isValid();

    /**
     * Apply options
     *
     * @return $this
     */
    protected function applyOptions()
    {
        // TODO isValid here ?
        foreach ($this->options as $name => $value) {
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
        // Zip name
        $zip_name = !empty($this->options['name']) ? $this->options['name'] : time();
        $zip_name = sprintf('%s.zip', $zip_name);
        if (touch(TEMP_DIR . $zip_name) === false) {
            throw new \Exception('Backup::Zip::Permission denied.');
        }
        if ($zip->open(TEMP_DIR . $zip_name, \ZipArchive::OVERWRITE) == true) {
            foreach ($this->files_to_backup as $file => $name) {
                $zip->addFile($file, $name); // Adding files into zip
            }

            foreach ($this->streams_to_backup as $file => $name) {
                $zip->addFromString($file, $name); // Adding streams into zip
            }
            $zip->close();
        } else {
            throw new \Exception('Backup::Zip::Can\'t zip the given backup.');
        }

        $this->files_to_backup = [TEMP_DIR . $zip_name => $zip_name];
        $this->streams_to_backup = [];

        return $this;
    }

    /**
     * Add the current date with the given format into the files names
     *
     * @param string $format date format
     *
     * @return $this
     */
    protected function setOptionAddDate($format = 'Ymd')
    {
        if ($format === true) {
            $format = 'Ymd';
        }
        $tmpFiles = array();
        foreach ($this->files_to_backup as $file => $name) {
            $nameA = explode('.', $name);
            $nameA[] = end($nameA);
            $nameA[count($nameA) - 2] = date($format);
            $name = implode('.', $nameA);
            $tmpFiles[$file] = $name;
        }
        $this->files_to_backup = $tmpFiles;

        $tmpStream = array();
        foreach ($this->streams_to_backup as $name => $stream) {
            $tmpStream[$name . '-' . date($format)] = $stream;
        }
        $this->streams_to_backup = $tmpStream;

        return $this;
    }

    /**
     * Add the current time with the given format into the files names
     *
     * @param string $format time format
     *
     * @return $this
     */
    protected function setOptionAddTime($format = 'his')
    {
        if ($format === true) {
            $format = 'his';
        }
        $tmpFiles = array();
        foreach ($this->files_to_backup as $file => $name) {
            $nameA = explode('.', $name);
            $nameA[] = end($nameA);
            $nameA[count($nameA) - 2] = date($format);
            $name = implode('.', $nameA);
            $tmpFiles[$file] = $name;
        }
        $this->files_to_backup = $tmpFiles;

        $tmpStream = array();
        foreach ($this->streams_to_backup as $name => $stream) {
            $tmpStream[$name . '-' . date($format)] = $stream;
        }
        $this->streams_to_backup = $tmpStream;

        return $this;
    }

    /**
     * Set option name
     *
     * @param mixed $name option's name
     *
     * @throws \Exception
     *
     * @SuppressWarnings("unused")
     */
    protected function setOptionName($name)
    {
        if (empty($this->options['zip'])) {
            throw new \Exception('name option is for zip only.');
        }
    }
}
