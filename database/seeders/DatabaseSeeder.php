<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Banner;
use App\Models\BannerGroup;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@messarat.com',
        ]);

        // Categories
        $categories = collect([
            'الروبوتات', 'البرمجة', 'الإلكترونيات', 'الطباعة ثلاثية الأبعاد',
            'الذكاء الاصطناعي', 'إنترنت الأشياء', 'الطاقة المتجددة', 'الفضاء',
        ])->map(fn ($name) => Category::create(['name' => $name]));

        // Articles
        $articles = [
            [
                'title' => 'مقدمة في عالم الروبوتات للأطفال',
                'description' => 'تعرف على عالم الروبوتات المثير! في هذا المقال سنأخذك في رحلة ممتعة لاكتشاف كيف تعمل الروبوتات وكيف يمكنك بناء روبوتك الأول. سنتعلم عن المحركات والمستشعرات والمتحكمات الدقيقة بطريقة سهلة وبسيطة.',
                'views' => 1250,
                'downloads' => 340,
                'release_date' => '2026-01-15',
                'category_ids' => [1, 6],
            ],
            [
                'title' => 'تعلم البرمجة مع سكراتش',
                'description' => 'سكراتش هو أسهل طريقة لتعلم البرمجة! في هذا الدليل الشامل سنتعلم كيف نصنع ألعاباً ورسوماً متحركة وقصصاً تفاعلية باستخدام لغة سكراتش. مناسب للأعمار من 7 إلى 14 سنة.',
                'views' => 2100,
                'downloads' => 580,
                'release_date' => '2026-01-20',
                'category_ids' => [2],
            ],
            [
                'title' => 'دوائر إلكترونية ممتعة',
                'description' => 'هل تريد أن تفهم كيف تعمل الأجهزة الإلكترونية من حولك؟ في هذا المقال سنبني دوائر إلكترونية بسيطة باستخدام مصابيح LED ومقاومات وبطاريات. كل تجربة مصممة لتكون آمنة وممتعة للأطفال.',
                'views' => 890,
                'downloads' => 210,
                'release_date' => '2026-02-01',
                'category_ids' => [3],
            ],
            [
                'title' => 'اطبع أحلامك ثلاثياً!',
                'description' => 'الطباعة ثلاثية الأبعاد تحول أفكارك إلى أشياء حقيقية! تعلم أساسيات التصميم ثلاثي الأبعاد واستخدام الطابعات ثلاثية الأبعاد. سنصمم معاً نماذج بسيطة ونطبعها خطوة بخطوة.',
                'views' => 760,
                'downloads' => 190,
                'release_date' => '2026-02-10',
                'category_ids' => [4],
            ],
            [
                'title' => 'الذكاء الاصطناعي للصغار',
                'description' => 'ما هو الذكاء الاصطناعي؟ كيف تتعلم الآلات؟ في هذا المقال نشرح مفاهيم الذكاء الاصطناعي بطريقة مبسطة وممتعة مع أمثلة من حياتنا اليومية. سنتعرف على كيفية تدريب نموذج بسيط للتعرف على الصور.',
                'views' => 1800,
                'downloads' => 420,
                'release_date' => '2026-02-15',
                'category_ids' => [5, 2],
            ],
            [
                'title' => 'بيتك الذكي مع إنترنت الأشياء',
                'description' => 'تخيل أن تتحكم في أضواء غرفتك من هاتفك! في هذا المشروع سنبني نظام بيت ذكي بسيط باستخدام أردوينو ومستشعرات مختلفة. سنتعلم كيف نربط الأجهزة بالإنترنت ونتحكم بها عن بعد.',
                'views' => 1450,
                'downloads' => 380,
                'release_date' => '2026-02-20',
                'category_ids' => [6, 3, 1],
            ],
            [
                'title' => 'الطاقة الشمسية: مشاريع للأطفال',
                'description' => 'الشمس مصدر طاقة مذهل! في هذا المقال سنتعلم عن الطاقة الشمسية ونبني مشاريع بسيطة مثل سيارة تعمل بالطاقة الشمسية وشاحن هاتف شمسي. كل مشروع مصمم ليكون ممتعاً وتعليمياً.',
                'views' => 670,
                'downloads' => 150,
                'release_date' => '2026-03-01',
                'category_ids' => [7, 3],
            ],
            [
                'title' => 'رحلة إلى الفضاء',
                'description' => 'هل تحلم بالسفر إلى الفضاء؟ تعرف على عجائب الكون من الكواكب والنجوم والمجرات. سنتعلم عن الصواريخ وكيف يعيش رواد الفضاء في محطة الفضاء الدولية. رحلة علمية ممتعة!',
                'views' => 2300,
                'downloads' => 610,
                'release_date' => '2026-03-05',
                'category_ids' => [8],
            ],
            [
                'title' => 'بايثون للمبتدئين الصغار',
                'description' => 'بايثون من أسهل لغات البرمجة وأكثرها استخداماً! في هذا الدليل سنتعلم أساسيات بايثون من خلال ألعاب وتحديات ممتعة. سنكتب برامج بسيطة مثل آلة حاسبة ولعبة تخمين الأرقام.',
                'views' => 1900,
                'downloads' => 490,
                'release_date' => '2026-03-10',
                'category_ids' => [2, 5],
            ],
            [
                'title' => 'صنع روبوت متتبع الخط',
                'description' => 'مشروع عملي رائع! سنبني روبوتاً يتتبع خطاً أسود على الأرض تلقائياً. سنتعلم عن المستشعرات الضوئية والمحركات وكيفية برمجة الروبوت ليتخذ قرارات بسيطة. مشروع مناسب للمبتدئين.',
                'views' => 1100,
                'downloads' => 290,
                'release_date' => '2026-03-15',
                'category_ids' => [1, 3, 2],
            ],
            [
                'title' => 'عالم الدرونز والطائرات بدون طيار',
                'description' => 'كيف تطير الطائرات بدون طيار؟ في هذا المقال سنتعرف على تقنية الدرونز وكيف تعمل. سنتعلم أساسيات الطيران والتحكم وحتى كيف نبرمج درون بسيط ليطير بشكل مستقل.',
                'views' => 1650,
                'downloads' => 400,
                'release_date' => '2026-03-18',
                'category_ids' => [1, 6, 3],
            ],
            [
                'title' => 'تصميم الألعاب الإلكترونية',
                'description' => 'هل تحب الألعاب الإلكترونية؟ ماذا لو صنعت لعبتك الخاصة؟ في هذا الدليل سنتعلم أساسيات تصميم الألعاب باستخدام محرك بسيط. سنصمم شخصيات ومراحل ونضيف أصواتاً وتأثيرات.',
                'views' => 2500,
                'downloads' => 700,
                'release_date' => '2026-03-19',
                'category_ids' => [2, 5],
            ],
        ];

        foreach ($articles as $data) {
            $categoryIds = $data['category_ids'];
            unset($data['category_ids']);

            $article = Article::create($data);
            $article->categories()->attach($categoryIds);
        }

        // Reviews
        $reviewers = [
            ['name' => 'أحمد محمد', 'email' => 'ahmed@example.com'],
            ['name' => 'فاطمة علي', 'email' => 'fatima@example.com'],
            ['name' => 'يوسف خالد', 'email' => 'youssef@example.com'],
            ['name' => 'مريم حسن', 'email' => 'mariam@example.com'],
            ['name' => 'عمر سعيد', 'email' => 'omar@example.com'],
            ['name' => 'نور الدين', 'email' => 'nour@example.com'],
            ['name' => 'سارة أحمد', 'email' => 'sara@example.com'],
            ['name' => 'خالد عبدالله', 'email' => 'khaled@example.com'],
        ];

        $reviewTexts = [
            'مقال رائع جداً! استفاد منه ابني كثيراً وبدأ يطبق ما تعلمه.',
            'شرح مبسط وواضح، مناسب للأطفال. شكراً لكم على هذا المحتوى المميز.',
            'محتوى تعليمي ممتاز. أتمنى المزيد من المقالات مثل هذه.',
            'ابنتي أحبت هذا المقال وطلبت المزيد! استمروا في هذا العمل الرائع.',
            'معلومات قيمة مقدمة بطريقة ممتعة. أنصح كل الآباء بمشاركتها مع أطفالهم.',
            'من أفضل المقالات التعليمية للأطفال باللغة العربية. محتوى غني ومفيد.',
            'أعجبني الأسلوب البسيط في الشرح. الصور والرسومات ساعدت كثيراً في الفهم.',
            'مقال ممتاز! نحتاج المزيد من هذا المحتوى العربي المميز للأطفال.',
        ];

        $ipCounter = 1;
        foreach (Article::all() as $article) {
            $numReviews = rand(2, 5);
            $shuffled = collect($reviewers)->shuffle()->take($numReviews);

            foreach ($shuffled as $i => $reviewer) {
                Review::create([
                    'article_id' => $article->id,
                    'name' => $reviewer['name'],
                    'email' => $reviewer['email'],
                    'rating' => rand(3, 5),
                    'review' => $reviewTexts[array_rand($reviewTexts)],
                    'ip_address' => '192.168.1.' . $ipCounter++,
                ]);
            }
        }

        // Banners
        $banners = [
            Banner::create([
                'title' => 'اكتشف عالم الروبوتات',
                'subtitle' => 'مقالات تعليمية ممتعة للأطفال',
                'link' => '/article/1',
                'image_path' => 'banners/placeholder.jpg',
                'is_active' => true,
            ]),
            Banner::create([
                'title' => 'تعلم البرمجة بسهولة',
                'subtitle' => 'ابدأ رحلتك مع سكراتش وبايثون',
                'link' => '/article/2',
                'image_path' => 'banners/placeholder.jpg',
                'is_active' => true,
            ]),
            Banner::create([
                'title' => 'مشاريع إلكترونية للمبدعين',
                'subtitle' => 'حوّل أفكارك إلى حقيقة',
                'link' => '/article/6',
                'image_path' => 'banners/placeholder.jpg',
                'is_active' => true,
            ]),
        ];

        BannerGroup::create([
            'name' => 'الرئيسية',
            'banner_ids' => [$banners[0]->id, $banners[1]->id, $banners[2]->id],
            'is_active' => true,
        ]);
    }
}