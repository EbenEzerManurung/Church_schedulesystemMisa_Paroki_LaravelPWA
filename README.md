
# ⛪️ Church_schedulesystemMisa_Paroki_LaravelPWA
[![Laravel](https://img.shields.io/badge/Laravel-13.14.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-38BDF8?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.x-FF6384?style=for-the-badge&logo=chart.js&logoColor=white)](https://www.chartjs.org/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![PWA](https://img.shields.io/badge/PWA-Enabled-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://web.dev/progressive-web-apps/)
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
| **Role-Based Access** | Five user roles with hierarchical permissions |
| **Data Visualization** | Charts and statistics to monitor workload and performance |
| **Upcoming Reminders** | Show assignments within the next 12 days |
| **Operational Efficiency** | Prevent schedule conflicts and ensure staff availability |
| **Modern & Responsive** | Comfortable experience on desktop, tablet, and mobile |

## 🧱 Technology Stack

### Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 13.14.0 | PHP Framework – MVC, Routing, ORM, Authentication |
| **MySQL** | 8.0+ | Relational Database |
| **Eloquent ORM** | – | Active Record pattern for database interactions |

### Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| **Tailwind CSS** | 4.x | Utility‑first CSS framework for responsive UI |
| **Blade Templates** | – | Laravel's templating engine |
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



### 🔐 Multi-Role Authentication

- Secure Login / Logout
- Middleware-based route protection
- Session management
- **👥 Five user roles:**
  - 🛡️ **Super Admin** – Full system access
  - ⛪ **Diocese Admin** – Manage churches within their diocese
  - 🏛️ **Church Admin** – Manage schedules, duties, and assignments for a specific church
  - 🎵 **Choir PIC** – View and respond to assignments (e.g., choir coordinator)
  - 👤 **Regular User** – View and accept/reject own assignments

### 📊 Dashboard

- **Greeting** – Welcome message with user name and role

- **Key Statistics** (Super Admin only):

&#x20; - Total Dioceses, Churches, Users, and Schedules

- **Assignment Statistics** – Pending, Accepted, Rejected, Completed

- **Interactive Charts**:

&#x20; - Assignment Statistics (bar)

&#x20; - Status Distribution (doughnut)

&#x20; - Assignments per Church (line)

- **Real‑time Clock** – Current date and time



### 📅 Worship Schedule Management

- View list of available worship schedules

- Schedule name, description, and active status

- Compact display on the dashboard sidebar



### 📋 Duty Assignments

- **For Regular Users & Choir PIC**:

&#x20; - View assignments given to themselves

&#x20; - Show upcoming assignments (max 12 days ahead)

&#x20; - Status: Pending / Accepted

- **For Admins**:

&#x20; - View all assignments grouped by duty type

&#x20; - List of assigned personnel for each duty

&#x20; - Nearest schedule per person

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



### Steps



Clone the repository

&#x20;  ```bash

&#x20;  git clone https://github.com/EbenEzerManurung/Church_schedulesystemMisa_Paroki_LaravelPWA.git

&#x20;  cd Church_schedulesystemMisa_Paroki_LaravelPWA



&#x20;  Navigate to the project directory:



```bash

cd Church_schedulesystemMisa_Paroki_LaravelPWA

```



Restore dependencies:



```bash

composer install

```

```bash

cp .env.example .env

php artisan key:generate

```

Migrate and Seeder:



```bash

php artisan migrate --seed

```



Run the application:

```bash

&#x20;npm install

```

```bash

npm run dev

```



```bash

php artisan ser

or by port

php artisan ser --port=7000

```



# Screenshots

## Login:

<img width="1861" height="952" alt="image" src="https://github.com/user-attachments/assets/a293bd0d-5c23-4872-ac42-56986ae75c5f" />



## Dashboard Superadmin:

<img width="1918" height="942" alt="image" src="https://github.com/user-attachments/assets/1f2134b1-3fca-4096-a415-4088b6977778" />



## PWA:

<img width="1885" height="832" alt="image" src="https://github.com/user-attachments/assets/377023a0-86a2-4b28-9a08-60e700a51a9b" />



## Kesuskupan/Diocese:

<img width="1909" height="939" alt="image" src="https://github.com/user-attachments/assets/44acc998-2957-4694-a121-808b6b83249b" />



## Detail Kesuskupan/Diocese:

<img width="1909" height="958" alt="image" src="https://github.com/user-attachments/assets/2215cca9-2d32-4f59-ac90-3b1339d057eb" />



## Gereja/Church:

<img width="1897" height="928" alt="image" src="https://github.com/user-attachments/assets/011cbe38-b5f2-4477-a0af-1ea9a9d9b71d" />



## Gereja-gereja dalam satu keuskupan/Churches within one diocese:

<img width="1918" height="931" alt="image" src="https://github.com/user-attachments/assets/42687e80-c773-4fc7-b160-6e9248a2ea2e" />



## Jadwal Misa/Mass Schedule:

<img width="1915" height="946" alt="image" src="https://github.com/user-attachments/assets/e6f2a484-c3d0-449a-8481-5bdeab695c16" />



## Tugas Kategori Pelayanan Dalam Misa/Duties Related to Service Categories During Mass:

<img width="1918" height="946" alt="image" src="https://github.com/user-attachments/assets/1e391b46-68e2-4ecb-9f45-37f83c913fa8" />



## User PIC pelayanan memilih jadwal anggotanya/The service PIC selects the schedule for their members:

<img width="1908" height="943" alt="image" src="https://github.com/user-attachments/assets/485ca462-6dd9-42f2-aaab-715b612349f2" />



## User PIC pelayanan memilih jadwal anggotanya/The service PIC selects the schedule for their members:

<img width="1908" height="943" alt="image" src="https://github.com/user-attachments/assets/485ca462-6dd9-42f2-aaab-715b612349f2" />



## User anggota mengecek dashboard nya untuk melihat tanggal jadwal pelayannya/The member checks their dashboard to view the date of the scheduled service

<img width="1911" height="940" alt="image" src="https://github.com/user-attachments/assets/58fe4072-a411-4d9b-928a-674ffb5403da" />



## User dapat inisiatif mengambil jadwal pelayanan/Users can take the initiative to select a service schedule

<img width="1918" height="927" alt="image" src="https://github.com/user-attachments/assets/ddf539ed-6df7-4101-baf3-16be907112ee" />



## Detail Dashboard, Kalender liturgi

<img width="1909" height="931" alt="image" src="https://github.com/user-attachments/assets/2ed52a36-7b49-4244-92e4-277145aa8261" />



# License



MIT License



---



# Author



**Eben Nezer Manurung**
=======
# ⛪️ Church_schedulesystemMisa_Paroki_LaravelPWA

[![Laravel](https://img.shields.io/badge/Laravel-13.14.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-38BDF8?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.x-FF6384?style=for-the-badge&logo=chart.js&logoColor=white)](https://www.chartjs.org/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![PWA](https://img.shields.io/badge/PWA-Enabled-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://web.dev/progressive-web-apps/)
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
| **Laravel** | 13.14.0 | PHP Framework – MVC, Routing, ORM, Authentication |
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

### Steps

Clone the repository
   ```bash
   git clone https://github.com/EbenEzerManurung/Church_schedulesystemMisa_Paroki_LaravelPWA.git
   cd Church_schedulesystemMisa_Paroki_LaravelPWA

   Navigate to the project directory:

```bash
cd Church_schedulesystemMisa_Paroki_LaravelPWA
```

Restore dependencies:

```bash
composer install
```
```bash
cp .env.example .env
php artisan key:generate
```
Migrate and Seeder:

```bash
php artisan migrate --seed
```

Run the application:
```bash
 npm install
```
```bash
npm run dev
```

```bash
php artisan ser
or by port
php artisan ser --port=7000
```

# Screenshots
## Login: 
<img width="1861" height="952" alt="image" src="https://github.com/user-attachments/assets/a293bd0d-5c23-4872-ac42-56986ae75c5f" />

## Dashboard Superadmin: 
<img width="1918" height="942" alt="image" src="https://github.com/user-attachments/assets/1f2134b1-3fca-4096-a415-4088b6977778" />

## PWA: 
<img width="1885" height="832" alt="image" src="https://github.com/user-attachments/assets/377023a0-86a2-4b28-9a08-60e700a51a9b" />

## Kesuskupan/Diocese: 
<img width="1909" height="939" alt="image" src="https://github.com/user-attachments/assets/44acc998-2957-4694-a121-808b6b83249b" />

## Detail Kesuskupan/Diocese: 
<img width="1909" height="958" alt="image" src="https://github.com/user-attachments/assets/2215cca9-2d32-4f59-ac90-3b1339d057eb" />

## Gereja/Church: 
<img width="1897" height="928" alt="image" src="https://github.com/user-attachments/assets/011cbe38-b5f2-4477-a0af-1ea9a9d9b71d" />

## Gereja-gereja dalam satu keuskupan/Churches within one diocese: 
<img width="1918" height="931" alt="image" src="https://github.com/user-attachments/assets/42687e80-c773-4fc7-b160-6e9248a2ea2e" />

## Jadwal Misa/Mass Schedule: 
<img width="1915" height="946" alt="image" src="https://github.com/user-attachments/assets/e6f2a484-c3d0-449a-8481-5bdeab695c16" />

## Tugas Kategori Pelayanan Dalam Misa/Duties Related to Service Categories During Mass: 
<img width="1918" height="946" alt="image" src="https://github.com/user-attachments/assets/1e391b46-68e2-4ecb-9f45-37f83c913fa8" />

## User PIC pelayanan memilih jadwal anggotanya/The service PIC selects the schedule for their members: 
<img width="1908" height="943" alt="image" src="https://github.com/user-attachments/assets/485ca462-6dd9-42f2-aaab-715b612349f2" />

## User PIC pelayanan memilih jadwal anggotanya/The service PIC selects the schedule for their members: 
<img width="1908" height="943" alt="image" src="https://github.com/user-attachments/assets/485ca462-6dd9-42f2-aaab-715b612349f2" />

## User anggota mengecek dashboard nya untuk melihat tanggal jadwal pelayannya/The member checks their dashboard to view the date of the scheduled service
<img width="1911" height="940" alt="image" src="https://github.com/user-attachments/assets/58fe4072-a411-4d9b-928a-674ffb5403da" />

## User dapat inisiatif mengambil jadwal pelayanan/Users can take the initiative to select a service schedule
<img width="1918" height="927" alt="image" src="https://github.com/user-attachments/assets/ddf539ed-6df7-4101-baf3-16be907112ee" />

## Detail Dashboard, Kalender liturgi
<img width="1909" height="931" alt="image" src="https://github.com/user-attachments/assets/2ed52a36-7b49-4244-92e4-277145aa8261" />

# License

MIT License

---

# Author

**Eben Nezer Manurung**




