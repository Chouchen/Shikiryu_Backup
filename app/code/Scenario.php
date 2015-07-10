<?php

namespace Shikiryu\Backup;

use Shikiryu\Backup\Backup\BackupAbstract;
use Shikiryu\Backup\Transport\TransportAbstract;
use Shikiryu\Backup\Backup\Factory as BackupFactory;
use Shikiryu\Backup\Transport\Factory as TransportFactory;

class Scenario {

    /* @var $backup BackupAbstract */
    private $backup;
    /* @var $to TransportAbstract */
    private $transport;

    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public static function autoload($class)
    {
        // Autoload only "sub-namespaced" class
        if (strpos($class, __NAMESPACE__.'\\') === 0)
        {
            // Delete current namespace from class one
            $relative_NS     = str_replace(__NAMESPACE__, '', $class);
            // Translate namespace structure into directory structure
            $translated_path = str_replace('\\', '/', $relative_NS);
            // Load class suffixed by ".class.php"
            require __DIR__ . '/' . $translated_path . '.php';
        }
    }

    /**
     * @param array $scenario
     */
    private function __construct(array $scenario)
    {
        define('TEMP_DIR', __DIR__.'/../temp/');
        $this->backup       = BackupFactory::build($scenario['backup']);
        $this->transport    = TransportFactory::build($this->backup, $scenario['transport']);
    }

    public function send()
    {
        $this->transport->send();
    }

    public static function launch($scenario)
    {
        // add autoloader
        static::register();

        // check the given scenario
        if (is_readable($scenario)) {
            $scenario = json_decode(file_get_contents($scenario), true);
            if (!is_null($scenario) && static::isValid($scenario)) {
                $scenario = new self($scenario);
                $scenario->send();
            }
            throw new \Exception('invalid scenario.');
        }
        throw new \Exception('scenario not found.');
    }

    public static function isValid(array $scenario)
    {
        return
            isset($scenario['backup']) &&
            count($scenario['backup']) === 1 &&
            isset($scenario['transport']) &&
            count($scenario['transport']) === 1;
    }

}