# 🍽️ Point of Sale - Backend API

Backend REST API for **Self Order System** built using Laravel.  
This API handles authentication, transactions, kitchen workflows, payment verification, analytics, and reporting systems for restaurant operations.

---

# ✨ Features

## 🔐 Authentication
- Login authentication using Laravel Sanctum
- Role-based access control
- Protected API routes

---

# 👥 User Roles

| Role | Access |
|---|---|
| Admin | Full system access |
| Cashier | Payment verification |
| Kitchen | Kitchen order management |
| Customer | Self ordering |

---

# 🍽️ Main Features

## 👨‍🍳 Customer Features
- Create orders
- View customer orders
- Self-order system
- Payment method selection

## 🍳 Kitchen Features
- View incoming kitchen orders
- Update cooking status
- Order workflow management

## 🧾 Cashier Features
- Verify customer payments
- Payment confirmation

## 🛠️ Admin Features
- User management
- Role management
- Product management
- Category management
- Staff management
- Transaction management
- Analytics dashboard API
- Transaction reports API

---

# 🧑‍💻 Tech Stack

## Backend
- Laravel
- Laravel Sanctum
- MySQL
- Eloquent ORM

## API Features
- RESTful API
- Role middleware
- Transaction management
- Analytics API

---

# 📁 Project Structure

```bash
app/
├── Helpers/
├── Http/
│   ├── Controllers/
│   │   └── Api/
├── Models/
├── Middleware/
routes/
├── api.php
database/
```

---

# 🗄️ Database Relationships

```txt
Roles 1 : N Users
Categories 1 : N Products
Transactions 1 : N TransactionDetails
Products 1 : N TransactionDetails
Transactions 1 : 1 PaymentVerification
```

---

# 🔐 Authentication

Authentication uses:

```txt
Laravel Sanctum
```

Protected routes use:

```php
Route::middleware(['auth:sanctum'])
```

---

# 🛣️ API Routes

## Public Routes

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/auth/login` | User login |
| POST | `/api/transactions` | Create transaction |
| GET | `/api/my-orders/{token}` | Customer order tracking |
| GET | `/api/products` | Get all products |
| GET | `/api/products/{id}` | Get product detail |

---

## Admin Routes

| Method | Endpoint |
|---|---|
| GET | `/api/transactions/report` |
| GET | `/api/transactions/analytics` |
| API Resource | `/api/users` |
| API Resource | `/api/roles` |
| API Resource | `/api/categories` |
| API Resource | `/api/staff` |

---

## Cashier Routes

| Method | Endpoint |
|---|---|
| POST | `/api/payment-verifications/{transactionId}` |
| GET | `/api/payment-verifications/{transactionId}` |

---

## Kitchen Routes

| Method | Endpoint |
|---|---|
| GET | `/api/kitchen/orders` |

---

# 📊 Analytics API

Analytics endpoint:

```txt
/api/transactions/analytics
```

Analytics include:
- Total revenue
- Total paid transactions
- Most ordered products
- Revenue charts

Only transactions with:

```txt
status = paid
```

are included in analytics calculations.

---

# 📄 Reporting System

Transaction reports include:
- Total revenue
- Best seller products
- Most used payment method
- Product sales statistics

---

# 🚀 Installation

## 1. Clone Repository

```bash
git clone https://github.com/glriadomenica-debug/Backend_iconic-resto.git
```

---

## 2. Go To Project Folder

```bash
cd Backend_iconic-resto
```

---

## 3. Install Dependencies

```bash
composer install
```

---

## 4. Environment Setup

Copy `.env`:

```bash
cp .env.example .env
```

---

## 5. Generate Application Key

```bash
php artisan key:generate
```

---

## 6. Configure Database

Edit `.env`:

```env
DB_DATABASE=iconic_resto
DB_USERNAME=root
DB_PASSWORD=
```

---

## 7. Run Migration

```bash
php artisan migrate
```

---

## 8. Run Laravel Server

```bash
php artisan serve
```

Server:

```txt
http://localhost:8000
```

---

# 🔄 Transaction Workflow

```txt
pending
   ↓
cooking
   ↓
ready
   ↓
served
   ↓
paid
```

---

# 📦 API Response Format

Example success response:

```json
{
  "status": "success",
  "message": "Success get transactions",
  "data": []
}
```

---

# 🧠 System Overview

This backend powers a restaurant self-ordering system where:
- customers place orders independently,
- kitchen staff manage food preparation,
- cashiers verify payments,
- admins monitor business analytics.

---

# 📄 License

This project is for educational and portfolio purposes.

---

# 👩‍💻 Developer

Developed by Gloria Domenica Ferreira Da Costa E Silva

GitHub:
https://github.com/glriadomenica-debug
