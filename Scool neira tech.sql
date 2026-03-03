create database school_db;
USE school_db;

CREATE TABLE Users(
    User_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('Principal','Staff') NOT NULL

);

select * from Users;

CREATE TABLE Students (
    Student_ID INT AUTO_INCREMENT PRIMARY KEY,
    Admission_No VARCHAR(20) UNIQUE NOT NULL,
    Full_Name VARCHAR(100),
    DOB DATE,
    Birth_Certificate_No VARCHAR(50),
    Gender VARCHAR(10),
    Religion VARCHAR(50),
    Guardian_Name VARCHAR(100),
    Address TEXT,
    Contact_Number VARCHAR(20),
    WhatsApp_No VARCHAR(20),
    Email VARCHAR(50)
);
ALTER TABLE Students;

select * from Students;

CREATE TABLE Admissions (
    Admission_ID INT AUTO_INCREMENT PRIMARY KEY,
    Student_ID INT NOT NULL,
    Course VARCHAR(50),
    Academic_Year VARCHAR(20),
    Admission_Date DATE,
    Date_of_Leaving DATE,
    Reason_for_Leaving VARCHAR(255),
    Dropout_Last_Date DATE,
    FOREIGN KEY (Student_ID) REFERENCES Students(Student_ID) ON DELETE CASCADE
);

ALTER TABLE Admissions
MODIFY Date_of_Leaving DATE NULL,
MODIFY Reason_for_Leaving VARCHAR(255) NULL,
MODIFY Dropout_Last_Date DATE NULL;

ALTER TABLE Admissions
MODIFY Academic_Year TINYINT NOT NULL;

DESCRIBE Admissions;

SET SQL_SAFE_UPDATES = 0;

UPDATE Admissions
SET Course = TRIM(Course);

SET SQL_SAFE_UPDATES = 1;

select * from Admissions;


CREATE TABLE Payments (
    Payment_ID INT AUTO_INCREMENT PRIMARY KEY,
    Admission_ID INT NOT NULL,
    Amount DECIMAL(10,2),
    Payment_Date DATE,
    Payment_Status ENUM('Paid','Pending','Partial'),
    FOREIGN KEY (Admission_ID) REFERENCES Admissions(Admission_ID) ON DELETE CASCADE
);
ALTER TABLE Payments
MODIFY  Amount DECIMAL NULL;


select * from Payments;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE Students;
TRUNCATE TABLE Admissions;
TRUNCATE TABLE Payments;

SET FOREIGN_KEY_CHECKS = 1;