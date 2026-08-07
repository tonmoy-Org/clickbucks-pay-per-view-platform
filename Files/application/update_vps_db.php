<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GeneralSetting;
use App\Models\Frontend;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Form;
use App\Models\WithdrawMethod;

echo "Starting database updates on VPS...\n";

// 1. General Settings Rebranding & Contact
$gs = GeneralSetting::first();
if ($gs) {
    $gs->site_name = 'PPV Bucks';
    $gs->save();
    echo "Site name updated to PPV Bucks.\n";
}

// 2. Frontend Section Content (Banners, about, etc.)
$frontends = Frontend::all();
foreach ($frontends as $frontend) {
    if (isset($frontend->data_values)) {
        $val = json_encode($frontend->data_values);
        
        // Rebrand ClickBucks -> PPV Bucks
        $val = str_ireplace('ClickBucks', 'PPV Bucks', $val);
        // Replace Lorem Ipsum & old PTC branding with Digital Marketing
        $val = str_ireplace('PTC Ads', 'PPV & Digital Marketing', $val);
        $val = str_ireplace('PTC and view ads', 'PPV and Digital Marketing', $val);
        
        // Contact details update
        if ($frontend->data_keys == 'contact_us.content') {
            $frontend->data_values->email_address = 'info@ppvbucks.com';
            $frontend->data_values->contact_number = '316-320-3196';
            $frontend->data_values->contact_details = '331 James Avenue, El Dorado, KS 67042';
            $frontend->data_values->short_details = 'PPV Bucks: Your Gateway to Earn Online';
        } else if ($frontend->data_keys == 'about.content') {
            $frontend->data_values->heading = 'Start Earning From The Comfort Of Home!';
            $frontend->data_values->description = 'PPV Bucks is a leading digital marketing agency specializing in driving online traffic and maximizing conversions for businesses. With a focus on ROI-driven strategies';
        } else {
            $frontend->data_values = json_decode($val);
        }
        
        $frontend->save();
    }
}
echo "Frontend content and contact info updated.\n";

// 3. Pages / Menus Update
$pages = Page::all();
foreach ($pages as $page) {
    if (stripos($page->name, 'PTC') !== false) {
        $page->name = str_ireplace('PTC', 'PPV & Digital Marketing', $page->name);
        $page->save();
    }
}
echo "Page names updated.\n";

// 4. Update Blogs (titles, descriptions, unique images)
$blogs = Frontend::where('data_keys', 'blog.element')->get();
$blogsData = [
    [
        'title' => 'Maximizing ROI with Pay-Per-View Advertising',
        'description' => 'Discover how PPV ads can transform your digital marketing strategy. We dive into actionable techniques that ensure every click delivers value.',
        'image' => 'blog_social.png'
    ],
    [
        'title' => 'Top 5 Digital Marketing Trends for the Year',
        'description' => 'Stay ahead of the curve! Learn about the emerging trends in online advertising and how you can leverage them to boost your campaign performance.',
        'image' => 'blog_banner.png'
    ],
    [
        'title' => 'How to Optimize Your Ad Spend Effectively',
        'description' => 'Are you wasting money on poorly optimized campaigns? Read our ultimate guide on managing budgets and increasing conversion rates through strategic targeting.',
        'image' => 'blog_about.png'
    ],
    [
        'title' => 'The Future of Monetization and Traffic Generation',
        'description' => 'As the digital landscape evolves, so do the methods to monetize traffic. Explore new pathways to generate revenue through our innovative platform.',
        'image' => 'blog_faq.png'
    ]
];

foreach ($blogs as $index => $blog) {
    if (isset($blog->data_values) && isset($blogsData[$index])) {
        $values = $blog->data_values;
        $values->title = $blogsData[$index]['title'];
        $values->description = $blogsData[$index]['description'];
        $values->blog_image = $blogsData[$index]['image'];
        $blog->data_values = $values;
        $blog->save();
    }
}
echo "Blogs updated with unique text and images.\n";

// 5. Update Testimonials
$testimonials = Frontend::where('data_keys', 'testimonial.element')->get();
$feedbackData = [
    [
        'name' => 'Sarah Jenkins',
        'designation' => 'Digital Marketing Director',
        'description' => 'PPV Bucks completely transformed our ad strategy. The precision targeting and high-quality traffic have increased our conversion rate by 150% in just two months!',
        'star_count' => '5'
    ],
    [
        'name' => 'Michael Chen',
        'designation' => 'E-commerce Entrepreneur',
        'description' => 'I have tried several pay-per-view networks, but none offer the transparency and rapid ROI that this platform provides. The dashboard is incredibly intuitive.',
        'star_count' => '5'
    ],
    [
        'name' => 'Elena Rodriguez',
        'designation' => 'Affiliate Marketer',
        'description' => 'Since switching my campaigns to PPV Bucks, my earnings have skyrocketed. Their anti-fraud measures ensure every click is genuine, which gives me peace of mind.',
        'star_count' => '5'
    ]
];

foreach ($testimonials as $index => $testimonial) {
    if (isset($testimonial->data_values) && isset($feedbackData[$index])) {
        $values = $testimonial->data_values;
        $values->name = $feedbackData[$index]['name'];
        $values->designation = $feedbackData[$index]['designation'];
        $values->description = $feedbackData[$index]['description'];
        $values->star_count = $feedbackData[$index]['star_count'];
        $testimonial->data_values = $values;
        $testimonial->save();
    }
}
echo "Testimonials updated.\n";

// 6. Create 3 Plans (Standard, Gold, Platinum) - manually to bypass mass assignment
$plans = [
    ['name' => 'Standard', 'price' => 10, 'point' => 100, 'status' => 1],
    ['name' => 'Gold', 'price' => 50, 'point' => 600, 'status' => 1],
    ['name' => 'Platinum', 'price' => 100, 'point' => 1500, 'status' => 1]
];

foreach ($plans as $p) {
    $plan = Plan::where('name', $p['name'])->first();
    if (!$plan) {
        $plan = new Plan();
        $plan->name = $p['name'];
    }
    $plan->price = $p['price'];
    $plan->point = $p['point'];
    $plan->status = $p['status'];
    $plan->save();
}
echo "Subscription plans updated.\n";

// 7. Create Withdrawal Methods - manually to bypass mass assignment
function getOrCreateForm($label) {
    $form = Form::where('act', 'withdraw_method')
                ->where('form_data', 'like', '%' . $label . '%')
                ->first();
    if (!$form) {
        $form = new Form();
        $form->act = 'withdraw_method';
        $form->form_data = json_encode([
            'account_details' => [
                'name' => 'Account Details',
                'label' => $label,
                'is_required' => 'required',
                'extensions' => '',
                'options' => [],
                'type' => 'text',
            ]
        ]);
        $form->save();
    }
    return $form->id;
}

$withdrawMethods = [
    [
        'name' => 'Bank Transfer',
        'min_limit' => 10,
        'max_limit' => 1000,
        'fixed_charge' => 1,
        'percent_charge' => 0,
        'rate' => 1,
        'currency' => 'USD',
        'description' => 'Withdraw directly to your bank account.',
        'label' => 'Enter your Bank Account Details'
    ],
    [
        'name' => 'PayPal',
        'min_limit' => 5,
        'max_limit' => 500,
        'fixed_charge' => 0.5,
        'percent_charge' => 2,
        'rate' => 1,
        'currency' => 'USD',
        'description' => 'Withdraw instantly to your PayPal account.',
        'label' => 'Enter your PayPal Email Address'
    ],
    [
        'name' => 'USDT (TRC20)',
        'min_limit' => 10,
        'max_limit' => 10000,
        'fixed_charge' => 1,
        'percent_charge' => 0,
        'rate' => 1,
        'currency' => 'USDT',
        'description' => 'Withdraw securely via TRC20 Crypto network.',
        'label' => 'Enter your USDT (TRC20) Wallet Address'
    ],
    [
        'name' => 'Skrill',
        'min_limit' => 10,
        'max_limit' => 1000,
        'fixed_charge' => 1,
        'percent_charge' => 1,
        'rate' => 1,
        'currency' => 'USD',
        'description' => 'Withdraw to your Skrill wallet quickly.',
        'label' => 'Enter your Skrill Email'
    ]
];

foreach ($withdrawMethods as $wm) {
    $formId = getOrCreateForm($wm['label']);
    $wmRecord = WithdrawMethod::where('name', $wm['name'])->first();
    if (!$wmRecord) {
        $wmRecord = new WithdrawMethod();
        $wmRecord->name = $wm['name'];
    }
    $wmRecord->form_id = $formId;
    $wmRecord->min_limit = $wm['min_limit'];
    $wmRecord->max_limit = $wm['max_limit'];
    $wmRecord->fixed_charge = $wm['fixed_charge'];
    $wmRecord->percent_charge = $wm['percent_charge'];
    $wmRecord->rate = $wm['rate'];
    $wmRecord->currency = $wm['currency'];
    $wmRecord->description = $wm['description'];
    $wmRecord->status = 1;
    $wmRecord->save();
}
echo "Withdrawal methods updated.\n";
echo "Database update completed successfully!\n";
