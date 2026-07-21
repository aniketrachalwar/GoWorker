-- GoWorker Database Initializer & Local Collaborative Network Setup
-- Database: goworker

-- 1. Create LAN Developer user with secure remote network permissions
-- The password matches the DB_PASS in config.php.
-- '%' allows connection from any local network client IP.
CREATE USER IF NOT EXISTS 'goworker_dev'@'%' IDENTIFIED BY 'GoWorkerLAN2026!';
GRANT ALL PRIVILEGES ON `goworker`.* TO 'goworker_dev'@'%';
FLUSH PRIVILEGES;

-- 2. Setup Database schema
CREATE DATABASE IF NOT EXISTS `goworker` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `goworker`;

-- Drop existing tables in reverse dependency order to prevent constraints errors
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `worker_profiles`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- 3. Create Users Table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `user_type` ENUM('customer', 'worker') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_user_type` (`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create Categories Table
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) UNIQUE NOT NULL,
  `icon_class` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create Worker Profiles Table
CREATE TABLE `worker_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `category_id` INT DEFAULT NULL,
  `title` VARCHAR(100) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `hourly_rate` DECIMAL(10,2) DEFAULT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `availability` VARCHAR(255) DEFAULT NULL,
  `skills` TEXT DEFAULT NULL,
  `experience_years` INT DEFAULT 0,
  `profile_picture` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_worker_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_worker_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  INDEX `idx_worker_location` (`location`),
  INDEX `idx_worker_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create Bookings Table
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT NOT NULL,
  `worker_id` INT NOT NULL,
  `booking_date` DATE NOT NULL,
  `time_slot` VARCHAR(50) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `total_price` DECIMAL(10,2) DEFAULT NULL,
  `status` ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_booking_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_booking_worker` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_booking_customer` (`customer_id`),
  INDEX `idx_booking_worker` (`worker_id`),
  INDEX `idx_booking_date` (`booking_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Create Reviews Table
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `customer_id` INT NOT NULL,
  `worker_id` INT NOT NULL,
  `rating` INT NOT NULL,
  `review_text` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_review_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_worker` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_review_booking` (`booking_id`),
  INDEX `idx_review_worker` (`worker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Seed Service Categories
INSERT INTO `categories` (`id`, `name`, `icon_class`) VALUES
(1, 'Electrician', 'fa-bolt'),
(2, 'Plumber', 'fa-faucet'),
(3, 'Carpenter', 'fa-hammer'),
(4, 'Painter', 'fa-paint-roller'),
(5, 'Cleaner', 'fa-broom'),
(6, 'Appliance Repair', 'fa-screwdriver-wrench'),
(7, 'Mechanic', 'fa-gears'),
(8, 'Mason (Mistri)', 'fa-trowel-bricks'),
(9, 'Construction Labour', 'fa-person-digging'),
(10, 'Helper Labour', 'fa-hands-holding'),
(11, 'Tile Fitter', 'fa-border-all'),
(12, 'POP Worker', 'fa-paint-roller'),
(13, 'Painter Labour', 'fa-fill-drip'),
(14, 'Concrete Worker', 'fa-cubes'),
(15, 'Scaffolding Worker', 'fa-building-shield'),
(16, 'Wireman', 'fa-plug'),
(17, 'CCTV Installer', 'fa-video'),
(18, 'Inverter Technician', 'fa-car-battery'),
(19, 'Solar Panel Technician', 'fa-solar-panel'),
(20, 'Borewell Technician', 'fa-water'),
(21, 'Water Tank Cleaner', 'fa-faucet-drip'),
(22, 'Furniture Assembler', 'fa-couch'),
(23, 'Modular Furniture Installer', 'fa-chair'),
(24, 'House Cleaner', 'fa-broom'),
(25, 'Deep Cleaning Service', 'fa-soap'),
(26, 'Sofa Cleaner', 'fa-couch'),
(27, 'Carpet Cleaner', 'fa-rug'),
(28, 'Bathroom Cleaner', 'fa-shower'),
(29, 'AC Technician', 'fa-snowflake'),
(30, 'Refrigerator Repair', 'fa-temperature-arrow-down'),
(31, 'Washing Machine Repair', 'fa-soap'),
(32, 'TV Repair', 'fa-tv'),
(33, 'Microwave Repair', 'fa-fire-burner'),
(34, 'Water Purifier Repair', 'fa-filter'),
(35, 'Gardener', 'fa-seedling'),
(36, 'Pest Control', 'fa-bugs'),
(37, 'Security Guard', 'fa-user-shield'),
(38, 'Driver', 'fa-car'),
(39, 'Cook', 'fa-utensils'),
(40, 'Maid', 'fa-broom'),
(41, 'Babysitter', 'fa-baby'),
(42, 'Elder Care Assistant', 'fa-user-nurse'),
(43, 'Welder', 'fa-fire'),
(44, 'Fabricator', 'fa-industry'),
(45, 'Steel Worker', 'fa-cubes'),
(46, 'Aluminium Worker', 'fa-sheet-plastic'),
(47, 'Loader', 'fa-box-open'),
(48, 'Unloader', 'fa-dolly'),
(49, 'Packers & Movers', 'fa-truck-ramp-box'),
(50, 'Tempo Service', 'fa-truck-pickup'),
(51, 'Truck Helper', 'fa-truck-front'),
(52, 'Bike Repair', 'fa-motorcycle'),
(53, 'Car Washing', 'fa-car-wash'),
(54, 'Puncture Repair', 'fa-circle-dot'),
(55, 'Computer Repair', 'fa-desktop'),
(56, 'Laptop Repair', 'fa-laptop'),
(57, 'Mobile Repair', 'fa-mobile-screen-button'),
(58, 'Network Technician', 'fa-wifi'),
(59, 'Farm Labour', 'fa-wheat-awn'),
(60, 'Tractor Driver', 'fa-tractor'),
(61, 'Irrigation Worker', 'fa-droplet'),
(62, 'Dairy Worker', 'fa-cow'),
(63, 'Photographer', 'fa-camera'),
(64, 'Videographer', 'fa-video'),
(65, 'DJ Service', 'fa-music'),
(66, 'Event Decorator', 'fa-icons'),
(67, 'Beautician', 'fa-scissors'),
(68, 'Hair Stylist', 'fa-scissors'),
(69, 'Makeup Artist', 'fa-wand-magic-sparkles'),
(70, 'Mehendi Artist', 'fa-hand-sparkles')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `icon_class` = VALUES(`icon_class`);

-- 9. Seed Sample Developer Users (Passwords hashed using standard password_hash with DEFAULT)
-- Passwords are: 'password123'
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `location`, `user_type`) VALUES
(1, 'Aniket Rachalwar', 'aniket@example.com', '$2y$10$UoVkvMvpx/e8eX4l4dF0PeehL7KkX0uW6k4g2tVb/6P0j/U4qQYqK', '9876543210', 'Pune', 'customer'),
(2, 'Ramesh Kumar', 'ramesh@example.com', '$2y$10$UoVkvMvpx/e8eX4l4dF0PeehL7KkX0uW6k4g2tVb/6P0j/U4qQYqK', '9876543211', 'Pune', 'worker'),
(3, 'Sohan Singh', 'sohan@example.com', '$2y$10$UoVkvMvpx/e8eX4l4dF0PeehL7KkX0uW6k4g2tVb/6P0j/U4qQYqK', '9876543212', 'Mumbai', 'worker'),
(4, 'Sunita Patil', 'sunita@example.com', '$2y$10$UoVkvMvpx/e8eX4l4dF0PeehL7KkX0uW6k4g2tVb/6P0j/U4qQYqK', '9876543213', 'Pune', 'customer')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- 10. Seed Worker Profiles
INSERT INTO `worker_profiles` (`user_id`, `category_id`, `title`, `bio`, `hourly_rate`, `location`, `availability`, `skills`, `experience_years`, `profile_picture`) VALUES
(2, 1, 'Senior Certified Electrician', 'Expert in residential wiring, short-circuit diagnostics, and smart home appliances installer.', 299.00, 'Pune', 'Mon, Tue, Wed, Thu, Fri', 'Wiring, Fuse repairs, Smart Home, Inverter Setup', 5, 'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=150&fit=crop'),
(3, 2, 'Expert Plumbing & Leak Repair Tech', '24/7 emergency pipe repair, bathroom fittings installations, and sewage cleaning specialist.', 399.00, 'Mumbai', 'Mon, Wed, Fri, Sat', 'Leak repair, Faucet Fitting, Drainage cleaning', 8, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&fit=crop')
ON DUPLICATE KEY UPDATE `bio` = VALUES(`bio`);

-- 11. Seed Sample Bookings
INSERT INTO `bookings` (`id`, `customer_id`, `worker_id`, `booking_date`, `time_slot`, `description`, `address`, `total_price`, `status`) VALUES
(1, 1, 2, CURDATE(), '09:00 AM - 11:00 AM', 'AC wiring circuit breaker is tripping repeatedly.', 'Flat 402, Royal Residency, Kothrud, Pune', 598.00, 'confirmed'),
(2, 4, 3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '02:00 PM - 04:00 PM', 'Kitchen sink pipe is leaking extensively.', 'Flat 12, Rosewood Society, Bandra, Mumbai', 798.00, 'completed')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- 12. Seed Sample Reviews
INSERT INTO `reviews` (`booking_id`, `customer_id`, `worker_id`, `rating`, `review_text`) VALUES
(2, 4, 3, 5, 'Very quick service and professional behavior! Replaced the pipe immediately.')
ON DUPLICATE KEY UPDATE `review_text` = VALUES(`review_text`);
