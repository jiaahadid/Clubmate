# ClubMate

KPMIM Club Registration System. Students apply to clubs, committee members approve or decline requests, and admins manage clubs and committee roles.

## Requirements

- XAMPP (PHP 8 and MySQL/MariaDB)
- Browser

## Setup

1. Copy this project into `C:\xampp\htdocs\clubmate`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open phpMyAdmin at [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
4. Create a database named `clubmate_db`.
5. Import the tables below (or restore your existing `clubmate_db` dump).
6. Open [http://localhost/clubmate/login.php](http://localhost/clubmate/login.php).

Database settings are in `includes/db.php`:

- Host: `localhost`
- User: `root`
- Password: *(empty, XAMPP default)*
- Database: `clubmate_db`

## Roles

| Role | After login | Can do |
| --- | --- | --- |
| Student | `dashboard.php` | Browse clubs, apply, check registration status |
| Committee | `commitee/home.php` | Approve or decline applications for their club |
| Admin | `admin/home.php` | Manage clubs, view registrations, assign committee roles |

A student can only be an approved member of one club. Applying is blocked when a club has no remaining availability.

## Project structure

```
clubmate/
├── index.php              Redirects to login
├── login.php
├── register.php
├── logout.php
├── dashboard.php          Student club list
├── apply.php              Student application form
├── status.php             Student registration status
├── includes/db.php        Database connection
├── admin/                 Admin panel
└── commitee/              Committee panel
```

## Database tables

```sql
CREATE DATABASE IF NOT EXISTS clubmate_db;
USE clubmate_db;

CREATE TABLE users (
  ID varchar(20) NOT NULL PRIMARY KEY,
  Name varchar(100) NOT NULL,
  Password varchar(255) NOT NULL,
  Role enum('admin','student','committee') NOT NULL
);

CREATE TABLE clubs (
  ClubID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ClubName varchar(100) NOT NULL UNIQUE,
  Category varchar(100) NOT NULL,
  Description text,
  Availability int(11) NOT NULL
);

CREATE TABLE registrations (
  RegistrationID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ID varchar(20) NOT NULL,
  ClubID int(11) NOT NULL,
  Status enum('Pending','Approved','Declined') DEFAULT 'Pending',
  Date date DEFAULT NULL
);

CREATE TABLE commitees (
  CommiteesID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  MemberID varchar(20) DEFAULT NULL,
  ID varchar(20) NOT NULL,
  ClubID int(11) NOT NULL,
  Role varchar(50) DEFAULT 'Member'
);
```

Club remaining slots are stored in `clubs.Availability`.
