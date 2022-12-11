<?php


namespace App\Repository\Mailbot;

use App\Entity\Sonstiges\nMailbot;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use Doctrine\ORM\EntityRepository;

class MailbotRepository
{

    use TraitSingletonRepository;

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