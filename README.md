# Ecommerce Backend

A Laravel-based backend API for an e-commerce application with admin panel and customer-facing endpoints.

## Features

- **User Authentication** - Registration, login, and logout using Laravel Sanctum for API token-based authentication
- **Product Management** - CRUD operations for products with category relationships
- **Category Management** - Product categorization system
- **Shopping Cart** - Add, update, remove products in user cart
- **Wishlist** - Save products to wishlist for later purchase
- **Order Management** - Place orders, view order history
- **Payment Management** - Upload QR payment proof, payment approval/rejection workflow, admin payment verification
- **Product Reviews** - Customers can review products
- **Admin Panel** - Dashboard, orders management, payments management, users management, products/categories CRUD

## Technologies

- **Laravel 12** - PHP web framework
- **Laravel Sanctum** - API token authentication
- **SQLite** - Default database (configurable to MySQL/PostgreSQL)
- **Tailwind CSS** - Frontend styling via Vite

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or MySQL/PostgreSQL
- Node.js (for frontend assets)

## Quick Start

```bash
# Clone and setup
git clone <repository-url>
cd ecommerce-backend

# Install dependencies
composer install
npm install

# Setup environment
# On Windows: copy .env.example .env
# On Unix: cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

Visit `http://localhost:8000` for the admin panel or use the API endpoints listed below.

### Admin Credentials (after seeding)

- Email: `admin@gmail.com`
- Password: `password`

## Environment Configuration

Copy `.env.example` to `.env` and configure:

```env
APP_NAME="Ecommerce Backend"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=ecommerce
# DB_USERNAME=root
# DB_PASSWORD=
```

## API Endpoints

All authenticated API endpoints require a `Bearer` token in the `Authorization` header.

```
Authorization: Bearer <your-api-token>
```

> Paginated endpoints support `?page=N` and `?per_page=N` query parameters (default: 10, max: 100).

### Public Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | No | Register a new user (name, email, password, password_confirmation) |
| POST | `/api/login` | No | Login with email/password, returns API token |
| GET | `/api/categories` | No | List all active categories (paginated) |
| GET | `/api/products` | No | List all active products with category (paginated) |
| GET | `/api/products/{id}` | No | Get a single product by ID |
| GET | `/api/products-search?q=` | No | Search products by name (paginated) |
| GET | `/api/products/{id}/reviews` | No | Get product reviews (paginated) |
| POST | `/api/contact` | No | Send contact form message (rate limited: 5/min) |

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | No | Register new user → returns token + user |
| POST | `/api/login` | No | Login → returns token + user |
| POST | `/api/logout` | Yes | Revoke all API tokens |

### Profile

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/profile` | Yes | Get authenticated user profile |
| PUT | `/api/profile` | Yes | Update name and email |
| PUT | `/api/change-password` | Yes | Change password (current_password, password, password_confirmation) |

### Products & Categories

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/categories` | No | List all active categories (paginated) |
| GET | `/api/products` | No | List all active products (paginated) |
| GET | `/api/products/{id}` | No | Get single product with category |
| GET | `/api/products-search?q=` | No | Search products by name (paginated) |

### Cart

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/cart` | Yes | View cart items with total amount |
| POST | `/api/cart` | Yes | Add item to cart (product_id, quantity) |
| PUT | `/api/cart/{cart}` | Yes | Update cart item quantity |
| DELETE | `/api/cart/{cart}` | Yes | Remove item from cart |

### Wishlist

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/wishlist` | Yes | Get wishlist with products (paginated) |
| POST | `/api/wishlist` | Yes | Add product to wishlist (product_id) |
| DELETE | `/api/wishlist/{productId}` | Yes | Remove product from wishlist |

### Checkout & Orders

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/checkout` | Yes | Place order from cart (payment_method: cod/qr) |
| GET | `/api/orders` | Yes | Get order history (paginated) |
| GET | `/api/orders/{order}` | Yes | Get single order with items |

### Payments

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/payments` | Yes | Get payment history (paginated) |
| GET | `/api/payments/{payment}` | Yes | Get payment detail with order items |
| GET | `/api/payments/order/{order}` | Yes | Check payment status by order |
| POST | `/api/payments/upload/{order}` | Yes | Upload QR payment proof (image file) |

### Reviews

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/products/{id}/reviews` | No | Get product reviews (paginated) |
| POST | `/api/products/{product}/reviews` | Yes | Submit a product review (product_id, rating, comment) |

### Contact

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/contact` | No | Send message via Telegram (rate limited: 5/min) |

## Admin Routes

Access `/admin/login` to access the admin panel. All admin routes require authentication.

### Admin Credentials (after seeding)

- Email: `admin@gmail.com`
- Password: `password`

### Dashboard & Auth

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/login` | Admin login page |
| POST | `/admin/login` | Submit login |
| POST | `/admin/logout` | Logout |
| GET | `/admin/dashboard` | Admin dashboard with stats |
| GET | `/admin/profile` | View profile |
| PUT | `/admin/profile` | Update profile |

### Orders Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/orders` | List all orders (paginated) |
| GET | `/admin/orders/{order}` | View order details |
| POST | `/admin/orders/{order}/status` | Update order status |

### Payments Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/payments` | List all payments with status (paginated) |
| GET | `/admin/payments/{payment}` | View payment detail with proof image |
| POST | `/admin/payments/{payment}/approve` | Approve pending payment |
| POST | `/admin/payments/{payment}/reject` | Reject payment with reason |

### Users Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/users` | List all users (paginated) |
| GET | `/admin/users/{user}` | View user details |

### Categories CRUD

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/categories` | List categories (paginated) |
| GET | `/admin/categories/create` | Create category form |
| POST | `/admin/categories` | Create category |
| GET | `/admin/categories/{category}/edit` | Edit category form |
| PUT | `/admin/categories/{category}` | Update category |
| DELETE | `/admin/categories/{category}` | Delete category |

### Products CRUD

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/products` | List products with category (paginated) |
| GET | `/admin/products/create` | Create product form |
| POST | `/admin/products` | Create product |
| GET | `/admin/products/{product}/edit` | Edit product form |
| PUT | `/admin/products/{product}` | Update product |
| DELETE | `/admin/products/{product}` | Delete product |

## Database Structure

- **users** - User accounts with role field (customer/admin)
- **categories** - Product categories (name, slug, description, status)
- **products** - Products with price, stock, image, status, category relationship
- **carts** - User shopping cart items (user_id, product_id, quantity)
- **wishlists** - User wishlist items (user_id, product_id)
- **orders** - Order headers with total_amount, status, user relationship
- **order_items** - Individual items in orders (order_id, product_id, quantity, price)
- **reviews** - Product reviews by users (user_id, product_id, rating, comment)
- **payments** - Payment records with proof image, amount, status (pending/approved/rejected), order relationship
- **personal_access_tokens** - Sanctum API tokens (Laravel default)

## Development Scripts

```bash
# Run development server
php artisan serve

# Run tests
composer test

# Run linter
./vendor/bin/pint

# Generate Swagger documentation
php artisan l5-swagger:generate

# Access API documentation at /api/documentation
```

## API Documentation

Swagger UI is available at `http://localhost:8000/api/documentation` after running the generate command.

## License

MIT License