INSERT INTO spielplan_paarungen VALUES
                        ('8er_jgj_bfinal',1,4,7,4,7),
                        ('8er_jgj_bfinal',2,1,6,1,6),
                        ('8er_jgj_bfinal',3,1,7,1,7),
                        ('8er_jgj_bfinal',4,4,6,4,6),
                        ('8er_jgj_bfinal',5,1,4,1,4),
                        ('8er_jgj_bfinal',6,6,7,6,7),
                        ('8er_jgj_bfinal',7,7,5,7,5),
                        ('8er_jgj_bfinal',8,1,3,1,3),
                        ('8er_jgj_bfinal',9,3,7,3,7),
                        ('8er_jgj_bfinal',10,1,5,1,5),
                        ('8er_jgj_bfinal',11,1,8,1,8),
                        ('8er_jgj_bfinal',12,2,7,2,7),
                        ('8er_jgj_bfinal',13,7,8,7,8),
                        ('8er_jgj_bfinal',14,1,2,1,2),
                        ('8er_jgj_bfinal',15,3,5,3,5),
                        ('8er_jgj_bfinal',16,2,8,2,8),
                        ('8er_jgj_bfinal',17,3,8,3,8),
                        ('8er_jgj_bfinal',18,2,5,2,5),
                        ('8er_jgj_bfinal',19,5,8,5,8),
                        ('8er_jgj_bfinal',20,2,3,2,3),
                        ('8er_jgj_bfinal',21,4,8,4,8),
                        ('8er_jgj_bfinal',22,2,6,2,6),
                        ('8er_jgj_bfinal',23,2,4,2,4),
                        ('8er_jgj_bfinal',24,8,6,8,6),
                        ('8er_jgj_bfinal',25,4,5,4,5),
                        ('8er_jgj_bfinal',26,3,6,3,6),
                        ('8er_jgj_bfinal',27,6,5,6,5),
                        ('8er_jgj_bfinal',28,4,3,4,3);

INSERT INTO spielplan_details VALUES
    ('8er_jgj_bfinal','8er_jgj_bfinal',8,2,5,5,'6,30#10,930#20,30#24,930',8);

UPDATE `turniere_liga` SET `spielplan_vorlage` = '8er_jgj_bfinal' WHERE `turniere_liga`.`turnier_id` = 1005;

