ALTER TABLE `montage_ont_photos`
    ADD FOREIGN KEY (`montage_ont_id`) REFERENCES `montage_onts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
