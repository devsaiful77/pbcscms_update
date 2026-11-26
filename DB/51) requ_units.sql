SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `units`  AS SELECT `roles`.`id` AS `id`, `roles`.`display_name` AS `name`, `roles`.`description` AS `description`, `roles`.`is_user` AS `status`, `roles`.`is_head` AS `is_head`, `roles`.`module` AS `module` FROM `roles` WHERE (`roles`.`is_user` = 1) ;

COMMIT;