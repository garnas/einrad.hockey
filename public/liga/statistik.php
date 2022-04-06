<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$max_turniere = Stats::get_turniere_team(limit: 999);
$max_gew = Stats::get_gew_spiele_team(limit: 999);
$max_ausrichter = Stats::get_max_ausrichter(limit: 999);
$max_turnierorte = Stats::get_max_turnierorte(limit: 999);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Statistik | Deutsche Einradhockeyliga";
Html::$content = "Zahlen zur aktuellen Saison";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<h1 class="w3-text-primary">Statistik der Saison <?=Html::get_saison_string()?></h1>

<h3 class="w3-text-secondary">Turnierspieler</h3>
<p class="w3-border-top w3-border-grey w3-text-grey">
    <span>Anzahl der gespielten Turniere pro Team</span>
</p>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th><b>Platz</b></th>
                <th><b>Team</b></th>
                <th><b>Anzahl</b></th>
            </tr>
        </thead>
        <?php foreach ($max_turniere as $team):?>
            <tr>
                <td><?=$team['platz']?></td>
                <td><?=$team['teamname']?></td>
                <td><?=$team['gespielt']?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<h3 class="w3-text-secondary">Spielsieger</h3>
<p class="w3-border-top w3-border-grey w3-text-grey">
    <span>Anzahl der Siege pro Team</span>
</p>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th><b>Platz</b></th>
                <th><b>Team</b></th>
                <th><b>Anzahl</b></th>
            </tr>
        </thead>
        <?php foreach ($max_gew as $team):?>
            <tr>
                <td><?=$team['platz']?></td>
                <td><?=$team['teamname']?></td>
                <td><?=$team['siege']?></td>
            </tr>
        <?php endforeach; ?>
    </table>    
</div>

<h3 class="w3-text-secondary">Ausrichter</h3>
<p class="w3-border-top w3-border-grey w3-text-grey">
    <span>Anzahl der ausgerichteten Turniere nach Team</span>
</p>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th><b>Platz</b></th>
                <th><b>Team</b></th>
                <th><b>Anzahl</b></th>
            </tr>
        </thead>
        <?php foreach ($max_ausrichter as $team):?>
            <tr>
                <td><?=$team['platz']?></td>
                <td><?=$team['teamname']?></td>
                <td><?=$team['anzahl']?></td>
            </tr>
        <?php endforeach; ?>
    </table>    
</div>

<h3 class="w3-text-secondary">Turnierorte</h3>
<p class="w3-border-top w3-border-grey w3-text-grey">
    <span>Anzahl der ausgerichteten Turniere nach Ort</span>
</p>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th><b>Platz</b></th>
                <th><b>Team</b></th>
                <th><b>Anzahl</b></th>
            </tr>
        </thead>
        <?php foreach ($max_turnierorte as $ort):?>
            <tr>
                <td><?=$ort['platz']?></td>
                <td><?=$ort['ort']?></td>
                <td><?=$ort['anzahl']?></td>
            </tr>
        <?php endforeach; ?>
    </table>    
</div>

<?php
include Env::BASE_PATH . '/templates/footer.tmp.php';