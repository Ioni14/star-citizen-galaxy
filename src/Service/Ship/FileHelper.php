<?php

namespace App\Service\Ship;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\FileBinary;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelper implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private FilterManager $filterManager;

    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    public function handleFile(UploadedFile $file, string $slug, ?string $oldPath, string $filter, FilesystemInterface $filesystem): string
    {
        $filteredFile = new FileBinary(
            $file->getRealPath(),
            $file->getMimeType(),
            $file->guessExtension(),
        );
        if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            // TODO : MOM!!
            $filteredFile = $this->filterManager->applyFilter($filteredFile, $filter);
        }

        if ($oldPath !== null) {
            try {
                // TODO : MOM!!
                $filesystem->delete($oldPath);
            } catch (FileNotFoundException $e) {
                $this->logger->error('[Filesystem] Unable to delete {path}.', ['exception' => $e, 'path' => $oldPath]);
            }
        }
        $path = $slug.'.'.$file->guessExtension();
        try {
            // TODO : MOM!!
            $filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            $this->logger->error('[Filesystem] Unable to delete {path}.', ['exception' => $e, 'path' => $path]);
        }
        $result = $filesystem->write($path, $filteredFile->getContent());
        if (!$result) {
            throw new \RuntimeException(sprintf('Unable to write file %s to images filesystem.', $file->getRealPath()));
        }

        return $path;
    }
}
