CREATE TABLE `montage_job_comments` (
  `id` int UNSIGNED NOT NULL,
  `job_id` int UNSIGNED NOT NULL,
  `coworker_id` int UNSIGNED NOT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `montage_job_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `coworker_id` (`coworker_id`);

ALTER TABLE `montage_job_comments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `montage_job_comments`
  ADD CONSTRAINT `montage_job_comments_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `montage_jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `montage_job_comments_ibfk_2` FOREIGN KEY (`coworker_id`) REFERENCES `coworkers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
