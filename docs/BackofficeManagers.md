## For Back-Office Managers

Get parking fee per time period
```sql
SELECT ROUND(SUM(cost)/100, 2) as amount
FROM parking
WHERE started_at BETWEEN '2023-09-12 00:00:00' AND '2023-11-30 23:59:59';
```

Get parking fee per time period
```sql
SELECT ROUND(SUM(amount)/100, 2) as amount
FROM ticket
WHERE issued_at BETWEEN '2023-09-12 00:00:00' AND '2023-11-30 23:59:59';
```

Show the efficiency of employees
```sql
SELECT operator.username, count(*) as total
FROM `check`
INNER JOIN vehicle ON `check`.vehicle_id = vehicle.id
INNER JOIN operator ON `check`.operator_id = operator.id
GROUP BY `check`.operator_id
ORDER BY total DESC;
```

Number of vehicles per time period
```sql
SELECT zone.name, count(*)
FROM parking
INNER JOIN zone ON parking.zone_id = zone.id
INNER JOIN vehicle ON parking.vehicle_id = vehicle.id
WHERE parking.started_at BETWEEN '2023-09-12 00:00:00' AND '2023-11-30 23:59:59'
GROUP BY zone.name;
```

Show frequent users
```sql
SELECT vehicle.license_plate, count(*) as total
FROM parking
INNER JOIN vehicle ON parking.vehicle_id = vehicle.id
WHERE parking.started_at BETWEEN '2023-09-12 00:00:00' AND '2023-11-30 23:59:59'
GROUP BY vehicle.license_plate
HAVING total > 1;
```
