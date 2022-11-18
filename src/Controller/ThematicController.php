<?php

namespace App\Controller;

use App\Entity\TThematicData;
use App\Form\ThematicType;
use App\Repository\TMainDataRepository;
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
    public function viewThematic(?string $id, ManagerRegistry $managerRegistry, TThematicDataRepository $tThematicDataRepository, TMainDataRepository $tMainDataRepository): Response
    {
        //untuk mengambil id MainData dari table MainData
        $MainData = $tMainDataRepository->find($id);
        //main data sebagai key, $id sebagai value (sesuai dengan struktur array)
        $Thematic = $tThematicDataRepository->findBy(['mainData' => $id]);

        $thematic = new TThematicData;
        $form = $this->createForm(ThematicType::class, $thematic);

        return $this->render('thematic/viewThematic.html.twig', [
            'Thematic' => $Thematic,
            'Thematic_form' => $form->createView(),
            'MainData' => $MainData,
        ]);
    }

    /**
     * @Route("/thematic/{id}", name="create_thematic")
     */
    public function createThematic(?string $id, Request $request, EntityManagerInterface $entityManagerInterface, TTopicRepository $tTopicRepository, Environment $twig, TMainDataRepository $tMainDataRepository): Response
    {
        $MainData = $tMainDataRepository->find($id);
        $thematic = new TThematicData;
        $form = $this->createForm(ThematicType::class, $thematic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //mengambil input an kode tematik
            $codeThematic = $thematic->getCode();
            //mengambil kode MainData
            $CodeMainData = $MainData->getCode();
            //meng set kode mainData agar tergabung dengan kode tematik
            $thematic->setCode($CodeMainData . "." . $codeThematic);
            //set parentnya
            $thematic->setMainData($MainData);
            $entityManagerInterface->persist($thematic);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('view_thematic', [
                'id' => $id
            ]);
        }
        return new Response($twig->render('thematic/formCreateThematic.html.twig', [
            'Thematic_form' => $form->createView(),
            'MainData' => $MainData,
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
