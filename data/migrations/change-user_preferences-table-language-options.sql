ALTER TABLE `user_preferences`
    CHANGE `language` `language` ENUM('en','de','es')
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;