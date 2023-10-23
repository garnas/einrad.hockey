<?php
/**
 * TeamsName
 * 
 * @ORM\Entity
 * @ORM\Table(name="teams_name", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unique", columns={"saison", "team_id"})
 * })
 */

class TeamName
{
    /**
     * @var int
     * 
     * @ORM\Column(name="saison", type="integer", nullable="false")
     */
    private int $saison;

    /**
     * @var int
     * 
     * @ORM\Column(name="team_id", type="integer", nullable="false")
     */
    private int $team_id;

    /**
     * @var string
     * 
     * @ORM\Column(name="teamname", type="string", nullable="false")
     */
    private int $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}