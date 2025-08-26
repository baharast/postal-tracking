# 📦 Postal Tracking API

A modular **Laravel 12** backend project for postal package tracking.  
Supports **multi-role authentication** (Sender, Carrier, Admin), package lifecycle management, shipment requests, and full API documentation with **Swagger (OpenAPI)** and **Docsify**.

---

## ✨ Features
- 🔑 **Authentication & Authorization**
  - Laravel Sanctum (API tokens)
  - Role-based access control (Sender, Carrier, Admin) via Gates & Policies
- 📦 **Package Management**
  - Create, view, update status
  - Lifecycle: `created → in_transit → delivered → cancelled`
  - Unique tracking code (UUID)
- 🚚 **Shipment Requests**
  - Carriers can request to deliver packages
  - Sender approves one → all others auto-rejected (via Event Listener)
- 📑 **API Documentation**
  - Swagger UI (auto-generated via l5-swagger)
  - Docsify frontend documentation
- 🧪 **Testing**
  - Feature and unit tests included

---

## 🛠️ Tech Stack
- **Backend**: Laravel 12 (PHP 8.3)
- **Database**: MySQL 8+
- **Auth**: Laravel Sanctum
- **Architecture**: Modular (`nwidart/laravel-modules`)
- **Docs**: l5-swagger (OpenAPI 3.0) + Docsify
- **Testing**: PHPUnit

---

## 🚀 Getting Started

### 1. Clone the repository
```bash
git clone https://github.com/your-username/postal-tracking.git
cd postal-tracking
```

### 2. Install dependencies
```bash
composer install
```

### 3. Setup environment
Copy `.env.example` to `.env` and update:
```ini
APP_URL=http://localhost:8000
DB_DATABASE=postal_tracking
DB_USERNAME=root
DB_PASSWORD=secret
```

### 4. Run migrations & seeders
```bash
php artisan migrate --seed
```
This will create roles and test users.

### 5. Start development server
```bash
php artisan serve
```

---

## 👥 Test Users & Roles

| Role    | Email                | Password    |
|---------|----------------------|-------------|
| Sender  | sender@test.com      | Sender@123  |
| Carrier | carrier1@test.com    | Carrier@123 |
| Admin   | admin@test.com       | Admin@123   |

---

## 📑 API Documentation

### Swagger (auto-generated)
Generate docs:
```bash
php artisan l5-swagger:generate
```
Then open [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

### Docsify (static docs)
Run docsify locally:
```bash
npm install -g docsify-cli
docsify serve docs
```
Docs will be available at [http://localhost:3000](http://localhost:3000)

---

## 🧪 Running Tests
```bash
php artisan test
```

---

## 📂 Project Structure
```
Modules/
  User/               # Authentication, roles
  Package/            # Package CRUD & status management
  ShipmentRequest/    # Carriers requests & approval flow
app/Swagger/          # OpenAPI annotations & schemas
tests/                # Unit & Feature tests
```

---

## 📬 API Examples

- **Login**  
  `POST /api/v1/auth/login`
- **Get current user**  
  `GET /api/v1/auth/me`
- **Create package (Sender only)**  
  `POST /api/v1/packages`
- **Approve shipment request (Sender)**  
  `POST /api/v1/requests/{request_id}/approve`

---

## 📜 License
This project is licensed under the MIT License.
