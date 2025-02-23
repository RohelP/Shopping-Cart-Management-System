CREATE TABLE IF NOT EXISTS supplier (
	`id` int(6) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`name` varchar(200) NOT NULL,
	`address` varchar(200) NOT NULL DEFAULT '',
	`phone` varchar(200),
	`email` varchar(200)
) ENGINE=INNODB;