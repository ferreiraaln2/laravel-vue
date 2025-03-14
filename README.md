# Pet Shop Manager

A simple pet shop management system developed with Laravel, Vue.js, and Tailwind CSS.

## Features

- Pet management (registration, editing, listing, and deletion)
- Service management (registration, editing, listing, and deletion)
- Service scheduling (booking, editing, listing, and cancellation)
- Dashboard with an overview of the data

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or another compatible database

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd pet-shop-manager
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Copy the environment file and configure it:
```bash
cp .env.example .env
```

5. Configure the `.env` file with your database credentials.

6. Generate the application key:
```bash
php artisan key:generate
```

7. Run the migrations to create the tables in the database:
```bash
php artisan migrate
```

8. (Optional) Run the seeders to populate the database with sample data:
```bash
php artisan db:seed
```

## Running the Project

1. Start the Laravel development server:
```bash
php artisan serve
```

2. In another terminal, compile the assets and start the Vite development server:
```bash
npm run dev
```

3. Access the application at [http://localhost:8000](http://localhost:8000)

## Project Structure

- `app/Models` - Eloquent Models
- `app/Http/Controllers/Api` - API Controllers
- `database/migrations` - Database Migrations
- `resources/js/components` - Vue.js Components
- `routes/api.php` - API Routes
- `routes/web.php` - Web Routes

## Technologies Used

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3
- **CSS**: Tailwind CSS
- **Database**: MySQL
- **Build Tools**: Vite

## License

This project is licensed under the MIT license.
