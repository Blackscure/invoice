### Invoice System

This is a backend system that i have build apis

## Prerequisites

PHP >= 8.0

Composer

Node.js & npm

Ngrok

## Installation Steps

1.Clone the Repository
    git clone <your-repository-url>

2. Install Dependencies
    composer install
    npm install

3. Environment Setup
   Copy the .env.example file to create your .env file:

4. Generate Application Key
   php artisan key:generate

5. Configure Database
   
Update your .env file to use SQLite. Make sure the following settings are in place:
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

Create the SQLite database file:

touch /absolute/path/to/database/database.sqlite

Make sure the database directory exists:

mkdir -p database

6. Run Migrations
   php artisan migrate


7. Start Ngrok
Download and install Ngrok from ngrok.com.

Start Ngrok to tunnel HTTP requests to your local server:
ngrok http 8000

8. Update Callback URLs
Update your config/services.php file with the Ngrok forwarding URL:

9. Serve the Application
Start the Laravel development server:

php artisan serve




