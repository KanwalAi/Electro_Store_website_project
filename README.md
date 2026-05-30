# ElectroStore - Electronics E-Commerce Platform

A modern, fully-featured electronics e-commerce platform built with PHP, MySQL, Bootstrap 5, and JavaScript.

# Demo Video

[Video Link](https://drive.google.com/file/d/1iCctnj52wxTHuGVUkKo7jQ1ckiI3eRbJ/view?usp=sharing)

## Features

### Customer Features
- User authentication (Login/Register)
- Browse products with advanced filtering and search
- View detailed product information
- Shopping cart management
- Secure checkout process
- Order tracking and history
- User profile management
- Customer reviews and ratings

### Admin Features
- Complete product management (Add, Edit, Delete)
- Order management with status updates
- Customer management
- Message inbox for customer inquiries
- Dashboard with statistics
- Inventory tracking

### Technical Features
- Responsive design (Mobile, Tablet, Desktop)
- Bootstrap 5 UI framework
- Font Awesome icons
- AJAX for dynamic filtering
- Secure password hashing
- Input validation and error handling
- Session-based authentication
- Clean, modular code structure

## Installation

### Requirements
- PHP 7.0+
- MySQL/MariaDB
- Apache/XAMPP

### Setup Steps

1. **Download Files**
   - Place all files in `c:\xampp\htdocs\electronics_store\`

2. **Database Setup**
   - Open PhpMyAdmin
   - Create a new database named `electronic_store`
   - Import the `setup.sql` file to create all tables and sample data
   
   Or run manually:
   ```sql
   -- Execute all SQL commands from setup.sql
   ```

3. **Create Images Directory**
   - Create a folder named `images` in the project root
   - This folder will store product images

4. **Configure Database**
   - The `db.php` file connects to:
     - Host: `localhost`
     - Username: `root`
     - Password: `` (empty)
     - Database: `electronic_store`
   
   - If your credentials differ, edit `db.php` accordingly

5. **Run the Application**
   - Navigate to 'http://localhost/electronics_store/frontend/pages'
   - Start shopping!



## Default Admin Account

- **Email**: admin@electronicsstore.com
- **Password**: 123456

You can create your own admin account by:
1. Register a new account
2. Manually update the `role` in the database from 'customer' to 'admin'

```sql
UPDATE users SET role='admin' WHERE email='your-email@example.com';
```

## Database Schema

### Users Table
- id, name, email, password, phone, address, city, state, zipcode, country, role, created_at, updated_at

### Products Table
- id, name, description, category, brand, price, stock, image, rating, reviews, created_at, updated_at

### Orders Table
- id, user_id, total_amount, status, payment_method, shipping_address, created_at, updated_at

### Order Items Table
- id, order_id, product_id, quantity, price

### Messages Table
- id, user_name, user_email, message, status, created_at

### Reviews Table
- id, product_id, user_id, rating, comment, created_at

## User Roles

- **Customer**: Can browse products, make purchases, track orders
- **Admin**: Full access to management features and dashboard

## Features by Page

### Home (index.php)
- Featured products slider
- Call-to-action buttons
- Service highlights

### Shop (shop.php)
- Product grid with filtering
- Brand filter
- Category filter
- Price range slider
- Product search
- Add to cart functionality

### Product Details (product-details.php)
- Full product information
- Product images
- Customer reviews
- Rating display
- Stock availability
- Add to cart with quantity

### Cart (cart.php)
- View all cart items
- Update quantities
- Remove items
- Order summary
- Checkout button

### Checkout (checkout.php)
- Shipping address selection
- Payment method selection
- Order review
- Order placement

### Admin Dashboard (admin_dashboard.php)
- Statistics cards
- Recent orders table
- Quick links to management pages

### Manage Products (manage_products.php)
- Product listing with thumbnails
- Add new products
- Edit existing products
- Delete products
- Image upload support

### Manage Orders (manage_orders.php)
- View all orders
- Update order status
- Customer details
- Order amounts

## Security Features

- Password hashing with bcrypt
- Session-based authentication
- SQL injection prevention (input escaping)
- CSRF token support (can be added)
- XSS prevention with htmlspecialchars()
- Admin role verification on all admin pages

## Future Enhancements

- Email notifications for orders
- Payment gateway integration
- Advanced product recommendations
- Bulk operations for admin
- Product variant support
- Multi-language support
- Advanced reporting

## Support

For issues or questions:
1. Check the database connection in `db.php`
2. Ensure all required tables exist
3. Verify file permissions
4. Check browser console for JavaScript errors

## License

This project is provided as-is for educational and commercial use.

---

Built with ❤️ using PHP, MySQL, Bootstrap, and JavaScript
