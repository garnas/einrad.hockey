<?php
namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereLog;
use DateTime;
use Helper;

class TurnierLogService {

    private array $logTexts;
    private Turnier $turnier;

    public function __construct(Turnier $turnier)
    {
        $this->logTexts = [];
        $this->turnier = $turnier;
    }

    /**
     * Schreibt in den Turnierlog.
     *
     * Turnierlogs werden bei Zerstörung des Objektes in die DB geschrieben.
     *
     * @param string $log_text
     */
    public function addLog(string $log_text): void
    {
        $this->logTexts[] = $log_text;
    }

    public function getLogAsString(): string
    {
        return implode("\r\n", $this->logTexts);
    }

    public function autoLog(string $name, mixed $alt, mixed $neu): void
    {
        if ($alt === false) {
            $alt = "false";
        }

        if ($alt === true) {
            $alt = "true";
        }

        if ($alt instanceof DateTime) {
            $alt = $alt->format("d.m.Y");
        }

        if ($neu instanceof DateTime) {
            $neu = $neu->format("d.m.Y");
        }

        if ($alt !== $neu){
            $this->addLog($name . ": " . $alt . " -> " . $neu );
        }
    }

    public function addAllLogs(): void
    {
        if (!empty($this->logTexts)) {
            $log = new TurniereLog();
            $log->setAutor(Helper::get_akteur());
            $log->setTurnier($this->turnier);
            $logText = $this->getLogAsString();
            $log->setLogText($logText);
            $this->turnier->getLogs()->add($log);
        }
    }

}
