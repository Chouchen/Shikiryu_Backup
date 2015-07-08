<?php

class Shikiryu_Backup_Transport_Ftp extends Shikiryu_Backup_Transport_Abstract
{

    private $path;
    
    private $connection;
    
    private $files;
    private $streams;

    public function __construct($backup, $server, $login, $pwd, $path='/') {
        parent::__construct($backup);

        $this->path = $this->config['path'];
        if (!empty($this->config['path'])) {
            $this->path = sprintf('/%s/', ltrim(rtrim($this->config['path'], '/'),'/'));
        }

        $this->connection = ftp_connect($this->config['host']);

        $login = ftp_login($this->connection, $this->config['login'], $this->config['password']);
        if (!$this->connection || !$login) {
            throw new Exception('Connexion FTP refusée.');
        }

        $this->setFiles($this->backup->getFilesToBackup());
        $this->setStreams($this->backup->getStreamsToBackup());
    }
    
    private function setFiles($files = array())
    {
        if (is_array($files) && !empty($files))
            $this->files = $files;
        return $this;
    }

    private function setStreams($streams = array()) {
        if (is_array($streams) && !empty($streams))
            $this->streams = $streams;
        return $this;
    }
    
    public function send()
    {
        $sent = true;
        if (!empty($this->files)){
            foreach ($this->files as $file => $name) {
                // TODO PASSIVE MODE
                $upload = ftp_put($this->connection, $this->path.$name, $file, FTP_ASCII);
                if (!$upload) {
                    $sent = false;
                    echo 'FTP upload manquée de '.$file.' vers '.$this->path.$name;
                }
            }
        }
        
        if (!empty($this->streams)){
            foreach ($this->streams as $name => $stream) {
                if (count(explode('.', $name)) < 2)
                    $name = 'backup' . $name . '.txt';
                file_put_contents($name, $stream);
                // TODO PASSIVE MODE
                $upload = ftp_put($this->connection, $this->path.$name, $name, FTP_ASCII);
                if (!$upload) {
                    echo 'FTP upload manquée de '.$name.' vers '.$this->_path.$name;
                    $sent = false;
                }
                unlink($name);
            }
        }
    }
    
    public function __destruct()
    {
        ftp_close($this->connection);
    }

    

        

        

        

        
}
?>
