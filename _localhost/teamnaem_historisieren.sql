-- SAISON anpassen!

INSERT INTO db_localhost.teams_name_historic (saison, team_id, name)
SELECT
    30 AS saison,
    team_id,
    teamname AS name
FROM
    db_localhost.teams_liga
WHERE
    aktiv = 'Ja' and ligateam = 'Ja';