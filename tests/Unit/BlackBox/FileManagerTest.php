<?php

declare(strict_types=1);

namespace YaPro\SymfonyFilesystemExt\Tests\Unit\BlackBox;

use PHPUnit\Framework\TestCase;
use YaPro\SymfonyFilesystemExt\FileManager;

class FileManagerTest extends TestCase
{
    private static FileManager $fileManager;

    public static function setUpBeforeClass(): void
    {
        self::$fileManager = new FileManager();
    }

    public function providerGetFileAddress(): array
    {
        $baseSet = [
            [
                'fileAddress' => '#hash"',
                'ignoreDomain' => '',
                'expected' => '',
            ],
            [
                'fileAddress' => '#',
                'ignoreDomain' => '',
                'expected' => '',
            ],
            [
                'fileAddress' => '/',
                'ignoreDomain' => '',
                'expected' => '',
            ],
            [
                'fileAddress' => '',
                'ignoreDomain' => '',
                'expected' => '',
            ],
        ];
        $getData = function (string $fileAddressPrefix = 'http') {
            $fileAddress = $fileAddressPrefix . '://other.com/media/images/По ту сторону кровати.2008.(Rus).SILVERFILM.&amp;.ShareReactor.ru.0-01-35.8499541.jpg';
            $expected = $fileAddressPrefix . '://other.com/media/images/%D0%9F%D0%BE%20%D1%82%D1%83%20%D1%81%D1%82%D0%BE%D1%80%D0%BE%D0%BD%D1%83%20%D0%BA%D1%80%D0%BE%D0%B2%D0%B0%D1%82%D0%B8.2008.(Rus).SILVERFILM.&.ShareReactor.ru.0-01-35.8499541.jpg';

            return [
                [
                    'fileAddress' => $fileAddress,
                    'ignoreDomain' => '',
                    'expected' => $expected,
                ],
                [
                    'fileAddress' => $fileAddress . '#hash',
                    'ignoreDomain' => '',
                    'expected' => $expected,
                ],
                [
                    'fileAddress' => $fileAddressPrefix . '://other.com/path/to/file.ext',
                    'ignoreDomain' => 'my.com',
                    'expected' => $fileAddressPrefix . '://other.com/path/to/file.ext',
                ],
                [
                    'fileAddress' => $fileAddressPrefix . '://my.com/path/to/file.ext',
                    'ignoreDomain' => 'my.com',
                    'expected' => '',
                ],
            ];
        };

        return array_merge($baseSet, array_merge($getData(), $getData('https')));
    }

    /**
     * @dataProvider providerGetFileAddress
     *
     * @param string $fileAddress
     * @param string $ignoreDomain
     * @param string $expected
     */
    public function testGetFileAddress(string $fileAddress, string $ignoreDomain, string $expected)
    {
        $result = self::$fileManager->getFileAddress($fileAddress, $ignoreDomain);
        $this->assertSame($expected, $result);
    }

    public function testMoveToFolder()
    {
        $expectedFolder = '/tmp/' . self::$fileManager->getUniqString() . '/' . self::$fileManager->getUniqString();
        $expectedExt = '.png';
        $filePathWithWrongExt = '/tmp/logo.gif';
        copy(__DIR__ . '/logo' . $expectedExt, $filePathWithWrongExt);
        $filePathWithRightExt = self::$fileManager->moveToFolder($filePathWithWrongExt, $expectedFolder);
        $this->assertFileExists($filePathWithRightExt);
        $this->assertSame(0, mb_strpos($filePathWithRightExt, $expectedFolder));
        $this->assertSame($expectedExt, mb_substr($filePathWithRightExt, -4));
    }

    public function testGetFilePathWithoutLeftPart()
    {
        $start = 'start_';
        $end = 'end';
        $result = self::$fileManager->getFilePathWithoutLeftPart($start, $start . $end);
        $this->assertSame($end, $result);
    }
}
