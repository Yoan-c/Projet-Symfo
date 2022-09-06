<?php

namespace App\EventSubscriber;

use App\Service\MailerService;
use App\Event\AddPersonneEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonneEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerService $mailer,
        private LoggerInterface $logger
    ) {
    }
    public static function getSubscribedEvents()
    {
        return [
            AddPersonneEvent::ADD_PERSONNE_EVENT => ['onAddPersonneEvent', 3000]
        ];
    }

    public function onAddPersonneEvent(AddPersonneEvent $event)
    {
        $personne = $event->getPersonne();
        $mailMessage = $personne->getFirstname() . "" . $personne->getName() . " a été ajouté ";
        $this->logger->info("Envoi d'email pour subscribe");
        $this->mailer->sendEmail(content: $mailMessage, subject: 'Mail sent from EventSubscriber');
    }
}
