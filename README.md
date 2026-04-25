Student Biodata Management System
📌 Project Description

The Student Biodata Management System is a web-based application developed using PHP and MySQL that allows students to submit their personal and academic details, while administrators can manage, view, and download student records efficiently.

This system replaces traditional manual record-keeping with a digital solution, making data handling faster, secure, and more organized.

🎯 Objective
To collect and manage student biodata in a centralized database
To provide an easy interface for students to submit their details
To allow administrators to view, download, and manage student records
🛠️ Technologies Used
Frontend: HTML, CSS
Backend: PHP
Database: MySQL
Server: XAMPP / Apache
⚙️ Features
👨‍🎓 Student
Login securely
Fill biodata form
Submit personal, academic, and family details
👨‍💼 Admin
Secure login
View all student records
Download individual or all records
Delete student data
🔄 System Working
User logs into the system
System verifies credentials
Based on role:
Student fills and submits biodata
Admin views and manages records
Data is stored in MySQL database
Admin can download or delete records
User logs out securely
🗂️ Project Structure
project/
│── login.php
│── logout.php
│── biodata.php
│── admin.php
│── download.php
│── download_all.php
│── delete.php
│── config.php (database connection)
│── assets/
│── database/
💾 Database
Uses MySQL to store student information
Contains fields like:
Name
Email
Phone
Academic details
Family details
🚀 How to Run the Project
Install XAMPP/WAMP
Start Apache and MySQL

Copy project folder to:

htdocs/
Import the database file into phpMyAdmin

Open browser and run:

http://localhost/project-folder-name
🔐 Login Credentials (Example)
Student:
Username: student
Password: student123

Admin:
Username: admin
Password: admin123
⚠️ Limitations
Basic UI design
No password encryption
Limited validation
🔮 Future Enhancements
Add student photo upload
Improve UI using Bootstrap
Add search and filter functionality
Implement password hashing
Export data as PDF
