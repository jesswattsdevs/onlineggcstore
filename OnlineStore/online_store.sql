CREATE DATABASE IF NOT EXISTS onlinestore;
USE onlinestore;

DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_date DATE NOT NULL,
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    pw VARCHAR(120) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    profile_image VARCHAR(255) DEFAULT '',
    security_question_1 VARCHAR(255) NOT NULL,
    security_answer_1 VARCHAR(255) NOT NULL,
    security_question_2 VARCHAR(255) NOT NULL,
    security_answer_2 VARCHAR(255) NOT NULL
);

CREATE TABLE items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    upload_date DATE NOT NULL,
    sold_date DATE DEFAULT NULL,
    seller_id INT NOT NULL,
    buyer_id INT DEFAULT NULL,
    status ENUM('available', 'sold') NOT NULL DEFAULT 'available',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_items_seller FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_items_buyer FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO users
    (registration_date, first_name, last_name, email, phone, pw, role, security_question_1, security_answer_1, security_question_2, security_answer_2)
VALUES
    (CURDATE(), 'Admin', 'User', 'admin@ggc.edu', '770-555-1000', 'admin123', 'admin', 'Favorite color?', 'orange', 'Dream job?', 'teacher'),
    (CURDATE(), 'Maya', 'Student', 'maya@ggc.edu', '770-555-2000', 'student123', 'student', 'Favorite snack?', 'chips', 'Birth city?', 'atlanta'),
    (CURDATE(), 'Jordan', 'Seller', 'jordan@ggc.edu', '770-555-3000', 'student123', 'student', 'Favorite snack?', 'cookies', 'Birth city?', 'duluth');

INSERT INTO items
    (item_name, description, price, image_name, upload_date, sold_date, seller_id, buyer_id, status, is_featured)
VALUES
    ('Calculus Textbook', 'Used textbook in good condition with highlighted notes.', 42.00, '', CURDATE(), NULL, 2, NULL, 'available', 1),
    ('Desk Lamp', 'Small dorm lamp with adjustable neck and warm light bulb.', 18.50, '', CURDATE(), NULL, 3, NULL, 'available', 0),
    ('Mini Fridge', 'Compact fridge, works well, a few scratches on the side.', 85.00, '', CURDATE(), NULL, 3, NULL, 'available', 1);

INSERT INTO announcements (title, body, created_at, active)
VALUES
    ('Welcome to the GGC Online Store', 'Browse items without logging in. Register or log in when you are ready to buy or sell.', NOW(), 1),
    ('Safety Reminder', 'Meet in a public place on campus when exchanging items.', NOW(), 1);
