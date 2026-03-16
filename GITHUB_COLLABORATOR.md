# إضافة Collaborator وحساب آخر للرفع على GitHub

## 1. إضافة شخص كمتعاون (Collaborator) على الريبو

إذا كان لديك صلاحية على ريبو **hsusony/concrete**:

1. افتح: **https://github.com/hsusony/concrete**
2. اذهب إلى **Settings** (الإعدادات)
3. من القائمة الجانبية اختر **Collaborators** (أو **Collaborators and teams**)
4. اضغط **Add people**
5. اكتب اسم المستخدم أو البريد الذي تريد إضافته (مثلاً: ayuob-darrab)
6. اختر الصلاحية **Write** أو **Maintain** ثم **Add**

بعدها المستخدم المضاف يستطيع الرفع من جهازه بعد تسجيل الدخول بحسابه.

---

## 2. الرفع من Git بعد الإضافة

إذا أضيفت كمتعاون، من مجلد المشروع نفّذ:

```powershell
cd C:\laragon\www\ConcreteERP
git push -u origin main
```

إذا طلب منك اسم مستخدم وكلمة مرور:
- **Username:** حسابك على GitHub (الذي أضيف كمتعاون)
- **Password:** استخدم **Personal Access Token** وليس كلمة مرور الحساب

### إنشاء Personal Access Token (اختياري)

1. GitHub → **Settings** → **Developer settings** → **Personal access tokens**
2. **Generate new token (classic)**
3. فعّل صلاحية **repo**
4. انسخ الـ Token واستخدمه مكان كلمة المرور عند `git push`

---

## 3. إذا كنت تريد الرفع بحساب hsusony من جهازك

استخدم الرابط مع اسم المستخدم ليفتح لك نافذة تسجيل الدخول:

```powershell
cd C:\laragon\www\ConcreteERP
git remote set-url origin https://hsusony@github.com/hsusony/concrete.git
git push -u origin main
```

سيُطلب منك كلمة مرور حساب **hsusony** (أو الـ Token الخاص به).
