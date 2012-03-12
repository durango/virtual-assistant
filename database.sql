CREATE TABLE `logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `guestName` varchar(100) NOT NULL DEFAULT '',
  `employeeName` varchar(100) NOT NULL DEFAULT '',
  `topic` text NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `jabber_id` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;