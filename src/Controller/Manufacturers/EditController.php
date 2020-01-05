<?php

namespace App\Controller\Manufacturers;

use App\Entity\Manufacturer;
use App\Form\Dto\ManufacturerDto;
use App\Form\Type\ManufacturerForm;
use App\Repository\ManufacturerRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(
        ManufacturerRepository $manufacturerRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->entityManager = $entityManager;
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

        $manufacturerDto = new ManufacturerDto(
            $manufacturer->getName(),
            $manufacturer->getCode(),
        );
        $form = $this->createForm(ManufacturerForm::class, $manufacturerDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manufacturer
                ->setName($manufacturerDto->name)
                ->setCode($manufacturerDto->code);

            $this->entityManager->flush();

            $this->addFlash('success', 'The manufacturer has been successfully modified.');

            return $this->redirectToRoute('manufacturers_edit', ['slug' => $manufacturer->getSlug()]);
        }

        return $this->render('manufacturers/edit.html.twig', [
            'manufacturer' => $manufacturer,
            'form' => $form->createView(),
        ]);
    }
}
