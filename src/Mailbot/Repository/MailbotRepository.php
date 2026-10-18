<?php

namespace App\Mailbot\Repository;

use App\Mailbot\Entity\nMailbot;
use App\Shared\DoctrineWrapper;
use App\Shared\SingletonRepositoryTrait;
use Doctrine\ORM\EntityRepository;

class MailbotRepository
{
    use SingletonRepositoryTrait;

    private EntityRepository $mailBot;

    private function __construct()
    {
        $this->mailBot = DoctrineWrapper::manager()->getRepository(nMailbot::class);
    }

    public function findAll(): array
    {
        return $this->mailBot->findAll();
    }


}
