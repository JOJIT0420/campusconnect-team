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

## How to Use

### Getting Started
1. Create an account by clicking the Register button
2. Fill in your details and choose your account type
3. Log in using your email and password

### For Students
-**Browse Events** - See all upcoming events on the homepage. Use the search bar or category filter to find what you're looking for.
-**View Details** - Click any event card to see the full description, date, time, venue, and available slots.
-**My Registration** - View all events you've signed up for. You can also cancel your registration here if needed.

### For Administrators
-**Admin Dashboard** - This is where you manage everything. Click "Add Event" to create new one.
-**Edit or Delete Events** - Use the Edit button to update event details, or delete/cancel events when needed.
-**View Registrations** - Click "View" on any event to see the list of students who registered. You'll see their names, emails, and how many slots are left.

### Quick Tips
- Use the search bar to find events by name or keyword
- Check the available slots before registering
- You can cancel your registration anytime before the event date
- Administrators can approve or reject account requests
- Bookmark the homepage for easy access to upcoming events

# Team Members
Alcantara, Aldrei Khen N. - Backend Developer/DevOps
Clapis, Jayjay - Frontend Developer/Database Administrator
Masagca, Jazmine A. - Documentation 
Masagca, Nicole A. - Quality Assurance

