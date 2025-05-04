# Ecommerce Demo

A robust Laravel 11 backend application.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)

## Prerequisites

Before installing the project, ensure you have the following installed on your system:

- **PHP**: Version 8.2 or higher
- **Composer**: Version 2.x (dependency manager for PHP)
- **Node.js**: Version 18.x or higher (optional, if frontend assets are included)
- **MySQL**: Version 8.0 or higher (or another compatible database like PostgreSQL)
- **Git**: For cloning the repository
- **Laravel CLI**: Optional, for artisan commands (install via `composer global require laravel/installer`)
- **Web Server**: Apache or Nginx (optional, if not using Laravel’s built-in server)

Verify PHP and Composer versions:
```bash
php -v
composer --version
```

Clone the Repository

git clone https://github.com/yourusername/yourproject.git
cd yourproject

Install PHP Dependencies
```bash
composer install
```

Copy Environment File

```bash
cp .env.example .env
```

Generate Application Key

```bash
php artisan key:generate
```

Run Migrations 
```bash
php artisan migrate --seed
```

Install and Generate Admin Acc
`` php artisan shield:install panel
   php artisan shield:super-admin
``

Start the Development Server
```bash
php artisan serve
```
