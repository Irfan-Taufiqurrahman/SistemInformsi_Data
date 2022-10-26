<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\Dataset;
use App\Entity\Variable;
use App\Form\DatasetType;
use App\Repository\VariableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DatasetController extends AbstractController
{
    /**
     * @Route("/dataset", name="app_dataset")
     */
    public function createDataset(HttpClientInterface $client, Environment $twig, Request $request, EntityManagerInterface $entityManagerInterface, VariableRepository $variableRepository)
    {
        $Dataset = new Dataset;
        $form = $this->createForm(DatasetType::class, $Dataset);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManagerInterface->persist($Dataset);
            $response = $client->request(
                'GET',
                $Dataset->getLinkAPI()
            );

            $entityManagerInterface->flush();
            //cek status error atau tidak
            // $statusCode = $response->getStatusCode();
            // $statusCode = 200

            //tipe respon nya apa
            $contentType = $response->getHeaders()['content-type'][0];
            // $contentType = 'application/json'

            //ini function ambil kontennya
            $content = $response->getContent();
            // $content = '{"id":521583, "name":"symfony-docs", ...}'

            //setelah diambil dijadikan array
            $content = $response->toArray();
            // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

            $dataResult = $content["result"]["data"];
            foreach ($dataResult[0] as $key => $Value) {
                $var = new Variable();
                $var->setDataset($Dataset);
                $var->setName($key);
                $entityManagerInterface->persist($var);
            }
            $entityManagerInterface->flush();

            $row_id = 1;

            foreach ($dataResult as $detail) {
                foreach ($detail as $key => $Value) {
                    $var = $variableRepository->findOneBy(['name' => $key, 'dataset' => $Dataset]);

                    $Data = new Data();
                    $Data->setDataset($Dataset);
                    $Data->setVar($var);
                    $Data->setContent($Value);
                    $Data->setRowId($row_id);
                    $entityManagerInterface->persist($Data);
                }
                $row_id++;
            }
            $entityManagerInterface->flush();


            return $this->redirectToRoute('app_dashboard');
        }

        return new Response($twig->render('dataset/index.html.twig', [
            'dataset_form' => $form->createView()
        ]));
    }
}
