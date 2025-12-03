# LiveNetStudios Payment Integration System

A comprehensive booking and payment system for LiveNetStudios supporting **Direct Bank Transfer, EFT, Cash, and Cash Send** payment methods.

---

## 🎯 Features

- ✅ 4 Payment Methods: Bank Transfer, EFT, Cash, Cash Send
- ✅ Professional booking form with service selection
- ✅ Automated email confirmations to customer and admin
- ✅ Proof of payment upload system
- ✅ Admin dashboard for payment verification
- ✅ Booking management and statistics
- ✅ Payment status tracking (Pending, Paid, Overdue, Cancelled)
- ✅ Responsive design for mobile and desktop

---

## 🏗️ Tech Stack

### Backend
- **Laravel 10+** - PHP framework
- **MySQL** - Database
- **Laravel Mail** - Email notifications

### Frontend
- **React 18** - UI library
- **Vite** - Build tool
- **React Router** - Navigation
- **Axios** - HTTP client

---

## 📦 Installation

### Backend Setup

1. **Navigate to backend directory**
   ```bash
   cd backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   copy .env.example .env
   ```

4. **Edit `.env` file with your database and mail credentials:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=livenetstudios
   DB_USERNAME=root
   DB_PASSWORD=your_password

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_FROM_ADDRESS=your-email@gmail.com
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Create database**
   Create a database named `livenetstudios` in MySQL

7. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

8. **Create storage link**
   ```bash
   php artisan storage:link
   ```

9. **Start Laravel server**
   ```bash
   php artisan serve
   ```
   Backend will be available at: `http://localhost:8000`

### Frontend Setup

1. **Navigate to frontend directory**
   ```bash
   cd frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Start development server**
   ```bash
   npm run dev
   ```
   Frontend will be available at: `http://localhost:5173`

---

## 🔑 Default Credentials

After running the seeders:

- **Admin Email:** diamondlipaz@gmail.com
- **Admin Password:** password (⚠️ Change this immediately!)

---

## 🏦 Banking Details Configuration

Update your banking details in the admin panel or directly in the database `bank_details` table:

```sql
UPDATE bank_details SET
  account_holder_name = 'LiveNetStudios',
  bank_name = 'YOUR BANK NAME',
  account_number = 'YOUR ACCOUNT NUMBER',
  branch_code = 'YOUR BRANCH CODE',
  cash_send_mobile = '+27XXXXXXXXX'
WHERE id = 1;
```

---

## 📧 Email Configuration

For Gmail SMTP:

1. Enable 2-Factor Authentication on your Google account
2. Generate an App Password: https://myaccount.google.com/apppasswords
3. Use the app password in `.env` file

---

## 🚀 Usage

### For Customers

1. Visit `http://localhost:5173`
2. Fill in booking form:
   - Personal information
   - Select service (Recording, Project, Album, Video shoot)
   - Choose dates
   - Select payment method
3. Submit booking
4. Receive confirmation email with payment instructions
5. Make payment and upload proof (for Bank Transfer/EFT)

### For Admin

1. Visit `http://localhost:5173/admin`
2. View dashboard with statistics
3. Filter bookings by payment status
4. Click "Verify" on pending payments
5. Review payment details and proof of payment
6. Approve or reject payments
7. Export payment reports

---

## 📁 Project Structure

```
ourstudios/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/API/
│   │   │   ├── BookingController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ServiceController.php
│   │   │   └── ConfigController.php
│   │   ├── Mail/
│   │   │   ├── BookingConfirmationMail.php
│   │   │   └── PaymentVerifiedMail.php
│   │   └── Models/
│   │       ├── Booking.php
│   │       ├── Payment.php
│   │       ├── Service.php
│   │       ├── PaymentMethod.php
│   │       └── BankDetail.php
│   ├── database/migrations/
│   ├── resources/views/emails/
│   └── routes/api.php
│
└── frontend/                   # React App
    ├── src/
    │   ├── components/
    │   │   ├── BookingForm.jsx
    │   │   ├── PaymentMethodSelector.jsx
    │   │   ├── PaymentInstructions.jsx
    │   │   └── admin/
    │   │       ├── BookingsTable.jsx
    │   │       └── PaymentVerificationModal.jsx
    │   ├── pages/admin/
    │   │   └── AdminDashboard.jsx
    │   ├── services/
    │   │   └── api.js
    │   ├── App.jsx
    │   └── main.jsx
    └── index.html
```

---

## 🔧 API Endpoints

### Public Endpoints
- `GET /api/services` - Get all services
- `GET /api/payment-methods` - Get payment methods
- `GET /api/bank-details` - Get bank details
- `POST /api/bookings` - Create booking
- `POST /api/payments/upload-proof` - Upload proof of payment

### Admin Endpoints
- `GET /api/admin/bookings` - List all bookings (with filters)
- `GET /api/admin/bookings/statistics` - Get statistics
- `PUT /api/admin/payments/{id}/verify` - Verify payment
- `GET /api/admin/payments/export` - Export payments CSV

---

## 💡 Next Steps

1. **Add Authentication** - Implement Laravel Sanctum for admin authentication
2. **SMS Notifications** - Add SMS alerts using Africa's Talking or Twilio
3. **WhatsApp Integration** - Send booking confirmations via WhatsApp
4. **Payment Gateway** - Integrate PayFast or Paystack for online payments
5. **Calendar Integration** - Sync bookings with Google Calendar
6. **Deploy** - Deploy to production server

---

## 🐛 Troubleshooting

### Backend Issues

**Database connection error:**
- Check `.env` database credentials
- Ensure MySQL is running
- Verify database exists

**Email not sending:**
- Check SMTP credentials in `.env`
- Verify Gmail app password is correct
- Check firewall/antivirus blocking port 587

### Frontend Issues

**API requests fail:**
- Ensure backend is running on `http://localhost:8000`
- Check `.env` file has correct `VITE_API_BASE_URL`
- Check browser console for CORS errors

---

## 📝 License

Proprietary - LiveNetStudios

---

## 👨‍💻 Support

For support, email: diamondlipaz@gmail.com
