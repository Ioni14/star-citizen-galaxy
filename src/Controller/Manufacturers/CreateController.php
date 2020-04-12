<?php

namespace App\Controller\Manufacturers;

use App\Entity\Manufacturer;
use App\Form\Dto\ManufacturerDto;
use App\Form\Type\ManufacturerForm;
use App\Service\Ship\FileHelper;
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
    private FilesystemInterface $manufacturersLogosFilesystem;
    private FileHelper $fileHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemInterface $manufacturersLogosFilesystem,
        FileHelper $fileHelper
    ) {
        $this->entityManager = $entityManager;
        $this->manufacturersLogosFilesystem = $manufacturersLogosFilesystem;
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Route("/manufacturers/create", name="manufacturers_create", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request): Response
    {
        $manufacturerDto = new ManufacturerDto();
        $form = $this->createForm(ManufacturerForm::class, $manufacturerDto, [
            'mode' => ManufacturerForm::MODE_CREATE,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manufacturer = (new Manufacturer(Uuid::uuid4()))
                ->setName($manufacturerDto->name)
                ->setCode($manufacturerDto->code);

            try {
                $this->entityManager->beginTransaction();
                $this->entityManager->persist($manufacturer);
                $this->entityManager->flush(); // we need the generated Slug of the ship

                if ($manufacturerDto->logo !== null) {
                    $path = $this->fileHelper->handleFile($manufacturerDto->logo, $manufacturer->getSlug(), $manufacturer->getLogoPath(), $this->manufacturersLogosFilesystem);
                    $manufacturer->setLogoPath($path);
                }
                $this->entityManager->flush();
                $dql = 'DELETE FROM Gedmo\Loggable\Entity\LogEntry e WHERE e.objectId = :oid AND e.action = :action';
                $this->entityManager->createQuery($dql)->setParameters([
                    'oid' => $manufacturer->getId()->toString(),
                    'action' => LoggableListener::ACTION_UPDATE,
                ])->execute();

                $this->entityManager->commit();
            } catch (\Exception $e) {
                $this->entityManager->rollback();
                throw $e;
            }

            $this->addFlash('success', 'The manufacturer has been successfully created.');

            return $this->redirectToRoute('manufacturers_details', ['slug' => $manufacturer->getSlug()]);
        }

        return $this->render('manufacturers/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
