<?php

namespace App\Controller\ShipChassis;

use App\Repository\ShipChassisRepository;
use App\Repository\ShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DetailsController extends AbstractController
{
    private ShipChassisRepository $shipChassisRepository;
    private ShipRepository $shipRepository;
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(
        ShipChassisRepository $shipChassisRepository,
        ShipRepository $shipRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->shipChassisRepository = $shipChassisRepository;
        $this->shipRepository = $shipRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/ship-chassis/details/{slug}", name="ship_chassis_details", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $shipChassis = $this->shipChassisRepository->findOneBy(['slug' => $slug]);
        if ($shipChassis === null) {
            throw new NotFoundHttpException('Manufacturer not found.');
        }
        $ships = $this->shipRepository->findBy(['chassis' => $shipChassis]);

        $logs = [];
        if ($this->security->isGranted('ROLE_MODERATOR')) {
            $logsQuery = $this->entityManager->getRepository(LogEntry::class)->getLogEntriesQuery($shipChassis);
            $logsQuery->setMaxResults(10);
            $logs = $logsQuery->getResult();
        }

        return $this->render('ship_chassis/details.html.twig', [
            'chassis' => $shipChassis,
            'ships' => $ships,
            'last_logs' => $logs,
        ]);
    }
}
