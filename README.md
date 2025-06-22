# Inventory Management System

This is a comprehensive Inventory Management System built with the Laravel framework. It provides a robust platform for managing warehouse inventory, products, suppliers, and financial transactions. The system is designed to streamline inventory control, automate auditing processes, and provide insightful financial reports.

## Key Features

-   **Dashboard:** An at-a-glance overview of key system metrics and summaries.
-   **User & Role Management:** Control user access with a flexible role-based permission system.
-   **Multi-Warehouse Support:** Manage inventory, staff, and operations across multiple warehouse locations.
-   **Product Management:** Full CRUD functionality for products, including categories, units of measure, and supplier information.
-   **Warehouse Audits:** A streamlined module for conducting and recording physical stock audits against system quantities.
-   **Inventory Transfers:** Manage the process of moving stock between different warehouses.
-   **Financial Reporting:** Generate essential PDF reports with Arabic language support, including:
    -   Invoices
    -   Purchase Orders
    -   Quotations
    -   Profit & Loss Statements
    -   Account Summaries
-   **Request Management:** Handle and track requests for product deletions and conversions of obsolete stock.

## Technology Stack

-   **Backend:** Laravel, PHP
-   **Frontend:** Livewire, Alpine.js, Blade
-   **UI Theme:** AdminLTE
-   **Database:** Configured for SQLite by default, easily switchable to MySQL, PostgreSQL, etc.
-   **Authentication:** Laravel Fortify, Laravel Jetstream

---

## Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

-   PHP (>= 8.2)
-   Composer
-   Node.js & npm
-   A database server (e.g., MySQL, MariaDB)

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/inventory-app-tutorial-master.git
    cd inventory-app-tutorial-master
    ```

2.  **Install PHP and JS dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Create your environment file:**
    Copy the example environment file.
    ```bash
    cp .env.example .env
    ```

4.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Configure your `.env` file:**
    Open the `.env` file and update the database credentials (`DB_*`) and your application URL (`APP_URL`).
    ```dotenv
    APP_URL=http://localhost:8000

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=inventory_app
    DB_USERNAME=root
    DB_PASSWORD=
    ```

6.  **Run database migrations and seeders:**
    This command will create the database schema and populate it with initial data, including roles and a default admin user.
    ```bash
    php artisan migrate:fresh --seed
    ```

7.  **Build frontend assets:**
    Compile the CSS and JavaScript files.
    ```bash
    npm run dev
    ```

8.  **Run the development server:**
    ```bash
    php artisan serve
    ```

9.  **Access the application:**
    Open your browser and navigate to the URL you set in your `.env` file (e.g., `http://localhost:8000`).

### Default Login Credentials

The database seeder creates a default administrator user with the following credentials. You can change these in `database/seeders/UserSeeder.php`.

-   **Email:** `admin@example.com`
-   **Password:** `password`
