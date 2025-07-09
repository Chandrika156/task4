# 🛡️ Secure Blog Application with Role-Based Access Control

This is a simple blog web application built using **PHP**, **MySQL**, and **Bootstrap**, with strong focus on **security**, **user roles**, and **form validation**.

## 🚀 Features

- ✅ User Registration & Login (with hashed passwords)
- 🔒 Secure Authentication with Sessions
- 🛡️ Prepared Statements (SQL Injection Protection)
- ✅ Server-side & Client-side Form Validation
- 👤 User Roles: `admin`, `editor`, `viewer`
- 🧑‍💼 Admin Dashboard (User management)
- ✍️ Create, Edit, Delete Posts
- ✨ Editors can edit others' posts
- 👀 Viewers can create their own posts
- 🔐 Role-based access to sensitive features

---


---

## 🧠 Roles & Permissions

| Role     | Can View Posts | Can Create | Edit Own | Edit All | Admin Access |
|----------|----------------|------------|----------|----------|--------------|
| Viewer   | ✅             | ✅         | ✅       | ❌       | ❌           |
| Editor   | ✅             | ✅         | ✅       | ✅       | ❌           |
| Admin    | ✅             | ✅         | ✅       | ✅       | ✅           |

---

## 🛡️ Security Features Implemented

- **✅ Prepared Statements** via PDO for all database interactions
- **✅ Server-side validation** for registration, login, and post actions
- **✅ Client-side validation** using JavaScript and HTML5
- **✅ Role-based access control** to restrict sensitive routes
- **✅ Passwords stored with `password_hash()` and verified using `password_verify()`**
- **✅ Session-based login system**

---

## 📦 Database Schema

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer'
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


