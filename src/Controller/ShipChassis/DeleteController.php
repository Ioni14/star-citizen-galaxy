<?php

namespace App\Controller\ShipChassis;

use App\Entity\ShipChassis;
use App\Repository\ShipChassisRepository;
use App\Repository\ShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ShipChassisRepository $shipChassisRepository;
    private ShipRepository $shipRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShipChassisRepository $shipChassisRepository,
        ShipRepository $shipRepository
    ) {
        $this->entityManager = $entityManager;
        $this->shipChassisRepository = $shipChassisRepository;
        $this->shipRepository = $shipRepository;
    }

    /**
     * @Route("/ships-chassis/delete/{chassisId}", name="ship_chassis_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function __invoke(Request $request, string $chassisId): Response
    {
        /** @var ShipChassis|null $shipChassis */
        $shipChassis = $this->shipChassisRepository->find($chassisId);
        if ($shipChassis === null) {
            $this->addFlash('danger', 'The ship chassis does not exist.');

            return $this->redirectToRoute('ship_chassis_list');
        }

        $countShips = $this->shipRepository->countShipsByChassis($shipChassis->getId());
        if ($countShips > 0) {
            $this->addFlash('danger', "Unable to delete the ship chassis {$shipChassis->getName()} because it's still used by $countShips ships.");

            return $this->redirectToRoute('ship_chassis_list');
        }

        $this->entityManager->remove($shipChassis);
        $this->entityManager->flush();

        $this->addFlash('success', "The ship chassis {$shipChassis->getName()} has been successfully deleted.");

        return $this->redirectToRoute('ship_chassis_list');
    }
}
