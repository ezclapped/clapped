SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `users` (
    `id` int(11) AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `is_admin` BOOLEAN DEFAULT FALSE,
    `is_banned` BOOLEAN DEFAULT FALSE,
    `apikey` varchar(50) NOT NULL,
    `created_at` datetime DEFAULT current_timestamp()
    `folder` varchar(255) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE licensekeys (
    `id` INT PRIMARY KEY,
    `key` VARCHAR(50) NOT NULL,
    `used` BOOLEAN DEFAULT FALSE
);

ALTER TABLE `users`
    ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

COMMIT;