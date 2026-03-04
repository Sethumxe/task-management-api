# My Task Management App

This is a simple PHP app for managing tasks. I built it to show how I think about problems.

## How to Set Up
1. Download this from GitHub.
2. Make a MySQL database called taskdb.
3. Run this SQL in your database tool (like phpMyAdmin):
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'done') DEFAULT 'pending',
    deleted_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

4. Copy .env.example to .env and add your DB info.
5. Put the folder in XAMPP htdocs and start Apache/MySQL.
6. Open frontend/login.html in browser.




