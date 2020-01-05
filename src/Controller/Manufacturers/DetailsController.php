<?php

namespace App\Controller\Manufacturers;

use App\Repository\ManufacturerRepository;
use App\Repository\ShipChassisRepository;
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
    private ManufacturerRepository $manufacturerRepository;
    private ShipChassisRepository $shipChassisRepository;
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(
        ManufacturerRepository $manufacturerRepository,
        ShipChassisRepository $shipChassisRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->shipChassisRepository = $shipChassisRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/manufacturers/details/{slug}", name="manufacturers_details", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $manufacturer = $this->manufacturerRepository->findOneBy(['slug' => $slug]);
        if ($manufacturer === null) {
            throw new NotFoundHttpException('Manufacturer not found.');
        }
        $chassis = $this->shipChassisRepository->findBy(['manufacturer' => $manufacturer]);

        $logs = [];
        if ($this->security->isGranted('ROLE_MODERATOR')) {
            $logsQuery = $this->entityManager->getRepository(LogEntry::class)->getLogEntriesQuery($manufacturer);
            $logsQuery->setMaxResults(5);
            $logs = $logsQuery->getResult();
        }

        return $this->render('manufacturers/details.html.twig', [
            'manufacturer' => $manufacturer,
            'manufacturerChassis' => $chassis,
            'last_logs' => $logs,
        ]);
    }
}
