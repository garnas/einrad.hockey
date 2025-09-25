alter table `ligaleitung`
change `funktion` `funktion`
enum(
    'ligaausschuss',
    'schiriausschuss',
    'oeffentlichkeitsausschuss',
    'technikausschuss',
    'schiriausbilder',
    'admin',
    'schiripruefer'
); 


insert into ligaleitung (spieler_id, funktion) values
    (191, 'schiripruefer'),
    (64, 'schiripruefer'),
    (2262, 'schiripruefer'),
    (168, 'schiripruefer'),
    (297, 'schiripruefer')
;