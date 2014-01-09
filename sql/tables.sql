DROP TABLE IF EXISTS `artist`;
 CREATE TABLE `artist` (
 `id` int(11) NOT NULL,
 `name` varchar(255) DEFAULT NULL,
 `gender` varchar(8) DEFAULT NULL,
 `dates` varchar(255) DEFAULT NULL,
 `yearOfBirth` varchar(255) DEFAULT NULL,
 `yearOfDeath` varchar(255) DEFAULT NULL,
 `placeOfBirth` varchar(255) DEFAULT NULL,
 `placeOfDeath` varchar(255) DEFAULT NULL,
 `url` varchar(255) DEFAULT NULL,
 KEY (`id`),
 KEY `gender` (`gender`),
 KEY `born` (`yearOfBirth`),
 KEY `died` (`yearOfDeath`),
 FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
LOAD DATA LOCAL INFILE 'artist_data.csv' INTO TABLE artist CHARACTER SET utf8 FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 LINES (`id`, `name`, `gender`, `dates`, `yearOfBirth`, `yearOfDeath` , `placeOfBirth`, `placeOfDeath`, `url`);

 DROP TABLE IF EXISTS `artwork`;
 CREATE TABLE `artwork` (
  `id` int(11) unsigned NOT NULL,
  `accession_number` varchar(255) DEFAULT NULL,
  `artist` varchar(255) DEFAULT NULL,
  `artistRole` varchar(32) DEFAULT NULL,
  `artistId` int(11) unsigned NOT NULL,
  `title` mediumtext,
  `dateText` varchar(255) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `creditLine` mediumtext,
  `year` int(4) DEFAULT NULL,
  `acquisitionYear` int(4) DEFAULT NULL,
  `dimensions` varchar(64) DEFAULT NULL,
  `width` varchar(8) DEFAULT NULL,
  `height` varchar(8) DEFAULT NULL,
  `depth` varchar(8) DEFAULT NULL,
  `units` varchar(2) DEFAULT NULL,
  `inscription` mediumtext DEFAULT NULL,
  `thumbnailCopyright` mediumtext,
  `thumbnailUrl` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `year` (`year`),
  KEY `artistID` (`artistId`),
  KEY `acquisitionYear` (`acquisitionYear`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `artist` (`artist`),
  FULLTEXT KEY `creditLine` (`creditLine`),
  FULLTEXT KEY `medium` (`medium`),
  FULLTEXT KEY `inscription` (`inscription`),
  FULLTEXT KEY `thumbnailCopyright` (`thumbnailCopyright`),
  FULLTEXT KEY `dateText` (`dateText`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 LOAD DATA LOCAL INFILE 'artwork_data.csv' INTO TABLE artwork CHARACTER SET utf8 FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' IGNORE 1 LINES (`id`, `accession_number`, `artist`, `artistRole`, `artistId`, `title`, `dateText`, `medium`, `creditLine`, `year`, `acquisitionYear`, `dimensions`, `width`, `height`, `depth`, `units`, `inscription`, `thumbnailCopyright`, `thumbnailUrl`, `url`);

UPDATE artwork SET creditLine = RTRIM(creditLine), SET title = RTRIM(title);