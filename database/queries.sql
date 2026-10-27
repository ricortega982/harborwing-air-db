SELECT 
    r.reservation_id,
    CONCAT(p.first_name, ' ', p.last_name) AS main_passenger,
    COALESCE(ba.agent_name, 'Direct Booking') AS booking_agent,
    r.number_in_party,
    GROUP_CONCAT(DISTINCT pay.payment_id) AS linked_payments,
    GROUP_CONCAT(DISTINCT leg.leg_id) AS linked_legs
FROM reservation r
JOIN passenger p ON r.passenger_id = p.passenger_id
LEFT JOIN booking_agent ba ON r.agent_id = ba.agent_id
LEFT JOIN reservation_payment_link rpl ON r.reservation_id = rpl.reservation_id
LEFT JOIN payment pay ON rpl.payment_id = pay.payment_id
LEFT JOIN reservation_leg_link rll ON r.reservation_id = rll.reservation_id
LEFT JOIN flight_leg leg ON rll.leg_id = leg.leg_id
WHERE r.reservation_id = 1
GROUP BY r.reservation_id, p.first_name, p.last_name, ba.agent_name, r.number_in_party;
SELECT 
    rll.leg_id,
    r.reservation_id,
    CONCAT(p.first_name, ' ', p.last_name) AS passenger_name,
    r.reservation_status_code
FROM reservation_leg_link rll
JOIN reservation r ON rll.reservation_id = r.reservation_id
JOIN passenger p ON r.passenger_id = p.passenger_id
WHERE rll.leg_id = 5;
SELECT 
    rpl.reservation_id,
    p.payment_id,
    p.payment_date,
    p.payment_amount,
    p.payment_status_code
FROM reservation_payment_link rpl
JOIN payment p ON rpl.payment_id = p.payment_id
WHERE rpl.reservation_id = 1;
SELECT 
    flight_number,
    aircraft_type_code,
    valid_from_date,
    valid_to_date,
    cost_amount
FROM flight_cost
WHERE flight_number = 'HW101'
  AND aircraft_type_code = 'AC_1'
  AND '2026-01-03' BETWEEN valid_from_date AND valid_to_date;
SELECT 'booking_agent' AS table_name, COUNT(*) AS row_count FROM booking_agent
UNION ALL SELECT 'passenger', COUNT(*) FROM passenger
UNION ALL SELECT 'airport', COUNT(*) FROM airport
UNION ALL SELECT 'calendar_date', COUNT(*) FROM calendar_date
UNION ALL SELECT 'payment', COUNT(*) FROM payment
UNION ALL SELECT 'reservation', COUNT(*) FROM reservation
UNION ALL SELECT 'flight_schedule', COUNT(*) FROM flight_schedule
UNION ALL SELECT 'flight_leg', COUNT(*) FROM flight_leg
UNION ALL SELECT 'flight_cost', COUNT(*) FROM flight_cost
UNION ALL SELECT 'reservation_payment_link', COUNT(*) FROM reservation_payment_link
UNION ALL SELECT 'reservation_leg_link', COUNT(*) FROM reservation_leg_link;
