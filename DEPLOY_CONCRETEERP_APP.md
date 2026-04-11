# رفع المشروع على https://concreteerp.app

## إعدادات .env على السيرفر

على السيرفر (الإنتاج) ضع في ملف `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://concreteerp.app
# لا تضف ASSET_URL أو اتركه فارغاً لاستخدام APP_URL تلقائياً
```

## ما تم ترتيبه في الكود

- **config/app.php**: القيمة الافتراضية لـ `APP_URL` أصبحت `https://concreteerp.app`، و`asset_url` يُحدد من `.env` فقط (مناسب للإنتاج).
- **AppServiceProvider**: يتم فرض جذر الروابط من `config('app.url')` في كل البيئات، لذا السايدبار وبطاقات الهوم والنماذج تستخدم نفس الأساس (محلي أو إنتاج).
- **الروابط الثابتة**: تم استبدال `/ConcreteERP` في القوائم ورابط «الرئيسية» وروابط التعديل في الجداول بـ `{{ url('/') }}` أو `{{ url('') }}` حتى تعمل على الموقع دون مسار فرعي.
- **السايدبار**: يعتمد على `$basePath` و`$u` و`$r`؛ على concreteerp.app المسار الفرعي فارغ فجميع الروابط تظهر صحيحة.
- **بطاقات صفحة الهوم (nav-cards)**: تستخدم `url()` و`route()` فتأخذ الأساس من `APP_URL` تلقائياً.

## محلياً (مع مسار فرعي مثل /ConcreteERP)

```env
APP_URL=http://localhost/ConcreteERP
ASSET_URL=/ConcreteERP/public
```

بعد تغيير `.env` على السيرفر نفّذ:

```bash
php artisan config:clear
php artisan cache:clear
```

## إذا ظهر خطأ 500 (صفحة بيضاء أو «Internal Server Error»)

1. **جذر الموقع (Document root)** يجب أن يشير إلى مجلد `public` داخل المشروع، وليس جذر المستودع.
2. تأكد من وجود **`APP_KEY`** في `.env` (وليس فارغاً). إن لم يكن: `php artisan key:generate` ثم `php artisan config:clear`.
3. صلاحيات المجلدات: على Linux يجب أن يكون للمستخدم الذي يشغّل PHP-FPM الكتابة في `storage` و`bootstrap/cache` (مثلاً `chmod -R ug+rwx storage bootstrap/cache` مع المالك المناسب).
4. راجع **`storage/logs/laravel.log`** وسجل أخطاء **nginx** و**php-fpm** على السيرفر لمعرفة السبب الحقيقي (امتداد PHP ناقص، فشل اتصال قاعدة البيانات، إلخ).
5. **PHP 8.2+** مطلوب لهذا المشروع (`composer.json`: `"php": "^8.2"`).
6. **TrustProxies**: في الكود تم ضبط الثقة بالوكلاء (`*`) حتى يعمل HTTPS خلف nginx/Cloudflare بشكل صحيح عند الحاجة.
