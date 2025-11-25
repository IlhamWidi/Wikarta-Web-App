<div align="center">

# Wikarta Provider

**Modern ISP Management System**

[![Laravel](https://img.shields.io/badge/Laravel-12.37-FF2D20?logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19.2-61DAFB?logo=react)](https://react.dev)
[![License](https://img.shields.io/badge/License-MIT-blue)](LICENSE)

Sistem manajemen lengkap untuk Internet Service Provider dengan otomasi WhatsApp billing notifications

[Demo](#) · [Documentation](#documentation) · [Report Bug](https://github.com/IlhamWidi/Real-Wikarta-/issues)

</div>

---

## Overview

Wikarta Provider adalah aplikasi full-stack untuk mengelola operasional ISP, dari customer management hingga automated billing dengan WhatsApp integration.

**Key Features:**
- 👥 Customer & subscription management
- 💰 Invoice & payment tracking
- 📱 WhatsApp dunning notifications (4-stage automated)
- 🎫 Support ticket system
- 📊 Real-time analytics dashboard
- 🔐 Role-based access control

**Tech Stack:** Laravel 12 · React 19 · MySQL · Tailwind CSS · Fonnte API


---

## Quick Start

### Prerequisites
- PHP ≥ 8.2
- Composer
- Node.js ≥ 18
- MySQL ≥ 8.0

### Installation

```bash
# Clone repository
git clone https://github.com/IlhamWidi/Real-Wikarta-.git
cd Real-Wikarta-

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_DATABASE=agusprovider
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate --seed

# Build assets
npm run dev

# Start server
php artisan serve
```

**Default credentials:** `admin@agusprovider.com` / `admin123`

---

## Configuration

### WhatsApp Notifications
Update `.env` with your Fonnte credentials:
```env
FONNTE_TOKEN=your_token
FONNTE_PHONE=628xxxxxxxxxx
```

### Task Scheduler
**Windows:**
```bash
schtasks /create /tn "Wikarta Scheduler" /tr "php C:\path\to\artisan schedule:run" /sc minute
```

**Linux/Mac:**
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Documentation

### API Endpoints
```
Authentication
POST   /api/auth/login
GET    /api/auth/user
POST   /api/auth/logout

Customers
GET    /api/customers
POST   /api/customers
PUT    /api/customers/{id}
DELETE /api/customers/{id}

Invoices
GET    /api/invoices
POST   /api/invoices
PUT    /api/invoices/{id}

Payments
POST   /api/payments
GET    /api/payments/{id}

Tickets
GET    /api/tickets
POST   /api/tickets
PUT    /api/tickets/{id}
```

Full API documentation: [Postman Collection](docs/api-collection.json)

---

## License

MIT License - see [LICENSE](LICENSE) file

---

## Support

- Email: hendrahams22@gmail.com
- WhatsApp: [+62 881 0106 6906](https://wa.me/6288101066906)
- Issues: [GitHub Issues](https://github.com/IlhamWidi/Real-Wikarta-/issues)

---

<div align="center">

Made with ❤️ by **Wikarta Development Team**

</div>

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
#   R e a l - W i k a r t a - 
 
 
#   W I K A R T A - P R O D U C T I O N  
 