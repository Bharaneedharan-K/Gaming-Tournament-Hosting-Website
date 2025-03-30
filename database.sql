-- Create database
CREATE DATABASE gaming41tournament;
USE gaming41tournament;

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tournaments table
CREATE TABLE tournaments (
    tournament_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT,
    tournament_name VARCHAR(100) NOT NULL,
    game_name ENUM('Among Us', 'Minecraft', 'Free Fire', 'BGMI') NOT NULL,
    tournament_date DATETIME NOT NULL,
    max_players INT NOT NULL,
    is_team_based BOOLEAN DEFAULT FALSE,
    team_size INT,
    max_teams INT,
    room_id VARCHAR(50),
    room_password VARCHAR(50),
    is_paid BOOLEAN DEFAULT FALSE,
    registration_fee DECIMAL(10,2),
    winning_prize DECIMAL(10,2),
    upi_id VARCHAR(50),
    contact_info TEXT,
    auto_approval BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id)
);

-- Tournament Participants table
CREATE TABLE tournament_participants (
    participant_id INT PRIMARY KEY AUTO_INCREMENT,
    tournament_id INT,
    user_id INT,
    team_name VARCHAR(50),
    transaction_id VARCHAR(100),
    is_approved BOOLEAN DEFAULT FALSE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Tournament Reports table
CREATE TABLE tournament_reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    tournament_id INT,
    reporter_id INT,
    report_reason TEXT NOT NULL,
    is_valid BOOLEAN DEFAULT FALSE,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id),
    FOREIGN KEY (reporter_id) REFERENCES users(user_id)
);

-- Tournament Winners table
CREATE TABLE tournament_winners (
    winner_id INT PRIMARY KEY AUTO_INCREMENT,
    tournament_id INT,
    user_id INT,
    team_name VARCHAR(50),
    position INT,
    declared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);