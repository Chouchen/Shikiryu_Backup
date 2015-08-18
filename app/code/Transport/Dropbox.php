<?php

namespace Shikiryu\Backup\Transport;

use Dropbox\Client;
use Shikiryu\Backup\Transport\TransportAbstract;

class Dropbox extends TransportAbstract
{

    private $dropbox;

    protected $token    = '';
    protected $app      = '';
    protected $folder   = '';

    public function __construct($backup, $config)
    {
        parent::__construct($backup, $config);
        $this->dropbox = new Client($this->token, $this->app);
    }

    /**
     * @return bool
     */
    public function send()
    {
        $sent = true;
        $files = $this->backup->getFilesToBackup();
        foreach ($files as $file => $name) {
            $file = fopen($file, 'r');
            $upload = $this->dropbox->uploadFile($this->folder.'/'.$name, \Dropbox\WriteMode::force(), $file);
            if (!$upload) {
                $sent = false;
                echo 'DROPBOX upload manquée de '.$file.' vers '.$this->folder.$name;
            }
        }
        $streams = $this->backup->getStreamsToBackup();
        foreach ($streams as $stream => $name) {
            $upload = $this->dropbox->uploadFileFromString($this->folder.'/'.$name, \Dropbox\WriteMode::force(), $stream);
            if (!$upload) {
                $sent = false;
                echo 'DROPBOX upload manquée de '.$file.' vers '.$this->folder.$name;
            }
        }
        return $sent;
    }
}