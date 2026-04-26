# Book Request Management System

A multi-role PHP web application for managing book requests using the Google Books API.

## Setup Instructions

1.  **Extract the Project**: Place the `book-request-system` folder in your `htdocs` (XAMPP) or `www` (WAMP) directory.
2.  **Database Import**:
    *   Open `phpMyAdmin`.
    *   Create a new database named `book_system`.
    *   Import the `database.sql` file provided in the project root.
3.  **Configuration**:
    *   Open `config/db.php`.
    *   Update the `$user` and `$pass` variables if your MySQL credentials are different from the default (root/no password).
4.  **Initial Super Admin**:
    *   Run the following SQL in `phpMyAdmin` to create the initial Super Admin:
    ```sql
    INSERT INTO users (username, email, password, role) 
    VALUES ('superadmin', 'super@system.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');
    ```
    *   **Login Credentials**: `superadmin` / `password`
5.  **Accessing the Application**:
    *   Navigate to `http://localhost/book-request-system/` in your browser.

## Features
- **User**: Register, login, select categories to fetch books from Google API, submit requests, and track status.
- **Admin**: Dashboard with real-time statistics of users and requests.
- **Super Admin**: Manage all requests (edit status/delete), manage users (reset password/delete), and manage admin accounts.

## Security
- Password hashing using `password_hash()`.
- Prepared statements via PDO to prevent SQL injection.
- Role-based session checks on every protected page.
- CSRF protection concepts applied to status changes.
