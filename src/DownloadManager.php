<?php

declare(strict_types=1);

namespace YaPro\SymfonyFilesystemExt;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;

class DownloadManager
{
    private Filesystem $filesystem;
    private FileManager $fileManager;
    private ?LoggerInterface $logger;

    public function __construct(FileManager $fileManager = null, LoggerInterface $logger = null)
    {
        $this->fileManager = $fileManager ?? new FileManager($logger);
        $this->logger = $logger ?? new NullLogger();
        $this->filesystem = new Filesystem();
    }

    /**
     * @param array<string, string> $files          [originalAddress => urlForDownload]
     * @param bool                  $throwException
     *
     * @return array<string, string> [originalAddress => pathToTmpFile]
     *
     * @throws \Throwable
     */
    public function downloadFiles(array $files, bool $throwException = true): array
    {
        $result = [];
        foreach ($files as $originalAddress => $urlForDownload) {
            // если файл не скачался, пропускаем его и логируем
            try {
                $result[$originalAddress] = $this->downloadFile($urlForDownload);
            } catch (\Throwable $exception) {
                if ($throwException) {
                    throw $exception;
                }
                $this->logger->warning('File not downloaded', [
                    'originalAddress' => $originalAddress,
                    'urlForDownload' => $urlForDownload,
                    'exception' => $exception,
                ]);
            }
        }

        return $result;
    }

    /**
     * @param string $url
     *
     * @return string file path that is located in /tmp and file has unique name
     */
    public function downloadFile(string $url): string
    {
        $content = $this->fileManager->getFileContent($url);
        $uniqFileName = $this->fileManager->getUniqString();
        $uniqFilePath = '/tmp/' . $uniqFileName;
        $this->filesystem->dumpFile($uniqFilePath, $content);

        return $uniqFilePath;
    }
}
