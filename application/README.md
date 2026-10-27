# HarborWing Air - Project 2: Application Layer

This repository contains the application layer (CRUD modules, search functionality, transaction management, and relationship linking) for **HarborWing Air**, built with PHP and PDO. It connects to the MySQL database established in Project 1 (Midterm Drill).

---

## Prerequisites & Dependencies
- **Web Server:** XAMPP (Apache and MySQL)
- **PHP:** Version 8.x recommended
- **Database:** MySQL / phpMyAdmin (populated via the Midterm drill schema and seed scripts)

---

## Installation & Setup Instructions

### 1. Set Up the Database
1. Open XAMPP and start **Apache** and **MySQL**.
2. Open phpMyAdmin (`http://localhost/phpmyadmin/`).
3. Import your database schema and seed data from your **Midterm drill** output repository (or the `database/` folder in this repo)[cite: 11].

### 2. Configure Database Credentials
1. Navigate to the `application/` folder.
2. Locate the database connection file (e.g., `db.php`).
3. Update your database host, username, password, and database name to match your local environment:
   ```php
   $host = 'localhost';
   $db   = 'harborwing_air_db';
   $user = 'root';
   $pass = '';
(Note: db.php is excluded from version control via .gitignore for security).
3. Start the Application
Place your project folder inside your local web root directory (e.g., htdocs in XAMPP).

Open your browser and navigate to:

Plaintext
http://localhost/harborwing-air/application/index.php
Core Features & Implementation
Full CRUD Operations: Complete create, read, update, and delete support across all core modules (Passengers, Reservations, Payments, Flight Schedules, Flight Legs, Flight Costs, Booking Agents, and Airports).

Parameterized Queries: All search bars and index data queries utilize PDO prepared statements to prevent SQL injection.

Relationship & Link Management: Allows linking and unlinking of flight legs and payments to reservations without deleting the primary records themselves.

Transaction Safety: Multi-table operations are wrapped in database transactions to maintain foreign-key integrity.

Known Limitations
The system is designed for local deployment environments (such as XAMPP) and requires active Apache and MySQL local services to run.


---

Author
Ricardo B. Ortega
BSIT student