<?php

namespace App\Controller\Ships;

use App\Entity\Ship;
use App\Form\Dto\ShipDto;
use App\Form\Type\ShipForm;
use App\Service\Ship\FileHelper;
use App\Service\Ship\HoldedShipsHelper;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\LoggableListener;
use League\Flysystem\FilesystemInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FilesystemInterface $picturesFilesystem;
    private FilesystemInterface $thumbnailsFilesystem;
    private HoldedShipsHelper $holdedShipsHelper;
    private FileHelper $fileHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemInterface $shipsPicturesFilesystem,
        FilesystemInterface $shipsThumbnailsFilesystem,
        HoldedShipsHelper $holdedShipsHelper,
        FileHelper $fileHelper
    ) {
        $this->entityManager = $entityManager;
        $this->picturesFilesystem = $shipsPicturesFilesystem;
        $this->thumbnailsFilesystem = $shipsThumbnailsFilesystem;
        $this->holdedShipsHelper = $holdedShipsHelper;
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Route("/ships/create", name="ships_create", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request): Response
    {
        $shipDto = new ShipDto();
        $form = $this->createForm(ShipForm::class, $shipDto, [
            'mode' => ShipForm::MODE_CREATE,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ship = (new Ship(Uuid::uuid4()))
                ->setName($shipDto->name)
                ->setChassis($shipDto->chassis)
                ->setHeight($shipDto->height)
                ->setLength($shipDto->length)
                ->setBeam($shipDto->beam)
                ->setMinCrew($shipDto->minCrew)
                ->setMaxCrew($shipDto->maxCrew)
                ->setSize($shipDto->size)
                ->setCargoCapacity($shipDto->cargoCapacity)
                ->setReadyStatus($shipDto->readyStatus)
                ->setCareer($shipDto->career)
                ->setPledgeUrl($shipDto->pledgeUrl)
                ->setStandalonePrice($shipDto->standalonePrice)
                ->setWarbondPrice($shipDto->warbondPrice);

            foreach ($shipDto->roles as $role) {
                $ship->addRole($role);
            }

            $this->holdedShipsHelper->computeHoldedShips($ship, $shipDto);

            try {
                $this->entityManager->beginTransaction();
                $this->entityManager->persist($ship);
                $this->entityManager->flush(); // we need the generated Slug of the ship

                if ($shipDto->picture !== null) {
                    $path = $this->fileHelper->handleFile($shipDto->picture, $ship->getSlug(), $ship->getPicturePath(), 'pictures', $this->picturesFilesystem);
                    $ship->setPicturePath($path);
                }
                if ($shipDto->thumbnail !== null) {
                    $path = $this->fileHelper->handleFile($shipDto->thumbnail, $ship->getSlug(), $ship->getThumbnailPath(), 'thumbnails', $this->thumbnailsFilesystem);
                    $ship->setThumbnailPath($path);
                }
                $this->entityManager->flush();
                $dql = 'DELETE FROM Gedmo\Loggable\Entity\LogEntry e WHERE e.objectId = :oid AND e.action = :action';
                $this->entityManager->createQuery($dql)->setParameters([
                    'oid' => $ship->getId()->toString(),
                    'action' => LoggableListener::ACTION_UPDATE,
                ])->execute();

                $this->entityManager->commit();
            } catch (\Exception $e) {
                $this->entityManager->rollback();
                throw $e;
            }

            $this->addFlash('success', 'The ship has been successfully created.');

            return $this->redirectToRoute('ships_details', ['slug' => $ship->getSlug()]);
        }

        return $this->render('ships/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
