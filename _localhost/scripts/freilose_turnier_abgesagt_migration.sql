-- Migration: Freilosgrund TURNIER_ABGESAGT
--
-- Wird ein Turnier abgesagt, erhalten angemeldete Teams ihr gesetztes Freilos zurück.
-- Das gesetzte Freilos bleibt als verbraucht bestehen; das Team erhält ein neues Freilos
-- mit dem Grund TURNIER_ABGESAGT, das über vorheriges_freilos auf das verbrauchte verweist.
--
-- Das Skript ist wiederholbar (die Spalte wird jeweils vollständig neu definiert).

ALTER TABLE `freilose`
  MODIFY `grund` enum('SCHIRI','TURNIER_AUSGERICHTET','FREILOS_GESETZT','NEUES_LIGATEAM','SONSTIGES','TURNIER_ABGESAGT') NOT NULL;
