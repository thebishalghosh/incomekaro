# Deployment Checklist

This is a step-by-step guide for deploying the IncomeKaro application to a live server.

---

### 1. Pre-Deployment

- [ ] **Backup Local Database:** Before making any changes, create a backup of your local database.
- [ ] **Version Control:** Ensure all your latest code changes are committed to your Git repository.

---

### 2. Deployment Steps

- [ ] **Upload Files:**
  - Upload all project files to your server's root directory (e.g., `public_html` or `www`).
  - **DO NOT** upload the `.env` file or the `.idea` directory.

- [ ] **Database Setup:**
  - On your hosting control panel (like cPanel), create a new MySQL database.
  - Create a new database user and assign it to the new database with all privileges.
  - Import the database structure by running the `db.sql` file in phpMyAdmin.

- [ ] **Configure Environment (`.env` file):**
  - Create a new `.env` file on the server.
  - Copy the contents of `.env.example` into it.
  - Update the following values for your live server:
    ```
    # Application Settings
    APP_ENV=production
    APP_URL=https://www.yourdomain.com

    # Database Settings (from your hosting provider)
    DB_HOST=localhost
    DB_NAME=your_live_db_name
    DB_USER=your_live_db_user
    DB_PASS=your_live_db_password

    # SMTP Settings (from your email provider)
    SMTP_HOST=smtp.yourprovider.com
    SMTP_PORT=587
    SMTP_USER=your_email@yourdomain.com
    SMTP_PASS=your_email_password
    SMTP_FROM_EMAIL=noreply@yourdomain.com
    SMTP_FROM_NAME=IncomeKaro
    ```

- [ ] **Set File Permissions:**
  - Ensure the `public/uploads/` directory and its subdirectories (`logos`, `partners`, `users`, `documents`, `services`) are writable by the server. You may need to set the permissions to `775`.

---

### 3. Initial Setup (First-Time Deployment)

- [ ] **Seed Roles:**
  - In your browser, visit `https://www.yourdomain.com/seed_roles.php`.
  - This will populate the `roles` table with `SUPER_ADMIN`, `RM`, etc.

- [ ] **Create Super Admin User:**
  - In your browser, visit `https://www.yourdomain.com/test.php`.
  - This will create the initial Super Admin user (`test@test.com` / `1234`).

- [ ] **Seed Services Hierarchy:**
  - In your browser, visit `https://www.yourdomain.com/seed_services.php`.
  - This will create the default Loan hierarchy (Loan -> Govt/Private -> MUDRA/Personal etc.).

---

### 4. Post-Deployment

- [ ] **Test Application:**
  - Go to your live domain and log in with the Super Admin credentials.
  - Test key functionalities like creating a partner to ensure everything is working.

- [ ] **IMPORTANT: Secure Your Application:**
  - **Delete the following files from your server for security:**
    - `public/seed_roles.php`
    - `public/seed_services.php`
    - `public/test.php`
    - `deployment_checklist.md` (Optional, but good practice)
    - `db.sql` (Optional, but good practice)
