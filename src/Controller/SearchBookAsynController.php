<?php

namespace App\Controller;

use App\Entity\UserBord;
use App\Repository\BordRepository;
use App\Repository\CommentRepository;
use App\Repository\UserBordRepository;
use App\Service\CalculatePriceBookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class SearchBookAsynController extends AbstractController
{
    #[Route('/asyn/pack4/books/data')]
    public function books4(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $categories = $request->query->all('categories');
        $books = [];
        $i = 0;
        foreach ($categories as $categoryEntity => $categoryItems) {
            foreach ($categoryItems as $categoryItem) {
                foreach ($categoryItem as $categoryId => $page) {
                    if($page <= 0) $page = 1;
                    $repository = $em->getRepository('App\Entity\\' . ucfirst($categoryEntity));
                    $categorie =  $repository->find($categoryId);
                    if(!$categorie || count($categorie->getBords()) > 2){
                        $categorie= $repository->findOneRandomWithBords();
                    }
                    $all_books = $categorie->getBords()->toArray();

                    $numPage = ceil(count($all_books)/4);
                    $booksPage = array_slice($all_books, ($page - 1)*4, 4);
                    if(count($booksPage) < 3){
                        $aleatoirePage = 1;
                        if($numPage > 1) $aleatoirePage = mt_rand(1, $numPage);
                        $booksPage = array_slice($all_books, ($aleatoirePage - 1)*4, 4);
                    }
                    $books[ $categorie->getTitle() . ' ' . $i][] = $booksPage;
                    $i++;
                }
            }
        }
        return $this->json($books,200, [], ['groups' => 'bord']);
    }

    #[Route('/asyn/comments/book/data')]
    public function comment(Request $request, CommentRepository $repository, BordRepository $bordRepository, SessionInterface $session, Security $security): JsonResponse
    {
        $page = $request->query->getInt('page');
        $limit = $request->query->getInt('limit');
        $bord =$bordRepository->find($session->get('book_id'));
        $user = $this->getUser();
        $comments = $repository->findCommentsBook($bord, $user);
        $offset = ($page - 1) * $limit;
        $comments = array_slice($comments, $offset, $limit);
        return $this->json($comments,200, [], ['groups' => 'comment']);
    }

    #[Route('/asyn/slide/books/data')]
    public function slideBooks(Request $request, BordRepository $repository): JsonResponse
    {
        $filtres = $request->query->all('filtres');
        $limit = $request->query->getInt('limit');
        $sort = $request->query->all('sort');
        $books = $repository->findByFilters($filtres, $sort, $limit);
        //dd($books);

        return $this->json($books,200, [], ['groups' => 'bord']);

    }

    #[Route('/asyn/basket/count/book/data')]
    public function basketCountBook(Request $request, UserBordRepository $repository): JsonResponse
    {
        $user = $this->getUser();
        $count = null;
        if($user){
            $baskets = $repository->findBy(
                [
                    'user' => $user,
                    'is_visible'=>1,
                ]
            );
            $count = count($baskets);
        }
        return $this->json($count);
    }

    #[Route('/asyn/basket/add/book/data')]
    public function basketAddBook(Request $request, UserBordRepository $repository, BordRepository $bordRepository, EntityManagerInterface $manager): JsonResponse
    {
        $add = false;
        $book_id = $request->query->getInt('book');
        $user = $this->getUser();
        if(!$user) return $this->json(false);
        $book = $bordRepository->find($book_id);
        $basket = $repository->findOneBy(
            [
                'user' => $user,
                'bord' => $book,
            ]
        );
        if(empty($basket)){
            $basket = new UserBord();
            $basket->setUser($user)
                ->setBord($book)
                ->setVisible(true)
                ->setRecordedAt(new \DateTimeImmutable())
                ->setEndAt(new \DateTimeImmutable());
            $manager->persist($basket);
            $manager->flush();
            $add = true;
        }
        elseif (!$basket->isVisible()){
            $basket->setVisible(true);
            $manager->flush();
            $add = true;
        }
        else{
            return $this->json('exist');
        }

        return $this->json($add);
    }



    #[Route('/asyn/book/price/session/data')]
    public function price(Request $request, SessionInterface $session, Security $security): JsonResponse
    {
        $time = $request->query->getString('time');
        $book_price = $session->get('book_price');
        $user = $security->getUser();
        $price = null;


        if ($book_price != null) {
            $price = json_decode(self::calculatePrice($book_price, $time)->getContent(), true);
            if($user && is_int($price)){
                $paiement = $session->get('paiement');
                $paiement['prix'] = intval($price);
                $paiement['temps'] = $time;
                $session->set('paiement', $paiement);
            }
        }
        return $this->json($price);
    }
    #[Route('/asyn/book/price/data/{book_price}/{time?}')]
    public static function calculatePrice(int $book_price, ?string $time): JsonResponse
    {
        $calculatePriceBookService = new CalculatePriceBookService();
        return new JsonResponse($calculatePriceBookService->calculate($book_price, $time));
    }


}
