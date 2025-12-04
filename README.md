# 📚 Library Management System (LMS)

A complete **Library Management System** built using **HTML, CSS, JavaScript, PHP & MySQL (PDO)**.
Supports book issuing, returning, fines, membership handling, user management, reports, and **role-based access (Admin/User)**.

---

## 🚀 Features

### 🔐 **Authentication**

* Admin Login
* User Login
* Secure session handling
* Role-based access control

---

## 🏠 **Dashboard**

Displays real-time library statistics:

* Total Books
* Total Movies
* Books Currently Issued
* Active Members
* **Global Search** (by title, author/director, serial no)
* Shows **Book Availability Status**

---

## 🛠️ Maintenance Module (Admin Only)

### 📘 **Books / Movies**

* Add Book / Movie
* Update Book / Movie
* View All Books / Movies
* Select Type (book / movie)
* Full validations

### 👤 **Membership Management**

* Add Membership (6 Months / 1 Year / 2 Years)
* Auto-generate Membership Number
* Auto-calculate Expiry Date
* Extend membership (+6 months)
* Cancel membership
* Dropdown list of all membership numbers

### 👥 **User Management**

* Add new user
* Update existing user
* Select role (Admin/User)

---

## 🔄 Transactions Module

### 📗 **Book Issue**

* Select Book from dropdown (books only)
* Auto-populate Author
* Select Member
* Issue Date ≥ Today
* Auto Return Date = Issue Date + 15 days
* Optional Remarks
* Full validation

### 📘 **Return Book**

* Serial Number dropdown (only issued books)
* Membership Number dropdown (with issued books)
  Auto-populates:
* Book Title
* Author
* Issue Date
* Due Date
* Editable Return Date
* Auto Fine Calculation (₹5/day late)

### 💰 **Fine Pay**

* Shows fine + issue details
* “Fine Paid” required if fine > 0
* Completes book return

---

## 📊 Reports Module

* Books Issued Today
* Overdue Books
* All Active Memberships
* Memberships Expiring This Month

---

## 📁 Project Structure

```
/root-folder
│
├── config.php
├── header.php
├── dashboard.php
│
├── login.php
├── logout.php
│
├── maintenance_items.php
├── maintenance_membership.php
├── books_list.php
│
├── transactions_issue.php
├── transactions_return.php
├── fine_pay.php
│
├── reports.php
├── chart.php
│
└── assets/
```

---

## 🗄️ Database Schema

### **items**

| Column          | Type                 |
| --------------- | -------------------- |
| id              | INT                  |
| type            | ENUM('book','movie') |
| title           | VARCHAR              |
| author_director | VARCHAR              |
| serial_no       | VARCHAR              |
| category        | VARCHAR              |
| created_at      | TIMESTAMP            |

### **members**

| Column        | Type                       |
| ------------- | -------------------------- |
| id            | INT                        |
| membership_no | VARCHAR                    |
| name          | VARCHAR                    |
| start_date    | DATE                       |
| expiry_date   | DATE                       |
| status        | ENUM('active','cancelled') |

### **issues**

| Column      | Type |
| ----------- | ---- |
| id          | INT  |
| item_id     | INT  |
| member_id   | INT  |
| issue_date  | DATE |
| due_date    | DATE |
| return_date | DATE |
| fine_amount | INT  |
| remarks     | TEXT |

### **users**

| Column   | Type                 |
| -------- | -------------------- |
| id       | INT                  |
| username | VARCHAR              |
| password | VARCHAR              |
| role     | ENUM('admin','user') |

---

## 🔧 Installation Guide

### 1️⃣ Clone Repository

```bash
git clone https://github.com/your-username/library-management-system.git
```

### 2️⃣ Import Database

1. Open **phpMyAdmin**
2. Create database: **library_system**
3. Import the provided SQL file

### 3️⃣ Configure Database (`config.php`)

```php
$host = 'localhost';
$db   = 'library_system';
$user = 'root';
$pass = '';
```

### 4️⃣ Run Project

Place folder inside:

* `htdocs/` (XAMPP)
* `www/` (WAMP)

Open in browser:

```
http://localhost/library-management-system/
```

---

## 🔐 Default Login Credentials

### **Admin**

```
username: admin
password: admin123
```

### **User**

```
username: user
password: user123
```

---

## 📄 Flow Chart

A complete application flow chart is available at:

```
chart.php
```

---

## 👨‍💻 Developed By

**D Arun Kumar**
**Email: kumardarun11@gmail.com**
**Linkedin: https://linkedin.com/in/kumardarun11**

---

---

## 🤝 Contributing

Pull requests are welcome!
Open an issue to discuss major changes.

---

## 📜 License

This project uses the **MIT License**.
