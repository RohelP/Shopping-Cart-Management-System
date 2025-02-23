CREATE TABLE IF NOT EXISTS cart (
	`_key` int(6) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`uid` varchar(20) NOT NULL,
	`fk_product` int(6) unsigned NOT NULL,
	`quantity` int(6) unsigned NOT NULL DEFAULT 0,
	CHECK (`quantity` >= 0),
	UNIQUE(`uid`, `fk_product`),
	FOREIGN KEY (`fk_product`) REFERENCES product(_key) ON DELETE CASCADE
) ENGINE=INNODB;