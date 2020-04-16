<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Shikiryu\Backup\Backup\Files;
use Shikiryu\Exceptions\BackupException;

class FilesTest extends TestCase
{
    public function testIsValid()
    {
        $files = [
            __DIR__ . '/file1',
            __DIR__ . '/file2',
        ];
        foreach ($files as $file) {
            touch($file);
        }
        $file = new Files([
            'files' => $files
        ]);
        $this->assertTrue($file->isValid());
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function testIsNotValid()
    {
        $file = new Files([
            'files' => [
                '/file1',
                '/file2',
            ],
        ]);
        $this->assertFalse($file->isValid());
    }

    public function testInvalidConstruct()
    {
        $this->expectException(BackupException::class);
        $file = new Files([]);
    }
}
