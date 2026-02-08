<?php

namespace App\Repository\Abstimmung;

use Doctrine\ORM\EntityRepository;

use Helper;
use Html;
use Env;
use db;

use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use App\Entity\Abstimmung\AbstimmungVote;
use App\Entity\Abstimmung\AbstimmungTeam;
use App\Entity\Team\nTeam;

class AbstimmungRepository
{
    
    use TraitSingletonRepository;

    private EntityRepository $vote_repo;
    private EntityRepository $team_repo;

    private function __construct()
    {
        $this->vote_repo = DoctrineWrapper::manager()->getRepository(AbstimmungVote::class);
        $this->team_repo = DoctrineWrapper::manager()->getRepository(AbstimmungTeam::class);
    }

   
    public function hasVote(nTeam $team): bool
    {
        return $this->team_repo->find($team->id()) !== null;
    }
    
    public function getStimme(string $crypt): array
    {
        $vote = $this->vote_repo->find($crypt);
        $plain = $vote->getStimme();
        return json_decode($plain);
    }
    
    public function setStimme(nTeam $team, string $crypt, array $stimme): string
    {
        
        $hasVote = $this->hasVote($team);
        $parsed = json_encode(array_keys($stimme));
        
        if (!$hasVote) {
        
            $new_team = new AbstimmungTeam();
            $new_team->setTeam($team);
            $new_team->setAenderungen(0);
            DoctrineWrapper::manager()->persist($new_team);
            
            $new_vote = new AbstimmungVote();
            $new_vote->setCrypt($crypt);
            $new_vote->setStimme($parsed);
            DoctrineWrapper::manager()->persist($new_vote);
            
            DoctrineWrapper::manager()->flush();
            Helper::log("abstimmung.log", $team->getName() . " hat seine Stimme abgegeben");
            return "Dein Team hat erfolgreich abgestimmt. Vielen Dank!";
        
        } else {

            $old_team = $this->team_repo->find($team->id());
            $old_team->addAenderung(1);
        
            $old_vote = $this->vote_repo->find($crypt);
            $old_vote->setStimme($parsed);
            DoctrineWrapper::manager()->flush();
            
            Helper::log("abstimmung.log", $team->getName() . " hat seine Stimme geändert");
            return "Dein Team hat erfolgreich neu abgestimmt. Vielen Dank!";
        
        }
        
    }

}