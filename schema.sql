-- HarborWing Air Database Schema Implementation
CREATE DATABASE IF NOT EXISTS harborwing_air;
USE harborwing_air;

-- -----------------------------------------------------
-- 1. Supporting Reference Tables
-- -----------------------------------------------------
CREATE TABLE status_code (
    status_code VARCHAR(20) PRIMARY KEY,
    description VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE ticket_type (
    ticket_type_code VARCHAR(20) PRIMARY KEY,
    description VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE travel_class (
    travel_class_code VARCHAR(20) PRIMARY KEY,
    description VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE aircraft_type (
    aircraft_type_code VARCHAR(20) PRIMARY KEY,
    model_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE airline (
    airline_code VARCHAR(10) PRIMARY KEY,
    airline_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 2. Core Operational Entities
-- -----------------------------------------------------
CREATE TABLE booking_agent (
    agent_id INT AUTO_INCREMENT PRIMARY KEY,
    agent_name VARCHAR(100) NOT NULL,
    additional_details TEXT
) ENGINE=InnoDB;

CREATE TABLE passenger (
    passenger_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    second_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(25) NOT NULL,
    email_address VARCHAR(100) NOT NULL UNIQUE,
    address_line1 VARCHAR(100) NOT NULL,
    address_line2 VARCHAR(100),
    city VARCHAR(50) NOT NULL,
    state_province_county VARCHAR(50) NOT NULL,
    country VARCHAR(50) NOT NULL,
    other_details TEXT
) ENGINE=InnoDB;

CREATE TABLE airport (
    airport_code VARCHAR(10) PRIMARY KEY,
    airport_name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    other_details TEXT
) ENGINE=InnoDB;

CREATE TABLE calendar_date (
    calendar_date DATE PRIMARY KEY,
    day_of_month TINYINT NOT NULL CHECK (day_of_month BETWEEN 1 AND 31),
    is_business_day BOOLEAN NOT NULL
) ENGINE=InnoDB;

CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_status_code VARCHAR(20) NOT NULL,
    payment_date DATETIME NOT NULL,
    payment_amount DECIMAL(10, 2) NOT NULL CHECK (payment_amount > 0),
    CONSTRAINT fk_payment_status FOREIGN KEY (payment_status_code) REFERENCES status_code(status_code)
) ENGINE=InnoDB;

CREATE TABLE reservation (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    passenger_id INT NOT NULL,
    agent_id INT NULL, -- NULL indicates direct booking
    reservation_status_code VARCHAR(20) NOT NULL,
    ticket_type_code VARCHAR(20) NOT NULL,
    travel_class_code VARCHAR(20) NOT NULL,
    creation_datetime DATETIME NOT NULL,
    number_in_party INT NOT NULL CHECK (number_in_party > 0),
    CONSTRAINT fk_res_passenger FOREIGN KEY (passenger_id) REFERENCES passenger(passenger_id) ON DELETE RESTRICT,
    CONSTRAINT fk_res_agent FOREIGN KEY (agent_id) REFERENCES booking_agent(agent_id) ON DELETE SET NULL,
    CONSTRAINT fk_res_status FOREIGN KEY (reservation_status_code) REFERENCES status_code(status_code),
    CONSTRAINT fk_res_ticket FOREIGN KEY (ticket_type_code) REFERENCES ticket_type(ticket_type_code),
    CONSTRAINT fk_res_class FOREIGN KEY (travel_class_code) REFERENCES travel_class(travel_class_code)
) ENGINE=InnoDB;

CREATE TABLE flight_schedule (
    flight_number VARCHAR(20) PRIMARY KEY, -- Simplification for drill; see step 5 for production context
    airline_code VARCHAR(10) NOT NULL,
    usual_aircraft_type_code VARCHAR(20) NOT NULL,
    origin_airport_code VARCHAR(10) NOT NULL,
    destination_airport_code VARCHAR(10) NOT NULL,
    scheduled_departure_datetime DATETIME NOT NULL,
    scheduled_arrival_datetime DATETIME NOT NULL,
    CONSTRAINT chk_schedule_airports CHECK (origin_airport_code <> destination_airport_code),
    CONSTRAINT chk_schedule_times CHECK (scheduled_arrival_datetime > scheduled_departure_datetime),
    CONSTRAINT fk_sched_airline FOREIGN KEY (airline_code) REFERENCES airline(airline_code),
    CONSTRAINT fk_sched_aircraft FOREIGN KEY (usual_aircraft_type_code) REFERENCES aircraft_type(aircraft_type_code),
    CONSTRAINT fk_sched_origin FOREIGN KEY (origin_airport_code) REFERENCES airport(airport_code),
    CONSTRAINT fk_sched_dest FOREIGN KEY (destination_airport_code) REFERENCES airport(airport_code)
) ENGINE=InnoDB;

CREATE TABLE flight_leg (
    leg_id INT AUTO_INCREMENT PRIMARY KEY,
    flight_number VARCHAR(20) NOT NULL,
    origin_airport_code VARCHAR(10) NOT NULL,
    destination_airport_code VARCHAR(10) NOT NULL,
    actual_departure_datetime DATETIME NULL, -- NULL before flight operates
    actual_arrival_datetime DATETIME NULL,
    CONSTRAINT chk_leg_airports CHECK (origin_airport_code <> destination_airport_code),
    CONSTRAINT chk_leg_times CHECK (actual_arrival_datetime IS NULL OR actual_departure_datetime IS NULL OR actual_arrival_datetime >= actual_departure_datetime),
    CONSTRAINT fk_leg_schedule FOREIGN KEY (flight_number) REFERENCES flight_schedule(flight_number) ON DELETE RESTRICT,
    CONSTRAINT fk_leg_origin FOREIGN KEY (origin_airport_code) REFERENCES airport(airport_code),
    CONSTRAINT fk_leg_dest FOREIGN KEY (destination_airport_code) REFERENCES airport(airport_code)
) ENGINE=InnoDB;

CREATE TABLE flight_cost (
    flight_number VARCHAR(20) NOT NULL,
    aircraft_type_code VARCHAR(20) NOT NULL,
    valid_from_date DATE NOT NULL,
    valid_to_date DATE NOT NULL,
    cost_amount DECIMAL(10, 2) NOT NULL CHECK (cost_amount >= 0),
    PRIMARY KEY (flight_number, aircraft_type_code, valid_from_date),
    CONSTRAINT chk_cost_dates CHECK (valid_to_date >= valid_from_date),
    CONSTRAINT fk_cost_schedule FOREIGN KEY (flight_number) REFERENCES flight_schedule(flight_number) ON DELETE CASCADE,
    CONSTRAINT fk_cost_aircraft FOREIGN KEY (aircraft_type_code) REFERENCES aircraft_type(aircraft_type_code),
    CONSTRAINT fk_cost_from_date FOREIGN KEY (valid_from_date) REFERENCES calendar_date(calendar_date),
    CONSTRAINT fk_cost_to_date FOREIGN KEY (valid_to_date) REFERENCES calendar_date(calendar_date)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 3. Junction/Linking Tables
-- -----------------------------------------------------
CREATE TABLE reservation_payment_link (
    reservation_id INT NOT NULL,
    payment_id INT NOT NULL,
    PRIMARY KEY (reservation_id, payment_id),
    CONSTRAINT fk_rpl_reservation FOREIGN KEY (reservation_id) REFERENCES reservation(reservation_id) ON DELETE CASCADE,
    CONSTRAINT fk_rpl_payment FOREIGN KEY (payment_id) REFERENCES payment(payment_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reservation_leg_link (
    reservation_id INT NOT NULL,
    leg_id INT NOT NULL,
    PRIMARY KEY (reservation_id, leg_id),
    CONSTRAINT fk_rll_reservation FOREIGN KEY (reservation_id) REFERENCES reservation(reservation_id) ON DELETE CASCADE,
    CONSTRAINT fk_rll_leg FOREIGN KEY (leg_id) REFERENCES flight_leg(leg_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 4. Performance Indexes
-- -----------------------------------------------------
CREATE INDEX idx_passenger_name ON passenger(last_name, first_name);
CREATE INDEX idx_reservation_passenger ON reservation(passenger_id);
CREATE INDEX idx_reservation_agent ON reservation(agent_id);
CREATE INDEX idx_flight_leg_schedule ON flight_leg(flight_number);
CREATE INDEX idx_payment_date ON payment(payment_date);