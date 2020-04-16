<?php

namespace App\Service\Ship;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelper implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function handleFile(UploadedFile $file, string $slug, ?string $oldPath, FilesystemInterface $filesystem): string
    {
        if ($oldPath !== null) {
            try {
                // TODO : MOM!!
                $filesystem->delete($oldPath);
            } catch (FileNotFoundException $e) {
                $this->logger->error('[Filesystem] Unable to delete {path}.', ['exception' => $e, 'path' => $oldPath]);
            }
            $this->cacheManager->remove($oldPath);
        }

        $contents = file_get_contents($file->getPathname());
        $path = $slug.'.'.substr(sha1($contents), 0, 8).'.'.$file->guessExtension();
        try {
            // TODO : MOM!!
            $result = $filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            $result = false;
        }
        if (!$result) {
            $this->logger->error('[Filesystem] Unable to delete {path}.', ['path' => $path]);
        }

        $result = $filesystem->write($path, $contents);
        if (!$result) {
            throw new \RuntimeException(sprintf('Unable to write file %s to images filesystem.', $file->getPathname()));
        }

        $this->cacheManager->remove($path);

        return $path;
    }
}
