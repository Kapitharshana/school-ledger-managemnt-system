# 🏫 School Admission Ledger Management System

[![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php&logoColor=white)](https://www.php.net/)  
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql&logoColor=white)](https://www.mysql.com/)  
[![HTML5](https://img.shields.io/badge/HTML5-orange?logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)  
[![CSS3](https://img.shields.io/badge/CSS3-blue?logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)  
[![JavaScript](https://img.shields.io/badge/JavaScript-yellow?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

---

## 🔹 Project Overview
A **digital school admission system** to manage student records, payments, and reports securely.  
- Replaces manual ledger entries.  
- Role-based access control (Principal & Staff).  
- Clean dashboard & responsive UI.  

---

## 🧑‍💻 Features

### User Roles
- **Principal (Super Admin):** Full system control, manage students & staff, view reports.  
- **Staff:** Add/edit student records, record payments, search, view reports.  

### Core Modules
1. **Student Registration:** Add students, auto-generate Admission Number, store personal details.  
2. **Admissions:** Course, academic year, admission & leaving dates, reason for leaving.  
3. **Payments:** Record fees, payment status, generate receipts.  
4. **Record Management:** Search, edit, delete (Principal only).  
5. **Reports:** Total admissions, course-wise, year-wise, payment & outstanding fees, exportable to PDF/Excel.  

---

## 🗄️ Database Structure

**Students**: `Student_ID`, `Admission_No`, `Full_Name`, `DOB`, `Gender`, `Guardian_Name`, `Contact`, `Email`  
**Admissions**: `Admission_ID`, `Student_ID`, `Course`, `Academic_Year`, `Admission_Date`, `Date_of_Leaving`, `Reason_for_Leaving`  
**Payments**: `Payment_ID`, `Admission_ID`, `Amount`, `Payment_Date`, `Payment_Status`  
**Users**: `User_ID`, `Username`, `Password (hashed)`, `Role`  

---

## ⚙️ Tech Stack
- **Backend:** PHP  
- **Database:** MySQL  
- **Frontend:** HTML, CSS, JavaScript  
- **Server:** XAMPP (Local)  

---
