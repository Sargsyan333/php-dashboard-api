ALTER TABLE `montage_jobs` ADD `tb_file_path` VARCHAR(255) NULL AFTER `hb_file_path`;

ALTER TABLE `montage_onts`
    ADD `preinstalled_at` DATETIME NULL AFTER `created_at`,
    ADD `installed_at` DATETIME NULL AFTER `preinstalled_at`;
