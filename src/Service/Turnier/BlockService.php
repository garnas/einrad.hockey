<?php

namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;
use App\Entity\Team\nTeam;
use Config;

class BlockService
{
    public static function getHigherBlocks($block): array
    {
        //  Höhere mögliche Turnierblöcke Blöcke werden gesucht und an sollen an turner_erstellen.tmp.php übergeben werden
        $block_higher = []; //  Array der möglichen höheren Turnierblöcke

        // Position des eigenen Blockes im Array der Blöcke
        $chosen = array_search($block, Config::BLOCK);

        while ($chosen >= 0) {
            $block_higher[] = Config::BLOCK[$chosen];
            --$chosen;
        }

        return $block_higher;
    }

    public static function toString(mixed $block): string
    {
        if (is_string($block)) {
            return "(" . $block . ")";
        }
        if (is_array($block)) {
            return "(" . implode(",", $block) . ")";
        }
        return "";
    }

    public static function isBlockPassend(Turnier $turnier, nTeam $team): bool
    {
        $blockTurnier = $turnier->getBlock();
        $blockTeam = $team->getBlock();

        // Block-String in Array auflösen
        $buchstabenTurnier = str_split($blockTurnier);
        $buchstachenTeam = str_split($blockTeam);

        // Check ob ein Buchstabe des Team-Blocks im Turnier-Block vorkommt
        foreach ($buchstachenTeam as $buchstabe) {
            if (in_array($buchstabe, $buchstabenTurnier)) {
                return true;
            }
        }

        return false;

    }
}