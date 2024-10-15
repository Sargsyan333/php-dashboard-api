ALTER TABLE `montage_hups`
    ADD `preinstalled_at` DATETIME NULL AFTER `created_at`,
    ADD `installed_at` DATETIME NULL AFTER `preinstalled_at`;
