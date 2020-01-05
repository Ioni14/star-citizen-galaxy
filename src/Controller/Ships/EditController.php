<?php

namespace App\Controller\Ships;

use App\Entity\HoldedShip;
use App\Entity\Ship;
use App\Form\Dto\HoldedShipDto;
use App\Form\Dto\ShipDto;
use App\Form\Type\ShipForm;
use App\Repository\ShipRepository;
use App\Service\Ship\FileHelper;
use App\Service\Ship\HoldedShipsHelper;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    private HoldedShipsHelper $holdedShipsHelper;
    private FileHelper $fileHelper;

    public function __construct(
        ShipRepository $shipRepository,
        EntityManagerInterface $entityManager,
        FilesystemInterface $shipsPicturesFilesystem,
        FilesystemInterface $shipsThumbnailsFilesystem,
        HoldedShipsHelper $holdedShipsHelper,
        FileHelper $fileHelper
    ) {
        $this->shipRepository = $shipRepository;
        $this->entityManager = $entityManager;
        $this->picturesFilesystem = $shipsPicturesFilesystem;
        $this->thumbnailsFilesystem = $shipsThumbnailsFilesystem;
        $this->holdedShipsHelper = $holdedShipsHelper;
        $this->fileHelper = $fileHelper;
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

            $this->holdedShipsHelper->computeHoldedShips($ship, $shipDto);

            if ($shipDto->picture !== null) {
                $path = $this->fileHelper->handleFile($shipDto->picture, $ship->getSlug(), $ship->getPicturePath(), 'pictures', $this->picturesFilesystem);
                $ship->setPicturePath($path);
            }
            if ($shipDto->thumbnail !== null) {
                $path = $this->fileHelper->handleFile($shipDto->thumbnail, $ship->getSlug(), $ship->getThumbnailPath(), 'thumbnails', $this->thumbnailsFilesystem);
                $ship->setThumbnailPath($path);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'The ship has been successfully modified.');

            return $this->redirectToRoute('ships_details', ['slug' => $ship->getSlug()]);
        }

        return $this->render('ships/edit.html.twig', [
            'ship' => $ship,
            'form' => $form->createView(),
        ]);
    }
}
