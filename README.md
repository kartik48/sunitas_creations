# Sunita's Creations

An e-commerce platform for authentic Warli and Rajasthani handicrafts, built with Laravel 12.

## 🎨 About

Sunita's Creations is a custom-built e-commerce website showcasing traditional Indian handicrafts. The platform features handcrafted items including dry fruit holders, wall decor, and festive items, all adorned with authentic Warli paintings and Rajasthani designs.

**A Gift of Love:** This project was built as a heartfelt gift for my mother, Sunita, who creates beautiful handicrafts inspired by traditional Warli and Rajasthani art. What started as a way to help her showcase and sell her creations online became an incredible learning journey in full-stack web development.

This project serves triple purpose:
- A functional e-commerce platform for my mom's business
- A demonstration of my web development skills for my portfolio
- A tribute to preserving and promoting traditional Indian art forms

## ✨ Features

### Customer-Facing
- **Warli-Inspired Homepage** - Traditional Indian aesthetic with terracotta color palette and geometric patterns
- **Featured Products Showcase** - Highlighting handpicked artisan creations
- **Category Browsing** - Organized product categories with visual navigation
- **Product Image Gallery** - Multiple high-quality images per product
- **Tag-Based Filtering** - Browse by design style (Warli, Rajasthani), materials, occasions

### Admin Panel
- **Product Management** - Full CRUD operations for products
- **Multi-Image Upload** - Upload multiple product images with primary image selection
- **Category & Tag System** - Flexible organization and filtering
- **Inventory Tracking** - Stock quantity management
- **Featured Products** - Mark products for homepage display
- **Rich Product Details** - Materials, dimensions, weight, descriptions

### Technical Features
- **Authentication System** - Secure login with admin roles via Laravel Breeze
- **Image Storage** - Efficient file management with Laravel Storage
- **Database Relationships** - Eloquent ORM with many-to-many tag relationships
- **Responsive Design** - Mobile-first approach with Tailwind CSS
- **SEO-Friendly** - Slug-based URLs and tag system

## 🛠️ Tech Stack

- **Framework:** Laravel 12
- **Frontend:** Blade Templates, Tailwind CSS
- **Database:** SQLite (development)
- **Authentication:** Laravel Breeze
- **File Storage:** Laravel Storage with symbolic links
- **Design:** Custom Warli-inspired CSS, SVG patterns
- **Version Control:** Git & GitHub

## 📦 Installation

### Prerequisites
- PHP 8.4+
- Composer
- Node.js (optional, for asset compilation)

### Setup

1. Clone the repository
```bash
git clone https://github.com/kartik48/sunitas_creations.git
cd sunitas_creations
```

2. Install dependencies
```bash
composer install
```

3. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Create database and run migrations
```bash
php artisan migrate
```

5. Seed sample data
```bash
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=TagSeeder
php artisan db:seed --class=AdminUserSeeder
```

6. Create storage link
```bash
php artisan storage:link
```

7. Start development server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 🔐 Admin Access

**Email:** admin@sunitas-creations.com
**Password:** admin123

(Remember to change these credentials in production!)

## 📂 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/ProductController.php
│   │   └── HomeController.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   └── ProductImage.php
├── resources/views/
│   ├── home.blade.php
│   └── admin/products/
├── database/
│   ├── migrations/
│   └── seeders/
└── public/storage/ (symlinked)
```

## 🎯 Key Learning Outcomes

This project demonstrates proficiency in:

- **Laravel Ecosystem** - Routing, Eloquent ORM, migrations, seeders, middleware
- **Authentication & Authorization** - Role-based access control
- **File Uploads** - Multi-image handling and storage management
- **Database Design** - Normalized schema with proper relationships
- **Frontend Development** - Responsive UI with Tailwind CSS
- **Git Workflow** - Meaningful commits and version control
- **UI/UX Design** - Cultural aesthetic implementation (Warli art patterns)

## 🚀 Roadmap

- [ ] Product catalog/shop page
- [ ] Individual product detail pages
- [ ] Shopping cart functionality
- [ ] Checkout and payment integration
- [ ] Order management system
- [ ] Customer reviews and ratings
- [ ] Search functionality
- [ ] Email notifications

## 🎨 Design Inspiration

The website's design draws from traditional Warli art - a tribal art form from Maharashtra, India. Key design elements include:

- Geometric patterns (circles, triangles, squares)
- Earthy color palette (terracotta, ochre, cream)
- Stick figure motifs representing daily life
- Traditional border patterns

## 📝 License

This project is for educational and portfolio purposes.

## 👤 Developer

**Kartik Mathur**
GitHub: [@kartik48](https://github.com/kartik48)

---

Built with ❤️ for preserving traditional Indian handicrafts
