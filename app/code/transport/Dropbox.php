<?php

namespace Shikiryu\Backup\Transport;

use Spatie\Dropbox\Client;

class Dropbox extends TransportAbstract
{
    /**
     * Dropbox Client
     *
     * @var Client
     */
    private $dropbox;

    protected $token    = '';
//    protected $app      = '';
    protected $folder   = '';

    public function __construct($backup, $config)
    {
        parent::__construct($backup, $config);
        $this->dropbox = new Client($this->token);
    }

    /**
     * @return bool
     */
    public function send()
    {
        $sent = true;
        $files = $this->backup->getFilesToBackup();
        foreach ($files as $file => $name) {
            $file = fopen($file, 'rb');
            $upload = $this->dropbox->upload($this->folder.'/'.$name, $file);
            if (!$upload) {
                $sent = false;
                echo 'DROPBOX upload manqu�e de '.$file.' vers '.$this->folder.$name;
            }
        }
        $streams = $this->backup->getStreamsToBackup();
        foreach ($streams as $stream => $name) {
            $upload = $this->dropbox->upload($this->folder . '/' . $name, $stream);
            if (!$upload) {
                $sent = false;
                echo 'DROPBOX upload manquée de ' . $file . ' vers '.$this->folder.$name;
            }
        }
        return $sent;
    }
}
