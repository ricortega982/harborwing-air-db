# harborwing-air-db
# HarborWing Air — Relational Database System

[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-blue.svg)](https://www.mysql.com/)
[![Python Version](https://img.shields.io/badge/Python-3.8%2B-green.svg)](https://www.python.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

> **Academic & Technical Deliverable**: Midterm Drill / IT8  

---

## Executive Summary
**HarborWing Air** is a relational database management system designed to replace legacy spreadsheets for a regional airline connecting coastal cities and islands. This solution eliminates data redundancy, enforces relational integrity, prevents payment duplication, and cleanly tracks multi-leg connecting flight.

---

## Repository Architecture & Directory Structure
```text
harborwing-air-db/
├── diagram/
│   ├── harborwing_eer.mwb      # MySQL Workbench EER model file
│   └── harborwing_eer.png      # Exported high-resolution diagram
├── schema.sql                  # Database DDL, constraints, indexes, and InnoDB engines
├── generate_data.py            # Deterministic Faker-based synthetic data generator
├── queries.sql                 # Operational example queries & row count verification
├── requirements.txt            # Python dependencies (Faker, mysql-connector-python)
└── README.md                   # Comprehensive project documentation
Technical Specifications & PrerequisitesDBMS: MySQL Server 8.0 or higherEnvironment: Python 3.8+ Design Engine: InnoDB (Selected for full ACID compliance, foreign key enforcement, and transaction safety)   Normalization Level: 3rd Normal Form (3NF)

Setup & Deployment Guide
1. Clone the Repository
Bash
git clone [https://github.com/ricortega982/harborwing-air-db.git](https://github.com/ricortega982/harborwing-air-db.git)
cd harborwing-air-db

2. Install Python Dependencies
Bash
pip install -r requirements.txt

3. Deploy the Database SchemaExecute the schema script in MySQL Workbench or via terminal to construct the database and its 11 core tables:   Bashmysql -u root -p < schema.sql

4. Seed Deterministic Synthetic DataUpdate your local MySQL credentials inside generate_data.py (db_config dictionary), then execute the script to populate all tables with $\ge 50$ rows:Bashpython generate_data.py

5. Verify Row Counts & Execute QueriesOpen queries.sql in MySQL Workbench and execute the verification script to confirm every table successfully contains 50 to 80 rows.   Core Database Design & Architectural DecisionsMany-to-Many ($M:N$) Resolution: Implemented reservation_payment_link and reservation_leg_link junction tables to decouple complex mappings without null-padded columns or data duplication. 
Temporal Data Separation: Maintained strict separation between scheduled departure/arrival timestamps and actual operational times to preserve historical schedules while tracking real-world delays.
Dynamic Pricing Composite Key: Configured flight_cost with a composite primary key of (flight_number, aircraft_type_code, valid_from_date) to support historical cost tracking and seasonal pricing tiers.

System Limitations & Trade-offsShared Payment Allocation: Multi-reservation payment links associate transactions efficiently, but do not natively store explicit fractional currency splits per reservation without an additional allocation schema layer. Passenger Manifest Scope: Party sizes are tracked via number_in_party counters to optimize storage, meaning individual companion names are omitted outside the designated main passenger record. Join Complexity: Normalization eliminates redundancy but requires multi-table JOIN operations for aggregated reporting

Author
Ricardo B. Ortega
BSIT student