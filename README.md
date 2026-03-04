# My Task Management App

This is a simple PHP app for managing tasks. I built it to show how I think about problems.

## How to Set Up
1. Download this from GitHub.
2. Make a MySQL database called taskdb.
3. Run this SQL in your database tool (like phpMyAdmin):
```sql
CREATE DATABASE taskdb;
USE taskdb;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255),
    description TEXT,
    status VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

4. Copy .env.example to .env and add your DB info.
5. Put the folder in XAMPP htdocs and start Apache/MySQL.
6. Open frontend/login.html in browser.




