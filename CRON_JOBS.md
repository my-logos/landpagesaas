# أوامر Cron Jobs المطلوبة للنظام

## المسار الأساسي للمشروع
```
/home/landing
```

---

## الأوامر المطلوبة لإضافة في cPanel Cron Jobs

### 1. تشغيل Laravel Scheduler (كل دقيقة)
هذا الأمر ضروري لتشغيل المهام المجدولة في Laravel
```
* * * * * cd /home/landing && php artisan schedule:run >> /dev/null 2>&1
```

### 2. تشغيل Queue Worker (كل دقيقة)
هذا الأمر لتشغيل المهام من Queue مثل GeneratePageJob
```
* * * * * cd /home/landing && php artisan queue:work --sleep=3 --tries=3 --max-time=3600 >> /dev/null 2>&1
```

**ملاحظة:** يمكنك استخدام `queue:listen` بدلاً من `queue:work` لكن `queue:work` أفضل للأداء:
```
* * * * * cd /home/landing && php artisan queue:listen --sleep=3 --tries=3 >> /dev/null 2>&1
```

---

## أوامر إضافية (اختيارية) لتطوير الأداء

### 3. تنظيف الكاش كل ساعة
```
0 * * * * cd /home/landing && php artisan cache:clear >> /dev/null 2>&1
```

### 4. تنظيف الجلسات القديمة يومياً (في 2 صباحاً)
```
0 2 * * * cd /home/landing && php artisan session:gc >> /dev/null 2>&1
```

### 5. تنظيف View Cache يومياً (في 3 صباحاً)
```
0 3 * * * cd /home/landing && php artisan view:clear >> /dev/null 2>&1
```

### 6. تنظيف Logs القديمة أسبوعياً (كل يوم أحد في 4 صباحاً)
```
0 4 * * 0 find /home/landing/storage/logs -name "*.log" -type f -mtime +7 -delete
```

---

## ملاحظات مهمة

### طريقة إضافة الأوامر في cPanel:
1. اذهب إلى **Cron Jobs** في cPanel
2. اختر **Standard (Unix style)**
3. أضف الأمر في حقل **Command**
4. حدد التوقيت المناسب

### الأوامر الأساسية الضرورية:
- ✅ الأمر رقم 1 (schedule:run) - **ضروري**
- ✅ الأمر رقم 2 (queue:work) - **ضروري جداً** لتشغيل GeneratePageJob وغيرها

### الأوامر الاختيارية:
يمكن إضافتها لتحسين الأداء ولكن ليست ضرورية للنظام الأساسي

---

## شرح المعاملات المستخدمة:

- `cd /home/landing` - الانتقال إلى مجلد المشروع
- `--sleep=3` - الانتظار 3 ثواني بين كل job
- `--tries=3` - عدد محاولات إعادة التشغيل عند الفشل
- `--max-time=3600` - الحد الأقصى لوقت عمل worker (ساعة واحدة)
- `>> /dev/null 2>&1` - تجاهل المخرجات (يمكن تغييرها لكتابة logs)

---

## مثال على إعداد كامل (للإنتاج):

```
# Laravel Scheduler - كل دقيقة
* * * * * cd /home/landing && php artisan schedule:run >> /home/landing/storage/logs/scheduler.log 2>&1

# Queue Worker - كل دقيقة
* * * * * cd /home/landing && php artisan queue:work --sleep=3 --tries=3 --max-time=3600 >> /home/landing/storage/logs/queue.log 2>&1
```

إذا أردت حفظ الـ logs بدلاً من تجاهلها، استخدم:
```
>> /home/landing/storage/logs/cron.log 2>&1
```

