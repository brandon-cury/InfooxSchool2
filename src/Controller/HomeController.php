<?php

namespace App\Controller;

use App\Repository\ExamenRepository;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FiliereRepository $repository, ExamenRepository $examenRepository): Response
    {
        $nouvellesF = $repository->findBy(
            [],
            [
                'created_at' => 'DESC'
            ],
            12
        );
        $populairesF = $repository->findBy(
            [],
            [
                'all_user' => 'DESC'
            ],
            12
        );
        $meilleuresF = $repository->findBy(
            [],
            [
                'sort' => 'ASC'
            ],
            12
        );
        $examens = $examenRepository->findAll();
        return $this->render('home/index.html.twig', [
            'populairesF' => $populairesF,
            'meilleuresF' => $meilleuresF,
            'nouvellesF'=> $nouvellesF,
            'examens' => $examens,
        ]);
    }
}
