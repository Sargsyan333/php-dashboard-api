ALTER TABLE `montage_hups`
    ADD `preinstalled_at` DATETIME NULL AFTER `created_at`,
    ADD `installed_at` DATETIME NULL AFTER `preinstalled_at`;

ALTER TABLE `montage_job_photos`
    ADD FOREIGN KEY (`montage_job_id`) REFERENCES `montage_jobs`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `montage_hups` DROP `opened_hup_photo_path`, DROP `closed_hup_photo_path`;