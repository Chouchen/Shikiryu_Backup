<?php

namespace Shikiryu\Backup;

use Shikiryu\Backup\Backup\BackupAbstract;
use Shikiryu\Backup\Transport\TransportAbstract;
use Shikiryu\Backup\Backup\Factory as BackupFactory;
use Shikiryu\Backup\Transport\Factory as TransportFactory;

class Scenario
{
    /* @var $backup BackupAbstract */
    private $backup;
    /* @var $to TransportAbstract */
    private $transport;

    public static function register()
    {
        include __DIR__.'/../../vendor/autoload.php';
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

    /**
     * check if backup is valid and then launch the transfer
     *
     * @see BackupAbstract::isValid
     * @see TransportAbstract::send
     *
     * @throws \Exception
     */
    public function send()
    {
        if ($this->backup->isValid()) {
            $this->transport->send();
        } else {
            throw new \Exception('Backup configuration is invalid.');
        }
    }

    /**
     * Launch the whole job
     *
     * @param $scenario
     *
     * @throws \Exception
     */
    public static function launch($scenario)
    {
        // add autoloader
        static::register();

        // check the given scenario
        if (is_readable($scenario)) {
            $scenario = json_decode(file_get_contents($scenario), true);
            if ($scenario !== null && static::isValid($scenario)) {
                try {
                    $scenario = new self($scenario);
                    $scenario->send();
                } catch (\Exception $e) {
                    throw $e;
                }
                exit;
            }
            throw new \Exception('invalid scenario.');
        }
        throw new \Exception('scenario not found.');
    }

    /**
     * Check given scenario validation
     *
     * @param array $scenario
     *
     * @return bool
     */
    public static function isValid(array $scenario)
    {
        return
            isset($scenario['backup'], $scenario['transport']) &&
            count($scenario['backup']) === 1 &&
            count($scenario['transport']) === 1;
    }
}
