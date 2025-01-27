<?php

namespace App\Controller;

use App\Repository\ExamenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
//
class ExamenController extends AbstractController
{
    #[Route('/examen/data', name: 'app_examen')]
    public function index(ExamenRepository $repository): JsonResponse
    {
        $examens = $repository->findAll();
        //dd($examens);
        return $this->json($examens,200, [], ['groups' => 'Examen']);
    }
}
