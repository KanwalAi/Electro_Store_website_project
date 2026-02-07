# Quick Start Guide - ElectroStore

## 🚀 Installation (5 Minutes)

### Step 1: Database Setup
1. Open **PhpMyAdmin** (http://localhost/phpmyadmin)
2. Create new database: `electronic_store`
3. Open the SQL tab and paste the contents of **setup.sql**
4. Click "Go" to execute

### Step 2: Verify Files
Ensure all files are in: `c:\xampp\htdocs\electronics_store\`

### Step 3: Access Website
- **Customer**: http://localhost/electronics_store/
- **Admin**: http://localhost/electronics_store/admin_dashboard.php

### Step 4: Test Accounts

**Admin Account:**
- Email: `admin123@gmail.com`
- Password: 123456

Create your own admin account:
1. Register as customer
2. Run SQL: 
"UPDATE users 
SET password = '$2y$10$OsXDv1fAL/e.GJDyccutKOX5BzhKKdOmzQnmOVmPtYJIEPSgCcdnC' 
WHERE email = 'youremail@example.com';"


Now 
Email: `youremail@example.com`
Password: 123456
---

## 📱 Main Pages

### Customer Pages
| Page | URL | Purpose |
|------|-----|---------|
| Home | `/` | Featured products, overview |
| Shop | `/shop.php` | Browse all products |
| Product Details | `/product-details.php?id=X` | Full product info |
| Cart | `/cart.php` | Manage shopping cart |
| Checkout | `/checkout.php` | Complete purchase |
| My Orders | `/my_orders.php` | Order history |
| Profile | `/profile.php` | Edit user info |
| Contact | `/contact.php` | Send message |

### Admin Pages
| Page | URL | Purpose |
|------|-----|---------|
| Dashboard | `/admin_dashboard.php` | Stats & overview |
| Products | `/manage_products.php` | Add/Edit/Delete |
| Orders | `/manage_orders.php` | Manage orders |
| Inbox | `/admin_inbox.php` | Customer messages |
| Users | `/manage_users.php` | Customer list |

---

## 🎯 Quick Features

### For Customers
✅ Product Search & Filtering
- Search by name
- Filter by brand (Intel, AMD, NVIDIA, Corsair, Samsung)
- Filter by category (Microchip, Resistor, Capacitor)
- Price range slider

✅ Shopping Features
- Add to cart (AJAX)
- Update quantities
- Checkout with address
- Track order status

✅ Account Features
- Register/Login
- Edit profile
- View order history
- Contact support

### For Admins
✅ Product Management
- Add new products
- Edit details
- Upload images
- Delete products
- Manage stock

✅ Order Management
- View all orders
- Update status
- Track customer orders
- See order details

✅ Communication
- View customer messages
- Mark read/unread
- Delete messages
- Track inquiries

---

## 🔧 Configuration

### Database Credentials (in db.php)
```php
$conn = mysqli_connect("localhost", "root", "", "electronic_store");
```

**If your setup differs:**
- Change `"root"` to your MySQL username
- Change `""` to your MySQL password
- Change `"electronic_store"` to your database name

### File Permissions
- `/images/` folder needs write permission
- Other folders need read permission

---

## 📊 Sample Data

The setup.sql includes:
- **8 Sample Products**: Processors, RAM, SSD, Power Supply, etc.
- **1 Admin User**: admin@electronicsstore.com
- **Sample Categories**: Microchip, Resistor, Capacitor
- **Sample Brands**: Intel, AMD, NVIDIA, Corsair, Samsung

---

## 🆘 Troubleshooting

### "Database Connection Failed"
→ Check MySQL is running in XAMPP
→ Verify database name in db.php
→ Verify credentials in db.php

### "Images not showing"
→ Create `/images` folder in project root
→ Upload product images there

### "Login not working"
→ Check database has users table
→ Clear browser cache and cookies
→ Try incognito mode

### "Admin pages show blank"
→ Verify you're logged in as admin
→ Check user role in database: `SELECT role FROM users WHERE email='...';`

---

## 📚 File Reference

### Core Files
- `db.php` - Database connection
- `header.php` - Navigation (included in all pages)
- `footer.php` - Footer (included in all pages)
- `style.css` - All styling
- `app.js` - JavaScript utilities

### Customer Pages
- `index.php` - Home
- `shop.php` - Product catalog
- `product-details.php` - Product info
- `cart.php` - Shopping cart
- `checkout.php` - Checkout
- `login.php` - User login
- `register.php` - Registration
- `profile.php` - User profile
- `my_orders.php` - Order history
- `order-details.php` - Order info
- `contact.php` - Contact form

### Admin Pages
- `admin_dashboard.php` - Admin home
- `manage_products.php` - Product CRUD
- `manage_orders.php` - Order management
- `admin_inbox.php` - Messages
- `manage_users.php` - User list

### API Files
- `fetch-data.php` - Product filtering
- `fetch_products.php` - Product data
- `api_add_to_cart.php` - Cart endpoint
- `api_remove_from_cart.php` - Cart endpoint
- `api_get_cart_count.php` - Cart count

---

## 🎨 Customization

### Change Colors
Edit `style.css` CSS variables:
```css
:root {
    --primary-color: #007bff;      /* Change blue */
    --secondary-color: #6c757d;    /* Change gray */
    --success-color: #28a745;      /* Change green */
    --danger-color: #dc3545;       /* Change red */
}
```

### Add Products
1. Go to Admin Dashboard
2. Click "Manage Products"
3. Click "Add Product" button
4. Fill in details
5. Upload image
6. Submit

### Change Store Name
Search for "ElectroStore" and replace with your name in:
- header.php
- footer.php
- setup.sql

---

## ✅ Checklist

Before going live:

- [ ] Database imported from setup.sql
- [ ] /images folder created
- [ ] db.php credentials verified
- [ ] Admin account accessible
- [ ] Sample products showing
- [ ] Shopping cart working
- [ ] Checkout functional
- [ ] Admin pages accessible
- [ ] Contact form working
- [ ] Orders displaying

---

## 📞 Support Resources

- **Database**: See setup.sql
- **Documentation**: See README.md
- **Code Comments**: Check PHP files for inline comments
- **Bootstrap Docs**: https://getbootstrap.com/docs/5.0/
- **Font Awesome**: https://fontawesome.com/icons

---

**Version**: 1.0  
**Last Updated**: January 2026  
**Status**: ✅ Production Ready

Good luck with your electronics store! 🎉
