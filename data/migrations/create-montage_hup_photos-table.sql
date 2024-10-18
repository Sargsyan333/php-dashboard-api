CREATE TABLE `montage_hup_photos` (
  `id` int UNSIGNED NOT NULL,
  `hup_id` int UNSIGNED NOT NULL,
  `photo_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` enum('OPENED','CLOSED') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `montage_hup_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hup_id` (`hup_id`);

ALTER TABLE `montage_hup_photos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `montage_hup_photos`
  ADD CONSTRAINT `montage_hup_photos_ibfk_1`
      FOREIGN KEY (`hup_id`) REFERENCES `montage_hups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;