<?php

declare(strict_types=1);

namespace YaPro\SymfonyFilesystemExt;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class FileManager
{
    private Filesystem $filesystem;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->filesystem = new Filesystem();
        $this->logger = $logger ?? new NullLogger();
    }

    public function getUniqString(): string
    {
        return sha1(random_bytes(10));
    }

    public function getFileContent(string $fileUrl): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_REFERER, $fileUrl); // for bypass the referer check
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible;)'); // for bypass the user agent check
        curl_setopt($curl, CURLOPT_URL, $fileUrl); // urlencode($fileUrl)
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // for curl_exec return result (not status)
        $result = curl_exec($curl);
        curl_close($curl);
        if (false === $result) {
            throw new \UnexpectedValueException('Problem to download file');
        }
        if (empty($result)) {
            throw new \UnexpectedValueException('File content is empty');
        }

        return $result;
    }

    public function removeLeftPart(string $leftPart, array $files): array
    {
        foreach ($files as $originalAddress => $filePath) {
            $files[$originalAddress] = $this->getFilePathWithoutLeftPart($leftPart, $filePath);
        }

        return $files;
    }

    public function getFilePathWithoutLeftPart(string $leftPart, string $filePath): string
    {
        $pos = mb_strpos($filePath, $leftPart);
        if ($pos !== 0) {
            return $filePath;
        }

        return mb_substr($filePath, mb_strlen($leftPart));
    }

    /**
     * Move and rename files to folder/UniqString-H-i-s.ExtByContent
     *
     * @param array  $files
     * @param string $folderPath
     *
     * @return array
     */
    public function movesToFolder(array $files, string $folderPath): array
    {
        $result = [];
        foreach ($files as $originalAddress => $filePath) {
            $result[$originalAddress] = $this->moveToFolder($filePath, $folderPath);
        }

        return $result;
    }

    /**
     * Move and rename file to folder/UniqString-H-i-s.ExtByContent
     *
     * @param string $filePath
     * @param string $folderPath
     *
     * @return string
     */
    public function moveToFolder(string $filePath, string $folderPath, string $fileName = ''): string
    {
        $folderPathExisted = rtrim($folderPath, '/');
        $this->filesystem->mkdir($folderPathExisted, 0700);
        $file = new File($filePath);
        $fileName = $fileName === '' ? $this->getUniqString() . date('-H-i-s') : $fileName;
        $filePathNew = $folderPathExisted . '/' . $fileName . '.' . $file->guessExtension();
        $this->filesystem->rename($filePath, $filePathNew);

        return $filePathNew;
    }

    public function getFileAddress(string $fileAddress, string $ignoreDomain = '', $isOnlyHttpAddress = true): string
    {
        // избавляемся от знака # и всего что правее данного знака
        $e = explode('#', $fileAddress);
        $fileAddressWithoutHashSign = trim($e['0']);

        // игнорируем то, что не следует скачивать
        if ($isOnlyHttpAddress &&
            mb_substr($fileAddressWithoutHashSign, 0, 7) !== 'http://' &&
            mb_substr($fileAddressWithoutHashSign, 0, 8) !== 'https://'
        ) {
            return '';
        }

        if (!empty($ignoreDomain) && $info = parse_url($fileAddressWithoutHashSign)) {
            if (isset($info['host']) && $info['host'] === $ignoreDomain) {
                return '';
            }
        }
        // приводим в порядок спец. символы в адресе, без этого файл не скачать
        return
            str_replace('%28', '(',
                str_replace('%29', ')',
                    str_replace('%26amp%3B', '&',
                        str_replace('%2F', '/',
                            str_replace('%3A', ':', rawurlencode($fileAddressWithoutHashSign))))));
    }
}
