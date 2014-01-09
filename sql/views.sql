DROP VIEW IF EXISTS `view_collection`;
CREATE VIEW `view_collection` AS
SELECT `w`.`id` AS `id`,
LEFT(`a`.`gender`,1) AS `gender`,
`a`.`name` AS `name`,
`w`.`acquisitionYear` AS `acquired`,
IF(INSTR(`creditLine`,'purchase'),1,0) AS purchased
FROM `artwork` `w`
LEFT JOIN `artist` `a` ON `a`.`id` = `w`.`artistId`
ORDER BY `acquisitionYear` ASC;

DROP VIEW IF EXISTS `view_acquired_by_gender`;
CREATE VIEW `view_acquired_by_gender` AS
SELECT `acquired` AS `acquired`,
COUNT(`id`) AS `total`,
`gender`,
`purchased`,
GROUP_CONCAT(`id` ORDER BY `name`) AS `ids`
FROM `view_collection`
GROUP BY `acquired`, `gender`, `purchased`;