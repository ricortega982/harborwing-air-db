import random
from datetime import datetime, timedelta
from faker import Faker
import mysql.connector

# Initialize Faker with seed for reproducibility
fake = Faker()
Faker.seed(42)
random.seed(42)

# Database connection details
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'harborwing_air'
}

def seed_database():
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    print("Cleaning existing data...")
    cursor.execute("SET FOREIGN_KEY_CHECKS = 0;")
    tables = [
        "reservation_payment_link", "reservation_leg_link", "flight_cost",
        "flight_leg", "flight_schedule", "reservation", "payment",
        "booking_agent", "passenger", "airport", "calendar_date",
        "status_code", "ticket_type", "travel_class", "aircraft_type", "airline"
    ]
    for t in tables:
        cursor.execute(f"TRUNCATE TABLE {t};")
    cursor.execute("SET FOREIGN_KEY_CHECKS = 1;")

    print("Seeding Reference Tables...")
    # Reference Data
    statuses = [(f"STAT_{i}", f"Status Description {i}") for i in range(1, 51)]
    ticket_types = [(f"TT_{i}", f"Ticket Type {i}") for i in range(1, 51)]
    travel_classes = [(f"TC_{i}", f"Travel Class {i}") for i in range(1, 51)]
    aircrafts = [(f"AC_{i}", f"Aircraft Model {i}") for i in range(1, 51)]
    airlines = [(f"AL_{i}", f"Airline {i}") for i in range(1, 51)]

    cursor.executemany("INSERT INTO status_code VALUES (%s, %s)", statuses)
    cursor.executemany("INSERT INTO ticket_type VALUES (%s, %s)", ticket_types)
    cursor.executemany("INSERT INTO travel_class VALUES (%s, %s)", travel_classes)
    cursor.executemany("INSERT INTO aircraft_type VALUES (%s, %s)", aircrafts)
    cursor.executemany("INSERT INTO airline VALUES (%s, %s)", airlines)

    print("Seeding Core Entities...")
    # 1. Booking Agent (50)
    agents = [(i, fake.name(), fake.text(max_nb_chars=50)) for i in range(1, 51)]
    cursor.executemany("INSERT INTO booking_agent VALUES (%s, %s, %s)", agents)

    # 2. Passenger (50)
    passengers = []
    for i in range(1, 51):
        passengers.append((
            i, fake.first_name(), fake.first_name(), fake.last_name(),
            fake.phone_number()[:25], f"p{i}_{fake.email()}",
            fake.street_address(), fake.secondary_address(),
            fake.city(), fake.state(), fake.country(), "Demo Passenger Details"
        ))
    cursor.executemany("INSERT INTO passenger VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", passengers)

    # 3. Airport (50)
    airports = []
    for i in range(1, 51):
        airports.append((f"AP{i:02d}", f"{fake.city()} Regional Airport", fake.city(), "Coastal Hub"))
    cursor.executemany("INSERT INTO airport VALUES (%s, %s, %s, %s)", airports)

    # 4. Calendar Date (60 days)
    start_date = datetime(2026, 1, 1).date()
    calendar_dates = []
    for i in range(60):
        d = start_date + timedelta(days=i)
        calendar_dates.append((d, d.day, d.weekday() < 5))
    cursor.executemany("INSERT INTO calendar_date VALUES (%s, %s, %s)", calendar_dates)

    # 5. Payment (60)
    payments = []
    for i in range(1, 61):
        payments.append((
            i, random.choice(statuses)[0],
            fake.date_time_between(start_date='-30d', end_date='now'),
            round(random.uniform(50.0, 1500.0), 2)
        ))
    cursor.executemany("INSERT INTO payment VALUES (%s, %s, %s, %s)", payments)

    # 6. Reservation (60)
    reservations = []
    for i in range(1, 61):
        agent_id = random.choice([None, random.randint(1, 50)]) # Direct & Agent assisted
        reservations.append((
            i, random.randint(1, 50), agent_id,
            random.choice(statuses)[0], random.choice(ticket_types)[0],
            random.choice(travel_classes)[0],
            fake.date_time_between(start_date='-60d', end_date='now'),
            random.randint(1, 6)
        ))
    cursor.executemany("INSERT INTO reservation VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", reservations)

    # 7. Flight Schedule (50)
    schedules = []
    for i in range(1, 51):
        orig, dest = random.sample([a[0] for a in airports], 2)
        dep = fake.date_time_between(start_date='now', end_date='+30d')
        arr = dep + timedelta(hours=random.randint(1, 5))
        schedules.append((
            f"HW{100+i}", random.choice(airlines)[0], random.choice(aircrafts)[0],
            orig, dest, dep, arr
        ))
    cursor.executemany("INSERT INTO flight_schedule VALUES (%s, %s, %s, %s, %s, %s, %s)", schedules)

    # 8. Flight Leg (60)
    legs = []
    for i in range(1, 61):
        sched = random.choice(schedules)
        dep_act = sched[5] + timedelta(minutes=random.randint(-10, 45))
        arr_act = dep_act + timedelta(hours=random.randint(1, 4))
        legs.append((i, sched[0], sched[3], sched[4], dep_act, arr_act))
    cursor.executemany("INSERT INTO flight_leg VALUES (%s, %s, %s, %s, %s, %s)", legs)

    # 9. Flight Cost (50)
    costs = []
    for i in range(50):
        sched = schedules[i]
        d1 = calendar_dates[i][0]
        d2 = calendar_dates[min(i+5, 59)][0]
        costs.append((sched[0], sched[2], d1, d2, round(random.uniform(100.0, 800.0), 2)))
    cursor.executemany("INSERT INTO flight_cost VALUES (%s, %s, %s, %s, %s)", costs)

    # 10. Reservation-Payment Link (80 - supporting N:M and multi-payment/shared-payment)
    res_pay_links = set()
    while len(res_pay_links) < 80:
        res_pay_links.add((random.randint(1, 60), random.randint(1, 60)))
    cursor.executemany("INSERT INTO reservation_payment_link VALUES (%s, %s)", list(res_pay_links))

    # 11. Reservation-Leg Link (80 - supporting multi-leg journeys)
    res_leg_links = set()
    while len(res_leg_links) < 80:
        res_leg_links.add((random.randint(1, 60), random.randint(1, 60)))
    cursor.executemany("INSERT INTO reservation_leg_link VALUES (%s, %s)", list(res_leg_links))

    conn.commit()
    cursor.close()
    conn.close()
    print("Database successfully populated with >50 rows per table!")

if __name__ == "__main__":
    seed_database()