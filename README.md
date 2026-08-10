# AUCA Student Portal (Student · Teacher · Admin)

Plain PHP + MySQL + vanilla JS/CSS. No frameworks, no build step.

## Setup

1. Install a local server stack: **XAMPP**, **WAMP**, or `php -S` + MySQL.
2. Put this whole `auca-portal` folder inside your server's web root
   (e.g. `htdocs/auca-portal` for XAMPP).
3. Create the database: open phpMyAdmin (or `mysql -u root -p`) and run the
   contents of `db.sql`. It creates the `auca_portal` database, all tables,
   and one seed admin account.
4. Edit `config.php` if your MySQL username/password differ from the defaults
   (`root` / empty password).
5. Visit `http://localhost/auca-portal/` in your browser.

## Default admin login

- Email: `admin@auca.ac.rw`
- Password: `Admin123`

(Change this password after first login by editing it directly in the
`users` table, or add a "change password" feature.)

## How the three portals work

- **Everyone signs in from the same page** (`auth/login.php`). After login,
  the user is redirected based on their `role` column: student, teacher, or
  admin.
- **Students** self-register at `auth/register.php` (always created with
  `role = student`). They can register for course groups, view results,
  pay fees, and edit their profile.
- **Teachers** cannot self-register — an admin creates teacher accounts from
  `admin/users.php`. Teachers manage their assigned groups, take attendance,
  enter grades, and message students.
- **Admins** manage everything: create/disable/delete any user and change
  roles, create courses and groups and assign teachers, issue fees, and view
  reports. An admin account can also be promoted/demoted via the role
  dropdown on the Users page.

## Folder structure

```
auca-portal/
  config.php          DB connection
  db.sql               Full schema + seed admin
  includes/
    auth.php           Session/role guard helpers
    layout.php          Shared sidebar/topbar shell
  assets/css/style.css  All styling
  auth/                 login.php, register.php, logout.php
  student/               dashboard, register_courses, results, fees, profile
  teacher/               dashboard, courses, attendance, grades, messages
  admin/                  dashboard, users, courses, fees, reports
```

## Notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored
  in plain text, matching the reference design's security promise.
- This is intentionally minimal: no CSRF tokens, no pagination, no file
  uploads. Add those before using it in production.
- Currency is shown as RWF; change the `number_format`/labels in each file
  if you need a different currency.
