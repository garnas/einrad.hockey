<?php

namespace App\Repository;

use Config;
use Helper;
use Psr\Log\LoggerInterface;
use Stringable;

class Logger implements LoggerInterface
{
    private function isWriteOperation(array $context): bool
    {
        if (!key_exists("sql", $context)) {
            return false;
        }
        $sql = strtolower($context["sql"]);
        return stripos($sql, "select") !== 0;    }

    private function isInsertTurniereLog(array $context): bool
    {
        if (!key_exists("sql", $context)) {
            return false;
        }
        $sql = strtolower($context["sql"]);
        return stripos($sql, "insert into turniere_log") === 0;
    }
    private function isInsertMailbot(array $context): bool
    {
        if (!key_exists("sql", $context)) {
            return false;
        }
        $sql = strtolower($context["sql"]);
        return stripos($sql, "insert into mailbot") === 0;
    }

    private function superLog(string $level, string $message, array $context = []): void
    {
        if (!in_array($level, ["debug", "info" , "notice"])) {
            $log = $level . " - " . $message . " - " . print_r($context, true);
            Helper::log(Config::LOG_DB_DOCTRINE, $log);
        }
        if (
            $this->isWriteOperation($context)
            && !$this->isInsertTurniereLog($context)
            && !$this->isInsertMailbot($context)
        ) {
            $sql = $context["sql"];
            $params = "\n?: " . implode("\n?: ", $context["params"]);
            $log = $sql . $params;
            Helper::log(Config::LOG_DB_DOCTRINE, $log);
        }
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->superLog("emergency", $message, $context);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->superLog("alert", $message, $context);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->superLog("critical", $message, $context);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->superLog("error", $message, $context);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->superLog("warning", $message, $context);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->superLog("notice", $message, $context);
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->superLog("info", $message, $context);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->superLog("debug", $message, $context);
    }

    # Logs with an arbitrary level, forward to superLog for maintainability
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->superLog($level, $message, $context);
    }
}