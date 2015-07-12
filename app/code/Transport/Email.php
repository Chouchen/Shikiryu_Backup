<?php

namespace Shikiryu\Backup\Transport;

use Shikiryu\Backup\Backup\BackupAbstract;

class Email extends TransportAbstract
{

    private $to;
    private $from;
    private $encoding;
    private $subject;
    private $message;
    private $files;
    private $streams;
    public static $mimeTypes = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'xhtml' => 'application/xhtml+xml',
        'xht' => 'application/xhtml+xml',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'xslt' => 'application/xslt+xml',
        'xsl' => 'application/xml',
        'dtd' => 'application/xml-dtd',
        'atom' => 'application/atom+xml',
        'mathml' => 'application/mathml+xml',
        'rdf' => 'application/rdf+xml',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'vxml' => 'application/voicexml+xml',
        'latex' => 'application/x-latex',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texinfo' => 'application/x-texinfo',
        'wrl' => 'model/vrml',
        'wrml' => 'model/vrml',
        'ics' => 'text/calendar',
        'ifb' => 'text/calendar',
        'sgml' => 'text/sgml',
        'htc' => 'text/x-component',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/x-icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'djvu' => 'image/vnd.djvu',
        'djv' => 'image/vnd.djvu',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',
        'tar' => 'application/x-tar',
        'gz' => 'application/x-gzip',
        'tgz' => 'application/x-gzip',
        // audio/video
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'wav' => 'audio/wav',
        'aiff' => 'audio/aiff',
        'aif' => 'audio/aiff',
        'avi' => 'video/msvideo',
        'wmv' => 'video/x-ms-wmv',
        'ogg' => 'application/ogg',
        'flv' => 'video/x-flv',
        'dvi' => 'application/x-dvi',
        'au' => 'audio/basic',
        'snd' => 'audio/basic',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'm3u' => 'audio/x-mpegurl',
        'm4u' => 'video/vnd.mpegurl',
        'ram' => 'audio/x-pn-realaudio',
        'ra' => 'audio/x-pn-realaudio',
        'rm' => 'application/vnd.rn-realmedia',
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'swf' => 'application/x-shockwave-flash',
        // ms office
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xla' => 'application/vnd.ms-excel',
        'xld' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
        'xlc' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'xll' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    /**
     * Determine mime type
     *
     * @param string $file path to the file
     *
     * @return string
     */
    public static function getMimeType($file) {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = (string) finfo_file($finfo, $file);
            finfo_close($finfo);
            return $type;
        } else if (function_exists('mime_content_type')) {
            return mime_content_type($file);
        } else {
            $ext = strtolower(array_pop(explode('.', $file)));
            if (array_key_exists($ext, self::$mimeTypes))
                return self::$mimeTypes[$ext];
            else
                return 'application/octet-stream';
        }
    }

    /**
     * @param BackupAbstract $backup
     */
    public function __construct(BackupAbstract $backup, array $config) {
        parent::__construct($backup, $config);
        $this->setFiles($this->backup->getFilesToBackup());
        $this->setStreams($this->backup->getStreamsToBackup());
        $this->to       = $this->config['to'];
        $this->from     = $this->config['from'];
        $this->encoding = $this->config['encoding'];
        $this->subject  = $this->config['subject'];
        $this->message  = $this->config['message'];
        $this->encoding = $this->config['encoding'];
    }

    /**
     * Add a recipient
     *
     * @param string $to
     *
     * @return $this
     */
    private function addTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Add the sender
     *
     * @param $from
     *
     * @return $this
     */
    private function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Add the subject
     *
     * @param $sub
     *
     * @return $this
     */
    private function setSubject($sub)
    {
        $this->subject = strip_tags($sub);
        return $this;
    }

    /**
     * Add the message (in text)
     *
     * @param $mes
     *
     * @return $this
     */
    private function setMessage($mes)
    {
        $this->message = strip_tags($mes);
        return $this;
    }

    private function setFiles($files = array())
    {
        if (is_array($files) && !empty($files)) {
            $this->files = $files;
        }
        return $this;
    }

    private function setStreams($streams = array())
    {
        if (is_array($streams) && !empty($streams)) {
            $this->streams = $streams;
        }
        return $this;
    }

    /**
     * Send the mail
     *
     * @see #mail
     * @return bool
     * @throws \Exception
     */
    public function send()
    {

        // TODO check if file is empty
	
		// Checking files are selected
		$zip = new \ZipArchive(); // Load zip library
		$zip_name = time().".zip"; // Zip name
		if ($zip->open(TEMP_DIR.$zip_name, \ZIPARCHIVE::CREATE)==TRUE) {
			if(!empty($this->files)) {
                foreach($this->files as $file => $name)
                {
                    $zip->addFile($file, $name); // Adding files into zip
                }
            }
			$zip->close();
		} else {
            throw new \Exception('Transport::Email::Can\'t zip the given backup.');
        }
		
		$this->files = array(TEMP_DIR.$zip_name=>$zip_name);

        $random_hash = md5(date('r', time()));
        $headers = "From: " . $this->from . "\r\nReply-To: " . $this->from;
        $headers .= "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"" . $random_hash . "\"";
        $output = "

--$random_hash
Content-Type: text/plain; charset='" . strtolower($this->encoding) . "'
Content-Transfer-Encoding: 8bit
 
" . $this->message . "\r\n";
        
        if(!empty($this->files))
        foreach($this->files as $file => $name) {
            $output .= "
--$random_hash
Content-Type: " . self::getMimeType($file) . "; name=" . $name . "
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename=" . $name . "
 
" . chunk_split(base64_encode(file_get_contents($file)));
        }
        
        if(!empty($this->streams))
        foreach($this->streams as $name=>$stream) {
            if(count(explode('.',$name))<2) $name = 'backup'.$name.'.txt';
            $output .= "
--$random_hash
Content-Type: text/plain; name=" . $name . "
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename=" . $name . "
 
" . chunk_split(base64_encode($stream));
        }
        $output.="--$random_hash--";
        return mail($this->to, $this->subject, $output, $headers);
    }

}

?>
