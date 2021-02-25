<?php

// Turnier-ID
$turnier_id = (int) ($_GET['turnier_id'] ?? 0);

// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist($turnier_id)) {
    Handler::not_found("Spielplan wurde nicht gefunden");
}

// Spielplan laden
$spielplan = new Spielplan_JgJ((new Turnier ($turnier_id)));