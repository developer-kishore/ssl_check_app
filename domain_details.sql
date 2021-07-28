CREATE TABLE `domain_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_on` datetime default CURRENT_TIMESTAMP,
  `project_name` varchar(255) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `email_sent` int(11) NOT NULL default '0',
  `days_to_remind` int(11) NOT NULL default '2',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;