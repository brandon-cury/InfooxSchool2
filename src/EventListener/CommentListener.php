<?php

namespace App\EventListener;

use App\Entity\Comment;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CommentListener
{
    private $urlGenerator;
    private $security;
    private $mailer;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security, MailerInterface $mailer)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->mailer = $mailer;
    }

    #[AsEventListener(event: 'preUpdate')]
    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();

        // Vérifiez si l'entité est de type Comment
        if (!$entity instanceof Comment) {
            return;
        }

        // Vérifiez si le champ 'send' a changé
        if($event->hasChangedField('is_published') && $event->getNewValue('is_published')){
            $this->sendMessagePublished($entity);
        }
        elseif ($event->hasChangedField('is_send') && $event->getNewValue('is_send')) {
                $this->sendMessageNoPublished($entity);
        }

    }

    private function sendMessagePublished(Comment $comment): void
    {
        // Créez l'URL vers la page de votre site
        $url = $this->urlGenerator->generate('app_book', ['b' => $comment->getBord()->getSlug(), 'id' => $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $user = $this->security->getUser();

        $email = (new Email())
            ->from($user->getEmail())
            ->to($comment->getUser()->getEmail())
            ->subject('Commentaire publié - InfooxSchool.com')
            ->html('<h1>Bonjour '. $comment->getUser()->getFirstName() .', votre commentaire a été publié avec succès !</h1> <a style="text-decoration: none" href="'. $url .'">aller au commentaire</a><h2>Merci pour votre commentaire:</h2> <div>'. $comment->getContent() .'</div>');
        $this->mailer->send($email);

    }
    private function sendMessageNoPublished(Comment $comment): void
    {
        // Créez l'URL vers la page de votre site
        $url = $this->urlGenerator->generate('app_book', ['b' => $comment->getBord()->getSlug(), 'id' => $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $user = $this->security->getUser();

        $email = (new Email())
            ->from($user->getEmail())
            ->to($comment->getUser()->getEmail())
            ->subject('Commentaire non publié - InfooxSchool.com')
            ->html('<h1>Bonjour ' . $comment->getUser()->getFirstName() . ', votre commentaire a été retiré.</h1> <p>Veuillez le modifier et nous le soumettre à nouveau !</p> <a style="text-decoration: none" href="' . $url . '">Aller au commentaire</a><h2>Merci pour votre commentaire:</h2> <div>' . $comment->getContent() . '</div>');
        $this->mailer->send($email);

    }



}
