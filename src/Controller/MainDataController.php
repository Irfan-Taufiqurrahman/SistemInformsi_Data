<?php

namespace App\Controller;

use App\Entity\TMainData;
use App\Form\MainDataType;
use App\Repository\TMainDataRepository;
use App\Repository\TThematicDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainDataController extends AbstractController
{
    /**
     * @Route("listMainData", name="view_mainData")
     */
    public function viewMainData(ManagerRegistry $managerRegistry, TMainDataRepository $tMainDataRepository): Response
    {
        $mainData = $tMainDataRepository->findAll();
        $MainData = new TMainData;
        $form = $this->createForm(MainDataType::class, $MainData);

        return $this->render('mainData/viewMainData.html.twig', [
            'MainData' => $mainData,
            'mainData_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/thematic", name="create_thematic")
     */
    public function CreateMainData(Request $request, EntityManagerInterface $entityManagerInterface, TThematicDataRepository $tThematicDataRepository): Response
    {
        $MainData = new TMainData;
        $form = $this->createForm(MainDataType::class, $MainData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($MainData);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('view_dataset');
        }
    }
}
