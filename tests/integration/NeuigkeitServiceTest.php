<?php

namespace integration;

use App\Entity\Sonstiges\Neuigkeit;
use App\Enum\NeuigkeitArt;
use App\Repository\DoctrineWrapper;
use App\Repository\Neuigkeit\NeuigkeitRepository;
use App\Service\Neuigkeit\PermissionService;
use DateTime;
use Helper;
use PHPUnit\Framework\TestCase;

class NeuigkeitServiceTest extends TestCase
{
    private array $createdIds = [];

    protected function tearDown(): void
    {
        Helper::$ligacenter = false;
        Helper::$team_social_media = false;
        unset($_SESSION['logins']['team']);

        foreach ($this->createdIds as $id) {
            $neuigkeit = NeuigkeitRepository::get()->findById($id);
            if ($neuigkeit) {
                NeuigkeitRepository::get()->delete($neuigkeit);
            }
        }
        $this->createdIds = [];
    }

    private function persistNeuigkeit(Neuigkeit $neuigkeit): int
    {
        NeuigkeitRepository::get()->create($neuigkeit);
        $this->createdIds[] = $neuigkeit->getNeuigkeitenId();
        return $neuigkeit->getNeuigkeitenId();
    }

    // Löst den Identity Map Cache von Doctrine, damit findById() wirklich aus der DB liest.
    private function fetchFresh(int $id): Neuigkeit
    {
        DoctrineWrapper::manager()->clear();
        return NeuigkeitRepository::get()->findById($id);
    }

    public function testCreateNeuigkeitPersistsAllFields(): void
    {
        $zeitpunkt = new DateTime('2026-08-01 10:00:00');
        $neuigkeit = (new Neuigkeit())
            ->setTitel('Neue Saison beginnt')
            ->setArt(NeuigkeitArt::NEUIGKEIT)
            ->setInhalt('Die neue Saison startet in zwei Wochen.')
            ->setLinkPdf('')
            ->setLinkJpg('')
            ->setBildVerlinken('')
            ->setEingetragenVon('Ligaausschuss')
            ->setZeit($zeitpunkt);

        $id = $this->persistNeuigkeit($neuigkeit);
        $gespeichert = $this->fetchFresh($id);

        $this->assertNotNull($gespeichert);
        $this->assertSame('Neue Saison beginnt', $gespeichert->getTitel());
        $this->assertSame(NeuigkeitArt::NEUIGKEIT, $gespeichert->getArt());
        $this->assertSame('Die neue Saison startet in zwei Wochen.', $gespeichert->getInhalt());
        $this->assertSame('Ligaausschuss', $gespeichert->getEingetragenVon());
        $this->assertEquals($zeitpunkt, $gespeichert->getZeit());
    }

    public function testCreateNeuigkeitDefaultsToAktivAndArtNeuigkeit(): void
    {
        $neuigkeit = (new Neuigkeit())
            ->setInhalt('Ein Text ohne explizite Art oder Aktiv-Angabe.')
            ->setLinkPdf('')
            ->setLinkJpg('')
            ->setBildVerlinken('')
            ->setEingetragenVon('Team Social Media')
            ->setZeit(new DateTime());

        $id = $this->persistNeuigkeit($neuigkeit);
        $gespeichert = $this->fetchFresh($id);

        $this->assertTrue($gespeichert->isAktiv());
        $this->assertSame(NeuigkeitArt::NEUIGKEIT, $gespeichert->getArt());
    }

    public function testEditNeuigkeitPersistsChangedFields(): void
    {
        $id = $this->persistNeuigkeit(
            (new Neuigkeit())
                ->setTitel('Alter Titel')
                ->setArt(NeuigkeitArt::NEUIGKEIT)
                ->setInhalt('Alter Inhalt')
                ->setLinkPdf('')
                ->setLinkJpg('')
                ->setBildVerlinken('')
                ->setEingetragenVon('Ligaausschuss')
                ->setZeit(new DateTime('2026-01-01 00:00:00')),
        );

        $neuerZeitpunkt = new DateTime('2026-08-10 12:30:00');
        $neuigkeit = NeuigkeitRepository::get()->findById($id);
        $neuigkeit->setTitel('Neuer Titel');
        $neuigkeit->setArt(NeuigkeitArt::FOERDERMITTEL);
        $neuigkeit->setInhalt('Neuer Inhalt');
        $neuigkeit->setZeit($neuerZeitpunkt);
        NeuigkeitRepository::get()->update($neuigkeit);

        $gespeichert = $this->fetchFresh($id);

        $this->assertSame('Neuer Titel', $gespeichert->getTitel());
        $this->assertSame(NeuigkeitArt::FOERDERMITTEL, $gespeichert->getArt());
        $this->assertSame('Neuer Inhalt', $gespeichert->getInhalt());
        $this->assertEquals($neuerZeitpunkt, $gespeichert->getZeit());
        // Unveränderte Felder bleiben erhalten.
        $this->assertSame('Ligaausschuss', $gespeichert->getEingetragenVon());
    }

    public function testCanEditAllowsLigaausschussForAnyEintrag(): void
    {
        Helper::$ligacenter = true;
        Helper::$team_social_media = false;

        $this->assertTrue(PermissionService::canEdit('Irgendein Team'));
        $this->assertTrue(PermissionService::canEdit('Ligaausschuss'));
    }

    public function testCanEditAllowsOeffentlichkeitsausschussExceptOwnLigaausschussEintraege(): void
    {
        Helper::$ligacenter = false;
        Helper::$team_social_media = true;

        $this->assertTrue(PermissionService::canEdit('Irgendein Team'));
        $this->assertFalse(PermissionService::canEdit('Ligaausschuss'));
    }

    public function testCanEditAllowsOnlyOwningTeam(): void
    {
        Helper::$ligacenter = false;
        Helper::$team_social_media = false;
        $_SESSION['logins']['team']['name'] = 'Eigenes Team';

        $this->assertTrue(PermissionService::canEdit('Eigenes Team'));
        $this->assertFalse(PermissionService::canEdit('Fremdes Team'));
    }

    public function testCanEditDeniesWhenNoRoleMatches(): void
    {
        Helper::$ligacenter = false;
        Helper::$team_social_media = false;
        unset($_SESSION['logins']['team']);

        $this->assertFalse(PermissionService::canEdit('Irgendein Team'));
    }

    public function testCanSetTimeAndEmbedLinkOnlyForLigacenterOrSocialMedia(): void
    {
        Helper::$ligacenter = false;
        Helper::$team_social_media = false;
        $this->assertFalse(PermissionService::canSetTime());
        $this->assertFalse(PermissionService::canEmbedLink());

        Helper::$ligacenter = true;
        $this->assertTrue(PermissionService::canSetTime());
        $this->assertTrue(PermissionService::canEmbedLink());

        Helper::$ligacenter = false;
        Helper::$team_social_media = true;
        $this->assertTrue(PermissionService::canSetTime());
        $this->assertTrue(PermissionService::canEmbedLink());
    }
}
