<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use Psr\Log\LoggerInterface;

class PersonneListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public function onnAddPersonneListener(AddPersonneEvent $event)
    {
        $this->logger->debug("coucou je suis en train d'écouter l'evenement personne.add et une 
        personne vien d'etre ajoutée et c'est " . $event->getPersonne()->getName());
    }

    public function onPersonneListAll(ListAllPersonneEvent $event)
    {
        $this->logger->debug(" Le nombre de personne dans la base est " . $event->getNbPersonne());
    }

    public function onPersonneListAll2(ListAllPersonneEvent $event)
    {
        $this->logger->debug(" Le deuxieme " . $event->getNbPersonne());
    }
}
