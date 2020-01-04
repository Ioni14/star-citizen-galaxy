<?php

namespace App\Controller\Ships;

use App\Entity\HoldedShip;
use App\Entity\Ship;
use App\Form\Dto\HoldedShipDto;
use App\Form\Dto\ShipDto;
use App\Form\Type\ShipForm;
use App\Repository\ShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\FileBinary;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    private ShipRepository $shipRepository;
    private EntityManagerInterface $entityManager;
    private FilesystemInterface $picturesFilesystem;
    private FilesystemInterface $thumbnailsFilesystem;
    private FilterManager $filterManager;
    private LoggerInterface $logger;

    public function __construct(
        ShipRepository $shipRepository,
        EntityManagerInterface $entityManager,
        FilesystemInterface $shipsPicturesFilesystem,
        FilesystemInterface $shipsThumbnailsFilesystem,
        FilterManager $filterManager,
        LoggerInterface $logger
    ) {
        $this->shipRepository = $shipRepository;
        $this->entityManager = $entityManager;
        $this->picturesFilesystem = $shipsPicturesFilesystem;
        $this->thumbnailsFilesystem = $shipsThumbnailsFilesystem;
        $this->filterManager = $filterManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/ships/edit/{slug}", name="ships_edit", methods={"GET","POST"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        /** @var Ship $ship */
        $ship = $this->shipRepository->findOneBy(['slug' => $slug]);
        if ($ship === null) {
            throw new NotFoundHttpException('Ship not found.');
        }

        $shipDto = new ShipDto(
            $ship->getName(),
            $ship->getChassis(),
            array_map(static function (HoldedShip $holdedShip): HoldedShipDto {
                return new HoldedShipDto($holdedShip->getHolded(), $holdedShip->getQuantity());
            }, $ship->getHoldedShips()->toArray()),
            $ship->getHeight(),
            $ship->getLength(),
            $ship->getMinCrew(),
            $ship->getMaxCrew(),
            $ship->getSize(),
            $ship->getReadyStatus(),
            $ship->getFocus(),
            $ship->getPledgeUrl(),
            $ship->getPicturePath(),
            $ship->getThumbnailPath(),
            $ship->getPrice(),
        );
        $form = $this->createForm(ShipForm::class, $shipDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ship
                ->setName($shipDto->name)
                ->setChassis($shipDto->chassis)
                ->setHeight($shipDto->height)
                ->setLength($shipDto->length)
                ->setMinCrew($shipDto->minCrew)
                ->setMaxCrew($shipDto->maxCrew)
                ->setSize($shipDto->size)
                ->setReadyStatus($shipDto->readyStatus)
                ->setFocus($shipDto->focus)
                ->setPledgeUrl($shipDto->pledgeUrl)
                ->setPrice($shipDto->price);

            $this->computeHoldedShips($ship, $shipDto);

            if ($shipDto->picture !== null) {
                $path = $this->handleFile($shipDto->picture, $ship->getSlug(), $ship->getPicturePath(), 'pictures', $this->picturesFilesystem);
                $ship->setPicturePath($path);
            }
            if ($shipDto->thumbnail !== null) {
                $path = $this->handleFile($shipDto->thumbnail, $ship->getSlug(), $ship->getThumbnailPath(), 'thumbnails', $this->thumbnailsFilesystem);
                $ship->setThumbnailPath($path);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'The ship has been successfully modified.');

            return $this->redirectToRoute('ships_edit', ['slug' => $ship->getSlug()]);
        }

        return $this->render('ships/edit.html.twig', [
            'ship' => $ship,
            'form' => $form->createView(),
        ]);
    }

    private function handleFile(UploadedFile $file, string $slug, ?string $oldPath, string $filter, FilesystemInterface $filesystem): string
    {
        // TODO : MOM!!
        $filteredFile = $this->filterManager->applyFilter(new FileBinary(
            $file->getRealPath(),
            $file->getMimeType(),
            $file->guessExtension(),
        ), $filter);

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
            throw new \RuntimeException(sprintf('Unable to write file %s to Ships images filesystem.', $file->getRealPath()));
        }

        return $path;
    }

    private function computeHoldedShips(Ship $ship, ShipDto $shipDto): void
    {
        // remove and change quantities
        foreach ($ship->getHoldedShips() as $holdedShip) {
            $found = false;
            foreach ($shipDto->holdedShips as $holdedShipDto) {
                if ($holdedShipDto->ship->getId()->equals($holdedShip->getHolded()->getId())) {
                    $found = true;
                    $holdedShip->setQuantity($holdedShipDto->quantity);
                    break;
                }
            }
            if (!$found) {
                $this->entityManager->remove($holdedShip);
                $ship->removeHoldedShip($holdedShip);
            }
        }
        // add new holded ships
        foreach ($shipDto->holdedShips as $holdedShipDto) {
            $found = false;
            foreach ($ship->getHoldedShips() as $holdedShip) {
                if ($holdedShipDto->ship->getId()->equals($holdedShip->getHolded()->getId())) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $ship->addHoldedShip(new HoldedShip($ship, $holdedShipDto->ship, $holdedShipDto->quantity));
            }
        }
    }
}
