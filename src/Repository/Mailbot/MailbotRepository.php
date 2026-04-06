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

    /**
     * @param int $limit
     * @return nMailbot[]
     */
    public function findPending(int $limit = 75): array
    {
        return $this->mailBot->findBy(['mailStatus' => 'warte'], ['zeit' => 'ASC'], $limit);
    }

    /**
     * @param int $mailId
     * @return nMailbot|null
     */
    public function findPendingById(int $mailId): ?nMailbot
    {
        return $this->mailBot->findOneBy(['mailId' => $mailId, 'mailStatus' => 'warte']);
    }

    public function countFailed(): int
    {
        return $this->mailBot->count(['mailStatus' => 'fehler']);
    }

    public function save(nMailbot $mail): void
    {
        DoctrineWrapper::manager()->persist($mail);
        DoctrineWrapper::manager()->flush();
    }

    public function flush(): void
    {
        DoctrineWrapper::manager()->flush();
    }
}
