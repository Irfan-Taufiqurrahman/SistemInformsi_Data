<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\Dataset;
use App\Entity\Variable;
use App\Repository\VariableRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/{id}", name="home")
     */
    public function index(String $id, ManagerRegistry $managerRegistry, VariableRepository $variableRepository): Response
    {
        $var_id = $variableRepository->findBy(['dataset' => $id], ['id' => 'ASC']);
        $sembarang = [];
        foreach ($var_id as $var) {
            $sembarang[] = $var->getName() . " varchar";
        }
        $column = implode(', ', $sembarang);

        $connection = $managerRegistry->getConnection();

        $query = "
        SELECT *
        FROM crosstab('SELECT a.row_id, b.id, a.content from 
     data a join variable b on a.var_id = b.id WHERE a.dataset_id = $id ORDER BY 1,2 ASC'::text) 
     ct(row_id integer, $column) ORDER BY ct.row_id;
        ";

        $statement = $connection->prepare($query);
        $resultSet = $statement->execute();
        dump($resultSet->fetchAll());
        exit;
    }

    /**
     * @Route("/coba", name="coba")
     */
    public function coba(): Response
    {
        return $this->render('Assets/tables.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    // /**
    //  * @Route("/bapeda", name="bapeda")
    //  */
    // public function bapeda(HttpClientInterface $client, ManagerRegistry $managerRegistry, VariableRepository $variableRepository)
    // {
    //     $entityManager = $managerRegistry->getManager();

    //     $response = $client->request(
    //         'GET',
    //         'https://sata.jatimprov.go.id/bapenda.php'
    //     );

    //     //cek status error atau tidak
    //     $statusCode = $response->getStatusCode();
    //     // $statusCode = 200

    //     //tipe respon nya apa
    //     $contentType = $response->getHeaders()['content-type'][0];
    //     // $contentType = 'application/json'

    //     //ini function ambil kontennya
    //     $content = $response->getContent();
    //     // $content = '{"id":521583, "name":"symfony-docs", ...}'

    //     //setelah diambil dijadikan array
    //     $content = $response->toArray();
    //     // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

    //     $Dataset = new Dataset();
    //     $Dataset->setTitle("Bapeda");
    //     $entityManager->persist($Dataset);
    //     //flush untuk menyimpan data ke DB dan cukup 1
    //     // $entityManager->flush();

    //     // $Dataset = new Dataset();
    //     // $Dataset->setTitle('Input the title');

    //     // $form = $this->createFormBuilder($Dataset)
    //     //     ->add('Dataset', TextType::class)
    //     //     ->add('save', SubmitType::class, ['label' => 'Create Dataset'])
    //     //     ->getForm();

    //     $dataResult = $content["result"]["data"];
    //     foreach ($dataResult[0] as $key => $Value) {
    //         $var = new Variable();
    //         $var->setDataset($Dataset);
    //         $var->setName($key);
    //         $entityManager->persist($var);
    //     }
    //     $entityManager->flush();

    //     $row_id = 1;

    //     foreach ($dataResult as $detail) {
    //         foreach ($detail as $key => $Value) {
    //             $var = $variableRepository->findOneBy(['name' => $key, 'dataset' => $Dataset]);

    //             $Data = new Data();
    //             $Data->setDataset($Dataset);
    //             $Data->setVar($var);
    //             $Data->setContent($Value);
    //             $Data->setRowId($row_id);
    //             $entityManager->persist($Data);
    //         }
    //         $row_id++;
    //     }
    //     $entityManager->flush();

    //     dump($content["result"]["data"]);
    //     exit;
    // }
}
