<?php

namespace integration;

use App\Repository\DoctrineWrapper;
use App\Repository\Turnier\TurnierRepository;
use db;
use nTurnier;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Tabelle;
use xml;

class TeamplanerApiTest extends TestCase
{
    public function testTurnierelisteToXml(): void
    {
        DoctrineWrapper::setup();
        $turniere = TurnierRepository::getKommendeTurniere()->toArray();
        $xmlContent = xml::turniereToXml(turniere: $turniere);
        $this->assertStringContainsString(needle: "<turniere", haystack: $xmlContent);
    }

    public function testRangtabelleToXml(): void
    {
        db::initialize();
        $rangtabelle = Tabelle::get_rang_tabelle(Tabelle::get_aktuellen_spieltag()-1);
        $xml = new SimpleXMLElement('<rangtabelle/>');
        $xmlContent = xml::array_to_xml($rangtabelle, $xml, "platz");
        $this->assertStringContainsString(needle: "<rangtabelle", haystack: $xmlContent);

    }

    public function testTurnieranmeldungenToXml(): void
    {
        db::initialize();
        $turnieranmeldungen = nTurnier::get_all_anmeldungen();
        $xml = new SimpleXMLElement('<turnieranmeldungen/>');
        $xmlContent = xml::array_to_xml($turnieranmeldungen,$xml,"meldungen","team");
        $this->assertStringContainsString(needle: "<turnieranmeldungen", haystack: $xmlContent);
    }

}