-- Use the database
USE project_db;

-- Insert Sample Data into Users Table
INSERT INTO users (username, email, password)
VALUES
('john_doe', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuvxyz123456'), -- Replace with a hashed password
('jane_smith', 'jane@example.com', '$2y$10$abcdefghijklmnopqrstuvxyz123456');

-- Insert Sample Data into Profiles Table
INSERT INTO profiles (user_id, first_name, last_name, mobile_number, social_link, profile_picture)
VALUES
(1, 'John', 'Doe', '1234567890', 'https://twitter.com/johndoe', 'profile_pic_1.jpg'),
(2, 'Jane', 'Smith', '0987654321', 'https://linkedin.com/in/janesmith', 'profile_pic_2.jpg');
