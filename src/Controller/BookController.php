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

class BookController extends AbstractController
{
    #[Route('/accessBook/{b}', name: 'app_access_book')]
    public function accessBook(string $b, BordRepository $repository, SessionInterface $session, EntityManagerInterface $manager, MailerInterface $mailer, CommentRepository $commentRepository, UserRepository $userRepository, Request $request, Security $security): Response
    {
        $idUser = null;
        $user = $security->getUser();
        if($user) $idUser = $user->getId();

        $book = $repository->findOneBy(
            ['slug' => $b]
        );

        if(!$book){
            throw new NotFoundHttpException('Pas de cours trouvé');
        }
        //enregistrement du prix du livre en session
        $session->set('book_id', $book->getId());
        $bookPrice = null;
        if($book->getPrice() != null){
            $session->set('book_price', $book->getPrice());
            $bookPrice = SearchBookAsynController::calculatePrice($book->getPrice(), '3 jours')->getContent();

            if($user){
                $session->set('paiement', [
                    'nom'=> $user->getLastName(),
                    'prenom'=> $user->getFirstName(),
                    'description'=> "Achat du bord",
                    'user'=> $user->getId(),
                    'book'=> $book->getId(),
                    'prix'=> $bookPrice,
                    'temps'=> '3 jours'
                ]);
            }

        }
        //compte le nombre de page de commentaires
        $comments = $commentRepository->findCommentsBook($book, $user);
        $numPageComments = ceil(count($comments)/6);

        //formulaire de commentaire
        $comment = new Comment();
        if($request->query->get('id')){
            $find_comment = $commentRepository->find($request->query->get('id'));
            if($find_comment){
                if($user->getId() == $find_comment->getUser()->getId()){
                    $comment = $find_comment;
                }
            }
        }


        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$user){
                return $this->redirectToRoute('app_login');
            }
            //verifier si c'est une insersion simple
            $updateId = $form->get("updateId")->getData();
            if(!$updateId){
                //verifier si l'utilisateur n'avais pas encore poster de commentaire:
                $findCommentUserBord = $commentRepository->findOneBy([
                    'bord' => $book,
                    'user' => $user
                ]);
                if($findCommentUserBord != null){
                    $this->addFlash('danger', 'Vous avez déjà poster un commentaire pour ce livre');
                    return $this->redirectToRoute('app_access_book', ['b'=> $book->getSlug(), 'id'=> $findCommentUserBord->getId()]);
                }

                $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setBord($book)
                    ->setPublished(false)
                    ->setSend(false)
                    ->setUser($user);
                $manager->persist($comment);
                $manager->flush();
                $this->addFlash('success', 'Votre commentaire à bien été enregistré');

            }
            else{
                $comment2 = $commentRepository->find($updateId);
                if($user->getId() == $comment2->getUser()->getId()){

                    $comment2->setContent($form->get('content')->getData())
                        ->setPublished(false)
                        ->setSend(false)
                        ->setRating($form->get('rating')->getData());
                    $manager->persist($comment2);
                    $manager->flush();
                    $this->addFlash('success', 'Votre commentaire à été modifié avec sucès');
                }
            }

            $team = $book->getEditor();
            $email = (new Email())
                ->from($user->getEmail())
                ->to($team->getEmail());
            if(!$updateId){
                $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . '/editor?crudAction=index&crudControllerFqcn=App\Controller\Editor\CommentCrudController&commentId=' . $comment->getId();
                $email->subject('Nouveau commentaire - InfooxSchool.com')
                    ->html('<h1>Bonjour '. $team->getFirstName() .', le nouveau commentaire de '. $user->getFirstName()  .' attend votre approbation pour être publié.</h1> <a style="text-decoration: none" href="'. $url .'">gérer le commentaire</a><h2>Voici le commentaire :</h2> <div>'. $comment->getContent() .'</div>');
            }else{
                $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . '/editor?crudAction=index&crudControllerFqcn=App\Controller\Editor\CommentCrudController&commentId=' . $comment2->getId();
                $email->subject('Commentaire Modifié - InfooxSchool.com')
                    ->html('<h1>Bonjour '. $team->getFirstName() .'</h1><p>'. $user->getFirstName() . ' a bien modifié son commentaire. Son commmentaire attend votre approbation pour être publié.</p> <a style="text-decoration: none" href="'. $url .'">gérer le commentaire</a><h2>Voici le commentaire :</h2> <div>'. $comment2->getContent() .'</div>');
            }

            $mailer->send($email);
            return $this->redirectToRoute('app_access_book', ['b'=> $book->getSlug()]);

        }
        return $this->render('book/accessBook.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
            'bookPrice' => $bookPrice,
            'numPageComments' => $numPageComments,
            'idUser' => $idUser,
        ]);

    }
    #[Route('/book/{b}', name: 'app_book')]
    public function book(string $b, BordRepository $repository, UserBordRepository $userBordRepository, SessionInterface $session, EntityManagerInterface $manager, MailerInterface $mailer, CommentRepository $commentRepository, UserRepository $userRepository, Request $request, Security $security): Response
    {
        $idUser = null;
        $user = $security->getUser();
        if($user) $idUser = $user->getId();

        $book = $repository->findOneBy(
            ['slug' => $b]
        );
        //verifier si l'utilisateur a acces au bord
        $corrige_autorisation = self::autorisationBord($book, $userBordRepository, $user);
        $bookPrice = null;
        if($book->getPrice()) {
            $bookPrice = SearchBookAsynController::calculatePrice($book->getPrice(), '3 jours')->getContent();
        }

        if(!$book){
            throw new NotFoundHttpException('Pas de cours trouvé');
        }
        //enregistrement du prix du livre en session
        $session->set('book_id', $book->getId());

        //compte le nombre de page de commentaires
        $comments = $commentRepository->findCommentsBook($book, $user);
        $numPageComments = ceil(count($comments)/6);

        //formulaire de commentaire
        $comment = new Comment();
        if($request->query->get('id')){
            $find_comment = $commentRepository->find($request->query->get('id'));
            if($find_comment){
                if($user->getId() == $find_comment->getUser()->getId()){
                    $comment = $find_comment;
                }
            }
        }


        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$user){
                return $this->redirectToRoute('app_login');
            }
            //verifier si c'est une insersion simple
            $updateId = $form->get("updateId")->getData();
            if(!$updateId){
                //verifier si l'utilisateur n'avais pas encore poster de commentaire:
                $findCommentUserBord = $commentRepository->findOneBy([
                    'bord' => $book,
                    'user' => $user
                ]);
                if($findCommentUserBord != null){
                    $this->addFlash('danger', 'Vous avez déjà poster un commentaire pour ce livre');
                    return $this->redirectToRoute('app_book', ['b'=> $book->getSlug(), 'id'=> $findCommentUserBord->getId()]);
                }
                $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setBord($book)
                    ->setPublished(false)
                    ->setSend(false)
                    ->setUser($user);
                $manager->persist($comment);
                $manager->flush();
                $this->addFlash('success', 'Votre commentaire à bien été enregistré');

            }
            else{
                $comment2 = $commentRepository->find($updateId);
                if($user->getId() == $comment2->getUser()->getId()){

                    $comment2->setContent($form->get('content')->getData())
                        ->setPublished(false)
                        ->setSend(false)
                        ->setRating($form->get('rating')->getData());
                    $manager->persist($comment2);
                    $manager->flush();
                    $this->addFlash('success', 'Votre commentaire à été modifié avec sucès');
                }
            }

            $team = $book->getEditor();
            $email = (new Email())
                ->from($user->getEmail())
                ->to($team->getEmail());
            if(!$updateId){
                $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . '/editor?crudAction=index&crudControllerFqcn=App\Controller\Editor\CommentCrudController&commentId=' . $comment->getId();
                $email->subject('Nouveau commentaire - InfooxSchool.com')
                    ->html('<h1>Bonjour '. $team->getFirstName() .', le nouveau commentaire de '. $user->getFirstName()  .' attend votre approbation pour être publié.</h1> <a style="text-decoration: none" href="'. $url .'">gérer le commentaire</a><h2>Voici le commentaire :</h2> <div>'. $comment->getContent() .'</div>');
            }else{
                $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . '/editor?crudAction=index&crudControllerFqcn=App\Controller\Editor\CommentCrudController&commentId=' . $comment2->getId();
                $email->subject('Commentaire Modifié - InfooxSchool.com')
                    ->html('<h1>Bonjour '. $team->getFirstName() .'</h1><p>'. $user->getFirstName() . ' a bien modifié son commentaire. Son commmentaire attend votre approbation pour être publié.</p> <a style="text-decoration: none" href="'. $url .'">gérer le commentaire</a><h2>Voici le commentaire :</h2> <div>'. $comment2->getContent() .'</div>');
            }
            $mailer->send($email);


            return $this->redirectToRoute('app_book', ['b'=> $book->getSlug()]);

        }


        return $this->render('book/book.html.twig', [
            'book' => $book,
            'prix'=> $bookPrice,
            'form' => $form->createView(),
            'numPageComments' => $numPageComments,
            'idUser' => $idUser,
            'corrige_autorisation' => $corrige_autorisation
        ]);

    }

    #[Route('/cour/{c}', name: 'app_cour')]
    public function cour(string $c, CourRepository $repository, Security $security, UserBordRepository $userBordRepository): Response
    {
        $user = $security->getUser();

        $cour = $repository->findOneBy(
            ['slug' => $c]
        );
        //dd($cour->getBord()->getId());

        $corrige_autorisation = self::autorisationBord($cour->getBord(), $userBordRepository, $user);

        if(!$cour){
            throw new NotFoundHttpException('Pas de cour trouvé');
        }
        $book = $cour->getBord();
        $bookPrice = null;
        if($book->getPrice()) {
            $bookPrice = SearchBookAsynController::calculatePrice($book->getPrice(), '3 jours')->getContent();
        }

        return $this->render('book/cour.html.twig', [
            'prix'=> $bookPrice,
            'cour' => $cour,
            'corrige_autorisation' => $corrige_autorisation,
            'pdf' => '/bords/test/test.pdf'

        ]);

    }

    #[Route('/exercices/{c}', name: 'app_exercices')]
    public function exercices(string $c, CourRepository $repository, Security $security, UserBordRepository $userBordRepository): Response
    {
        $user = $security->getUser();

        $cour = $repository->findOneBy(
            ['slug' => $c]
        );
        //dd($cour->getBord()->getId());

        $corrige_autorisation = self::autorisationBord($cour->getBord(), $userBordRepository, $user);

        if(!$cour){
            throw new NotFoundHttpException('Pas de cour trouvé');
        }
        $book = $cour->getBord();
        $bookPrice = null;
        if($book->getPrice()) {
            $bookPrice = SearchBookAsynController::calculatePrice($book->getPrice(), '3 jours')->getContent();
        }

        return $this->render('book/exercices.html.twig', [
            'prix'=> $bookPrice,
            'cour' => $cour,
            'corrige_autorisation' => $corrige_autorisation,

        ]);

    }

    #[Route('/epreuve/{e}/{type}', name: 'app_epreuve')]
    public function epreuve(string $e, string $type, EpreuveRepository $repository, Security $security, UserBordRepository $userBordRepository): Response
    {
        $user = $security->getUser();

        $epreuve = $repository->findOneBy(
            ['slug' => $e]
        );
        $book = $epreuve->getBord();
        $corrige_autorisation = self::autorisationBord($book, $userBordRepository, $user);

        if(!$epreuve){
            throw new NotFoundHttpException('Pas d\'epreuve trouvé');
        }

        $bookPrice = null;
        if($book->getPrice()) {
            $bookPrice = SearchBookAsynController::calculatePrice($book->getPrice(), '3 jours')->getContent();
        }

        return $this->render('book/epreuve.html.twig', [
            'prix'=> $bookPrice,
            'epreuve' => $epreuve,
            'type' => $type,
            'corrige_autorisation' => $corrige_autorisation,
            'pdf' => '/bords/test/test2.pdf'

        ]);

    }

    #[Route('/exercice/{e}/{type}', name: 'app_exercice')]
    public function exercice(string $e, string $type, ExerciceRepository $repository, Security $security, UserBordRepository $userBordRepository): Response
    {
        $user = $security->getUser();

        $exercice = $repository->findOneBy(
            ['slug' => $e]
        );
        if(!$exercice){
            throw new NotFoundHttpException('Pas d\'exercice trouvé');
        }
        $book = $exercice->getCour()->getBord();
        $corrige_autorisation = self::autorisationBord($book, $userBordRepository, $user);

        $bookPrice = null;
        if($book->getPrice()) {
            $bookPrice = SearchBookAsynController::calculatePrice($book->getPrice(), '3 jours')->getContent();
        }

        return $this->render('book/exercice.html.twig', [
            'prix'=> $bookPrice,
            'exercice' => $exercice,
            'type' => $type,
            'corrige_autorisation' => $corrige_autorisation,
            'pdf' => '/bords/test/test2.pdf'

        ]);

    }

    #[Route('/books/{c?}', name: 'app_books_category')]
    public function booksCategory(?string $c, BordRepository $repository, Request $request, ExamenRepository $examenRepository, FiliereRepository $filiereRepository, ClasseRepository $classeRepository): Response
    {
        $searchTerm = $request->query->get('b');

        $page = $request->query->getInt('page', 1);
        $books = [];
        $menu = [];
        $cat = explode('-', $c);
        $category = end($cat);
        // Extraire les IDs de la chaîne de critères
        preg_match('/m(\d*)/', $category, $matiereMatch);
        preg_match('/c(\d*)/', $category, $classeMatch);
        preg_match('/f(\d*)/', $category, $filiereMatch);
        preg_match('/e(\d*)/', $category, $examenMatch);


        if ((!empty($classeMatch[1]) && !empty($filiereMatch[1])) || !empty($matiereMatch[1])){
            $books = $repository->findBordsByCriteria($category, $page);
            if(!empty($books->getItems())){
                $aleatoireBook = array_rand($books->getItems());
                $menu = DinamicMenyController::bordMeny($books[$aleatoireBook]->getId(), $repository);
            }
        }
        elseif (!empty($examenMatch[1])){
            $books = $repository->findBordsByExamen($category, $page);
            $classesExamen = $examenRepository->find($examenMatch[1])->getClasses();
            $menu = DinamicMenyController::categoryMeny($classesExamen);
        }
        elseif (!empty($filiereMatch[1])){
            $books = $repository->findBordsByCriteria($category, $page);
            $classesFiliere = $filiereRepository->find($filiereMatch[1])->getClasses();
            $menu = DinamicMenyController::categoryMeny($classesFiliere);
        }elseif (!empty($classeMatch[1])){
            $books = $repository->findBordsByCriteria($category, $page);
            $matieresClasse = $classeRepository->find($classeMatch[1])->getMatieres();
            $menu = DinamicMenyController::categoryMeny($matieresClasse, 'm');
        }else{

            $books = $repository->searchBords($searchTerm, $page);
            if(!empty($books->getItems())){
                $aleatoireBook = array_rand($books->getItems());
                $menu = DinamicMenyController::bordMeny($books[$aleatoireBook]->getId(), $repository);
            }
        }

        return $this->render('book/books.html.twig', [
            'books'=> $books,
            'menu'=> $menu,
        ]);

    }

    private static function autorisationBord($book, $userBordRepository, $user = null):bool
    {
        if($book->getPrice()){
            if($user){
                $user_bord = $userBordRepository->findOneBy(
                    [
                        'user' => $user,
                        'bord' => $book
                    ]
                );
                if($user_bord){
                    $now = new \DateTimeImmutable();
                    if($user_bord->getEndAt() > $now){
                        return true;
                    }
                }
            }
        }else{
            return true;
        }
        return false;
    }






}
