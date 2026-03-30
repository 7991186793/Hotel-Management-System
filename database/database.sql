CREATE DATABASE hotel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hotel_db;

-- Users Table (Admin + Customer)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms Table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) UNIQUE,
    room_type VARCHAR(100) NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    description TEXT,
    image VARCHAR(255),
    status ENUM('available', 'booked', 'maintenance') DEFAULT 'available'
);

-- Bookings Table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    room_id INT,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

-- Sample Data
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@gmail.com', '1234', 'admin');   -- Simple password as you wanted

INSERT INTO rooms (room_number, room_type, price_per_night, capacity, description, status) VALUES
('101', 'Deluxe', 2500.00, 2, 'AC room with double bed and balcony', 'available'),
('102', 'Suite', 4500.00, 4, 'Luxury suite with living area', 'available'),
('103', 'Standard', 1500.00, 2, 'Basic AC room', 'available');