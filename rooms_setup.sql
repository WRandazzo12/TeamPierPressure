-- Create rooms table for cruise ship
-- 12 rooms total: 3 rooms per floor across 4 floors
-- Each floor has: Interior, Balcony, Luxury Suite
-- All rooms start as available - bookings are stored when guests book

-- Drop existing tables if they exist (to recreate with correct structure)
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `rooms`;

CREATE TABLE `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_number` varchar(10) NOT NULL,
  `floor` int NOT NULL,
  `room_type` varchar(20) NOT NULL COMMENT 'interior, ocean, balcony, suite',
  `price_per_night` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'available' COMMENT 'available, booked, maintenance',
  `description` text,
  `features` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_number` (`room_number`),
  KEY `floor` (`floor`),
  KEY `room_type` (`room_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert 12 rooms: 3 per floor, 4 floors
-- Floor 1: Interior, Balcony, Luxury Suite (all available)
INSERT INTO `rooms` (`room_number`, `floor`, `room_type`, `price_per_night`, `status`, `description`, `features`) VALUES
('101', 1, 'interior', 89.00, 'available', 'Comfortable interior stateroom with modern amenities', 'Queen bed, Private bathroom, TV & WiFi, Room service'),
('102', 1, 'balcony', 189.00, 'available', 'Private balcony with breathtaking ocean panoramas', 'Private balcony, Queen bed, Sitting area, Mini-bar, Priority boarding'),
('103', 1, 'suite', 299.00, 'available', 'Spacious suite with premium amenities and concierge service', 'Separate living room, King bed, Large balcony, Butler service, Priority everything');

-- Floor 2: Interior, Balcony, Luxury Suite (all available)
INSERT INTO `rooms` (`room_number`, `floor`, `room_type`, `price_per_night`, `status`, `description`, `features`) VALUES
('201', 2, 'interior', 89.00, 'available', 'Comfortable interior stateroom with modern amenities', 'Queen bed, Private bathroom, TV & WiFi, Room service'),
('202', 2, 'balcony', 189.00, 'available', 'Private balcony with breathtaking ocean panoramas', 'Private balcony, Queen bed, Sitting area, Mini-bar, Priority boarding'),
('203', 2, 'suite', 299.00, 'available', 'Spacious suite with premium amenities and concierge service', 'Separate living room, King bed, Large balcony, Butler service, Priority everything');

-- Floor 3: Interior, Balcony, Luxury Suite (all available)
INSERT INTO `rooms` (`room_number`, `floor`, `room_type`, `price_per_night`, `status`, `description`, `features`) VALUES
('301', 3, 'interior', 89.00, 'available', 'Comfortable interior stateroom with modern amenities', 'Queen bed, Private bathroom, TV & WiFi, Room service'),
('302', 3, 'balcony', 189.00, 'available', 'Private balcony with breathtaking ocean panoramas', 'Private balcony, Queen bed, Sitting area, Mini-bar, Priority boarding'),
('303', 3, 'suite', 299.00, 'available', 'Spacious suite with premium amenities and concierge service', 'Separate living room, King bed, Large balcony, Butler service, Priority everything');

-- Floor 4: Interior, Balcony, Luxury Suite (all available)
INSERT INTO `rooms` (`room_number`, `floor`, `room_type`, `price_per_night`, `status`, `description`, `features`) VALUES
('401', 4, 'interior', 89.00, 'available', 'Comfortable interior stateroom with modern amenities', 'Queen bed, Private bathroom, TV & WiFi, Room service'),
('402', 4, 'balcony', 189.00, 'available', 'Private balcony with breathtaking ocean panoramas', 'Private balcony, Queen bed, Sitting area, Mini-bar, Priority boarding'),
('403', 4, 'suite', 299.00, 'available', 'Spacious suite with premium amenities and concierge service', 'Separate living room, King bed, Large balcony, Butler service, Priority everything');

-- Create bookings table to track room bookings
-- Note: Foreign key constraints are commented out to work with different users table structures
-- Uncomment and adjust if your users table structure matches
CREATE TABLE `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `room_id` int NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `cruise_name` varchar(100) NOT NULL,
  `departure_date` date NOT NULL,
  `passengers` int NOT NULL DEFAULT 2,
  `total_price` decimal(10,2) NOT NULL,
  `booking_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'confirmed' COMMENT 'confirmed, cancelled, completed',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`),
  KEY `departure_date` (`departure_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No pre-booked rooms - all rooms start as available
-- Bookings will be created automatically when guests book rooms through the website
-- The book_room.php script handles storing bookings in this table

-- Optional: Add foreign key constraints if your users table structure supports it
-- Uncomment the following lines if your users table has an id column as primary key:
-- ALTER TABLE `bookings` ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
-- ALTER TABLE `bookings` ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

