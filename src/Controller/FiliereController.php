<?php

namespace App\Controller;

use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FiliereController extends AbstractController
{
    #[Route('/filiere/data', name: 'app_filiere')]
    public function index(FiliereRepository $repository): JsonResponse
    {
        $filieres = $repository->findAll();
        //dd($filieres);
        return $this->json($filieres, 200, [], ['groups' => ['filiere']]);
    }
}
