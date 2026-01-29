# Template Parts Structure

این پوشه شامل بخش‌های مختلف قالب است که به صورت ماژولار طراحی شده‌اند.

## ساختار پوشه‌ها

### `front-page/`
بخش‌های مختلف صفحه اصلی:

- **`hero-section.php`** - بخش اصلی (Hero) با عنوان و دکمه‌های عملیات
- **`features-section.php`** - بخش ویژگی‌های سایت
- **`latest-products-section.php`** - نمایش آخرین محصولات (نیاز به WooCommerce)
- **`latest-posts-section.php`** - نمایش آخرین مطالب

## نحوه استفاده

برای استفاده از هر بخش در فایل‌های دیگر:

```php
<?php
// فراخوانی بخش hero
get_template_part('template-parts/front-page/hero-section');

// فراخوانی بخش ویژگی‌ها
get_template_part('template-parts/front-page/features-section');

// فراخوانی بخش محصولات
get_template_part('template-parts/front-page/latest-products-section');

// فراخوانی بخش مطالب
get_template_part('template-parts/front-page/latest-posts-section');
?>
```

## مزایای این ساختار

1. **قابلیت استفاده مجدد**: هر بخش را می‌توان در صفحات دیگر استفاده کرد
2. **نگهداری آسان**: تغییرات در هر بخش فقط در یک فایل اعمال می‌شود
3. **سازماندهی بهتر**: کدها به صورت منطقی دسته‌بندی شده‌اند
4. **انعطاف‌پذیری**: امکان فعال/غیرفعال کردن بخش‌ها به راحتی

## نکات مهم

- هر بخش شامل header comment مناسب است
- بررسی‌های لازم (مثل WooCommerce) در هر بخش اعمال شده
- از `get_template_part()` برای فراخوانی استفاده می‌شود
