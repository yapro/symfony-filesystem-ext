<?php

declare(strict_types=1);

namespace YaPro\SymfonyFilesystemExt;

use Psr\Log\LoggerInterface;

class ImageManager
{
    private FileManager $fileManager;

    public function __construct(FileManager $fileManager = null, LoggerInterface $logger = null)
    {
        $this->fileManager = $fileManager ?? new FileManager($logger);
    }

    /**
     * @param string $text
     * @param string $ignoreDomain
     * @param bool   $isOnlyHttpAddress
     *
     * @return array в котором ключ это адрес, который нужно заменять в тексте, а значение это адрес по которому нужно
     *               скачивать изображение
     */
    public function getImagesAddress(string $text, string $ignoreDomain = '', $isOnlyHttpAddress = true): array
    {
        preg_match_all('/img src="(.+)"/sUu', $text, $imagesAddressInText);
        if (empty($imagesAddressInText['1'])) {
            return [];
        }
        $result = [];
        foreach ($imagesAddressInText['1'] as $imageRealAddress) {
            // переменная для замены (должна быть оригинальной)
            $result[$imageRealAddress] = $this->fileManager->getFileAddress(
                $imageRealAddress,
                $ignoreDomain,
                $isOnlyHttpAddress
            );
        }

        return array_filter($result);
    }
}
