<?php

namespace Shikiryu\Backup;

use Shikiryu\Backup\Backup\Factory as BackupFactory;
use Shikiryu\Backup\Transport\Factory as TransportFactory;

class Scenario {

    private $backup;
    private $to;

    /**
     * @param array $scenario
     */
    private function __construct(array $scenario)
    {
        $this->backup   = BackupFactory::build($scenario['backup']);
        $this->to       = TransportFactory::build($scenario['to']);
    }


    public static function launch($scenario)
    {
        if (is_readable($scenario)) {
            $scenario = json_decode(file_get_contents($scenario), true);
            if (static::isValid($scenario)) {
                $scenario = new self($scenario);
            }
            throw new \Exception('invalid scenario.');
        }
        throw new \Exception('scenario not found.');
    }

    public static function isValid(\StdClass $scenario)
    {
        return
            isset($scenario->backup) &&
            count($scenario->backup) === 1 &&
            isset($scenario->to) &&
            count($scenario->to) === 1;
    }

}