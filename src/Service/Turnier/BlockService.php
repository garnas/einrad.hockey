<?php

namespace App\Service\Turnier;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use Config;

class BlockService
{
    public static function getHigherBlocks($block): array
    {
        //  Höhere mögliche Turnierblöcke Blöcke werden gesucht und an sollen an turner_erstellen.tmp.php übergeben werden
        $block_higher = []; //  Array der möglichen höheren Turnierblöcke

        // Position des eigenen Blockes im Array der Blöcke
        $chosen = array_search($block, Config::BLOCK);

        foreach (Config::BLOCK as $blockVgl) {
            if (array_search($blockVgl, Config::BLOCK) <= $chosen) {
                $block_higher[] = $blockVgl;
            }
        }

        return $block_higher ?? [];
    }

    public static function isTurnierBlockHigher(Turnier $turnier, nteam $team): bool
    {
        $rangTurnierBock = array_search($turnier->getBlock(), Config::BLOCK_ALL, true);
        $rangTeamBlock = array_search($team->getBlock(), Config::BLOCK_ALL, true);

        return $rangTurnierBock < $rangTeamBlock;
    }

    public static function toString(mixed $blockContext): string
    {
        if (is_a($blockContext, Turnier::class)) {
            if ($blockContext->isSofortOeffnen() && $blockContext->isWartePhase()) {
                $blockToHighlight = $blockContext->getBlock();
                $string = str_replace(
                    $blockToHighlight,
                    "<span class='w3-text-black' style='font-style: normal'>$blockToHighlight</span>",
                    Config::BLOCK_ALL[0]);
                return "(<span class='w3-text-gray' style='font-style: italic'>" . $string . "</span>)";
            }
            return "(" . $blockContext->getBlock() . ")";
        }

        if (is_a($blockContext, nTeam::class)){
            return $blockContext->isLigaTeam() ? "(" . $blockContext->getBlock() . ")" : "";
        }

        if (is_string($blockContext)) {
            return "(" . $blockContext . ")";
        }

        if (is_array($blockContext)) {
            return "(" . implode(",", $blockContext) . ")";
        }

        return "";
    }

    public static function nextTurnierBlock(Turnier $turnier): string
    {
        return Config::BLOCK_ALL[array_search($turnier->getBlock(), Config::BLOCK_ALL)-1];
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