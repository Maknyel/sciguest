# SCIQUEST
**A Web-Based Virtual Physics Laboratory for Grade 9 Students**

Built with Laravel 9 + Vue 3 + Inertia.js

---

## Requirements

Make sure the following are installed on your machine before proceeding:

- **PHP** 8.0 or higher
- **Composer** (https://getcomposer.org)
- **Node.js** 16 or higher + **npm** (https://nodejs.org)
- **MySQL** 5.7 or higher

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/Maknyel/sciguest.git
cd sciguest
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Set up environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure the database

Open `.env` and update these lines with your MySQL credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sciquest
DB_USERNAME=root
DB_PASSWORD=
```

Also update `APP_URL` to match the port you will use:

```
APP_URL=http://localhost:8000
```

### 6. Create the database

Log in to MySQL and run:

```sql
CREATE DATABASE IF NOT EXISTS sciquest;
```

Or run it via PHP (if you don't have a MySQL client):

```bash
php -r "new PDO('mysql:host=127.0.0.1', 'root', '')->exec('CREATE DATABASE IF NOT EXISTS sciquest');"
```

### 7. Run migrations and seed the database

```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- 4 sections (Grade 9 - Acacia, Dao, Banaba, Gmelina)
- 4 physics modules with activities and quiz questions
- 1 default teacher account (see credentials below)

### 8. Create the storage symlink

```bash
php artisan storage:link
```

This allows uploaded images (module/activity covers) to be publicly accessible.

---

## Running the Application

You need **two terminals** running at the same time:

**Terminal 1 — Laravel backend:**
```bash
php artisan serve
```

**Terminal 2 — Vite frontend (for development):**
```bash
npm run dev
```

Then open your browser and go to:
```
http://localhost:8000
```

---

## Building for Production

If you want to run without `npm run dev`, build the assets once:

```bash
npm run build
php artisan serve
```

---

## Default Accounts

### Teacher (Admin)
| Field    | Value       |
|----------|-------------|
| Username | `teacher`   |
| Password | `teacher123`|

### Student
Register a new student account at `/register`. Students must be **approved** by the teacher before they can log in (via Account Management).

---

## Features

### Teacher
- Dashboard with per-section module progress charts
- Activity Management — create/edit modules and activities, upload cover images, lock/unlock, set deadlines, manage quiz questions
- Account Management — approve, decline, or remove student accounts
- Teacher Management — add, edit, or delete teacher accounts (primary teacher protected)
- Announcements — send announcements to specific sections or all students
- Notifications — see when students complete activities
- Profile — update name, username, and password

### Student
- Dashboard — browse 4 physics modules with progress tracking
- Module page — view all activities with lock status and completion badges
- Activity page — read safety reminders, follow procedures, then take a quiz
- Notifications — receive announcements from teachers
- History — view completed activities and quiz scores
