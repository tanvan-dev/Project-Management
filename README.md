## Tech Stack
- Backend: Laravel 10.x + MySQL
- Frontend: React 18 + Vite + Material-UI
- Authentication: Laravel Sanctum

## Setup Instructions

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```