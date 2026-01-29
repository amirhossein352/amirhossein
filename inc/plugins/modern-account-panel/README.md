# Modern Account Panel

یک افزونه حرفه‌ای برای ووکامرس که صفحات حساب کاربری پیش‌فرض را با یک پنل مدرن و زیبا جایگزین می‌کند.

## ویژگی‌ها

- ✅ طراحی مدرن و حرفه‌ای
- ✅ واکنش‌گرا (Responsive) برای تمام دستگاه‌ها
- ✅ سایدبار قابل جمع‌شدن
- ✅ ناوبری ساده و روان
- ✅ داشبورد با کارت‌های آماری
- ✅ جداول داده
- ✅ منوی کاربری
- ✅ پشتیبانی از زبان فارسی (RTL)
- ✅ Taskbar شبیه Windows 11
- ✅ Start Menu

## نصب

1. فایل افزونه را در پوشه `wp-content/plugins/` کپی کنید
2. از پنل مدیریت وردپرس، افزونه را فعال کنید
3. مطمئن شوید که ووکامرس نصب و فعال است

## نیازمندی‌ها

- WordPress 5.0 یا بالاتر
- WooCommerce 3.0 یا بالاتر
- PHP 7.2 یا بالاتر

## ساختار افزونه

```
modern-account-panel/
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   ├── sidebar.css
│   │   ├── header.css
│   │   ├── taskbar.css
│   │   ├── dashboard.css
│   │   ├── page-loader.css
│   │   ├── responsive.css
│   │   └── pages/
│   └── js/
│       ├── main.js
│       ├── sidebar.js
│       ├── navigation.js
│       ├── taskbar.js
│       ├── page-loader.js
│       ├── quick-access.js
│       └── pages/
├── includes/
│   └── class-modern-account-panel.php
├── templates/
│   ├── account/
│   │   ├── sidebar.php
│   │   ├── header.php
│   │   ├── taskbar.php
│   │   └── start-menu.php
│   └── woocommerce/
│       └── myaccount/
│           ├── dashboard.php
│           ├── orders.php
│           ├── downloads.php
│           ├── edit-account.php
│           └── edit-address.php
├── modern-account-panel.php
└── README.md
```

## صفحات پشتیبانی شده

- **پیشخوان (Dashboard)**: نمایش آمار و سفارش‌های اخیر
- **سفارش‌ها (Orders)**: لیست تمام سفارش‌های کاربر
- **دانلودها (Downloads)**: فایل‌های قابل دانلود
- **آدرس‌ها (Addresses)**: مدیریت آدرس‌های صورتحساب و ارسال
- **جزئیات حساب (Account Details)**: ویرایش اطلاعات حساب کاربری

## سفارشی‌سازی

### تغییر رنگ‌ها

رنگ‌های اصلی در فایل `assets/css/main.css` در بخش `:root` تعریف شده‌اند:

```css
:root {
    --primary-color: #6366f1;
    --sidebar-bg: #1e293b;
    /* ... */
}
```

## پشتیبانی

برای گزارش باگ یا پیشنهاد ویژگی جدید، لطفاً از Issues در مخزن GitHub استفاده کنید.

## لایسنس

این افزونه برای استفاده آزاد است.

