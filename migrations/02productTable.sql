CREATE TABLE IF NOT EXISTS product (
	`id` int(6) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`name` varchar(200) NOT NULL,
	`description` TEXT NOT NULL,
	`price` decimal(13,2) NOT NULL DEFAULT 0.00,
	`quantity` int(6) unsigned NOT NULL DEFAULT 0,
	`status` varchar(200) NOT NULL DEFAULT '',
	`fk_supplier` int(6) unsigned,
	CHECK (`price` >= 0),
	CHECK (`quantity` >= 0),
	FOREIGN KEY (`fk_supplier`) REFERENCES supplier(id) ON DELETE CASCADE
) ENGINE=INNODB;