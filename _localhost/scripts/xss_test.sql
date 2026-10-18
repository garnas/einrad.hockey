-- --- teams_liga (UNIQUE(teamname,aktiv) -> ID haelt den Wert eindeutig) ------
UPDATE teams_liga
   SET teamname = CONCAT('<img src=x onerror=alert("XSSCANARY teams_liga.teamname #', team_id, '")>');

-- --- teams_details ----------------------------------------------------------
UPDATE teams_details SET
    verein        = CONCAT('<script>alert("XSSCANARY teams_details.verein #', team_id, '")</script>'),
    ort           = CONCAT('"><svg onload=alert("XSSCANARY teams_details.ort #', team_id, '")>'),
    homepage      = CONCAT('javascript:alert("XSSCANARY teams_details.homepage #', team_id, '")'),
    ligavertreter = CONCAT('<img src=x onerror=alert("XSSCANARY teams_details.ligavertreter #', team_id, '")>');

-- --- teams_kontakt (E-Mail: @-Zeichen belassen) ---------------------------
UPDATE teams_kontakt
   SET email = CONCAT('"><script>alert("XSSCANARY teams_kontakt.email #', teams_kontakt_id, '")</script>@example.com');

-- --- teams_name_historic (name varchar(50) -> kompakte Payload) ----------
UPDATE teams_name_historic
   SET name = '<svg onload=alert("XSSCANARY name_historic")>';

-- --- spieler -----------------------------------------------------------
UPDATE spieler SET
    vorname  = CONCAT('<script>alert("XSSCANARY spieler.vorname #', spieler_id, '")</script>'),
    nachname = CONCAT('"><img src=x onerror=alert("XSSCANARY spieler.nachname #', spieler_id, '")>');

-- --- turniere_liga ---------------------------------------------------
UPDATE turniere_liga SET
    tname          = CONCAT('<script>alert("XSSCANARY turniere_liga.tname #', turnier_id, '")</script>'),
    canceled_grund = CONCAT('<img src=x onerror=alert("XSSCANARY turniere_liga.canceled_grund #', turnier_id, '")>');

-- --- turniere_details (plz varchar(6) wird ausgelassen: zu kurz) --------
UPDATE turniere_details SET
    hallenname   = CONCAT('"><svg onload=alert("XSSCANARY turniere_details.hallenname #', turnier_id, '")>'),
    strasse      = CONCAT('<script>alert("XSSCANARY turniere_details.strasse #', turnier_id, '")</script>'),
    ort          = CONCAT('<img src=x onerror=alert("XSSCANARY turniere_details.ort #', turnier_id, '")>'),
    haltestellen = CONCAT('<b onmouseover=alert("XSSCANARY turniere_details.haltestellen #', turnier_id, '")>x</b>'),
    hinweis      = CONCAT('<script>alert("XSSCANARY turniere_details.hinweis #', turnier_id, '")</script>'),
    organisator  = CONCAT('<img src=x onerror=alert("XSSCANARY turniere_details.organisator #', turnier_id, '")>'),
    handy        = CONCAT('"><svg onload=alert("XSSCANARY turniere_details.handy #', turnier_id, '")>'),
    startgebuehr = CONCAT('<script>alert("XSSCANARY turniere_details.startgebuehr #', turnier_id, '")</script>'),
    format       = CONCAT('"><script>alert("XSSCANARY turniere_details.format #', turnier_id, '")</script>');

-- --- turniere_berichte ---------------------------------------------
UPDATE turniere_berichte
   SET bericht = CONCAT('<script>alert("XSSCANARY turniere_berichte.bericht #', bericht_id, '")</script>');

-- --- neuigkeiten -------------------------------------------------
UPDATE neuigkeiten SET
    titel          = CONCAT('<script>alert("XSSCANARY neuigkeiten.titel #', neuigkeiten_id, '")</script>'),
    inhalt         = CONCAT('<img src=x onerror=alert("XSSCANARY neuigkeiten.inhalt #', neuigkeiten_id, '")>'),
    eingetragen_von= CONCAT('"><script>alert("XSSCANARY neuigkeiten.eingetragen_von #', neuigkeiten_id, '")</script>'),
    bild_verlinken = CONCAT('javascript:alert("XSSCANARY neuigkeiten.bild_verlinken #', neuigkeiten_id, '")');

-- --- teams_strafen --------------------------------------------
UPDATE teams_strafen SET
    grund       = CONCAT('<script>alert("XSSCANARY teams_strafen.grund #', strafe_id, '")</script>'),
    prozentsatz = CONCAT('<img src=x onerror=alert("XSSCANARY teams_strafen.prozentsatz #', strafe_id, '")>');

-- --- spieler_zeitstrafen -------------------------------------
UPDATE spieler_zeitstrafen SET
    spieler = CONCAT('<script>alert("XSSCANARY spieler_zeitstrafen.spieler #', zeitstrafe_id, '")</script>'),
    grund   = CONCAT('<b onmouseover=alert("XSSCANARY spieler_zeitstrafen.grund #', zeitstrafe_id, '")>x</b>'),
    team_a  = CONCAT('<img src=x onerror=alert("XSSCANARY spieler_zeitstrafen.team_a #', zeitstrafe_id, '")>'),
    team_b  = CONCAT('"><svg onload=alert("XSSCANARY spieler_zeitstrafen.team_b #', zeitstrafe_id, '")>');

-- --- spieler_ausleihen -----------------------------------
UPDATE spieler_ausleihen SET
    spieler  = CONCAT('<script>alert("XSSCANARY spieler_ausleihen.spieler #', ausleihe_id, '")</script>'),
    team_auf = CONCAT('<img src=x onerror=alert("XSSCANARY spieler_ausleihen.team_auf #', ausleihe_id, '")>'),
    team_ab  = CONCAT('"><svg onload=alert("XSSCANARY spieler_ausleihen.team_ab #', ausleihe_id, '")>');

-- --- ligakarte_gesuch -----------------------------------
UPDATE ligakarte_gesuch SET
    ort     = CONCAT('<script>alert("XSSCANARY ligakarte_gesuch.ort #', gesuch_id, '")</script>'),
    r_name  = CONCAT('<img src=x onerror=alert("XSSCANARY ligakarte_gesuch.r_name #', gesuch_id, '")>'),
    kontakt = CONCAT('"><svg onload=alert("XSSCANARY ligakarte_gesuch.kontakt #', gesuch_id, '")>');

-- --- regelwerk (regeltitel varchar(100)) ---------------
UPDATE regelwerk SET
    regeltitel = CONCAT('<script>alert("XSSCANARY regelwerk.regeltitel ', COALESCE(regelnummer,'?'), '")</script>'),
    regeltext  = CONCAT('<img src=x onerror=alert("XSSCANARY regelwerk.regeltext ', COALESCE(regelnummer,'?'), '")>');

-- --- ligaleitung (nur funktion; login/passwort/email unangetastet) --
UPDATE ligaleitung
   SET funktion = CONCAT('<script>alert("XSSCANARY ligaleitung.funktion #', ligaleitung_id, '")</script>');
