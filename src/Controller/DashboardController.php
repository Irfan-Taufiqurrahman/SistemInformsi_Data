<?php

namespace App\Controller;

use App\Repository\VariableRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/daftarData/{id}", name="app_daftarData")
     */
    public function daftarData(String $id, ManagerRegistry $managerRegistry, VariableRepository $variableRepository): Response
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
        //proses ekesekusi sql query
        $resultSet = $statement->execute();

        //ambil data dari proses eksekusi sql lalu diolah menjadi array
        $resultSet = $resultSet->fetchAll();
        // dump($resultSet);
        // exit;

        return $this->render('dashboard/daftarData.html.twig', [
            'resultSet' => $resultSet,
            'var'   => $var_id
        ]);
    }
}
