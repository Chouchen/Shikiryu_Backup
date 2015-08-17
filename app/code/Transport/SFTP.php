<?php

namespace Shikiryu\Backup\Transport;

use phpseclib\Net\SFTP as LibSFTP;

class Sftp extends TransportAbstract
{

    protected $host;
    protected $port = 22;
    protected $login;
    protected $password;
    protected $folder;

    private $connection;

    /**
     * @param \Shikiryu\Backup\Backup\BackupAbstract $backup
     * @param array $config
     * @throws \Exception
     */
    public function __construct($backup, $config)
    {
        parent::__construct($backup, $config);

        $this->connection = new LibSFTP($this->host, $this->port);
        if (!$this->connection->login($this->login, $this->password)) {
            throw new \Exception(sprintf('I can\'t connect to the FTP %s', $this->host));
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function send()
    {
        $sent = true;
        $files = $this->backup->getFilesToBackup();
        if (!empty($files)){
            foreach ($files as $file => $name) {
                $upload = $this->connection->put($this->folder.'/'.$name, $file, LibSFTP::SOURCE_LOCAL_FILE);
                if (!$upload) {
                    $sent = false;
                    echo 'SFTP upload manqu�e de '.$file.' vers '.$this->folder.$name;
                }
            }
        }

        $streams = $this->backup->getStreamsToBackup();
        if (!empty($streams)){
            foreach ($streams as $name => $stream) {
                $upload = $this->connection->put($this->folder.'/'.$name, $stream);
                if (!$upload) {
                    echo 'SFTP upload manqu�e de '.$name.' vers '.$this->folder.$name;
                    $sent = false;
                }
            }
        }

        if (!$sent) {
            throw new \Exception('At least an upload didnt work.');
        }
        return $sent;
    }
}