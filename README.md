# Mini Campus Event Hub

Mini Campus Event Hub is a web-based campus event management system developed for Pateros Technological College. It allows students to discover campus events, create accounts, register for events, and view their registrations. Administrators can manage events and monitor student registrations through an administrative dashboard.

## Project Overview

The system provides a centralized platform for managing campus activities such as seminars, workshops, sports events, academic activities, student organization activities, and other school events.

The project uses PHP and MySQL for the backend and Bootstrap, HTML, CSS, and JavaScript for the frontend.

## Main Features

### Student Features

- Create a student account
- Log in and log out securely
- View available campus events
- Search and browse events
- View complete event information
- Register for an event
- Prevent duplicate event registrations
- View personal event registrations
- View registration status

### Administrator Features

- Secure administrator login
- View event management dashboard
- Create new events
- Edit existing events
- Archive events
- Delete events
- View registration counts
- View registered students
- Monitor event capacity
- View remaining event slots

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