<?php

namespace App\Serializer;

use App\Entity\Manufacturer;
use App\Entity\Ship;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class FileFieldsNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;
    private CacheManager $cacheManager;

    public function __construct(NormalizerInterface $decorated, CacheManager $cacheManager)
    {
        $this->decorated = $decorated;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param Ship        $object
     * @param string|null $format
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->decorated->normalize($object, $format, $context);
        if (!is_array($data)) {
            return $data;
        }

        if ($object instanceof Ship) {
            $data['pictureUri'] = $object->getPicturePath() !== null ? $this->cacheManager->getBrowserPath($object->getPicturePath(), 'pictures') : null;
            $data['thumbnailUri'] = $object->getThumbnailPath() !== null ? $this->cacheManager->getBrowserPath($object->getThumbnailPath(), 'thumbnails') : null;
        }
        if ($object instanceof Manufacturer) {
            $data['logoUri'] = $object->getLogoPath() !== null ? $this->cacheManager->getBrowserPath($object->getLogoPath(), 'logos') : null;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
