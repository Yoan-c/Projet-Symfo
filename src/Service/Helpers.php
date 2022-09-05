<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class Helpers
{

    public function __construct(private LoggerInterface $logger, private Security $security)
    {
    }
    public function sayCc(): string
    {
        return "coucou";
    }
    // Pour recupérer un utilisateur au sein d'un service ex pour envoyer un mail, ou pour un autre service etc...
    public function getUser(): User
    {
        return $this->security->getUser();
    }
}
