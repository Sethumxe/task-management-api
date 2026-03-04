# Task Management REST API

Built with Vanilla PHP and PDO for MySQL. Includes user authentication, full CRUD for tasks with soft deletes, filtering by status, pagination, and a basic browser-based frontend.

## Setup Instructions

1. Clone the repository: `git clone https://github.com/Sethumxe/task-management-api.git`
2. Set up MySQL database:
   - Create a database named `taskdb`.
   - Run the following SQL to create tables:

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
