<?php

namespace Database\Seeders;

use App\Models\SupportFaq;
use Illuminate\Database\Seeder;

class SupportFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question_en' => 'How can I create a new landing page?',
                'question_ar' => 'كيف يمكنني إنشاء صفحة هبوط جديدة؟',
                'answer_en' => 'To create a new landing page, go to the "Pages" section in your dashboard, click on "Create New Page", fill in the product details, and the AI will generate a professional landing page for you automatically.',
                'answer_ar' => 'لإنشاء صفحة هبوط جديدة، انتقل إلى قسم "الصفحات" في لوحة التحكم، اضغط على "إنشاء صفحة جديدة"، املأ تفاصيل المنتج، وسيقوم الذكاء الاصطناعي بإنشاء صفحة هبوط احترافية لك تلقائياً.',
                'keywords_en' => ['create', 'landing page', 'new page', 'how to'],
                'keywords_ar' => ['إنشاء', 'صفحة هبوط', 'صفحة جديدة', 'كيف'],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question_en' => 'What is the maximum number of products in the free plan?',
                'question_ar' => 'ما الحد الأقصى لعدد المنتجات في الخطة المجانية؟',
                'answer_en' => 'The maximum is 1 product in the free plan. To increase this limit, you can upgrade to one of the paid plans.',
                'answer_ar' => 'الحد الأقصى هو منتج واحد في الخطة المجانية. لرفع هذا الحد، يمكنك الترقية إلى إحدى الخطط المدفوعة.',
                'keywords_en' => ['free plan', 'product limit', 'maximum products', 'upgrade'],
                'keywords_ar' => ['الخطة المجانية', 'حد المنتجات', 'أقصى عدد منتجات', 'ترقية'],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question_en' => 'How do I add funds to my wallet?',
                'question_ar' => 'كيف أضيف رصيداً لمحفظتي؟',
                'answer_en' => 'To add funds to your wallet, go to "Add Balance" in your dashboard, enter the amount you want to add, choose your payment method, and complete the payment process.',
                'answer_ar' => 'لإضافة رصيد لمحفظتك، انتقل إلى "إضافة رصيد" في لوحة التحكم، أدخل المبلغ الذي تريد إضافته، اختر طريقة الدفع، وأكمل عملية الدفع.',
                'keywords_en' => ['wallet', 'add balance', 'funds', 'payment'],
                'keywords_ar' => ['محفظة', 'إضافة رصيد', 'أموال', 'دفع'],
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question_en' => 'Why can\'t I edit a product after publishing it?',
                'question_ar' => 'لماذا لا أستطيع تعديل منتج بعد نشره؟',
                'answer_en' => 'This is a security feature to prevent changes to published products that may have active orders. You can create a new version of the product or contact support for assistance.',
                'answer_ar' => 'هذه ميزة أمان لمنع التغييرات على المنتجات المنشورة التي قد تحتوي على طلبات نشطة. يمكنك إنشاء نسخة جديدة من المنتج أو الاتصال بالدعم للمساعدة.',
                'keywords_en' => ['edit', 'product', 'published', 'modify'],
                'keywords_ar' => ['تعديل', 'منتج', 'منشور', 'تغيير'],
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question_en' => 'How do I track customer orders?',
                'question_ar' => 'كيف أتابع طلبات العملاء؟',
                'answer_en' => 'You can track all customer orders from the "Orders" section in your dashboard. You can filter by status, search by order number, and update shipping status for each order.',
                'answer_ar' => 'يمكنك متابعة جميع طلبات العملاء من قسم "الطلبات" في لوحة التحكم. يمكنك التصفية حسب الحالة والبحث برقم الطلب وتحديث حالة الشحن لكل طلب.',
                'keywords_en' => ['track', 'orders', 'customer orders', 'order management'],
                'keywords_ar' => ['متابعة', 'طلبات', 'طلبات العملاء', 'إدارة الطلبات'],
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            SupportFaq::updateOrCreate(
                ['question_en' => $faq['question_en']],
                $faq
            );
        }
    }
}

