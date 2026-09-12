# 📚 Class Attendance Management System

A web-based **Class Attendance Management System** developed using **PHP, MySQL, HTML, CSS, Bootstrap, JavaScript, jQuery, and Font Awesome**.

This system is designed to help manage students, teachers, classes, attendance, payments, income, student details, student ID cards, and unique student QR codes.

One of the main features of the system is the use of a **unique QR code for each student**. The QR code can be used to identify a student and access student-related information, mark attendance, and make payments.

---

## 🌟 Project Overview

The Class Attendance Management System provides a centralized platform for managing classroom and student-related activities.

The system supports different users such as:

- 👨‍💼 Administrators
- 👨‍🏫 Teachers
- 👨‍🎓 Students / Users

Students can be assigned a **unique QR code** that can be used to quickly identify them.

Using the student's QR code, the system can support operations such as:

- View student details
- Mark student attendance
- Make student payments
- Access student information
- Generate student ID cards
- Print student QR codes

---

# 🚀 Main Features

## 📷 Unique Student QR Code

Each student can have a unique QR code.

The QR code is associated with the student's information and can be used to quickly identify the correct student.

### QR code functionality includes:

- Generate unique student QR codes
- Print student QR codes
- Generate student ID cards
- Identify students using QR codes
- View student information
- Mark attendance
- Make payments

### QR Code Workflow

```text
              Student
                 │
                 ▼
        Unique Student QR Code
                 │
                 ▼
          Identify Student
                 │
       ┌─────────┼─────────┐
       │         │         │
       ▼         ▼         ▼
   Student    Attendance  Payment
   Details      Marking   Management
       │         │         │
       └─────────┼─────────┘
                 ▼
        Student Information
