<?php

namespace App\Repository\Mailbot;

use App\Entity\Mailbot\nMailbot;
use App\Repository\DoctrineWrapper;
use App\Repository\SingletonRepositoryTrait;
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
