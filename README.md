<div align="center">

# 🌐 Wikarta Provider Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.37.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/React-19.2.0-61DAFB?style=for-the-badge&logo=react&logoColor=black" alt="React">
  <img src="https://img.shields.io/badge/PHP-8.4.8-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Vite-7.2.2-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Status-Production%20Ready-success?style=flat-square" alt="Status">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square" alt="License">
  <img src="https://img.shields.io/badge/Maintained-Yes-green?style=flat-square" alt="Maintained">
  <img src="https://img.shields.io/badge/PRs-Welcome-brightgreen?style=flat-square" alt="PRs Welcome">
</p>

### 💼 Sistem Manajemen Internet Service Provider yang Modern & Powerful

*Kelola pelanggan, invoice, pembayaran, dan tiket support dengan antarmuka yang indah dan fitur otomatis yang canggih*

[✨ Features](#-features) • [🚀 Quick Start](#-quick-start) • [📚 Documentation](#-documentation) • [🤝 Contributing](#-contributing)

---

</div>

## 📖 Tentang Projek

**Wikarta Provider** adalah sistem manajemen lengkap untuk Internet Service Provider (ISP) yang dibangun dengan teknologi terkini. Sistem ini menggabungkan kekuatan Laravel sebagai backend dengan keindahan React untuk frontend, menghasilkan aplikasi yang cepat, responsif, dan mudah digunakan.

### 🎯 Tujuan

- ✅ Mempermudah pengelolaan data pelanggan ISP
- ✅ Otomasi proses billing dan payment tracking
- ✅ Notifikasi otomatis jatuh tempo via WhatsApp
- ✅ Manajemen tiket support yang efisien
- ✅ Dashboard analytics real-time
- ✅ Role-based access control (Admin/Superadmin)

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🎨 **Modern UI/UX**
- Glassmorphism design
- Smooth animations (Framer Motion)
- Dark mode ready
- Responsive mobile-first
- Toast notifications
- Loading skeletons

</td>
<td width="50%">

### 🔐 **Authentication & Security**
- Laravel Sanctum API tokens
- Role-based permissions (Spatie)
- Password hashing (Bcrypt)
- CSRF protection
- Secure session management

</td>
</tr>
<tr>
<td>

### 👥 **Customer Management**
- CRUD operations
- Paket internet assignment
- Payment history tracking
- Customer status monitoring
- Search & filter functionality

</td>
<td>

### 💰 **Billing & Invoicing**
- Auto-generate invoices
- Multiple payment status
- Payment gateway integration ready
- Invoice PDF export
- Payment history logs

</td>
</tr>
<tr>
<td>

### 📱 **WhatsApp Integration**
- Automated dunning notifications
- 4-stage reminder system:
  - T-7: 7 hari sebelum jatuh tempo
  - T-3: 3 hari sebelum jatuh tempo
  - T-1: 1 hari sebelum jatuh tempo
  - T+3: 3 hari setelah jatuh tempo
- Custom message templates
- Fonnte API integration

</td>
<td>

### 🎫 **Ticket System**
- Support ticket management
- Priority levels
- Status tracking
- Customer-admin communication
- Ticket history

</td>
</tr>
<tr>
<td>

### 📊 **Dashboard Analytics**
- Real-time statistics
- Revenue tracking
- Customer growth charts
- Payment status overview
- Quick action buttons

</td>
<td>

### ⚙️ **Task Automation**
- Laravel Task Scheduler
- Queue jobs (Database driver)
- Auto email/WhatsApp notifications
- Scheduled dunning runs
- Background job processing

</td>
</tr>
</table>

---

## 🛠️ Tech Stack

### Backend
```
🔹 Laravel 12.37.0      - PHP framework terbaik untuk web artisans
🔹 Laravel Sanctum      - API authentication dengan token
🔹 Spatie Permission    - Role & permission management
🔹 MySQL 8.0.30         - Reliable relational database
🔹 Queue System         - Background job processing
🔹 Task Scheduler       - Automated cron jobs
```

### Frontend
```
🔹 React 19.2.0         - Modern JavaScript library
🔹 Vite 7.2.2          - Lightning fast build tool
🔹 Tailwind CSS 4.1.17  - Utility-first CSS framework
🔹 Framer Motion       - Production-ready animations
🔹 React Router        - Client-side routing
🔹 React Hot Toast     - Beautiful toast notifications
🔹 Axios               - Promise-based HTTP client
```

### Development Tools
```
🔹 Composer            - PHP dependency manager
🔹 NPM                 - Node package manager
🔹 Laragon             - Portable development environment
🔹 Git                 - Version control
```

### External Services
```
🔹 Fonnte API          - WhatsApp messaging gateway
🔹 Gmail SMTP          - Email notifications (optional)
```

---

## 🚀 Quick Start

### Prerequisites

Pastikan sudah terinstall:
- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.0
- **MySQL** >= 8.0
- **Git**

### Installation

```bash
# 1. Clone repository
git clone https://github.com/ilhamwidi/wikarta-provider.git
cd wikarta-provider

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database (.env file)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agusprovider
DB_USERNAME=root
DB_PASSWORD=

# 7. Run migrations & seeders
php artisan migrate --seed

# 8. Configure timezone & queue
APP_TIMEZONE=Asia/Jakarta
QUEUE_CONNECTION=database

# 9. Configure WhatsApp (Optional - Fonnte)
FONNTE_TOKEN=your_fonnte_token_here
FONNTE_PHONE=628xxxxxxxxxx

# 10. Build frontend assets
npm run build
# atau untuk development:
npm run dev
```

### Running the Application

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Queue Worker (untuk background jobs)
php artisan queue:work --tries=3

# Terminal 3 - Vite Dev Server (untuk development)
npm run dev
```

Buka browser: **http://localhost:8000**

### Default Login
```
Email: admin@agusprovider.com
Password: admin123
```

---

## 📋 Task Scheduler Setup

### Windows (Task Scheduler)

```bash
# 1. Buat scheduled task yang run setiap menit:
schtasks /create /tn "Wikarta Laravel Scheduler" /tr "php C:\path\to\project\artisan schedule:run" /sc minute /mo 1

# 2. Verifikasi task sudah dibuat:
schtasks /query /tn "Wikarta Laravel Scheduler"
```

### Linux/Mac (Cron Job)

```bash
# Edit crontab
crontab -e

# Tambahkan line berikut:
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

> 💡 **Task Scheduler** akan menjalankan dunning notification otomatis setiap hari jam 08:00 WIB

---

## 📁 Project Structure

```
wikarta-provider/
├── 📂 app/
│   ├── Console/
│   │   ├── Commands/          # Custom artisan commands
│   │   │   └── DunningRun.php # Dunning notification command
│   │   └── Kernel.php         # Task scheduler config
│   ├── Http/
│   │   └── Controllers/       # API controllers
│   ├── Models/                # Eloquent models
│   └── Providers/             # Service providers
├── 📂 config/                  # Configuration files
├── 📂 database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── 📂 public/                  # Public assets
├── 📂 resources/
│   ├── css/
│   │   └── app.css           # Tailwind CSS
│   ├── js/
│   │   ├── components/       # Reusable React components
│   │   ├── pages/            # React page components
│   │   │   ├── Auth/         # Login & Register
│   │   │   └── Dashboard/    # Dashboard pages
│   │   ├── stores/           # Zustand state management
│   │   ├── utils/            # Utility functions
│   │   ├── app.jsx           # React entry point
│   │   └── bootstrap.js      # Axios config
│   └── views/
│       └── app.blade.php     # Main HTML template
├── 📂 routes/
│   ├── api.php               # API routes
│   ├── web.php               # Web routes
│   └── console.php           # Artisan commands
├── 📂 storage/                 # Storage & logs
├── .env                       # Environment variables
├── composer.json              # PHP dependencies
├── package.json               # JavaScript dependencies
├── vite.config.js            # Vite configuration
└── README.md                  # This file!
```

---

## 🎮 Usage Guide

### 1️⃣ Manajemen Customer

```
Dashboard → Customers → + New Customer
- Isi data customer (nama, alamat, kontak)
- Pilih paket internet
- Set tanggal jatuh tempo
- Save
```

### 2️⃣ Generate Invoice

```
Dashboard → Invoices → + Create Invoice
- Pilih customer
- Pilih paket
- Set due date
- Invoice otomatis ter-generate
```

### 3️⃣ Record Payment

```
Dashboard → Payments → + New Payment
- Pilih invoice
- Input jumlah bayar
- Upload bukti transfer
- Status invoice auto-update
```

### 4️⃣ Handle Support Ticket

```
Dashboard → Tickets
- Lihat tiket masuk
- Assign priority
- Update status
- Reply ke customer
```

### 5️⃣ WhatsApp Dunning (Otomatis)

```
Sistem otomatis akan:
1. Scan invoice yang mendekati jatuh tempo
2. Kirim reminder via WhatsApp (T-7, T-3, T-1, T+3)
3. Log aktivitas di database
4. Update status notification
```

---

## 🔧 Configuration

### WhatsApp (Fonnte)

1. Daftar di [Fonnte.com](https://fonnte.com)
2. Dapatkan token API
3. Update `.env`:
```env
FONNTE_TOKEN=your_token_here
FONNTE_PHONE=6288xxxxxxxxx
```

### Email SMTP (Optional)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@wikarta.com
MAIL_FROM_NAME="Wikarta Provider"
```

### Timezone

```env
APP_TIMEZONE=Asia/Jakarta
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run with coverage
php artisan test --coverage
```

---

## 📚 Documentation

### API Endpoints

#### Authentication
```
POST   /api/auth/login       - Login user
POST   /api/auth/logout      - Logout user
GET    /api/auth/user        - Get authenticated user
```

#### Customers
```
GET    /api/customers        - List all customers
POST   /api/customers        - Create new customer
GET    /api/customers/{id}   - Get customer detail
PUT    /api/customers/{id}   - Update customer
DELETE /api/customers/{id}   - Delete customer
```

#### Invoices
```
GET    /api/invoices         - List all invoices
POST   /api/invoices         - Create new invoice
GET    /api/invoices/{id}    - Get invoice detail
PUT    /api/invoices/{id}    - Update invoice
DELETE /api/invoices/{id}    - Delete invoice
```

#### Payments
```
GET    /api/payments         - List all payments
POST   /api/payments         - Record new payment
GET    /api/payments/{id}    - Get payment detail
DELETE /api/payments/{id}    - Delete payment
```

#### Tickets
```
GET    /api/tickets          - List all tickets
POST   /api/tickets          - Create new ticket
GET    /api/tickets/{id}     - Get ticket detail
PUT    /api/tickets/{id}     - Update ticket
DELETE /api/tickets/{id}     - Delete ticket
```

---

## 🐛 Troubleshooting

### Queue Jobs Not Processing

```bash
# Restart queue worker
php artisan queue:restart
php artisan queue:work --tries=3
```

### WhatsApp Not Sending

```bash
# Check Fonnte quota
curl -X POST https://api.fonnte.com/validate -H "Authorization: YOUR_TOKEN"

# Test manual dunning
php artisan dunning:run
```

### Frontend Not Building

```bash
# Clear npm cache
npm cache clean --force

# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install

# Rebuild
npm run build
```

### Database Issues

```bash
# Reset database
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🤝 Contributing

Contributions are welcome! Ikuti langkah berikut:

1. **Fork** repository ini
2. **Clone** fork ke local: `git clone https://github.com/ilhamwidi/wikarta-provider.git`
3. **Create branch** untuk feature: `git checkout -b feature/AmazingFeature`
4. **Commit** changes: `git commit -m 'Add some AmazingFeature'`
5. **Push** ke branch: `git push origin feature/AmazingFeature`
6. **Open Pull Request**

### Coding Standards

- Follow **PSR-12** untuk PHP
- Follow **ESLint** config untuk JavaScript
- Write **tests** untuk new features
- Update **documentation** jika diperlukan

---

## 📝 Changelog

### [1.0.0] - 2025-11-15

#### ✨ Added
- Initial release
- Customer management CRUD
- Invoice & payment system
- Ticket support system
- WhatsApp dunning notifications (4 stages)
- Role-based access control
- Dashboard analytics
- Task scheduler integration
- Queue system for background jobs

#### 🔧 Fixed
- Quick action buttons navigation
- Toast notification system
- Landing page access control
- Timezone configuration (Asia/Jakarta)

#### 📚 Documentation
- Complete README with badges
- API endpoint documentation
- Installation guide
- Configuration examples

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2025 Wikarta Provider

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

---

## 👨‍💻 Author

**Wikarta Development Team**

- Email: hendrahams22@gmail.com
- WhatsApp: +62 881 0106 6906
- GitHub: [@ilhamwidi](https://github.com/ilhamwidi)

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [React](https://react.dev) - The library for web and native user interfaces
- [Tailwind CSS](https://tailwindcss.com) - A utility-first CSS framework
- [Fonnte](https://fonnte.com) - WhatsApp API Gateway
- [Spatie](https://spatie.be) - Laravel Permission package

---

## 📞 Support

Butuh bantuan? Hubungi kami:

- 📧 Email: support@wikarta.com
- 💬 WhatsApp: [+62 881 0106 6906](https://wa.me/6288101066906)
- 🐛 Issues: [GitHub Issues](https://github.com/ilhamwidi/wikarta-provider/issues)

---

<div align="center">

### ⭐ Star this repository if you find it helpful!

**Made with ❤️ by Wikarta Development Team**

![Footer](https://capsule-render.vercel.app/api?type=waving&color=gradient&height=100&section=footer)

</div>
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License
<div align="center">

### ⭐ Star this repository if you find it helpful!

**Made with ❤️ by Wikarta Development Team**

![Footer](https://capsule-render.vercel.app/api?type=waving&color=gradient&height=100&section=footer)

</div>
#   R e a l - W i k a r t a -  
 