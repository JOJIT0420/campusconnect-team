# Mini Campus Event Hub

Mini Campus Event Hub is a web-based campus event management system developed for Pateros Technological College. It allows students to discover campus events, create accounts, register for events, and view their registrations. Administrators can manage events and monitor student registrations through an administrative dashboard.

## Project Overview

Mini Campus Event Hub is a web-based campus event management system developed for Pateros Technological College. It provides a centralized platform where students can explore campus activities, view event details, and register for events online. It also helps administrators organize campus events, manage event information, and monitor student registrations.

The system supports campus activities such as seminars, workshops, academic programs, sports events, and student organization activities. It uses PHP and MySQL for backend processing and database management, while HTML, CSS, Bootstrap, and JavaScript are used to build the user interface.

## Main Features

### Student Features

- Create a student account and log in securely
- Browse and search available campus events
- View event descriptions, schedules, venues, and capacity
- Register for available events
- Prevent duplicate registrations for the same event
- View personal event registrations and their status
- Check event availability and remaining slots

### Administrator Features

- Log in securely as an administrator
- Access the event management dashboard
- Create and update campus event information
- Archive or delete events
- Monitor registration counts and event capacity
- View lists of registered students
- Check remaining slots for each event
- Export attendee information to CSV

## Technologies Used

- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- PHP 8+
- MySQL / MariaDB
- PDO
- XAMPP
- phpMyAdmin

## Project Structure

```text
campus-event-hub/
│
├── admin/
│   ├── dashboard.php
│   ├── add-event.php
│   ├── edit-event.php
│   ├── delete-event.php
│   └── registrations.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   ├── js/
│   │   └── script.js
│   │
│   └── images/
│       ├── ptc-logo.png
│       └── ptc-campus.jpg
│
├── config/
│   └── database.php
│
├── database/
│   └── campus_event_hub.sql
│
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── footer.php
│
├── index.php
├── login.php
├── register.php
├── logout.php
├── event-details.php
├── register-event.php
├── my-registrations.php
└── README.md

# Team Members
Alcantara, Aldrei Khen N. - Backend Developer/DevOps
Clapis, Jayjay - Frontend Developer/Database Administrator
Masagca, Jazmine A. - Documentation 
Masagca, Nicole A. - Quality Assurance