CREATE TABLE `expired_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_on` datetime default CURRENT_TIMESTAMP,
  `project_name` varchar(255) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL default 'expired',
  `email_sent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;