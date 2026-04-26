-- Database schema for Book Request Management System

CREATE DATABASE IF NOT EXISTS book_system;
USE book_system;

-- Users table for all roles
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'superadmin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table to store fetched data
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    UNIQUE KEY unique_book (title, author, category)
);

-- Book requests table
CREATE TABLE IF NOT EXISTS book_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_title VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    is_notified TINYINT(1) DEFAULT 1, -- 1 means no new notification, 0 means user hasn't seen the change
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- API Usage Tracking (5 calls per 24 hours)
CREATE TABLE IF NOT EXISTS api_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    call_count INT DEFAULT 0,
    last_call TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert a default Super Admin (Password: super123)
-- In a real app, you'd hash this. I'll provide instructions in README.
-- INSERT INTO users (username, email, password, role) VALUES ('superadmin', 'super@system.com', '$2y$10$YourHashedPasswordHere', 'superadmin');
