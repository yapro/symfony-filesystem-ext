<?php

declare(strict_types=1);

namespace YaPro\SymfonyFilesystemExt\Tests\Unit\BlackBox;

use PHPUnit\Framework\TestCase;
use YaPro\SymfonyFilesystemExt\ImageManager;

class ImageManagerTest extends TestCase
{
    private static ImageManager $imageManager;

    public static function setUpBeforeClass(): void
    {
        self::$imageManager = new ImageManager();
    }

    public function providerGetImagesAddress(): array
    {
        $imageAddress = 'http://server.with/image/file.ext';

        return [
            [
                'text' => '<h1>my</h1><img src="' . $imageAddress . '#hash"><p>text</p>',
                'expected' => [$imageAddress . '#hash' => $imageAddress],
            ],
            [
                'text' => '<h1>my</h1>><p>text</p>',
                'expected' => [],
            ],
            [
                'text' => '',
                'expected' => [],
            ],
        ];
    }

    /**
     * @dataProvider providerGetImagesAddress
     *
     * @param string $text
     * @param array  $expected
     */
    public function testGetImagesAddress(string $text, array $expected)
    {
        $result = self::$imageManager->getImagesAddress($text);
        $this->assertSame($expected, $result);
    }
}
