<?php

namespace App\Controller\Ships;

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
    private ShipRepository $shipRepository;
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(
        ShipRepository $shipRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->shipRepository = $shipRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/ships/details/{slug}", name="ships_details", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $ship = $this->shipRepository->findOneShipJoinedChassis($slug);
        if ($ship === null) {
            throw new NotFoundHttpException('Ship not found.');
        }

        $logs = [];
        if ($this->security->isGranted('ROLE_MODERATOR')) {
            $logsQuery = $this->entityManager->getRepository(LogEntry::class)->getLogEntriesQuery($ship);
            $logsQuery->setMaxResults(10);
            $logs = $logsQuery->getResult();
        }

        return $this->render('ships/details.html.twig', [
            'ship' => $ship,
            'last_logs' => $logs,
        ]);
    }
}
