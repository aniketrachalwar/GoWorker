-- Create the Database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `goworker` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `goworker`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
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

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) UNIQUE NOT NULL,
  `icon_class` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Worker Profiles Table
CREATE TABLE IF NOT EXISTS `worker_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `category_id` INT DEFAULT NULL,
  `title` VARCHAR(100) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `hourly_rate` DECIMAL(10,2) DEFAULT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `availability` VARCHAR(255) DEFAULT NULL, -- e.g. "Mon, Tue, Wed, Thu, Fri"
  `skills` TEXT DEFAULT NULL, -- Comma-separated list of tags
  `experience_years` INT DEFAULT 0,
  `profile_picture` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_worker_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_worker_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  INDEX `idx_worker_location` (`location`),
  INDEX `idx_worker_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bookings Table
CREATE TABLE IF NOT EXISTS `bookings` (
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

-- 5. Reviews Table
CREATE TABLE IF NOT EXISTS `reviews` (
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

-- Seed Data: Service Categories
INSERT INTO `categories` (`name`, `icon_class`) VALUES
('Electrician', 'fa-bolt'),
('Plumber', 'fa-faucet'),
('Carpenter', 'fa-hammer'),
('Painter', 'fa-paint-roller'),
('Cleaner', 'fa-broom'),
('Appliance Repair', 'fa-screwdriver-wrench'),
('Mechanic', 'fa-gears'),
('Mason (Mistri)', 'fa-trowel-bricks'),
('Construction Labour', 'fa-person-digging'),
('Helper Labour', 'fa-hands-holding'),
('Tile Fitter', 'fa-border-all'),
('POP Worker', 'fa-paint-roller'),
('Painter Labour', 'fa-fill-drip'),
('Concrete Worker', 'fa-cubes'),
('Scaffolding Worker', 'fa-building-shield'),
('Wireman', 'fa-plug'),
('CCTV Installer', 'fa-video'),
('Inverter Technician', 'fa-car-battery'),
('Solar Panel Technician', 'fa-solar-panel'),
('Borewell Technician', 'fa-water'),
('Water Tank Cleaner', 'fa-faucet-drip'),
('Furniture Assembler', 'fa-couch'),
('Modular Furniture Installer', 'fa-chair'),
('House Cleaner', 'fa-broom'),
('Deep Cleaning Service', 'fa-soap'),
('Sofa Cleaner', 'fa-couch'),
('Carpet Cleaner', 'fa-rug'),
('Bathroom Cleaner', 'fa-shower'),
('AC Technician', 'fa-snowflake'),
('Refrigerator Repair', 'fa-temperature-arrow-down'),
('Washing Machine Repair', 'fa-soap'),
('TV Repair', 'fa-tv'),
('Microwave Repair', 'fa-fire-burner'),
('Water Purifier Repair', 'fa-filter'),
('Gardener', 'fa-seedling'),
('Pest Control', 'fa-bugs'),
('Security Guard', 'fa-user-shield'),
('Driver', 'fa-car'),
('Cook', 'fa-utensils'),
('Maid', 'fa-broom'),
('Babysitter', 'fa-baby'),
('Elder Care Assistant', 'fa-user-nurse'),
('Welder', 'fa-fire'),
('Fabricator', 'fa-industry'),
('Steel Worker', 'fa-cubes'),
('Aluminium Worker', 'fa-sheet-plastic'),
('Loader', 'fa-box-open'),
('Unloader', 'fa-dolly'),
('Packers & Movers', 'fa-truck-ramp-box'),
('Tempo Service', 'fa-truck-pickup'),
('Truck Helper', 'fa-truck-front'),
('Bike Repair', 'fa-motorcycle'),
('Car Washing', 'fa-car-wash'),
('Puncture Repair', 'fa-circle-dot'),
('Computer Repair', 'fa-desktop'),
('Laptop Repair', 'fa-laptop'),
('Mobile Repair', 'fa-mobile-screen-button'),
('Network Technician', 'fa-wifi'),
('Farm Labour', 'fa-wheat-awn'),
('Tractor Driver', 'fa-tractor'),
('Irrigation Worker', 'fa-droplet'),
('Dairy Worker', 'fa-cow'),
('Photographer', 'fa-camera'),
('Videographer', 'fa-video'),
('DJ Service', 'fa-music'),
('Event Decorator', 'fa-icons'),
('Beautician', 'fa-scissors'),
('Hair Stylist', 'fa-scissors'),
('Makeup Artist', 'fa-wand-magic-sparkles'),
('Mehendi Artist', 'fa-hand-sparkles')
ON DUPLICATE KEY UPDATE `icon_class` = VALUES(`icon_class`);

-- Phase 1 Tables: Favorites & Worker Availability
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT NOT NULL,
  `worker_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_fav_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_worker` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_fav` (`customer_id`, `worker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `worker_availability` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `worker_id` INT NOT NULL UNIQUE,
  `is_online` TINYINT(1) DEFAULT 1,
  `status_text` VARCHAR(100) DEFAULT 'Available Now',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_avail_worker` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
