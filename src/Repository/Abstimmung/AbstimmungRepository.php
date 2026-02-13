<?php

namespace App\Repository\Abstimmung;

use App\Entity\Abstimmung\AbstimmungTeam;
use App\Entity\Abstimmung\AbstimmungVote;
use App\Entity\Team\nTeam;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use Doctrine\ORM\EntityRepository;
use Helper;

class AbstimmungRepository
{
    
    use TraitSingletonRepository;

    private EntityRepository $abstimmungVote;
    private EntityRepository $abstimmungTeam;

    private function __construct()
    {
        $this->abstimmungVote = DoctrineWrapper::manager()->getRepository(AbstimmungVote::class);
        $this->abstimmungTeam = DoctrineWrapper::manager()->getRepository(AbstimmungTeam::class);
    }

   
    public function hasVote(nTeam $team): bool
    {
        return $this->abstimmungTeam->find($team->id()) !== null;
    }
    
    public function getStimme(string $crypt): array
    {
        $vote = $this->abstimmungVote->find($crypt);
        $plain = $vote->getStimme();
        return json_decode($plain);
    }
    
    public function setStimme(nTeam $team, string $crypt, array $stimme): string
    {
        
        $parsed = json_encode(array_keys($stimme));
        
        if (!$this->hasVote($team)) {
        
            $abstimmungTeam = new AbstimmungTeam();
            $abstimmungTeam->setTeam($team);
            $abstimmungTeam->setAenderungen(0);
            DoctrineWrapper::manager()->persist($abstimmungTeam);
            
            $abstimmungVote = new AbstimmungVote();
            $abstimmungVote->setCrypt($crypt);
            $abstimmungVote->setStimme($parsed);
            DoctrineWrapper::manager()->persist($abstimmungVote);
            
            DoctrineWrapper::manager()->flush();
            Helper::log("abstimmung.log", $team->getName() . " hat seine Stimme abgegeben");
            return "Dein Team hat erfolgreich abgestimmt. Vielen Dank!";
        
        } else {

            $abstimmungTeam = $this->abstimmungTeam->find($team->id());
            $abstimmungTeam->addAenderung(1);

            $abstimmungVote = $this->abstimmungVote->find($crypt);
            $abstimmungVote->setStimme($parsed);
            DoctrineWrapper::manager()->flush();
            
            Helper::log("abstimmung.log", $team->getName() . " hat seine Stimme geändert");
            return "Dein Team hat erfolgreich neu abgestimmt. Vielen Dank!";
        
        }
        
    }

    public function getAllVotes() {
        $result = $this->abstimmungVote->createQueryBuilder('v')
            ->select('v.stimme')
            ->getQuery()
            ->getScalarResult();

        return $result;
    }

    public function getParticipation() {
        $result = $this->abstimmungTeam->findAll();
        return $result;
    }

}