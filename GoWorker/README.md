# 🛠️ GoWorker

**GoWorker** is a premium, lightweight, and modern web application built in PHP and MySQL that connects customers with local service professionals (such as electricians, plumbers, carpenters, painters, and cleaners). 

With a tailored two-sided marketplace dashboard structure, GoWorker makes finding, booking, and managing local services completely seamless.

---

## 🚀 Key Features

* **💼 Two-Sided User Roles**:
  * **Customers**: Search for verified service workers by category or location, view worker bios, book appointments, and leave star reviews.
  * **Workers**: Register as a service provider, customize professional profiles (titles, hourly rates, categories, experience, bio, availability, and skills), and manage incoming service bookings.
* **📅 Booking Management**: Dynamic status tracking for bookings (`Pending`, `Confirmed`, `Completed`, `Cancelled`) with distinct dashboard workflows for both customers and workers.
* **⭐ Review System**: Customers can rate workers and provide text-based reviews on completed bookings.
* **🛡️ Built-in Security**: Includes CSRF form protection, PDO prepared statements for SQL injection prevention, and HTML entity output sanitization to prevent XSS.

---

## 📂 Project Architecture

```
GoWorker/
├── config/
│   └── database.php         # PDO database configuration & initialization
├── css/                     # Vanilla CSS style sheets (modern designs)
├── database/
│   ├── goworker.sql         # Main database schema & seeded categories
│   └── db-setup.php         # Automated CLI database installation script
├── includes/
│   ├── auth.php             # Session authentication and access control
│   ├── functions.php        # Security functions (CSRF, HTML escaping)
│   ├── header.php           # Standardized modern navigation and layout headers
│   └── footer.php           # Universal footer
├── js/                      # Front-end interactions and validation scripts
├── index.php                # Homepage / portal introduction
├── signup.php               # Unified User Registration (Customer / Worker)
├── login.php                # Unified Secure Login
├── logout.php               # Session termination
├── customer-dashboard.php   # Customer panel for managing active bookings and search
├── worker-dashboard.php     # Worker panel for viewing requests & updating status
├── become-worker.php        # Form for upgrading a profile to offer services
├── booking.php              # Service request scheduling page
├── find-workers.php         # Search & filter interface for finding specialists
└── profile.php              # Manage user details & settings
```

---

## 💻 Local Quickstart

### Prerequisites
1. Install **[XAMPP](https://www.apachefriends.org/)** (PHP 8.x and MySQL server).
2. Install **[Git](https://git-scm.com/)**.

### Step 1: Clone the Repository
Clone the project into your local XAMPP `htdocs` directory:
```bash
cd C:\xampp\htdocs
git clone https://github.com/aniketrachalwar/GoWorker.git
cd GoWorker/GoWorker
```

### Step 2: Configure the Database Connection
1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. If your MySQL credentials differ from the standard defaults (Host: `localhost`, User: `root`, Password: ``, DB: `goworker`), edit them in [config/database.php](file:///c:/xampp/htdocs/GoWorker/GoWorker/config/database.php).

### Step 3: Run the Database Setup Script
Initialize the schema and insert starter categories using the built-in command-line setup tool:
```bash
C:\xampp\php\php.exe database/db-setup.php
```
*(Alternatively, you can create a database named `goworker` in phpMyAdmin and import the SQL file located at [database/goworker.sql](file:///c:/xampp/htdocs/GoWorker/GoWorker/database/goworker.sql).)*

### Step 4: Access in the Browser
Open your browser and navigate to:
`http://localhost/GoWorker/GoWorker/`

---

## 🛠️ Built With

* **Core**: PHP (Vanilla)
* **Database**: MySQL (PDO Extension)
* **Styling**: Vanilla CSS (Responsive & Modern)
* **Icons**: FontAwesome 6+

---

## 🤝 Contributing
Please check out [CONTRIBUTING.md](file:///c:/xampp/htdocs/GoWorker/GoWorker/CONTRIBUTING.md) to understand our Git branching model, styling guidelines, and database migration rules before making changes.
