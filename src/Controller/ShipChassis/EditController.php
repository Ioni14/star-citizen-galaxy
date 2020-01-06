<?php

namespace App\Controller\ShipChassis;

use App\Entity\ShipChassis;
use App\Form\Dto\ShipChassisDto;
use App\Form\Type\ShipChassisForm;
use App\Repository\ShipChassisRepository;
use App\Service\LockHelper;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    private ShipChassisRepository $shipChassisRepository;
    private EntityManagerInterface $entityManager;
    private LockHelper $lockHelper;

    public function __construct(
        ShipChassisRepository $shipChassisRepository,
        EntityManagerInterface $entityManager,
        LockHelper $lockHelper
    ) {
        $this->shipChassisRepository = $shipChassisRepository;
        $this->entityManager = $entityManager;
        $this->lockHelper = $lockHelper;
    }

    /**
     * @Route("/ship-chassis/edit/{slug}", name="ship_chassis_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request, string $slug): Response
    {
        /** @var ShipChassis $shipChassis */
        $shipChassis = $this->shipChassisRepository->findOneBy(['slug' => $slug]);
        if ($shipChassis === null) {
            throw new NotFoundHttpException('Ship chassis not found.');
        }

        /** @var LogEntry $lastLog */
        $lastLog = $this->entityManager->getRepository(LogEntry::class)->getLogEntriesQuery($shipChassis)->setMaxResults(1)->getOneOrNullResult();
        $lastVersion = $lastLog !== null ? $lastLog->getVersion() : 0;

        $lockedBy = $this->lockHelper->acquireLock($shipChassis);
        $lockedByMe = $lockedBy !== null && $lockedBy->getId()->equals($this->getUser()->getId());
        if ($lockedByMe) {
            $this->entityManager->refresh($shipChassis);
        }

        $shipChassisDto = new ShipChassisDto(
            $shipChassis->getName(),
            $shipChassis->getManufacturer(),
            $lastVersion,
            $lastVersion,
        );
        $form = $this->createForm(ShipChassisForm::class, $shipChassisDto, [
            'disabled' => !$lockedByMe,
        ]);
        $form->handleRequest($request);
        if ($lockedByMe && $form->isSubmitted() && $form->isValid()) {
            $shipChassis
                ->setName($shipChassisDto->name)
                ->setManufacturer($shipChassisDto->manufacturer);

            $this->entityManager->flush();

            $this->lockHelper->releaseLock($shipChassis);
            $this->addFlash('success', 'The Ship chassis has been successfully modified.');

            return $this->redirectToRoute('ship_chassis_details', ['slug' => $shipChassis->getSlug()]);
        }

        return $this->render('ship_chassis/edit.html.twig', [
            'locked_by' => $lockedBy,
            'locked_by_me' => $lockedByMe,
            'chassis' => $shipChassis,
            'form' => $form->createView(),
        ]);
    }
}
