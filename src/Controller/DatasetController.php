<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\Dataset;
use App\Entity\Variable;
use App\Form\DatasetExcelType;
use App\Form\DatasetType;
use App\Repository\DatasetRepository;
use App\Repository\VariableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DatasetController extends AbstractController
{
    /**
     * @Route("/listDataset", name="view_dataset_api")
     */
    public function viewDataset(ManagerRegistry $managerRegistry, DatasetRepository $datasetRepository): Response
    {
        $dataset = $datasetRepository->findAll();
        $Dataset = new Dataset;
        $form = $this->createForm(DatasetType::class, $Dataset);

        return $this->render('dataset/daftarDatasetAPI.html.twig', [
            'dataset' => $dataset,
            'dataset_form' => $form->createView(),
            'datasetExcel_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dataset", name="create_dataset_excel")
     */
    public function createDatasetExcel(HttpClientInterface $client, Environment $twig, Request $request, EntityManagerInterface $entityManagerInterface, VariableRepository $variableRepository)
    {
        $Dataset = new Dataset;
        $form = $this->createForm(DatasetExcelType::class, $Dataset);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data_file = $request->files->get("file");
            $file_name = md5(uniqid() . $data_file->getClientOriginalName()) . '.' . $data_file->guessExtension();

            $Dataset->setLinkAPI($file_name);
            $upload_dir =  $this->getParameter('upload_directory');

            $data_file->move(
                $upload_dir,
                $file_name
            );
            //fungsinya untuk prepare
            $entityManagerInterface->persist($Dataset);
            $spreadsheet = IOFactory::load($upload_dir . '/' . $Dataset->getLinkAPI());
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $dataResult = $sheetData;
            $tempVar = [];
            foreach ($dataResult[1] as $key => $Value) {
                $var = new Variable;
                $var->setDataset($Dataset);
                $var->setName($Value);
                $entityManagerInterface->persist($var);

                $tempVar[] = $var;
            }

            $entityManagerInterface->flush();

            unset($dataResult[1]);

            $row_id = 0;
            foreach ($dataResult as $detail) {
                $detail = array_values($detail);
                foreach ($detail as $key => $Value) {
                    // $var = $variableRepository->findOneBy(['name' => $key, 'dataset' => $Dataset]);

                    $Data = new Data();
                    $Data->setDataset($Dataset);
                    $Data->setVar($tempVar[$key]);
                    $Data->setContent($Value);
                    $Data->setRowId($row_id);
                    $entityManagerInterface->persist($Data);
                }
                $row_id++;
            }
            $entityManagerInterface->flush();
            return $this->redirectToRoute('view_dataset_api');
        }


        return new Response($twig->render('dataset/formCreateDatasetExcel.html.twig', [
            'datasetExcel_form' => $form->createView()
        ]));
    }

    /**
     * @Route("/dataset_api", name="create_dataset_api")
     */
    public function createDatasetAPI(HttpClientInterface $client, Environment $twig, Request $request, EntityManagerInterface $entityManagerInterface, VariableRepository $variableRepository)
    {
        $Dataset = new Dataset;
        $form = $this->createForm(DatasetType::class, $Dataset);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($Dataset);
            $response = $client->request(
                'GET',
                $Dataset->getLinkAPI(),
                ['verify_host' => false, 'verify_peer' => false]
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

            if (isset($content["result"]["data"])) {
                $dataResult = $content["result"]["data"];
                foreach ($dataResult[0] as $key => $Value) {
                    $var = new Variable();
                    $var->setDataset($Dataset);
                    $var->setName($key);
                    $entityManagerInterface->persist($var);
                }
                $entityManagerInterface->flush();

                $row_id = 0;
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
            } else {
                $dataResult = $content;
                foreach ($dataResult[0] as $key => $Value) {
                    $var = new Variable();
                    $var->setDataset($Dataset);
                    $var->setName($key);
                    $entityManagerInterface->persist($var);
                }
                $entityManagerInterface->flush();

                $row_id = 0;
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
            }

            $entityManagerInterface->flush();


            return $this->redirectToRoute('view_dataset_api');
        }

        return new Response($twig->render('dataset/formCreateDataset.html.twig', [
            'dataset_form' => $form->createView()
        ]));
    }

    /**
     * @Route("/listDataset/{id}", name="delete_dataset")
     */
    public function deleteDataset(Dataset $dataset, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManagerInterface): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dataset->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dataset);
            $entityManager->flush();
        }
        return $this->redirectToRoute('view_dataset_api');
    }
}
