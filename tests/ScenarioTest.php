<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Shikiryu\Backup\Scenario;
use Shikiryu\Exceptions\ScenarioException;
use Shikiryu\Exceptions\ScenarioNotFoundException;

final class ScenarioTest extends TestCase
{
    private $scenario_path = __DIR__.'/scenario.json';

    protected function setUp(): void
    {
        $this->scenario_path = __DIR__.'/scenario.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->scenario_path)) {
            unlink($this->scenario_path);
        }
    }

    public function testLaunchWithNotFoundFileScenario()
    {
        $this->expectException(ScenarioNotFoundException::class);
        Scenario::launch('file_not_found.json');
    }

    /**
     * @ignore
     */
    /*public function testNoComposerInstallDone()
    {
        $this->expectException(\Shikiryu\Exceptions\ComposerNotFoundException::class);
        rename(__DIR__ . '/../vendor', __DIR__ . '/../vendor');
        Scenario::launch('file_not_found.json');
        rename(__DIR__ . '/../vendor', __DIR__ . '/../vendor');
    }*/

    public function testInvalidScenarioWithNoBackup()
    {
        $this->expectException(ScenarioException::class);
        file_put_contents($this->scenario_path, json_encode([
            'transport' => [
                'Folder' => [
                    'folder' => '/test',
                ]
            ]
        ]));
        Scenario::launch($this->scenario_path);
    }

    public function testInvalidScenarioWithNoTransport()
    {
        $this->expectException(ScenarioException::class);
        file_put_contents($this->scenario_path, json_encode([
            'backup' => [
                'SFTP' => [
                    'host' => 'localhost',
                    'login' => 'login',
                    'password' => 'password',
                    'files' => [
                        '/home/login/file1',
                        '/home/login/file2'
                    ]
                ]
            ]
        ]));
        Scenario::launch($this->scenario_path);
    }

    public function testIsNotValid()
    {
        $is_valid = ['backup' => [], 'transport' => []];
        $is_valid = Scenario::isValid($is_valid);
        $this->assertFalse($is_valid);
    }

    public function testIsValid()
    {
        $is_valid = ['backup' => ['SFTP' => [
            'host' => 'localhost',
            'login' => 'login',
            'password' => 'password',
            'files' => [
                '/home/login/file1',
                '/home/login/file2'
            ]
        ]], 'transport' => ['Folder' => [
            'folder' => '/test',
        ]]];
        $is_valid = Scenario::isValid($is_valid);
        $this->assertTrue($is_valid);
    }
}
