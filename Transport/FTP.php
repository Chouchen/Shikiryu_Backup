<?php

class Shikiryu_Backup_FTP {

    private $_path;
    
    private $_connection;
    
    private $_files;
    private $_streams;
    
    function __construct($server, $login, $pwd, $path='/') {
        if ($path != '') {
            $path = sprintf('/%s/', ltrim(rtrim($path, '/'),'/'));
//            if (substr($path, 0, 1) != '/')
//                $path = '/' . $path;
//            if (substr($path, 0, -1) != '/')
//                $path .= '/';
        }
        $this->_path = $path;
        $this->_connection = ftp_connect($server);

        $login = ftp_login($this->_connection, $login, $pwd);
        if (!$this->_connection || !$login) {
            throw new Exception('Connexion FTP refusée.');
        }
    }
    
    function setFiles($files = array())
    {
        if (is_array($files) && !empty($files))
            $this->_files = $files;
        return $this;
    }
    
    function setStreams($streams = array()) {
        if (is_array($streams) && !empty($streams))
            $this->_streams = $streams;
        return $this;
    }
    
    function send()
    {
        if (!empty($this->_files)){
            foreach ($this->_files as $file => $name) {
                $upload = ftp_put($this->_connection, $this->_path.$name, $file, FTP_ASCII);
                if (!$upload) {
                    echo 'FTP upload manquée de '.$file.' vers '.$this->_path.$name;
                }
//                else echo 'upload réussi de '.$file.' vers '.$this->_path.$name;
            }
        }
        
        if (!empty($this->_streams)){
            foreach ($this->_streams as $name => $stream) {
                if (count(explode('.', $name)) < 2)
                    $name = 'backup' . $name . '.txt';
                file_put_contents($name, $stream);
                $upload = ftp_put($this->_connection, $this->_path.$name, $name, FTP_ASCII);
                if (!$upload) {
                    echo 'FTP upload manquée de '.$name.' vers '.$this->_path.$name;
                }
//                else echo 'upload réussi de '.$name.' vers '.$this->_path.$name;
                unlink($name);
            }
        }
    }
    
    function __destruct()
    {
        ftp_close($this->_connection);
    }

    

        

        

        

        
}
?>
