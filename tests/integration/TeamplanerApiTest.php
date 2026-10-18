<?php

namespace integration;

use App\Turnier\Repository\TurnierRepository;
use App\Tabelle\Service\TabelleService;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use xml;

class TeamplanerApiTest extends TestCase
{
    public function testTurnierelisteToXml(): void
    {
        $turniere = TurnierRepository::get()->getKommendeTurniere()->toArray();
        $xmlContent = xml::turniereToXml(turniere: $turniere);
        $this->assertStringContainsString(needle: "<turniere", haystack: $xmlContent);
    }

    public function testRangtabelleToXml(): void
    {
        $rangtabelle = TabelleService::getRangTabelle(TabelleService::getAktuellenSpieltag() - 1);
        $xml = new SimpleXMLElement('<rangtabelle/>');
        $xmlContent = xml::array_to_xml($rangtabelle, $xml, "platz");
        $this->assertStringContainsString(needle: "<rangtabelle", haystack: $xmlContent);

    }

    public function testTurnieranmeldungenToXml(): void
    {
        $turnieranmeldungen = TurnierRepository::get()->getAnmeldungenOffeneTurniere();
        $xmlContent = xml::turnieranmeldungenToXml($turnieranmeldungen);
        $this->assertStringContainsString(needle: "<turnieranmeldungen", haystack: $xmlContent);
    }

}
