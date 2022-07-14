<?php

declare(strict_types=1);

namespace YaPro\SymfonyFilesystemExt\Tests\Unit\BlackBox;

use PHPUnit\Framework\TestCase;
use YaPro\SymfonyFilesystemExt\DownloadManager;

class DownloadManagerTest extends TestCase
{
    private static DownloadManager $downloadManager;

    public static function setUpBeforeClass(): void
    {
        self::$downloadManager = new DownloadManager();
    }

    public function testDownloadFile()
    {
        // $file = __DIR__ . '/logo.png';
        // $fileInTmpFolder = self::$fileManager->downloadFile($file);
        // $this->assertFileExists($fileInTmpFolder);
        // $this->assertSame(filesize($file), filesize($fileInTmpFolder));
        // https://web-dev.imgix.net/image/T4FyVKpzu4WKF1kBNvXepbi08t52/WOL6PIpIQHpqsgtKwbJv.png?auto=format
        // https://gblobscdn.gitbook.com/assets%2F-LcLcwJZuHK_1ijBGmcN%2F-LcLcybPiD1fXt9tv9MC%2F-LcLd-s1lguMVuyb1Loz%2Fcgi.jpeg

        $fileInTmpFolder = self::$downloadManager->downloadFile('https://studref.com/htm/img/12/8041/15.png');
        $this->assertFileExists($fileInTmpFolder);
    }

    public function uploadFile()
    {
        // Symfony HttpClient upload files:
        // https://github.com/symfony/symfony/issues/35443
        // https://github.com/symfony/symfony/issues/37500
        // https://symfony.com/doc/current/http_client.html
    }
}
