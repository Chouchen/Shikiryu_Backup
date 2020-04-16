<?php declare(strict_types=1);

namespace Shikiryu\Backup;

use Shikiryu\Backup\Backup\BackupAbstract;
use Shikiryu\Backup\Transport\TransportAbstract;
use Shikiryu\Backup\Backup\Factory as BackupFactory;
use Shikiryu\Backup\Transport\Factory as TransportFactory;
use Shikiryu\Exceptions\BackupException;
use Shikiryu\Exceptions\ComposerNotFoundException;
use Shikiryu\Exceptions\ScenarioException;
use Shikiryu\Exceptions\ScenarioNotFoundException;

final class Scenario
{
    /* @var $backup BackupAbstract */
    private $backup;
    /* @var $to TransportAbstract */
    private $transport;

    /**
     * @throws ComposerNotFoundException
     */
    public static function register()
    {
        $autoload_file = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoload_file)) {
            include $autoload_file;
        } else {
            throw new ComposerNotFoundException(sprintf('Autoloadfile «%s» not found.', $autoload_file));
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

    /**
     * check if backup is valid and then launch the transfer
     *
     * @return bool
     * @throws BackupException
     * @see BackupAbstract::isValid
     * @see TransportAbstract::send
     *
     */
    public function send(): bool
    {
        if ($this->backup->isValid()) {
            return $this->transport->send();
        }

        throw new BackupException('Backup configuration is invalid.');
    }

    /**
     * Launch the whole job
     *
     * @param $scenario
     *
     * @return bool
     * @throws BackupException
     * @throws ComposerNotFoundException
     * @throws ScenarioException
     * @throws ScenarioNotFoundException
     */
    public static function launch($scenario): bool
    {
        // add autoloader
        static::register();

        // check the given scenario
        if (is_readable($scenario)) {
            $scenario = json_decode(file_get_contents($scenario), true);
            if ($scenario !== null && static::isValid($scenario)) {
                try {
                    $scenario = new self($scenario);
                    return $scenario->send();
                } catch (BackupException $e) {
                    throw $e;
                }
            }
            throw new ScenarioException('invalid scenario.');
        }

        throw new ScenarioNotFoundException(sprintf('scenario «%s» not found.', $scenario));
    }

    /**
     * Check given scenario validation
     *
     * @param array $scenario
     *
     * @return bool
     */
    public static function isValid(array $scenario): bool
    {
        return
            isset($scenario['backup'], $scenario['transport']) &&
            count($scenario['backup']) === 1 &&
            count($scenario['transport']) === 1;
    }
}
