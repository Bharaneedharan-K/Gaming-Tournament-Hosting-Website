-- http://localhost/Game%20Tournament/login/


CREATE TABLE users (
    user_id VARCHAR(50) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS game_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_name VARCHAR(255) NOT NULL,
    game_image LONGBLOB NOT NULL
);


CREATE TABLE tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50),
    user_name VARCHAR(100),
    tournament_id VARCHAR(100) UNIQUE,
    tournament_name VARCHAR(255),
    tournament_date DATE,
    contact_info VARCHAR(255),
    game_name VARCHAR(255),
    num_players INT,
    team_size INT,
    fee_type ENUM('free', 'paid'),
    top_1_prize VARCHAR(255),
    top_2_prize VARCHAR(255),
    top_3_prize VARCHAR(255),
    upi_id VARCHAR(255),
    tournament_image LONGBLOB
);