<?php

namespace App\Command;

use App\Entity\Manufacturer;
use App\Entity\Ship;
use App\Repository\ManufacturerRepository;
use App\Repository\ShipRepository;
use Liip\ImagineBundle\Imagine\Cache\Helper\PathHelper;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResolveAllImagesCommand extends Command
{
    protected static $defaultName = 'app:resolve-all-images';

    private FilterService $filterService;
    private ShipRepository $shipRepository;
    private ManufacturerRepository $manufacturerRepository;

    public function __construct(
        FilterService $filterService,
        ShipRepository $shipRepository,
        ManufacturerRepository $manufacturerRepository
    ) {
        parent::__construct();
        $this->filterService = $filterService;
        $this->shipRepository = $shipRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Ship[] $ships */
        $ships = $this->shipRepository->findAll();
        foreach ($ships as $ship) {
            if ($ship->getThumbnailPath() !== null) {
                $url = $this->resolveImage($ship->getThumbnailPath(), 'thumbnails');
                $output->writeln($url);
            }
            if ($ship->getPicturePath() !== null) {
                $url = $this->resolveImage($ship->getPicturePath(), 'pictures');
                $output->writeln($url);
            }
        }

        /** @var Manufacturer[] $manufacturers */
        $manufacturers = $this->manufacturerRepository->findAll();
        foreach ($manufacturers as $manufacturer) {
            if ($manufacturer->getLogoPath() !== null) {
                $url = $this->resolveImage($manufacturer->getLogoPath(), 'logos');
                $output->writeln($url);
            }
        }
        $output->writeln('<info>Done.</info>');

        return 0;
    }

    private function resolveImage(string $path, string $filter): string
    {
        $path = PathHelper::urlPathToFilePath($path);

        return $this->filterService->getUrlOfFilteredImage($path, $filter);
    }
}
