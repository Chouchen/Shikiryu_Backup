<?php

namespace Shikiryu\Backup\Backup;

use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP as LibSFTP;
use phpseclib\Net\SSH2;

class SFTP extends BackupAbstract
{
    use IsDistantTrait;

    /**
     * @var LibSFTP
     */
    private $connection;
    /** @var string */
    protected $host;
    /** @var int */
    protected $port = 22;
    /** @var string */
    protected $login;
    /** @var string */
    protected $password;
    /** @var string */
    protected $key;

    public function __construct($config = [])
    {
        if (!isset($config['files'])) {
            throw new \Exception('Files needs a "files" configuration.');
        }
        $filesToBackup = $config['files'];
        if (!empty($filesToBackup) && is_array($filesToBackup)) {
            $names = array_map('basename', $filesToBackup);
            $this->files_to_backup = array_combine($filesToBackup, $names);
        }

        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    protected function preBuild()
    {
        // TODO: Implement preBuild() method.
    }

    /**
     * @inheritDoc
     */
    protected function postBuild()
    {
        // TODO: Implement postBuild() method.
    }

    /**
     * @inheritDoc
     */
    protected function build()
    {
        // TODO: Implement build() method.
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function isValid()
    {
        if (empty($this->password) && empty($this->key)) {
            return false;
        }
        define('NET_SSH2_LOGGING', SSH2::LOG_COMPLEX);
        $this->connection = new LibSFTP($this->host, $this->port);
        if (!empty($this->key)) {
            $this->password = new RSA();
            $this->password->loadKey(file_get_contents($this->key));
        }
        if (!$this->connection->login($this->login, $this->password)) {
            throw new \Exception(sprintf('I can\'t connect to the SFTP %s', $this->host));
        }

        $this->connection->enableQuietMode();
        $this->connection->exec('whoami');

        return $this->connection->getStdError() === '' && $this->connection->read() !== '';
    }

    /**
     * @return Files
     */
    public function retrieve()
    {
        $tmp_files = [];
        foreach ($this->files_to_backup as $path => $name) {
            $tmp_file = TEMP_DIR.$name;
            $tmp_files[] = $tmp_file;
            $this->connection->get($path, $tmp_file);
        }
        unset($tmp_file);
        try {
            $tmp_backup = new Files(['files' => $tmp_files]);
            unset($tmp_files);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $tmp_backup;
    }
}
