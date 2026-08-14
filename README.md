# ⛪️ Church_schedulesystemMisa_Paroki_LaravelPWA

[![Laravel](https://img.shields.io/badge/Laravel-13.9.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-38BDF8?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.x-FF6384?style=for-the-badge&logo=chart.js&logoColor=white)](https://www.chartjs.org/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![PWA](https://img.shields.io/badge/PWA-Disabled-lightgrey?style=for-the-badge)](https://web.dev/progressive-web-apps/)
[![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)

---

## 📋 Overview

**Church Schedule & Assignment System – Parish Mass** is a web‑based application built with **Laravel 13.14.0** and **Tailwind CSS**. It is designed to centrally manage worship schedules and service duty assignments within a church environment, making coordination efficient and transparent.

The system supports **five user roles** with distinct access levels: Super Admin, Diocese Admin, Church Admin, Choir PIC, and Regular User. With interactive statistics, a real‑time dashboard, and automatic filtering of upcoming assignments (next 12 days), the application simplifies service planning at the parish, diocese, and inter‑church levels.

---

## 🎯 Objectives

| Objective | Description |
|-----------|-------------|
| **Service Coordination** | Simplify scheduling and assignment of church service staff |
| **Status Transparency** | Display assignment statuses (pending, accepted, rejected, completed) |
| **Role‑Based Access** | Five user roles with hierarchical permissions |
| **Data Visualization** | Charts and statistics to monitor workload and performance |
| **Upcoming Reminders** | Show assignments within the next 12 days |
| **Operational Efficiency** | Prevent schedule conflicts and ensure staff availability |
| **Modern & Responsive** | Comfortable experience on desktop, tablet, and mobile |

---

## 🧱 Technology Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 13.9.0 | PHP Framework – MVC, Routing, ORM, Authentication |
| **MySQL** | 8.0+ | Relational Database |
| **Eloquent ORM** | – | Active Record pattern for database interactions |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| **Tailwind CSS** | 4.x | Utility‑first CSS framework for responsive UI |
| **Blade Templates** | – | Laravel’s templating engine |
| **Alpine.js** | – | Lightweight client‑side interactivity |
| **Chart.js** | 4.x | Data visualisation (bar, doughnut, line charts) |
| **Heroicons** | – | SVG icon set |
| **SweetAlert2** | – | Beautiful alerts and modal dialogs |

### Additional Features
- **Real‑time Clock** – Display current time on the dashboard
- **Responsive Design** – Supports all screen sizes
- **Soft Delete** – Remove data without permanent loss

---

## ✨ Features

### 🔐 Multi‑Role Authentication
- Secure Login / Logout
- Middleware‑based route protection
- Session management
- **Five user roles:**
  - **Super Admin** – Full system access
  - **Diocese Admin** – Manage churches within their diocese
  - **Church Admin** – Manage schedules, duties, and assignments for a specific church
  - **Choir PIC** – View and respond to assignments (e.g., choir coordinator)
  - **Regular User** – View and accept/reject own assignments

### 📊 Dashboard
- **Greeting** – Welcome message with user name and role
- **Key Statistics** (Super Admin only):
  - Total Dioceses, Churches, Users, and Schedules
- **Assignment Statistics** – Pending, Accepted, Rejected, Completed
- **Interactive Charts**:
  - Assignment Statistics (bar)
  - Status Distribution (doughnut)
  - Assignments per Church (line)
- **Real‑time Clock** – Current date and time

### 📅 Worship Schedule Management
- View list of available worship schedules
- Schedule name, description, and active status
- Compact display on the dashboard sidebar

### 📋 Duty Assignments
- **For Regular Users & Choir PIC**:
  - View assignments given to themselves
  - Show upcoming assignments (max 12 days ahead)
  - Status: Pending / Accepted
- **For Admins**:
  - View all assignments grouped by duty type
  - List of assigned personnel for each duty
  - Nearest schedule per person
- **Automatic Filter** – Only shows assignments within 12 days from today

### 🏛️ Church Management (Diocese Admin & Church Admin)
- List churches within the diocese
- Show number of staff and schedules per church
- Link to church details

### 📈 Statistics & Charts
- **Totals & Growth** – Data for dioceses, churches, users, schedules
- **Status Percentages** – Visual distribution of assignments
- **Per Church** – Assignment trends per church (this month)

### 🎨 Modern UI/UX
- Responsive design with Tailwind CSS
- Gradient stat cards
- Smooth animations (hover, transitions)
- Today / Tomorrow indicators on assignments
- Informative icons

---

## 🗂️ Application Architecture

The application follows a **modular MVC (Model-View-Controller)** architecture with clear separation of concerns between presentation, business logic, and data.

## 🗂️ Screenshot:
Login: 
<img width="1861" height="952" alt="image" src="https://github.com/user-attachments/assets/a293bd0d-5c23-4872-ac42-56986ae75c5f" />
Dashboard Superadmin: 
<img width="1918" height="942" alt="image" src="https://github.com/user-attachments/assets/1f2134b1-3fca-4096-a415-4088b6977778" />

PWA: 
<img width="1885" height="832" alt="image" src="https://github.com/user-attachments/assets/377023a0-86a2-4b28-9a08-60e700a51a9b" />


