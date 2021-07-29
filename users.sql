CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_on` datetime default CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

insert  into `users`(`username`,`password`) values ('admin','zeoner@123');

