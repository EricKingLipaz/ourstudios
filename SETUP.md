# Quick Setup Guide for LiveNetStudios

## ✅ What's Already Done:
- ✅ Environment file created (`.env`)
- ✅ Laravel app key generated
- ✅ Storage link created
- ✅ Frontend server is starting

## 📋 Final Steps Needed:

### Step 1: Create Database
You need to create the MySQL database manually. Choose ONE option:

**Option A: Using phpMyAdmin**
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click "New" in the left sidebar
3. Database name: `livenetstudios`
4. Click "Create"

**Option B: Using MySQL Workbench**
1. Open MySQL Workbench
2. Connect to your local server
3. Run: `CREATE DATABASE livenetstudios;`

**Option C: Using Command Line (if MySQL is in PATH)**
```bash
mysql -u root -p
CREATE DATABASE livenetstudios;
exit;
```

### Step 2: Run Migrations
After creating the database, run this command in the backend directory:
```bash
cd backend
php artisan migrate:fresh --seed
```

This will:
- Create all tables (bookings, payments, services, etc.)
- Seed default data (payment methods, services, bank details, admin user)

### Step 3: Start Backend Server
```bash
php artisan serve
```
Backend will run on: `http://localhost:8000`

### Step 4: Verify Frontend is Running
The frontend should already be running on: `http://localhost:5173`

If not, run in frontend directory:
```bash
cd frontend
npm run dev
```

## 🎯 Access the Application:

1. **Customer Booking Page**: http://localhost:5173
2. **Admin Dashboard**: http://localhost:5173/admin

## 🔑 Default Admin Credentials:
- Email: diamondlipaz@gmail.com
- Password: password

## ⚠️ Update Banking Details:
After setup, update your banking details in the admin panel or database:
```sql
UPDATE bank_details SET
  account_holder_name = 'LiveNetStudios',
  bank_name = 'YOUR BANK',
  account_number = 'YOUR ACCOUNT',
  branch_code = 'YOUR BRANCH CODE',
  cash_send_mobile = '+27XXXXXXXXX'
WHERE id = 1;
```

## 📧 Email Configuration (Optional):
To test email notifications, update in `.env`:
```
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
```

## 🐛 Troubleshooting:

**Database connection error:**
- Check `.env` file has correct database credentials
- Ensure MySQL is running
- Verify database name is `livenetstudios`

**Port already in use:**
- Backend: Use `php artisan serve --port=8001`
- Frontend: It will auto-select another port

**Frontend can't connect to backend:**
- Ensure backend is running on port 8000
- Check `.env` in frontend has: `VITE_API_BASE_URL=http://localhost:8000/api`
