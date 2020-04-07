<?php

namespace App\Controller\Ships;

use App\Entity\Ship;
use App\Repository\ShipRepository;
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
    private ShipRepository $shipRepository;
    private FilesystemInterface $shipsPicturesFilesystem;
    private FilesystemInterface $shipsThumbnailsFilesystem;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShipRepository $shipRepository,
        FilesystemInterface $shipsPicturesFilesystem,
        FilesystemInterface $shipsThumbnailsFilesystem,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->shipRepository = $shipRepository;
        $this->shipsPicturesFilesystem = $shipsPicturesFilesystem;
        $this->shipsThumbnailsFilesystem = $shipsThumbnailsFilesystem;
        $this->logger = $logger;
    }

    /**
     * @Route("/ships/delete/{shipId}", name="ships_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function __invoke(Request $request, string $shipId): Response
    {
        /** @var Ship|null $ship */
        $ship = $this->shipRepository->find($shipId);
        if ($ship === null) {
            $this->addFlash('danger', 'The ship does not exist.');

            return $this->redirectToRoute('ships_list');
        }

        $this->entityManager->remove($ship);
        $this->entityManager->flush();

        if ($ship->getPicturePath() !== null) {
            try {
                // TODO : MOM!!
                $this->shipsPicturesFilesystem->delete($ship->getPicturePath());
            } catch (FileNotFoundException $e) {
                $this->logger->error('[Filesystem] Unable to delete {path}.', ['exception' => $e, 'path' => $ship->getPicturePath()]);
            }
        }
        if ($ship->getThumbnailPath() !== null) {
            try {
                // TODO : MOM!!
                $this->shipsThumbnailsFilesystem->delete($ship->getThumbnailPath());
            } catch (FileNotFoundException $e) {
                $this->logger->error('[Filesystem] Unable to delete {path}.', ['exception' => $e, 'path' => $ship->getThumbnailPath()]);
            }
        }

        $this->addFlash('success', "The ship {$ship->getName()} has been successfully deleted.");

        return $this->redirectToRoute('ships_list');
    }
}
