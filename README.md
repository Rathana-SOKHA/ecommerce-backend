# Ecommerce Backend

A Laravel-based backend API for an e-commerce application with admin panel and customer-facing endpoints.

## Features

- **User Authentication** - Registration, login, and logout using Laravel Sanctum for API token-based authentication
- **Product Management** - CRUD operations for products with category relationships
- **Category Management** - Product categorization system
- **Shopping Cart** - Add, update, remove products in user cart
- **Wishlist** - Save products to wishlist for later purchase
- **Order Management** - Place orders, view order history
- **Product Reviews** - Customers can review products
- **Admin Panel** - Dashboard, orders management, users management, products/categories CRUD

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

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List all categories |
| GET | `/api/products` | List all products |
| GET | `/api/products/{id}` | Get single product |
| GET | `/api/products-search?q=term` | Search products by name |
| GET | `/api/products/{id}/reviews` | Get product reviews |
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login user |

### Authenticated Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/profile` | Get user profile |
| PUT | `/api/profile` | Update user profile |
| PUT | `/api/change-password` | Change password |
| POST | `/api/logout` | Logout user |
| GET | `/api/wishlist` | Get user wishlist |
| POST | `/api/wishlist` | Add to wishlist |
| DELETE | `/api/wishlist/{productId}` | Remove from wishlist |
| GET | `/api/cart` | Get user cart |
| POST | `/api/cart` | Add to cart |
| PUT | `/api/cart/{cart}` | Update cart item |
| DELETE | `/api/cart/{cart}` | Remove from cart |
| POST | `/api/checkout` | Place order |
| GET | `/api/orders` | Get user orders |
| GET | `/api/orders/{order}` | Get single order |
| POST | `/api/reviews` | Create product review |

## Admin Routes

All admin routes require authentication. Access `/admin/login` to access the admin panel.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/login` | Admin login page |
| POST | `/admin/login` | Submit login |
| GET | `/admin/dashboard` | Admin dashboard |
| GET | `/admin/profile` | Admin profile |
| PUT | `/admin/profile` | Update profile |
| POST | `/admin/logout` | Logout |
| GET | `/admin/users` | List users |
| GET | `/admin/users/{user}` | View user |
| GET | `/admin/orders` | List orders |
| GET | `/admin/orders/{order}` | View order |
| POST | `/admin/orders/{order}/status` | Update order status |
| GET | `/admin/categories` | List categories |
| GET | `/admin/categories/create` | Create category form |
| POST | `/admin/categories` | Create category |
| GET | `/admin/categories/{category}/edit` | Edit category form |
| PUT | `/admin/categories/{category}` | Update category |
| DELETE | `/admin/categories/{category}` | Delete category |
| GET | `/admin/products` | List products |
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