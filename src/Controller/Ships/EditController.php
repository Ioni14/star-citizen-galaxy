<?php

namespace App\Controller\Ships;

use App\Entity\HoldedShip;
use App\Entity\LoanerShip;
use App\Entity\Ship;
use App\Form\Dto\HoldedShipDto;
use App\Form\Dto\LoanerShipDto;
use App\Form\Dto\ShipDto;
use App\Form\Type\ShipForm;
use App\Repository\ShipRepository;
use App\Service\LockHelper;
use App\Service\Ship\FileHelper;
use App\Service\Ship\HoldedShipsHelper;
use App\Service\Ship\LoanerShipsHelper;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use League\Flysystem\FilesystemInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
    private LoanerShipsHelper $loanerShipsHelper;
    private FileHelper $fileHelper;
    private LockHelper $lockHelper;

    public function __construct(
        ShipRepository $shipRepository,
        EntityManagerInterface $entityManager,
        FilesystemInterface $shipsPicturesFilesystem,
        FilesystemInterface $shipsThumbnailsFilesystem,
        HoldedShipsHelper $holdedShipsHelper,
        LoanerShipsHelper $loanerShipsHelper,
        FileHelper $fileHelper,
        LockHelper $lockHelper
    ) {
        $this->shipRepository = $shipRepository;
        $this->entityManager = $entityManager;
        $this->picturesFilesystem = $shipsPicturesFilesystem;
        $this->thumbnailsFilesystem = $shipsThumbnailsFilesystem;
        $this->holdedShipsHelper = $holdedShipsHelper;
        $this->loanerShipsHelper = $loanerShipsHelper;
        $this->fileHelper = $fileHelper;
        $this->lockHelper = $lockHelper;
    }

    /**
     * @Route("/ships/edit/{slug}", name="ships_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request, string $slug): Response
    {
        /** @var Ship $ship */
        $ship = $this->shipRepository->findOneBy(['slug' => $slug]);
        if ($ship === null) {
            throw new NotFoundHttpException('Ship not found.');
        }

        /** @var LogEntry $lastLog */
        $lastLog = $this->entityManager->getRepository(LogEntry::class)->getLogEntriesQuery($ship)->setMaxResults(1)->getOneOrNullResult();
        $lastVersion = $lastLog !== null ? $lastLog->getVersion() : 0;

        $lockedBy = $this->lockHelper->acquireLock($ship);
        $lockedByMe = $lockedBy !== null && $lockedBy->getId()->equals($this->getUser()->getId());
        if ($lockedByMe) {
            $this->entityManager->refresh($ship);
        }

        $shipDto = new ShipDto(
            $ship->getName(),
            $ship->getChassis(),
            array_map(static function (HoldedShip $holdedShip): HoldedShipDto {
                return new HoldedShipDto($holdedShip->getHolded(), $holdedShip->getQuantity());
            }, $ship->getHoldedShips()->toArray()),
            array_map(static function (LoanerShip $loanerShip): LoanerShipDto {
                return new LoanerShipDto($loanerShip->getLoaned(), $loanerShip->getQuantity());
            }, $ship->getLoanerShips()->toArray()),
            $ship->getHeight(),
            $ship->getLength(),
            $ship->getBeam(),
            $ship->getMinCrew(),
            $ship->getMaxCrew(),
            $ship->getSize(),
            $ship->getCargoCapacity(),
            $ship->getCareer(),
            $ship->getRoles()->toArray(),
            $ship->getReadyStatus(),
            $ship->getPledgeUrl(),
            $ship->getPicturePath(),
            $ship->getThumbnailPath(),
            $ship->getPledgeCost(),
            $lastVersion,
            $lastVersion,
        );
        $form = $this->createForm(ShipForm::class, $shipDto, [
            'disabled' => !$lockedByMe,
            'mode' => ShipForm::MODE_EDIT,
        ]);
        $form->handleRequest($request);
        if ($lockedByMe && $form->isSubmitted() && $form->isValid()) {
            $ship
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
                ->setPledgeCost($shipDto->pledgeCost);

            $ship->clearRoles();
            foreach ($shipDto->roles as $role) {
                $ship->addRole($role);
            }

            $this->holdedShipsHelper->computeHoldedShips($ship, $shipDto);
            $this->loanerShipsHelper->computeLoanerShips($ship, $shipDto);

            if ($shipDto->picture !== null) {
                $path = $this->fileHelper->handleFile($shipDto->picture, $ship->getSlug(), $ship->getPicturePath(), $this->picturesFilesystem);
                $ship->setPicturePath($path);
            }
            if ($shipDto->thumbnail !== null) {
                $path = $this->fileHelper->handleFile($shipDto->thumbnail, $ship->getSlug(), $ship->getThumbnailPath(), $this->thumbnailsFilesystem);
                $ship->setThumbnailPath($path);
            }

            $this->entityManager->flush();

            $this->lockHelper->releaseLock($ship);
            $this->addFlash('success', 'The ship has been successfully modified.');

            return $this->redirectToRoute('ships_details', ['slug' => $ship->getSlug()]);
        }

        return $this->render('ships/edit.html.twig', [
            'locked_by' => $lockedBy,
            'locked_by_me' => $lockedByMe,
            'ship' => $ship,
            'form' => $form->createView(),
        ]);
    }
}
