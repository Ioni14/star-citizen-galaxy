<?php

namespace App\Controller\Manufacturers;

use App\Entity\Manufacturer;
use App\Form\Dto\ManufacturerDto;
use App\Form\Type\ManufacturerForm;
use App\Repository\ManufacturerRepository;
use App\Service\LockHelper;
use App\Service\Ship\FileHelper;
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
    private ManufacturerRepository $manufacturerRepository;
    private EntityManagerInterface $entityManager;
    private FilesystemInterface $manufacturersLogosFilesystem;
    private FileHelper $fileHelper;
    private LockHelper $lockHelper;

    public function __construct(
        ManufacturerRepository $manufacturerRepository,
        EntityManagerInterface $entityManager,
        FilesystemInterface $manufacturersLogosFilesystem,
        FileHelper $fileHelper,
        LockHelper $lockHelper
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->entityManager = $entityManager;
        $this->manufacturersLogosFilesystem = $manufacturersLogosFilesystem;
        $this->fileHelper = $fileHelper;
        $this->lockHelper = $lockHelper;
    }

    /**
     * @Route("/manufacturers/edit/{slug}", name="manufacturers_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request, string $slug): Response
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = $this->manufacturerRepository->findOneBy(['slug' => $slug]);
        if ($manufacturer === null) {
            throw new NotFoundHttpException('Manufacturer not found.');
        }

        /** @var LogEntry $lastLog */
        $lastLog = $this->entityManager->getRepository(LogEntry::class)->getLogEntriesQuery($manufacturer)->setMaxResults(1)->getOneOrNullResult();
        $lastVersion = $lastLog !== null ? $lastLog->getVersion() : 0;

        $lockedBy = $this->lockHelper->acquireLock($manufacturer);
        $lockedByMe = $lockedBy !== null && $lockedBy->getId()->equals($this->getUser()->getId());
        if ($lockedByMe) {
            $this->entityManager->refresh($manufacturer);
        }

        $manufacturerDto = new ManufacturerDto(
            $manufacturer->getName(),
            $manufacturer->getCode(),
            $manufacturer->getLogoPath(),
            $lastVersion,
            $lastVersion,
        );
        $form = $this->createForm(ManufacturerForm::class, $manufacturerDto, [
            'disabled' => !$lockedByMe,
            'mode' => ManufacturerForm::MODE_EDIT,
        ]);
        $form->handleRequest($request);
        if ($lockedByMe && $form->isSubmitted() && $form->isValid()) {
            $manufacturer
                ->setName($manufacturerDto->name)
                ->setCode($manufacturerDto->code);

            if ($manufacturerDto->logo !== null) {
                $path = $this->fileHelper->handleFile($manufacturerDto->logo, $manufacturer->getSlug(), $manufacturer->getLogoPath(), $this->manufacturersLogosFilesystem);
                $manufacturer->setLogoPath($path);
            }

            $this->entityManager->flush();

            $this->lockHelper->releaseLock($manufacturer);
            $this->addFlash('success', 'The manufacturer has been successfully modified.');

            return $this->redirectToRoute('manufacturers_details', ['slug' => $manufacturer->getSlug()]);
        }

        return $this->render('manufacturers/edit.html.twig', [
            'locked_by' => $lockedBy,
            'locked_by_me' => $lockedByMe,
            'manufacturer' => $manufacturer,
            'form' => $form->createView(),
        ]);
    }
}
