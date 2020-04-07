<?php

namespace App\Controller\Manufacturers;

use App\Entity\Manufacturer;
use App\Repository\ManufacturerRepository;
use App\Repository\ShipChassisRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ManufacturerRepository $manufacturerRepository;
    private ShipChassisRepository $shipChassisRepository;
    private FilesystemInterface $manufacturersLogosFilesystem;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManufacturerRepository $manufacturerRepository,
        ShipChassisRepository $shipChassisRepository,
        FilesystemInterface $manufacturersLogosFilesystem,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->shipChassisRepository = $shipChassisRepository;
        $this->manufacturersLogosFilesystem = $manufacturersLogosFilesystem;
        $this->logger = $logger;
    }

    /**
     * @Route("/manufacturers/delete/{manufacturerId}", name="manufacturers_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function __invoke(Request $request, string $manufacturerId): Response
    {
        /** @var Manufacturer|null $manufacturer */
        $manufacturer = $this->manufacturerRepository->find($manufacturerId);
        if ($manufacturer === null) {
            $this->addFlash('danger', 'The manufacturer does not exist.');

            return $this->redirectToRoute('manufacturers_list');
        }

        $countChassis = $this->shipChassisRepository->countChassisByManufacturer($manufacturer->getId());
        if ($countChassis > 0) {
            $this->addFlash('danger', "Unable to delete the manufacturer {$manufacturer->getName()} because it's still used by $countChassis ship chassis.");

            return $this->redirectToRoute('manufacturers_list');
        }

        $this->entityManager->remove($manufacturer);
        $this->entityManager->flush();

        if ($manufacturer->getLogoPath() !== null) {
            try {
                // TODO : MOM!!
                $this->manufacturersLogosFilesystem->delete($manufacturer->getLogoPath());
            } catch (FileNotFoundException $e) {
                $this->logger->error('[Filesystem] Unable to delete {path}.', ['exception' => $e, 'path' => $manufacturer->getLogoPath()]);
            }
        }

        $this->addFlash('success', "The manufacturer {$manufacturer->getName()} has been successfully deleted.");

        return $this->redirectToRoute('manufacturers_list');
    }
}
