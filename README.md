# Hostel Booking System

The Hostel Booking System is a web-based application developed using PHP, MySQL, HTML, CSS, JavaScript, and Ajax.  
It allows users to search and book hostel rooms online, while administrators can manage rooms and view bookings.

## Login Credentials

### Admin Account
- Email: admin@test.com
- Password: admin
- Role: Admin

### User Account
- Email: user@test.com
- Password: user123
- Role: User


## Setup Instructions

1. Install **XAMPP** on your system.
2. Start **Apache** and **MySQL** services.
3. Copy the project folder **hostel_booking** into:
# Hostel Booking System

The Hostel Booking System is a web-based application developed using PHP, MySQL, HTML, CSS, JavaScript, and Ajax.  
It allows users to search and book hostel rooms online, while administrators can manage rooms and view bookings.

---

## Login Credentials

### Admin Account
- Email: admin@example.com
- Password: admin123
- Role: Admin

### User Account
- Email: user@example.com
- Password: user123
- Role: User

---

## Setup Instructions

1. Install **XAMPP** or **WAMP** on your system.
2. Start **Apache** and **MySQL** services.
3. Copy the project folder **hostel_booking** into:
    C:\xampp\htdocs\FullStack\
4. Open your browser and go to **phpMyAdmin**.
5. Create a new database named:
    hostel_db
6. Import the provided SQL file into the database.
7. Open a browser and visit:
    http://localhost/FullStack/hostel_booking


## Features Implemented

### User Features
- User registration and login
- Search available rooms
- Book hostel rooms
- View only own bookings
- Live room availability check using Ajax

### Admin Features
- Admin login
- Add new rooms
- Edit existing rooms
- Delete rooms
- Search rooms
- View all user bookings

## Security Features

- **SQL Injection Protection** using prepared statements (PDO)
- **XSS Protection** using proper output escaping
- **CSRF Protection** using CSRF token in booking form
- **Session-based authentication** and role-based access control

## Ajax Implementation

- Room availability is checked live using Ajax
- Page does not reload while checking availability
- Booking button is disabled if the room is already booked

## Responsive Design

- Website layout adjusts for mobile, tablet, and desktop screens
- CSS media queries are used for responsiveness
- Forms and tables are mobile-friendly

## Known Issues

- Admin cannot filter bookings by date
- Password reset functionality is not available
