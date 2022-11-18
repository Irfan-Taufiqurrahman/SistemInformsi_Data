<?php

namespace App\Controller;

use App\Entity\TSubTopic;
use App\Form\SubTopicType;
use App\Repository\TSubTopicRepository;
use App\Repository\TTopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;
use Twig\Environment;

class SubTopicController extends AbstractController
{
    /**
     * @Route("/listSubTopic/{id}", name="view_SubTopic")
     */
    public function viewSubTopic(?string $id, ManagerRegistry $managerRegistry, TSubTopicRepository $tSubTopicRepository, TTopicRepository $tTopicRepository): Response
    {
        $topic = $tTopicRepository->find($id);
        //main data sebagai key, $id sebagai value (sesuai dengan struktur array)
        $SubTopic = $tSubTopicRepository->findBy(['Topic' => $id]);

        $subTopic = new TSubTopic;
        $form = $this->createForm(SubTopicType::class, $subTopic);

        return $this->render('sub_topic/viewSubTopic.html.twig', [
            'SubTopic' => $SubTopic,
            'SubTopic_form' => $form->createView(),
            'topic' => $topic,
        ]);
    }

    /**
     * @Route("/SubTopic/{id}", name="create_SubTopic")
     */
    public function createSubTopic(?string $id, Request $request, EntityManagerInterface $entityManagerInterface, TSubTopicRepository $tSubTopicRepository, Environment $twig, TTopicRepository $tTopicRepository): Response
    {
        $topic = $tTopicRepository->find($id);

        $subTopic = new TSubTopic;
        $form = $this->createForm(SubTopicType::class, $subTopic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $codeSubTopic = $subTopic->getCode();
            $codeTopic = $topic->getCode();
            $subTopic->setCode($codeTopic . "." . $codeSubTopic);
            $subTopic->setTopic($topic);
            $entityManagerInterface->persist($subTopic);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('view_SubTopic', [
                'id' => $id
            ]);
        }
        return new Response($twig->render('sub_topic/formCreateSubTopic.html.twig', [
            'SubTopic_form' => $form->createView(),
            'topic' => $topic,
        ]));
    }

    /**
     * @Route("/SubTopic/{id}", name="delete_SubTopic")
     */
    public function deleteSubTopic(TSubTopic $tSubTopic, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManagerInterface): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tSubTopic->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tSubTopic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('view_SubTopic');
    }
}
