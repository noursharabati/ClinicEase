# 🏥 ClinicEase

A full-stack academic clinic appointment booking system developed using PHP, MySQL, and Bootstrap 5. The project demonstrates database design, CRUD operations, and secure backend implementation. 

## ✨ Features

- 📅 **Book Appointments** — patients can schedule appointments with a specific doctor
- 👁️ **View Appointments** — live search + time-based color coding (morning / afternoon / evening)
- 🛠️ **Admin Dashboard** — manage (edit & delete) all appointments with SweetAlert2 confirmations
- ⭐ **Doctor Rating** — patients can rate doctors with a 5-star system

## 🖼️ Screenshots

> Add screenshots to a `/screenshots` folder and update these paths.

| Home | Book | Admin |
|------|------|-------|
| ![Home](screenshots/Home.png) | ![Book](screenshots/book.png) | ![Admin](screenshots/admin.png) |

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, Bootstrap 5.3, SweetAlert2 |
| Backend  | PHP 8+ (PDO) |
| Database | MySQL |

## 🚀 Getting Started

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- A local server: [XAMPP](https://www.apachefriends.org/) / [WAMP](https://www.wampserver.com/) / [Laragon](https://laragon.org/)

### Installation

1. **Clone the repository**
   ```bash
git clone https://github.com/noursharabati/ClinicEase.git
   cd ClinicEase
   ```

2. **Set up the database**
   - Open phpMyAdmin (or any MySQL client)
   - Create a new database named `clinicease_db`
   - Import the provided SQL file:
     ```
     File → Import → clinicease_db.sql
     ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` with your database credentials:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=your_password
   DB_NAME=clinicease_db
   ```

4. **Run the project**
   - Place the project folder inside your server's web root (`htdocs` / `www`)
   - Open your browser and go to:
     ```
     http://localhost/ClinicEase/
     ```

## 📁 Project Structure

```
ClinicEase/
├── index.php           # Home page with services & doctors
├── book.php            # Appointment booking form
├── view.php            # View all appointments (live search)
├── admin.php           # Admin dashboard (edit / delete)
├── rate.php            # Doctor rating page
├── db.php              # Database connection (PDO)
├── clinicease_db.sql   # Database schema & sample data
├── .env.example        # Environment variables template
├── .gitignore
└── README.md
```

## 👥 Team Members

| Name | Contribution |
|------|-------------|
| **Noor Sharabati** | Booking system, View Appointments, Rating feature |
| **Malak Shalabi**  | Admin dashboard, database setup |
| **Raneem Toumar**  | UI / Bootstrap styling, PowerPoint slides |

## 🔒 Security Notes

- All database queries use **PDO prepared statements** to prevent SQL Injection
- User input is sanitized with `htmlspecialchars()` before display
- Doctor selection is validated against a whitelist on the server side
- Database credentials are stored in `.env` (never committed to version control)

## 📄 License

This project was developed as an academic assignment.
