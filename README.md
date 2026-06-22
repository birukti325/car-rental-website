# REVS - Car Rental & Dealership Platform

A full-stack web application that allows users to browse, rent, and purchase cars online, with an admin panel for managing inventory.

## Features
- User registration and login with secure password hashing
- Session management with automatic timeout after 10 minutes
- Browse cars by type: SUV, Sedan, Sport, Truck, Van
- Rent a car by specifying duration and delivery address
- Purchase a car with full order confirmation receipt
- Order history page showing all rentals and purchases
- Admin panel for adding new cars to the inventory
- Responsive dark-themed UI

## Technologies Used
- PHP
- MySQL
- HTML, CSS, JavaScript
- mysqli for database interaction

## Project Structure
├── home.php               # Landing page with hero section
├── login.php              # User login with CSRF protection
├── registration.php       # User registration with validation
├── logout.php             # Secure session destruction
├── cartype.php            # Browse cars by category
├── carbrand.php           # Sedan brand listings
├── carbrandSUV.php        # SUV brand listings
├── carbrandSport.php      # Sport brand listings
├── carbrandTruck.php      # Truck brand listings
├── carbrandVan.php        # Van brand listings
├── rent.php               # Rental form
├── buy.php                # Purchase form
├── process_order.php      # Order processing and confirmation
├── my_orders.php          # User order history
├── admin_add_car.php      # Admin car management
└── db_conn.php            # Database connection

## How to Run

1. Install XAMPP or any local PHP/MySQL server
2. Clone or copy the project into your htdocs folder
3. Start Apache and MySQL from the XAMPP control panel
4. Create a database named car_rental_system in phpMyAdmin
5. Import the required tables: users, cars, bookings, sales
6. Open your browser and go to: http://localhost/your-folder-name/home.php

## Default Admin Access
To access the admin panel, set is_admin = 1 for your user in the users table via phpMyAdmin.

## Security Features
- Password hashing using PHP password_hash()
- Session ID regeneration on login
- Input sanitization with mysqli_real_escape_string()
- Automatic session timeout
- Browser cache prevention on protected pages
