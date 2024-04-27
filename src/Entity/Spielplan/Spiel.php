<?php

namespace App\Entity\Spielplan;

use Doctrine\DBAL\Types\Types;

#[Entity]
#[Table(name: 'spiele')]
class Spiel
{

    #[Column(name: 'turnier_id', type: Types::INTEGER)]
    private int $turnierId;

    #[Column(name: 'spiel_id', type: Types::INTEGER)]
    private int $spielId;

    #[Column(name: 'team_id_a', type: Types::INTEGER)]
    private int $teamIdA;

    #[Column(name: 'team_id_b', type: Types::INTEGER)]
    private int $teamIdB;

    #[Column(name: 'schiri_team_id_a', type: Types::INTEGER)]
    private int $schiriTeamIdA;

    #[Column(name: 'schiri_team_id_b', type: Types::INTEGER)]
    private int $schiriTeamIdB;

    #[Column(name: 'tore_a', type: Types::INTEGER, nullable: true)]
    private ?int $toreA;

    #[Column(name: 'tore_b', type: Types::INTEGER, nullable: true)]
    private ?int $toreB;

    #[Column(name: 'penalty_a', type: Types::INTEGER, nullable: true)]
    private ?int $penaltyA;

    #[Column(name: 'penalty_b', type: Types::INTEGER, nullable: true)]
    private ?int $penaltyB;

}