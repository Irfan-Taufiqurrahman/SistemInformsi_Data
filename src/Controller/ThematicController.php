<?php

namespace App\Controller;

use App\Entity\TThematicData;
use App\Form\ThematicType;
use App\Repository\TThematicDataRepository;
use App\Repository\TTopicRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ThematicController extends AbstractController
{
    /**
     * @Route("/listThematic/{id}", name="view_thematic")
     */
    public function viewThematic(?string $id, ManagerRegistry $managerRegistry, TThematicDataRepository $tThematicDataRepository): Response
    {
        //main data sebagai key, $id sebagai value (sesuai dengan struktur array)
        $Thematic = $tThematicDataRepository->findBy(['mainData' => $id]);

        $thematic = new TThematicData;
        $form = $this->createForm(ThematicType::class, $thematic);

        return $this->render('thematic/viewThematic.html.twig', [
            'Thematic' => $Thematic,
            'Thematic_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/thematic", name="create_thematic")
     */
    public function createThematic(Request $request, EntityManagerInterface $entityManagerInterface, TTopicRepository $tTopicRepository, Environment $twig): Response
    {
        $thematic = new TThematicData;
        $form = $this->createForm(ThematicType::class, $thematic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($thematic);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('view_thematic');
        }
        return new Response($twig->render('thematic/formCreateThematic.html.twig', [
            'Thematic_form' => $form->createView()
        ]));
    }

    /**
     * @Route("/thematic/{id}", name="delete_thematic")
     */
    public function deleteThematic(TThematicData $tThematicData, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManagerInterface): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tThematicData->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tThematicData);
            $entityManager->flush();
        }

        return $this->redirectToRoute('view_thematic');
    }
}
