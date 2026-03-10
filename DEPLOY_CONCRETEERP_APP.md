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
