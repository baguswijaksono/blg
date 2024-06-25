CREATE TABLE `blogs` (
  `id` int(6) UNSIGNED NOT NULL,
  `topic` varchar(255) NOT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `title` longtext NOT NULL,
  `hypertext` longtext DEFAULT NULL,
  `shortdesc` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tags` (
  `id` int(6) UNSIGNED NOT NULL,
  `tag_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `blog_tags` (
  `blog_id` int(6) UNSIGNED NOT NULL,
  `tag_id` int(6) UNSIGNED NOT NULL,
  PRIMARY KEY (`blog_id`, `tag_id`),
  KEY `blog_id` (`blog_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `blog_tags_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`),
  CONSTRAINT `blog_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `views` (
  `id` int(6) UNSIGNED NOT NULL,
  `content_id` int(6) UNSIGNED DEFAULT NULL,
  `views_count` int(6) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  CONSTRAINT `views_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `blogs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `blogs`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

ALTER TABLE `tags`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `views`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;
