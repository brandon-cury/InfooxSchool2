<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Exercice;
use App\Form\CommentType;
use App\Repository\BordRepository;
use App\Repository\ClasseRepository;
use App\Repository\CommentRepository;
use App\Repository\CourRepository;
use App\Repository\EpreuveRepository;
use App\Repository\ExamenRepository;
use App\Repository\ExerciceRepository;
use App\Repository\FiliereRepository;
use App\Repository\MatiereRepository;
use App\Repository\UserBordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class BasketController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function basket(UserBordRepository $repository, BordRepository $bordRepository): Response
    {
        $user = $this->getUser();
        if(!$user) return $this->redirectToRoute('app_login');

        $baskets = $repository->findBy(
            [
                'user' => $user,
                'is_visible' => true
            ]
        );
        $books = $bordRepository->findAll();
        $book =$books[array_rand($books)];

        return $this->render('basket/basket.html.twig', [
            'baskets'=> $baskets,
            'book_id' => $book->getId()
        ]);
    }

    #[Route('/basket/delete/book/{b}', name: 'app_basket_delete_book', methods: ['POST'])]
    public function deleteBookBasket(int $b, UserBordRepository $repository, BordRepository $bordRepository, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if(!$user) return $this->redirectToRoute('app_login');
        $book = $bordRepository->find($b);
        $basket = $repository->findOneBy(
            [
                'user' => $user,
                'bord' =>$book
            ]
        );
        if(!$basket){
            $this->addFlash('danger', 'Vous n\'avez pas accès à ce livre!');
            return $this->redirectToRoute('app_basket');
        }
        $basket->setVisible(false);
        $manager->flush();

        return $this->redirectToRoute('app_basket');
    }




}
