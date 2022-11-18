<?php

namespace App\Controller;

use App\Entity\TThematicData;
use App\Entity\TTopic;
use App\Form\TopicType;
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

class TopicController extends AbstractController
{
    /**
     * @Route("/listTopic/{id}", name="view_topic")
     */
    public function viewTopic(?string $id, ManagerRegistry $managerRegistry, TTopicRepository $tTopicRepository): Response
    {
        //main data sebagai key, $id sebagai value (sesuai dengan struktur array)
        $Topic = $tTopicRepository->findBy(['thematicData' => $id]);

        $topic = new TTopic;
        $form = $this->createForm(TopicType::class, $topic);

        return $this->render('topic/viewTopic.html.twig', [
            'Topic' => $Topic,
            'Topic_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/topic", name="create_topic")
     */
    public function createTopic(Request $request, EntityManagerInterface $entityManagerInterface, TSubTopicRepository $tSubTopicRepository, Environment $twig): Response
    {
        $topic = new TTopic;
        $form = $this->createForm(TopicType::class, $topic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($topic);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('view_topic');
        }
        return new Response($twig->render('topic/formCreateTopic.html.twig', [
            'Topic_form' => $form->createView()
        ]));
    }

    /**
     * @Route("/topic/{id}", name="delete_topic")
     */
    public function deleteTopic(TTopic $tTopic, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManagerInterface): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tTopic->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tTopic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('view_thematic');
    }
}
