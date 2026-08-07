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
            $values = json_decode($val);
            $values->email_address = 'info@ppvbucks.com';
            $values->contact_number = '316-320-3196';
            $values->contact_details = '331 James Avenue, El Dorado, KS 67042';
            $values->short_details = 'PPV Bucks: Your Gateway to Earn Online';
            $frontend->data_values = $values;
        } else if ($frontend->data_keys == 'about.content') {
            $values = json_decode($val);
            $values->heading = 'Start Earning From The Comfort Of Home!';
            $values->description = 'PPV Bucks is a leading digital marketing agency specializing in driving online traffic and maximizing conversions for businesses. With a focus on ROI-driven strategies';
            $frontend->data_values = $values;
        } else {
            $frontend->data_values = json_decode($val);
        }
        
        $frontend->save();
    }
}
echo "Frontend content and contact info updated.\n";

// Update about elements (bullet points)
$aboutElements = Frontend::where('data_keys', 'about.element')->orderBy('id', 'asc')->get();
$aboutElementTexts = [
    "Elevating your online. Discover our top-tier digital marketing and PPV advertising services designed to maximize your ROI and engage your target audience effectively.",
    "Where strategy meets. Discover our top-tier digital marketing and PPV advertising services designed to maximize your ROI and engage your target audience effectively.",
    "We drive results with PPV. Discover our top-tier digital marketing and PPV advertising services designed to maximize your ROI and engage your target audience effectively.",
    "Your digital growth. Discover our top-tier digital marketing and PPV advertising services designed to maximize your ROI and engage your target audience effectively."
];
foreach ($aboutElements as $index => $el) {
    if (isset($aboutElementTexts[$index])) {
        $el->data_values = (object)['content' => $aboutElementTexts[$index]];
        $el->save();
    }
}
echo "About elements updated.\n";

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
$blogsData = array (
  0 => 
  array (
    'title' => 'Maximizing ROI with Pay-Per-View Advertising',
    'description' => '<p>Discover How PPV Ads Can Transform Your Digital Marketing Strategy</p><p>In today\'s highly competitive digital landscape, businesses are constantly searching for advertising methods that maximize results while keeping costs under control. Traditional advertising models often require businesses to pay for impressions or clicks that may not lead to meaningful engagement. This is where <strong>Pay-Per-View (PPV) advertising</strong> stands out as an effective alternative.</p><p>PPV advertising allows businesses to reach highly targeted audiences and only pay when their advertisements are actually viewed. With the right strategy, PPV campaigns can generate quality traffic, improve brand awareness, increase conversions, and deliver a stronger return on investment (ROI).</p><p>What Are PPV Ads?</p><p>Pay-Per-View (PPV) ads are a digital advertising model where advertisers pay each time their advertisement is viewed by a user. Unlike Pay-Per-Click (PPC), where payment is made only after someone clicks an ad, PPV focuses on gaining visibility and reaching potential customers through targeted impressions.</p><p>PPV ads can appear in various formats, including:</p><p>Pop-up and pop-under advertisements</p><p>Display banners</p><p>Video advertisements</p><p>Native advertising placements</p><p>In-app advertisements</p><p>Content recommendation widgets</p><p>Why Businesses Are Choosing PPV Advertising</p><p>Digital marketers are increasingly adopting PPV advertising because it offers several significant advantages.</p><p>Cost-Effective Marketing</p><p>PPV campaigns often cost less than highly competitive PPC campaigns. Since you\'re paying for actual views rather than broad exposure, your advertising budget can be used more efficiently.</p><p>Better Audience Targeting</p><p>Modern PPV platforms provide sophisticated targeting options, including:</p><p>Geographic location</p><p>Age and gender</p><p>Interests</p><p>Browsing behavior</p><p>Device type</p><p>Operating system</p><p>Language preferences</p><p>This level of targeting ensures your ads reach people who are genuinely interested in your offerings.</p><p>Increased Brand Visibility</p><p>Even if users don\'t immediately interact with your advertisement, repeated exposure helps build brand recognition. Consistent visibility increases the likelihood that potential customers will remember your business when they\'re ready to make a purchase.</p><p>Faster Campaign Launch</p><p>Most PPV platforms allow businesses to create and launch campaigns within minutes. This makes PPV an excellent option for promotions, seasonal offers, product launches, and limited-time campaigns.</p><p>How PPV Ads Transform Your Marketing Strategy</p><p>PPV advertising can improve nearly every stage of your digital marketing funnel by attracting qualified audiences, increasing engagement, and driving measurable business growth.</p><p>Generate High-Quality Traffic</p><p>When properly targeted, PPV campaigns attract users who are already interested in your niche, resulting in more qualified website visitors.</p><p>Improve Conversion Rates</p><p>A highly targeted audience combined with compelling landing pages often results in higher conversion rates. Users who see relevant advertisements are more likely to purchase products, sign up for newsletters, request quotes, or book appointments.</p><p>Strengthen Brand Authority</p><p>Consistent exposure through PPV campaigns builds trust and positions your business as a credible brand within your industry.</p><p>Support Multi-Channel Marketing</p><p>PPV advertising becomes even more effective when combined with:</p><p>Search Engine Optimization (SEO)</p><p>Content Marketing</p><p>Email Marketing</p><p>Social Media Advertising</p><p>Retargeting Campaigns</p><p>Influencer Marketing</p><p>Best Practices for Successful PPV Campaigns</p><p>Running a successful PPV campaign requires planning, testing, and continuous optimization.</p><p>Understand Your Audience</p><p>Research your audience\'s interests, online behavior, demographics, and purchasing habits before launching any campaign.</p><p>Write Compelling Ad Copy</p><p>Create attention-grabbing headlines, focus on customer benefits, and include strong calls-to-action that encourage users to take the next step.</p><p>Create High-Converting Landing Pages</p><p>Your landing page should include:</p><p>Clear messaging</p><p>Fast loading speed</p><p>Mobile-friendly design</p><p>Trust signals</p><p>Easy navigation</p><p>Strong CTA buttons</p><p>Test Multiple Variations</p><p>A/B testing different headlines, creatives, images, and landing pages helps identify the highest-performing combinations.</p><p>Track Performance Metrics</p><p>Measure campaign success using key performance indicators such as:</p><p>View Rate</p><p>Click-Through Rate (CTR)</p><p>Conversion Rate</p><p>Cost Per Acquisition (CPA)</p><p>Return on Ad Spend (ROAS)</p><p>Common Mistakes to Avoid</p><p>Many advertisers fail because they target audiences that are too broad, neglect landing page optimization, use weak ad copy, or fail to monitor campaign performance regularly.</p><p>Measuring Success</p><p>Evaluate PPV campaigns based on meaningful business outcomes such as lead generation, sales growth, customer acquisition, website engagement, and overall return on investment.</p><p>The Future of PPV Advertising</p><p>Artificial intelligence, machine learning, and predictive analytics are making PPV advertising more intelligent and efficient. Future campaigns will become increasingly personalized, automated, and data-driven.</p><p>Final Thoughts</p><p>Pay-Per-View advertising is a powerful way to increase visibility, generate qualified traffic, and improve marketing ROI. By combining precise audience targeting, engaging ad creatives, optimized landing pages, and continuous testing, businesses can turn every ad view into a valuable opportunity for growth. Whether you\'re a startup or an established brand, PPV advertising can become an essential part of a successful digital marketing strategy.</p>',
    'image' => '6a7579c59cd561786083781.png',
  ),
  1 => 
  array (
    'title' => 'Top 5 Digital Marketing Trends for the Year',
    'description' => '<p>Stay ahead of the curve! Learn about the emerging trends in online advertising and how you can leverage them to boost your campaign performance.</p>',
    'image' => '6a7579e966eba1786083817.jpg',
  ),
  2 => 
  array (
    'title' => 'How to Optimize Your Ad Spend Effectively',
    'description' => '<p>Are you wasting money on poorly optimized campaigns? Read our ultimate guide on managing budgets and increasing conversion rates through strategic targeting.</p>',
    'image' => '6a757a3b4a0cf1786083899.jpg',
  ),
  3 => 
  array (
    'title' => 'The Future of Monetization and Traffic Generation',
    'description' => '<p>As the digital landscape evolves, so do the methods to monetize traffic. Explore new pathways to generate revenue through our innovative platform.</p>',
    'image' => '6a757a57b8b471786083927.jpg',
  ),
);

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
