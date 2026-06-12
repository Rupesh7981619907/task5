-- SQL for final-blog-project (import into phpMyAdmin / MySQL)

CREATE DATABASE IF NOT EXISTS `final-blog-project` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `final-blog-project`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posts table
CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `author_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example user (replace the password hash with one you generate)
INSERT INTO `users` (`username`, `password`) VALUES
('admin','REPLACE_WITH_HASH');

-- Example posts (ensure the admin user id matches)
INSERT INTO `posts` (`title`, `content`, `author_id`) VALUES
('Welcome to the Blog','This is the first sample post. Edit or remove it.', 1),
('Second Post','Another example post to show listing and pagination.', 1);
