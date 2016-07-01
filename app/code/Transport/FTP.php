<?php
namespace Shikiryu\Backup\Transport;

class Ftp extends TransportAbstract
{

    private $connection;

    protected $host;
    protected $login;
    protected $password;
    protected $folder;

    private $files;
    private $streams;

    public function __construct($backup, $config) {
        parent::__construct($backup, $config);

//        $this->path = $this->config['path'];
        if (!empty($this->folder)) {
            $this->folder = sprintf('/%s/', ltrim(rtrim($this->folder, '/'),'/'));
        }

        $this->connection = ftp_connect($this->host);
        if ($this->connection == false) {
            throw new \Exception(sprintf('I can\'t connect to the FTP %s', $this->host));
        }

        $login = @ftp_login($this->connection, $this->login, $this->password);
        if ($login === false) {
            throw new \Exception(sprintf('Connexion FTP %s refusée avec %s et %s', $this->host, $this->login, $this->password));
        }

        $this->setFiles($this->backup->getFilesToBackup());
        $this->setStreams($this->backup->getStreamsToBackup());
    }
    
    private function setFiles($files = array())
    {
        if (is_array($files) && !empty($files)) {
            $this->files = $files;
        }
        return $this;
    }

    private function setStreams($streams = array()) {
        if (is_array($streams) && !empty($streams)) {
            $this->streams = $streams;
        }
        return $this;
    }
    
    /**
     * @return bool
     */
    public function send()
    {
        $sent = true;
        ftp_pasv($this->connection, true);
        if (!empty($this->files)){
            foreach ($this->files as $file => $name) {
                $upload = ftp_put($this->connection, $this->folder.$name, $file, FTP_BINARY);
                if (!$upload) {
                    $sent = false;
                    echo 'FTP upload manquée de '.$file.' vers '.$this->folder.$name;
                }
            }
        }
        
        if (!empty($this->streams)){
            foreach ($this->streams as $name => $stream) {
                if (count(explode('.', $name)) < 2)
                    $name = 'backup' . $name . '.txt';
                file_put_contents($name, $stream);
                $upload = ftp_put($this->connection, $this->folder.$name, $name, FTP_ASCII);
                if (!$upload) {
                    echo 'FTP upload manquée de '.$name.' vers '.$this->folder.$name;
                    $sent = false;
                }
                unlink($name);
            }
        }

        if (!$sent) {
            throw new \Exception('At least an upload didnt work.');
        }
        
        return $sent;
    }
    
    public function __destruct()
    {
        ftp_close($this->connection);
    }

    

        

        

        

        
}
?>
