<?php
/**
 * Digital Commerce landing page (/digital-commerce, template "digital-commerce").
 *
 * Built on the home page's components and palette: /assets/home10.css and
 * home10.js load first and the page sits in .h10, so buttons, pills, the word
 * reveal on headings, tints and the footer are shared with Home, About and
 * Business Systems. What is particular to this page is /assets/dcom.css
 * (everything under .dc) and /assets/dcom.js. Every section has its own layout,
 * taken from what it says and from the shop itself:
 *
 *   01 hero          the statement beside a live product page, with the four
 *                    differentiators as a ticker underneath
 *   02 challenge     a shrinking funnel beside a store-audit receipt that lists
 *                    the six challenges as line items
 *   03 engineer      six paper tickets, the same ones as the home page's approach
 *   04 ecosystem     four lanes on one rail, an order travelling through them,
 *                    then the platforms we connect
 *   05 approach      a checkout stepper: four steps on a line that fills in
 *   06 industries    a category menu with a preview: each category shows the
 *                    piece of shop that matters most for it
 *   07 projects      one browser window, the three projects as its tabs
 *   08 final CTA     the home page's light full-width close
 *
 * All copy is server-rendered inside <section>s, so Admin → Pages → Page
 * Content can edit it. Everything reads in full without JavaScript, and stands
 * still under prefers-reduced-motion. Product screens use sample data.
 */
$activePage = 'digital-commerce';
include __DIR__ . '/partials/nav.php';

$dcIc = fn (string $id, string $class = 'ic'): string => '<svg class="' . $class . '" aria-hidden="true" focusable="false"><use href="#d-' . $id . '"/></svg>';
$dcArrow = $dcIc('arrow', 'ic h10-btn__arr');
$dcN = fn (int $i): string => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
$dcCase = function (string $slug, string $fallback) use ($pdo): string {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM case_studies WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() ? '/case-studies/' . $slug : $fallback;
    } catch (PDOException $e) {
        return $fallback;
    }
};
$dcWork = '/case-studies?service=Ecommerce+Solutions';

// 01 · differentiators [icon, label]
$dcDiff = [['store', 'Custom-Built Storefronts'], ['target', 'Conversion-Focused Experiences'], ['bolt', 'App-Light Functionality'], ['trend', 'Built to Scale']];

// 02 · challenges [title, text]
$dcProblems = [
    ['Generic Storefront Designs', 'Your online store looks like every other store, making it difficult to communicate what makes your brand different.'],
    ['Visitors Without Purchases', 'Customers visit your website but leave without adding products to their cart or completing an order.'],
    ['Complicated Shopping Experiences', 'Poor navigation, confusing product information and unnecessary steps make shopping difficult.'],
    ['Too Many Apps and Integrations', 'Multiple apps increase complexity, create dependencies and make your store harder to manage.'],
    ['Manual Order Management', 'Disconnected inventory, shipping, payment and order processes create unnecessary operational work.'],
    ['Difficult to Scale', 'Your existing ecommerce setup struggles to support new products, growing orders and changing business requirements.'],
];
// the funnel: [stage, bar width %] (illustrative)
$dcFunnel = [['Visitors', 100], ['Product views', 64], ['Added to cart', 30], ['Checkout', 16], ['Orders', 8]];

// 03 · capabilities [icon, tint, title, text, items, ticket label]
$dcBuild = [
    ['code', 'peach', 'Custom Ecommerce Development', 'Build an online store that reflects your brand instead of relying on a generic template.', ['Custom Shopify Store Development', 'Headless & Custom Ecommerce Experiences', 'Custom UI/UX Design', 'Responsive Storefront Development'], 'Develop'],
    ['target', 'sky', 'Conversion-Focused Storefronts', 'Make it easier for customers to discover products, understand their value and complete purchases.', ['Conversion-Focused Product Pages', 'Intuitive Navigation & Search', 'Optimized Cart & Checkout Experience', 'Customer Journey Optimization'], 'Convert'],
    ['grid', 'lilac', 'Product & Catalog Management', 'Organize and present your products through structured, scalable ecommerce experiences.', ['Product & Collection Management', 'Custom Product Page Experiences', 'Product Variants & Configurations', 'Catalog & Inventory Integration'], 'Catalog'],
    ['plug', 'lime', 'Ecommerce Integrations', 'Connect the platforms and services that keep your ecommerce business running.', ['Payment Gateway Integration', 'Shipping & Logistics Integration', 'CRM & Marketing Integrations', 'Inventory & Order Management'], 'Integrate'],
    ['workflow', 'aqua', 'Ecommerce Automation', 'Reduce repetitive work and improve the efficiency of everyday ecommerce operations.', ['Order & Fulfilment Automation', 'WhatsApp Notifications', 'Customer Communication Workflows', 'Marketing & Retention Automation'], 'Automate'],
    ['chart', 'rose', 'Analytics & Growth Optimization', 'Understand customer behaviour and identify opportunities to improve store performance.', ['Google Analytics & GTM', 'Ecommerce Conversion Tracking', 'Customer Behaviour Analysis', 'Conversion Rate Optimization'], 'Grow'],
];
// the ticket paper for each tint (the home page's approach tickets use the same four first)
$dcTk = ['peach' => '#F8E6D6', 'sky' => '#DFEAF9', 'lilac' => '#E8E2F5', 'lime' => '#EDF0D7', 'aqua' => '#DCEFF0', 'rose' => '#F5E1E9', 'mint' => '#DAF1E5'];
// the ticket illustrations, drawn with the home page's .il classes in a 200 × 140 box
$dcIl = fn (string $id, int $x, int $y, int $s = 18, string $c = 'var(--green)'): string => '<svg x="' . $x . '" y="' . $y . '" width="' . $s . '" height="' . $s . '" style="color:' . $c . '"><use href="#d-' . $id . '"/></svg>';
$dcArt = [
    // develop: a storefront in a browser, a code badge floating over it
    '<rect class="c" x="22" y="14" width="150" height="110" rx="12"/><circle class="k2" cx="36" cy="27" r="3"/><circle class="k2" cx="46" cy="27" r="3"/><circle class="k2" cx="56" cy="27" r="3"/><path class="ln" d="M22 38H172"/><rect class="gs2" x="34" y="48" width="60" height="42" rx="7"/><rect class="g typeln" x="102" y="50" width="50" height="6" rx="3" style="--d:0s"/><rect class="k2 typeln" x="102" y="62" width="38" height="5" rx="2.5" style="--d:.3s"/><rect class="k typeln" x="102" y="73" width="44" height="5" rx="2.5" style="--d:.6s"/><rect class="g" x="102" y="84" width="34" height="10" rx="5"/><rect class="k" x="34" y="100" width="28" height="14" rx="4"/><rect class="k" x="68" y="100" width="28" height="14" rx="4"/><g class="float" style="--d:.3s"><rect class="c" x="136" y="92" width="50" height="34" rx="10"/>' . $dcIl('code', 149, 97, 24) . '</g>',
    // convert: a product card, a pointer on "add to cart", sales rising beside it
    '<rect class="c" x="22" y="14" width="88" height="112" rx="12"/><rect class="gs2" x="32" y="24" width="68" height="48" rx="8"/><rect class="k2" x="32" y="80" width="48" height="6" rx="3"/><rect class="k" x="32" y="91" width="30" height="5" rx="2.5"/><rect class="g pulse" x="32" y="103" width="68" height="13" rx="6.5"/><rect class="g" x="32" y="103" width="68" height="13" rx="6.5"/><path d="M88 104v17l4.6-4.4 3.6 7.4 3-1.4-3.6-7.2h6.4z" fill="#1f2937" stroke="#fff" stroke-width="1.4" stroke-linejoin="round"/><g class="float" style="--d:.4s"><rect class="c" x="122" y="34" width="60" height="76" rx="12"/><rect class="g rise" x="133" y="80" width="9" height="20" rx="2" style="--d:0s"/><rect class="g rise" x="147" y="68" width="9" height="32" rx="2" style="--d:.15s"/><rect class="g rise" x="161" y="52" width="9" height="48" rx="2" style="--d:.3s"/>' . $dcIl('trend', 132, 42, 14) . '</g>',
    // catalog: a grid of products, one picked out, its variants underneath
    '<rect class="c" x="24" y="14" width="46" height="48" rx="9"/><rect class="k" x="32" y="22" width="30" height="22" rx="5"/><rect class="k2" x="32" y="50" width="22" height="5" rx="2.5"/><rect class="c g-outline tile" x="77" y="14" width="46" height="48" rx="9"/><rect class="gs2" x="85" y="22" width="30" height="22" rx="5"/><rect class="g" x="85" y="50" width="22" height="5" rx="2.5"/><rect class="c" x="130" y="14" width="46" height="48" rx="9"/><rect class="k" x="138" y="22" width="30" height="22" rx="5"/><rect class="k2" x="138" y="50" width="22" height="5" rx="2.5"/><rect class="c" x="24" y="70" width="46" height="48" rx="9"/><rect class="k" x="32" y="78" width="30" height="22" rx="5"/><rect class="k2" x="32" y="106" width="22" height="5" rx="2.5"/><rect class="c" x="130" y="70" width="46" height="48" rx="9"/><rect class="k" x="138" y="78" width="30" height="22" rx="5"/><rect class="k2" x="138" y="106" width="22" height="5" rx="2.5"/><g class="float" style="--d:.2s"><rect class="c" x="80" y="78" width="40" height="40" rx="10"/><circle class="g" cx="92" cy="92" r="5"/><circle class="k2" cx="108" cy="92" r="5"/><circle class="gs2" cx="92" cy="106" r="5"/><circle class="k" cx="108" cy="106" r="5"/></g>',
    // integrate: the store in the middle, payments, shipping, CRM and ads wired in
    '<circle cx="100" cy="70" r="36" class="ring spin" fill="none"/><path class="gl flow" d="M64 34L84 56M136 34L116 56M64 106L84 84M136 106L116 84" stroke-dasharray="4 5"/><rect class="c" x="20" y="16" width="46" height="30" rx="9"/>' . $dcIl('cart', 34, 22) . '<rect class="c" x="134" y="16" width="46" height="30" rx="9"/>' . $dcIl('truck', 148, 22) . '<rect class="c" x="20" y="94" width="46" height="30" rx="9"/>' . $dcIl('users', 34, 100) . '<rect class="c" x="134" y="94" width="46" height="30" rx="9"/>' . $dcIl('chart', 148, 100) . '<circle class="g" cx="100" cy="70" r="22"/>' . $dcIl('store', 88, 58, 24, '#fff'),
    // automate: an order comes in, a rule fires, the customer gets a message
    '<rect class="c" x="16" y="46" width="48" height="48" rx="11"/>' . $dcIl('box', 28, 55, 24) . '<rect class="k2" x="26" y="84" width="28" height="4" rx="2"/><path class="gl flow" d="M66 70H80M120 70H132" stroke-dasharray="4 5"/><circle class="g pulse" cx="100" cy="70" r="20"/><circle class="g" cx="100" cy="70" r="20"/>' . $dcIl('bolt', 89, 59, 22, '#fff') . '<g class="float" style="--d:.3s"><path class="c" d="M144 40h34a10 10 0 0 1 10 10v20a10 10 0 0 1-10 10h-22l-10 9v-9h-2a10 10 0 0 1-10-10V50a10 10 0 0 1 10-10z"/><rect class="g" x="144" y="50" width="30" height="5" rx="2.5"/><rect class="k2" x="144" y="61" width="20" height="5" rx="2.5"/></g><rect class="gs2" x="138" y="96" width="46" height="14" rx="7"/><circle class="g blink" cx="148" cy="103" r="3"/><rect class="g" x="155" y="101" width="22" height="4" rx="2"/>',
    // grow: the conversion line climbing, a CRO tag on it
    '<rect class="c" x="20" y="14" width="160" height="112" rx="12"/><path class="ln faint" d="M34 48H166M34 72H166M34 96H166"/><path class="gl draw" pathLength="1" d="M34 108L60 94 84 100 110 74 136 80 164 44"/><circle class="g pulse" cx="164" cy="44" r="5"/><circle class="g" cx="164" cy="44" r="5"/><g class="float" style="--d:.2s"><rect class="c" x="32" y="22" width="58" height="20" rx="10"/><circle class="g blink" cx="44" cy="32" r="3.5"/><text class="chip" x="68" y="35" text-anchor="middle">CRO</text></g><g class="float" style="--d:.6s"><rect class="c" x="126" y="92" width="46" height="24" rx="8"/><rect class="g rise" x="134" y="104" width="5" height="6" rx="1.5" style="--d:0s"/><rect class="g rise" x="143" y="100" width="5" height="10" rx="1.5" style="--d:.15s"/><rect class="g rise" x="152" y="96" width="5" height="14" rx="1.5" style="--d:.3s"/></g>',
];

// 04 · ecosystem lanes [icon, tint, title, items]
$dcEco = [
    ['store', 'sky', 'Customer Experience', ['Online Storefront', 'Product Discovery', 'Product Pages', 'Shopping Cart', 'Checkout Experience']],
    ['box', 'peach', 'Commerce Operations', ['Product & Catalog Management', 'Order Management', 'Inventory Management', 'Payment Processing', 'Shipping & Fulfilment']],
    ['users', 'lilac', 'Customer Relationships', ['Customer Profiles', 'CRM Integration', 'WhatsApp Communication', 'Email Automation', 'Customer Retention']],
    ['chart', 'mint', 'Marketing & Intelligence', ['SEO & Search Visibility', 'Paid Advertising Integration', 'Conversion Tracking', 'Ecommerce Analytics', 'Performance Reporting']],
];
// the order's stops along the rail, one per lane
$dcOrder = ['Order placed', 'Paid & packed', 'Customer updated', 'Sale tracked'];
$dcTech = [['shopify', 'Shopify'], ['razorpay', 'Razorpay'], ['stripe', 'Stripe'], ['shiprocket', 'Shiprocket'], ['google-analytics', 'Google Analytics'], ['tag-manager', 'Google Tag Manager'], ['meta-ads', 'Meta Ads'], ['google-ads', 'Google Ads'], ['hotjar', 'Hotjar'], ['search-console', 'Search Console']];

// 05 · approach [label, text, tags]
$dcSteps = [
    ['Discover Your Brand', 'Understand your brand identity, products, customers, business model and ecommerce goals.', 'Brand Discovery · Product Structure · Customer Research · Business Requirements'],
    ['Design the Experience', 'Plan the storefront, product discovery, navigation and customer journey around your brand and target customers.', 'UI/UX Strategy · Wireframes · Customer Journey · Conversion Planning'],
    ['Engineer & Integrate', 'Develop your custom ecommerce experience and connect the essential platforms, tools and business workflows.', 'Custom Development · Shopify Engineering · Integrations · Automation'],
    ['Launch & Optimize', 'Test the complete shopping experience, launch your store and continuously improve it using customer behaviour and performance insights.', 'Quality Testing · Store Launch · Analytics · CRO & Optimization'],
];
$dcStepIc = ['search', 'pen', 'code', 'rocket'];
$dcStepTint = ['peach', 'sky', 'lilac', 'mint'];

// 06 · categories [icon, tint, name, question, text, cta, preview]
$dcInd = [
    ['shirt', 'sky', 'Fashion & Apparel', 'Are customers struggling to find the right products, sizes and styles?', 'Build intuitive product discovery and shopping experiences.', 'Build Your Fashion Store', 'fashion'],
    ['gem', 'peach', 'Jewellery & Luxury', 'Does your online store communicate the value and detail of your products?', 'Create premium digital storefronts that reflect your brand identity.', 'Build Your Jewellery Store', 'jewel'],
    ['drop', 'rose', 'Beauty & Wellness', 'Are product discovery and customer trust affecting online purchases?', 'Create engaging product experiences with clear information and intuitive navigation.', 'Build Your Beauty Store', 'beauty'],
    ['cup', 'lime', 'Food & Beverages', 'Are ordering and product selection creating friction for your customers?', 'Build convenient ecommerce experiences for product discovery and ordering.', 'Build Your Food Commerce Store', 'food'],
    ['bag', 'lilac', 'D2C & Lifestyle', 'Are you struggling to create a distinctive online experience for your brand?', 'Build a direct-to-consumer storefront designed around your brand and customers.', 'Build Your D2C Store', 'd2c'],
    ['chip', 'aqua', 'Electronics & Consumer Products', 'Are complex product choices making online shopping difficult?', 'Create structured product pages, comparisons and intuitive buying journeys.', 'Build Your Product Store', 'tech'],
];

// 07 · projects [tint, tab, label, title, text, capabilities, from, to, href, mock]
$dcProjects = [
    ['peach', 'Jewellery', 'Jewellery & Luxury Commerce', 'A Premium Digital Storefront for a Jewellery Brand.', 'An ecommerce experience designed to present jewellery collections through a brand-focused storefront, structured product discovery and a customer-friendly shopping journey.', ['Shopify Development', 'Custom Storefront Design', 'Product & Collection Structure', 'Ecommerce Experience Development'], 'A standard online product catalogue.', 'A distinctive branded jewellery shopping experience.', $dcWork, 'jewel'],
    ['sky', 'D2C brand', 'D2C Ecommerce', 'Reimagining the Online Shopping Experience for a D2C Brand.', 'A Shopify revamp focused on improving the storefront experience, organizing product discovery and creating a more consistent digital presence for the brand.', ['Shopify Store Revamp', 'Custom UI/UX', 'Product Page Experience', 'Storefront Optimization'], 'An existing ecommerce experience with room for improvement.', 'A refreshed, customer-focused online store.', $dcWork, 'd2c'],
    ['lilac', 'Custom build', 'Custom Ecommerce Experience', 'Beyond Standard Templates. Custom Experiences for Unique Products.', 'A tailored ecommerce experience for a brand with specific product presentation and shopping requirements, designed around its catalogue and customer journey.', ['Custom Ecommerce Development', 'Advanced Product Page Experiences', 'Custom UI Components', 'Responsive Storefront Engineering'], 'Standard ecommerce layouts and limitations.', 'A custom shopping experience aligned with the brand\'s requirements.', $dcWork, 'custom'],
];

// a small ring, used on the jewellery screens
$dcRing = fn (string $fill = '#F8E6D6'): string => '<svg viewBox="0 0 120 120" aria-hidden="true" focusable="false"><rect width="120" height="120" rx="18" fill="' . $fill . '"/><ellipse cx="60" cy="98" rx="26" ry="4" fill="rgba(15,58,36,.08)"/><circle cx="60" cy="70" r="24" fill="none" stroke="#b8803e" stroke-width="6"/><path d="M50 40l10-12 10 12-10 8z" fill="#fff" stroke="#b8803e" stroke-width="2.5" stroke-linejoin="round"/></svg>';
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/dcom.css') ?>">
<script>document.documentElement.classList.add('js')</script>

<div class="h10 dc">
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>
<symbol id="d-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
<symbol id="d-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></symbol>
<symbol id="d-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>
<symbol id="d-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7l10 10M17 7L7 17"/></symbol>
<symbol id="d-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></symbol>
<symbol id="d-store" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9l1.5-5h13L20 9M4 9h16v2a2.7 2.7 0 01-5.3 0 2.7 2.7 0 01-5.4 0A2.7 2.7 0 014 11zM5 13v7h14v-7M10 20v-4h4v4"/></symbol>
<symbol id="d-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/></symbol>
<symbol id="d-bolt" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M13 3L5 13.5h6L10 21l8-10.5h-6z"/></symbol>
<symbol id="d-trend" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol>
<symbol id="d-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/></symbol>
<symbol id="d-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></symbol>
<symbol id="d-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol>
<symbol id="d-workflow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/></symbol>
<symbol id="d-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol>
<symbol id="d-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M4 7.5l8 4.5 8-4.5M12 12v9M8 5.3l8 4.5"/></symbol>
<symbol id="d-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol>
<symbol id="d-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></symbol>
<symbol id="d-pen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4.2-1 10.6-10.6a2.1 2.1 0 00-3-3L5.2 16z"/><path d="M14 7l3 3"/></symbol>
<symbol id="d-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol>
<symbol id="d-shirt" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3.5L4 6l-1.5 5 3 1V20.5h13V12l3-1L20 6l-5-2.5a3 3 0 01-6 0z"/></symbol>
<symbol id="d-gem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12l4 5-10 11L2 9z"/><path d="M2 9h20M9 4L7 9l5 11M15 4l2 5-5 11"/></symbol>
<symbol id="d-drop" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="9" width="8" height="12" rx="2.5"/><path d="M10 9V6h4v3M11 3h2v3h-2zM10.5 14h3"/></symbol>
<symbol id="d-cup" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h12v5a6 6 0 01-12 0zM17 9.5h1.5a2.5 2.5 0 010 5H17M8 2.5c-.6 1 .6 2 0 3M12 2.5c-.6 1 .6 2 0 3M4 21h14"/></symbol>
<symbol id="d-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></symbol>
<symbol id="d-chip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4"/></symbol>
<symbol id="d-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2.5l2.2 11h10.6L20.5 7H6.4"/><circle cx="9.5" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/></symbol>
<symbol id="d-star" viewBox="0 0 24 24"><path fill="currentColor" d="M12 3.5l2.6 5.4 5.9.8-4.3 4.1 1 5.8L12 16.8l-5.2 2.8 1-5.8-4.3-4.1 5.9-.8z"/></symbol>
<symbol id="d-truck" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v10H3zM14 9.5h4l3 3.5v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17.5" cy="17.5" r="1.8"/></symbol>
<symbol id="d-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7.5-4.4-7.5-10A4.3 4.3 0 0112 7.3 4.3 4.3 0 0119.5 10c0 5.6-7.5 10-7.5 10z"/></symbol>
</defs></svg>
<a class="skip" href="#dc-top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>

<!-- 01 · Hero: the statement beside a live product page, the differentiators as a ticker -->
<section class="bsh dc-hero" id="dc-top">
  <div class="wrap dc-hero__grid">
    <div class="dc-hero__copy">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Digital Commerce · Ecommerce Development · Conversion Engineering</span>
      <h1 class="dc-hero__h1">Your Brand Deserves More Than <span class="dc-hero__em">Just an Online Store.</span></h1>
      <p class="dc-hero__lede reveal" style="--d:.15s">We design and engineer high-performing ecommerce experiences that bring your brand to life, simplify the customer journey and turn more visitors into customers. From custom Shopify storefronts to tailored ecommerce functionality, every experience is built around your business and your customers.</p>
      <div class="dc-hero__ctas reveal" style="--d:.24s">
        <a class="h10-btn h10-btn--dark" href="/book" data-book><span>Build Your Ecommerce Store</span><?= $dcArrow ?></a>
        <a class="h10-btn dc-btn-line" href="#projects"><span>Explore Our Work</span><?= $dcArrow ?></a>
      </div>
    </div>
    <figure class="dc-pdp reveal" style="--d:.1s" aria-hidden="true">
      <div class="dc-pdp__win">
        <div class="dc-pdp__bar"><span class="dc-dots"><i></i><i></i><i></i></span><span class="dc-pdp__url">yourbrand.com/products/glow-serum</span><span class="dc-pdp__cart"><?= $dcIc('cart') ?><b>2</b></span></div>
        <div class="dc-pdp__body">
          <div class="dc-pdp__img">
            <svg viewBox="0 0 200 240" focusable="false"><ellipse cx="100" cy="222" rx="58" ry="7" fill="rgba(15,58,36,.09)"/><rect x="58" y="82" width="84" height="138" rx="24" fill="#fff"/><rect x="68" y="122" width="64" height="58" rx="12" fill="#E8E2F5"/><rect x="80" y="136" width="40" height="5" rx="2.5" fill="#6a4fb0"/><rect x="86" y="148" width="28" height="4" rx="2" fill="#b9a8e3"/><rect x="82" y="56" width="36" height="28" rx="7" fill="#1d7a49"/><rect x="88" y="36" width="24" height="22" rx="5" fill="#32B46F"/></svg>
            <span class="dc-pdp__badge">New</span>
            <span class="dc-pdp__thumbs"><i class="on"></i><i></i><i></i></span>
          </div>
          <div class="dc-pdp__info">
            <small>Skincare · Serums</small>
            <b class="dc-pdp__name">Glow Serum</b>
            <span class="dc-pdp__stars"><?= str_repeat($dcIc('star'), 5) ?><em>4.9 · 1,284 reviews</em></span>
            <span class="dc-pdp__price">₹1,249 <s>₹1,599</s></span>
            <span class="dc-pdp__lbl">Size</span>
            <span class="dc-pdp__opts"><i>15 ml</i><i class="on">30 ml</i><i>50 ml</i></span>
            <span class="dc-pdp__add"><?= $dcIc('cart') ?>Add to cart</span>
            <span class="dc-pdp__ship"><?= $dcIc('truck') ?>Free delivery by Thursday</span>
          </div>
        </div>
      </div>
      <span class="dc-float dc-float--a"><i><?= $dcIc('check') ?></i><span><b>Order #1042 placed</b><small>Paid with UPI · just now</small></span></span>
      <span class="dc-float dc-float--b"><i><?= $dcIc('bolt') ?></i><span><b>Checkout in 3 steps</b><small>No app needed</small></span></span>
    </figure>
  </div>
  <div class="dc-tick" role="list" aria-label="What makes our stores different">
    <div class="dc-tick__track">
<?php for ($r = 0; $r < 2; $r++): foreach ($dcDiff as [$icon, $label]): ?>
      <span class="dc-tick__it"<?= $r ? ' aria-hidden="true"' : ' role="listitem"' ?>><?= $dcIc($icon) ?><?= h($label) ?></span>
<?php endforeach; endfor; ?>
    </div>
  </div>
</section>

<!-- 02 · The challenge: a shrinking funnel beside a store-audit receipt -->
<section class="dc-chal" id="challenges">
  <div class="wrap dc-chal__grid">
    <div class="dc-chal__copy">
      <span class="pill dc-pill t-rose reveal"><span class="pill__dot" aria-hidden="true"></span>Your Online Store Should Work as Hard as Your Brand</span>
      <h2 class="h2">Getting Visitors to Your Store Is Only Half the Battle.</h2>
      <p class="lede reveal" style="--d:.1s">A good-looking website doesn't automatically generate sales. Poor navigation, confusing product pages, complicated checkout experiences and disconnected tools can make customers leave without purchasing.</p>
      <p class="lede lede--strong reveal" style="--d:.14s">We help ecommerce businesses identify these gaps and engineer better shopping experiences.</p>
      <div class="dc-funnel reveal" style="--d:.18s" role="img" aria-label="Illustration: of every hundred visitors, fewer view products, fewer add to cart, fewer reach checkout and only a handful order.">
<?php foreach ($dcFunnel as $i => [$stage, $w]): ?>
        <div class="dc-funnel__row" style="--w:<?= $w ?>%;--i:<?= $i ?>" aria-hidden="true"><span><?= $stage ?></span><i></i></div>
<?php endforeach; ?>
        <p class="dc-funnel__cap" aria-hidden="true"><span>Illustrative</span>Where shoppers quietly leave</p>
      </div>
      <a class="h10-btn h10-btn--dark reveal" href="/book" data-book><span>Let's Fix Your Ecommerce Experience</span><?= $dcArrow ?></a>
    </div>
    <div class="dc-rcpt-w reveal" style="--d:.08s">
      <div class="dc-rcpt">
        <div class="dc-rcpt__hd"><b>Store Audit</b><span>Common Ecommerce Challenges</span></div>
        <ol class="dc-rcpt__list" role="list">
<?php foreach ($dcProblems as $i => [$title, $text]): ?>
          <li><span class="dc-rcpt__n"><?= $dcN($i) ?></span><div><h3><?= h($title) ?></h3><p><?= h($text) ?></p></div><i class="dc-rcpt__x"><?= $dcIc('x') ?></i></li>
<?php endforeach; ?>
        </ol>
        <div class="dc-rcpt__total" aria-hidden="true"><span>Lost sales</span><b>Too many</b></div>
        <div class="dc-rcpt__code" aria-hidden="true"></div>
      </div>
    </div>
  </div>
</section>

<!-- 03 · What we engineer: six paper tickets, the same ones as the home page's approach -->
<section class="dc-tix" id="capabilities">
  <div class="wrap">
    <div class="dc-head">
      <div>
        <span class="pill dc-pill t-sky reveal"><span class="pill__dot" aria-hidden="true"></span>End-to-End Ecommerce Engineering</span>
        <h2 class="h2">Everything Your Brand Needs to Sell Online. Built Around Your Business.</h2>
      </div>
      <p class="lede dc-head__txt reveal" style="--d:.1s">From the first customer interaction to completed orders, we engineer ecommerce experiences that connect your storefront, products, customers and business operations.</p>
    </div>
    <ol class="dc-tks" role="list">
<?php foreach ($dcBuild as $i => [$icon, $tint, $title, $text, $items, $cls]): ?>
      <li class="step reveal" style="--tk:<?= $dcTk[$tint] ?>;--d:<?= ($i % 3) * .1 ?>s">
        <div class="step__card tk">
          <div class="tk-a">
            <div class="tk-top"><span class="tk-cls"><?= h($cls) ?></span><span class="tk-adm">No. <?= $dcN($i) ?> · Your Brand</span></div>
            <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false"><rect class="pn" width="200" height="140"/><?= $dcArt[$i] ?></svg></div>
          </div>
          <div class="tk-b">
            <h3 class="tk-h"><?= h($title) ?></h3>
            <p><?= h($text) ?></p>
            <ul class="dc-tk__list" role="list"><?php foreach ($items as $t): ?><li><?= $dcIc('check') ?><?= h($t) ?></li><?php endforeach; ?></ul>
          </div>
        </div>
      </li>
<?php endforeach; ?>
    </ol>
    <div class="dc-center reveal"><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Build Your Custom Ecommerce Experience</span><?= $dcArrow ?></a></div>
  </div>
</section>

<!-- 04 · Connected ecosystem: four lanes on one rail, an order travelling through -->
<section class="dc-eco" id="ecosystem">
  <div class="dc-eco__stage">
    <div class="wrap">
      <div class="dc-head dc-head--center">
        <span class="pill dc-pill t-lilac reveal"><span class="pill__dot" aria-hidden="true"></span>More Than a Storefront. A Connected Commerce Ecosystem.</span>
        <h2 class="h2">Every Part of Your Ecommerce Business. Working Together.</h2>
        <p class="lede reveal" style="--d:.1s">Your ecommerce business involves much more than a website. Products, customers, orders, payments, inventory, shipping and marketing all need to work together.</p>
        <p class="lede lede--strong reveal" style="--d:.14s">We connect the essential parts of your ecommerce operations to create a smoother experience for both your customers and your team.</p>
      </div>
      <div class="dc-lanes reveal">
        <div class="dc-lanes__rail" aria-hidden="true"><i class="dc-lanes__order"><?= $dcIc('box') ?></i></div>
<?php foreach ($dcEco as $i => [$icon, $tint, $title, $items]): ?>
        <div class="dc-lane t-<?= $tint ?>" style="--i:<?= $i ?>">
          <span class="dc-lane__stop" aria-hidden="true"><i></i><?= h($dcOrder[$i]) ?></span>
          <div class="dc-lane__hd"><i><?= $dcIc($icon) ?></i><h3><?= h($title) ?></h3></div>
          <ul role="list"><?php foreach ($items as $t): ?><li><?= h($t) ?></li><?php endforeach; ?></ul>
        </div>
<?php endforeach; ?>
      </div>
      <div class="dc-logos reveal">
        <span class="dc-kick">Platforms we connect</span>
        <ul role="list"><?php foreach ($dcTech as [$file, $name]): ?><li><img src="/assets/home10/tech/<?= $file ?>.webp" alt="<?= h($name) ?>" width="28" height="28" loading="lazy" decoding="async"><span><?= h($name) ?></span></li><?php endforeach; ?></ul>
      </div>
      <div class="dc-center reveal"><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Connect Your Ecommerce Business</span><?= $dcArrow ?></a></div>
    </div>
  </div>
</section>

<!-- 05 · Engineering approach: a checkout stepper, the line fills as it comes in -->
<section class="dc-appr" id="approach">
  <div class="wrap">
    <div class="dc-head">
      <div>
        <span class="pill dc-pill t-peach reveal"><span class="pill__dot" aria-hidden="true"></span>Built Around Your Brand. Engineered for Your Customers.</span>
        <h2 class="h2">We Don't Start With a Theme. We Start With Your Business.</h2>
      </div>
      <p class="lede dc-head__txt reveal" style="--d:.1s">Every brand has its own identity, products, customers and selling process. We work closely with your team to understand these requirements before designing and engineering your ecommerce experience.</p>
    </div>
    <ol class="dc-steps reveal" role="list">
<?php foreach ($dcSteps as $i => [$label, $text, $tags]): ?>
      <li class="dc-step t-<?= $dcStepTint[$i] ?>" style="--i:<?= $i ?>">
        <span class="dc-step__dot" aria-hidden="true"><b><?= $i + 1 ?></b><?= $dcIc('check') ?></span>
        <div class="dc-step__card">
          <span class="dc-step__k"><i><?= $dcIc($dcStepIc[$i]) ?></i>Step <?= $dcN($i) ?></span>
          <h3><?= h($label) ?></h3>
          <p><?= h($text) ?></p>
          <ul class="dc-step__tags" role="list"><?php foreach (explode(' · ', $tags) as $tag): ?><li><?= h($tag) ?></li><?php endforeach; ?></ul>
        </div>
      </li>
<?php endforeach; ?>
    </ol>
    <div class="dc-center reveal"><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Let's Engineer Your Ecommerce Store</span><?= $dcArrow ?></a></div>
  </div>
</section>

<!-- 06 · Industries: a category menu with a preview of the shop each one needs -->
<section class="dc-cat" id="industries">
  <div class="wrap">
    <div class="dc-head">
      <div>
        <span class="pill dc-pill t-aqua reveal"><span class="pill__dot" aria-hidden="true"></span>Different Brands. Different Customers. Different Shopping Experiences.</span>
        <h2 class="h2">Your Ecommerce Experience Should Reflect What You Sell.</h2>
      </div>
      <div class="dc-head__txt reveal" style="--d:.1s">
        <p class="lede">A jewellery brand doesn't sell like a fashion brand. A food business doesn't work like an electronics store. Each category needs its own product experience, customer journey and buying process.</p>
        <p class="lede lede--strong">We engineer ecommerce experiences around the products you sell and the customers you serve.</p>
      </div>
    </div>
    <div class="dc-cat__box reveal" data-dc-cat>
      <div class="dc-cat__menu" role="tablist" aria-label="Commerce categories" aria-orientation="vertical">
<?php foreach ($dcInd as $i => [$icon, $tint, $name]): ?>
        <button type="button" role="tab" class="dc-cat__tab t-<?= $tint ?>" id="dct-<?= $i ?>" aria-controls="dcp-<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i ? ' tabindex="-1"' : '' ?>><em><?= $dcN($i) ?></em><span><?= h($name) ?></span><i><?= $dcIc('arrow') ?></i></button>
<?php endforeach; ?>
      </div>
<?php foreach ($dcInd as $i => [$icon, $tint, $name, $q, $text, $cta, $ui]): ?>
      <article class="dc-cat__panel t-<?= $tint ?><?= $i === 0 ? ' is-on' : '' ?>" role="tabpanel" id="dcp-<?= $i ?>" aria-labelledby="dct-<?= $i ?>">
        <div class="dc-ui dc-ui--<?= $ui ?>" aria-hidden="true">
          <span class="dc-ui__ic"><?= $dcIc($icon) ?></span>
<?php if ($ui === 'fashion'): ?>
          <div class="dc-ui__card"><small>Find your fit</small><span class="dc-ui__chips"><i>XS</i><i>S</i><i class="on">M</i><i>L</i><i>XL</i></span><span class="dc-ui__sw"><i style="background:#1f2937"></i><i style="background:#c8b8a6"></i><i style="background:#6d8fbf"></i><i style="background:#fff"></i></span><b>Linen Shirt · Relaxed fit</b></div>
<?php elseif ($ui === 'jewel'): ?>
          <div class="dc-ui__card dc-ui__card--row"><span class="dc-ui__ring"><?= $dcRing('#fff') ?></span><span class="dc-ui__spec"><b>Solitaire Ring</b><i><small>Metal</small>18K Gold</i><i><small>Weight</small>3.8 g</i><i><small>Certified</small>Hallmarked</i></span></div>
<?php elseif ($ui === 'beauty'): ?>
          <div class="dc-ui__card"><span class="dc-ui__stars"><?= str_repeat($dcIc('star'), 5) ?><em>4.8 · verified reviews</em></span><small>Made for your skin</small><span class="dc-ui__chips"><i class="on">Oily</i><i>Dry</i><i>Sensitive</i><i>Combination</i></span><b>Ingredients · How to use · Results</b></div>
<?php elseif ($ui === 'food'): ?>
          <div class="dc-ui__card"><b>Cold Brew Coffee · Pack of 6</b><span class="dc-ui__qty"><i>−</i><b>2</b><i>+</i></span><small>Delivery slot</small><span class="dc-ui__chips"><i>Today 6–8 PM</i><i class="on">Tomorrow 9–11 AM</i></span></div>
<?php elseif ($ui === 'd2c'): ?>
          <div class="dc-ui__card"><span class="dc-ui__sub"><i class="on"></i><span><b>Subscribe &amp; save 10%</b><small>Delivered every 30 days</small></span></span><small>₹250 away from free shipping</small><span class="dc-ui__meter"><i></i></span><b>Your brand story, on every page</b></div>
<?php else: ?>
          <div class="dc-ui__card"><small>Compare models</small><table class="dc-ui__cmp"><tr><th></th><th>Model A</th><th class="on">Model B</th></tr><tr><td>Battery</td><td>12 h</td><td class="on">18 h</td></tr><tr><td>Weight</td><td>1.4 kg</td><td class="on">1.2 kg</td></tr><tr><td>Warranty</td><td>1 yr</td><td class="on">2 yr</td></tr></table></div>
<?php endif; ?>
        </div>
        <div class="dc-cat__txt">
          <span class="dc-kick"><?= $dcN($i) ?> · <?= h($name) ?></span>
          <h3><?= h($q) ?></h3>
          <p><?= h($text) ?></p>
          <a class="dc-cat__cta" href="/book" data-book><span><?= h($cta) ?></span><i><?= $dcIc('arrow') ?></i></a>
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="dc-fine reveal">Shop screens use sample data.</p>
  </div>
</section>

<!-- 07 · Selected projects: one browser window, the projects as its tabs -->
<section class="dc-work" id="projects">
  <div class="wrap">
    <div class="dc-head dc-head--center">
      <span class="pill dc-pill t-mint reveal"><span class="pill__dot" aria-hidden="true"></span>Selected Work. Custom-Engineered Experiences.</span>
      <h2 class="h2">We Understood the Brand. We Engineered the Experience.</h2>
      <p class="lede reveal" style="--d:.1s">Every ecommerce project starts with a different challenge. Here are examples of the types of digital commerce experiences we have worked on.</p>
    </div>
    <div class="dc-br reveal" data-dc-br>
      <div class="dc-br__bar">
        <span class="dc-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <div class="dc-br__tabs" role="tablist" aria-label="Selected projects">
<?php foreach ($dcProjects as $i => [$tint, $tab]): ?>
          <button type="button" role="tab" class="dc-br__tab t-<?= $tint ?>" id="dcw-t<?= $i ?>" aria-controls="dcw-p<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i ? ' tabindex="-1"' : '' ?>><i></i>Project <?= $dcN($i) ?> · <?= h($tab) ?></button>
<?php endforeach; ?>
        </div>
      </div>
<?php foreach ($dcProjects as $i => [$tint, $tab, $label, $title, $text, $caps, $from, $to, $href, $mock]): ?>
      <article class="dc-br__panel t-<?= $tint ?><?= $i === 0 ? ' is-on' : '' ?>" role="tabpanel" id="dcw-p<?= $i ?>" aria-labelledby="dcw-t<?= $i ?>">
        <div class="dc-site dc-site--<?= $mock ?>" aria-hidden="true">
          <div class="dc-site__nav"><b>Your Brand</b><span><i>Shop</i><i>Collections</i><i>Story</i></span><?= $dcIc('bag') ?></div>
<?php if ($mock === 'jewel'): ?>
          <div class="dc-site__hero"><small>The Bridal Edit</small><b>Crafted to be worn forever.</b><span>Explore collection</span></div>
          <div class="dc-site__grid"><?php foreach ([['Solitaire Ring', '₹48,500', '#F8E6D6'], ['Gold Hoops', '₹22,900', '#F5E1E9'], ['Pearl Band', '₹31,200', '#EDF0D7']] as [$pn, $pp, $pc]): ?><span><?= $dcRing($pc) ?><b><?= $pn ?></b><small><?= $pp ?></small></span><?php endforeach; ?></div>
<?php elseif ($mock === 'd2c'): ?>
          <div class="dc-site__photo"><img src="/assets/home10/work/ecommerce.webp" alt="" width="1000" height="1000" loading="lazy" decoding="async"><span><small>New season</small><b>Everyday essentials, made better.</b><i>Shop now</i></span></div>
          <div class="dc-site__cats"><i>Tees</i><i>Shirts</i><i>Outerwear</i><i>Bestsellers</i></div>
<?php else: ?>
          <div class="dc-site__conf">
            <span class="dc-site__prev"><svg viewBox="0 0 160 160" focusable="false"><rect width="160" height="160" rx="18" fill="#E8E2F5"/><rect x="34" y="52" width="92" height="60" rx="10" fill="#8a6a46"/><rect x="34" y="52" width="92" height="14" rx="7" fill="#a5835d"/><rect x="44" y="112" width="8" height="26" rx="3" fill="#6f5338"/><rect x="108" y="112" width="8" height="26" rx="3" fill="#6f5338"/><text x="80" y="92" text-anchor="middle" font-family="Montserrat,sans-serif" font-size="12" font-weight="700" fill="#fff">A &amp; R</text></svg></span>
            <span class="dc-site__opts"><small>Wood</small><span><i>Oak</i><i class="on">Walnut</i><i>Teak</i></span><small>Size</small><span><i>S</i><i class="on">M</i><i>L</i></span><small>Engraving</small><span><i class="on">A &amp; R</i></span><b>₹12,400 <em>updates as you choose</em></b></span>
          </div>
<?php endif; ?>
        </div>
        <div class="dc-br__copy">
          <span class="dc-kick">Project <?= $dcN($i) ?> — <?= h($label) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
          <div class="dc-ft">
            <div><small>From</small><span><?= h($from) ?></span></div>
            <i aria-hidden="true"><?= $dcIc('arrow') ?></i>
            <div><small>To</small><span><?= h($to) ?></span></div>
          </div>
          <span class="dc-br__cl">Capabilities</span>
          <ul class="dc-br__chips" role="list"><?php foreach ($caps as $c): ?><li><?= h($c) ?></li><?php endforeach; ?></ul>
          <a class="h10-btn h10-btn--dark" href="<?= h($href) ?>"><span>Explore Project</span><?= $dcArrow ?></a>
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="dc-nda reveal"><span>Confidential</span>Selected projects are presented without client-identifying details where confidentiality applies.</p>
  </div>
</section>

<!-- 08 · Final CTA: the same light full-width close as the home page -->
<section class="final final--card" id="contact">
  <div class="wrap">
    <div class="final__card">
      <div class="final__in">
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Let's Build Something Your Customers Will Love</span>
        <h2 class="h1x reveal" style="--d:.06s">Don't Just Launch Another Online Store. <span class="grn">Build a Better Way to Sell.</span></h2>
        <p class="lede lede--light reveal" style="--d:.1s">Whether you're launching a new ecommerce brand, upgrading an existing Shopify store or building a custom shopping experience, let's understand your business and engineer the right solution.</p>
        <div class="final__ctas reveal" style="--d:.16s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Build My Ecommerce Store</span><?= $dcArrow ?></a><a class="h10-btn h10-btn--ghost" href="/book" data-book><span>Book a Strategy Call</span><?= $dcArrow ?></a></div>
        <p class="final__line reveal" style="--d:.2s">From brand experience and custom development to integrations and conversion optimization — let's build your next stage of digital commerce.</p>
      </div>
    </div>
  </div>
</section>
</main><footer class="foot">
  <?php $signClass = 'wrap'; include __DIR__ . '/partials/footer-signoff.php'; ?>
  <div class="wrap foot__in">
    <a href="#dc-top" aria-label="Drawlead – back to top"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <?php if ($footLinks = footer_menu_links($pdo, 'links')): ?><nav aria-label="Footer"><?= $footLinks ?></nav><?php endif; ?>
  </div>
  <div class="wrap foot__base">
    <span>official@drawlead.co.in &nbsp;·&nbsp; www.drawlead.co.in &nbsp;·&nbsp; Chennai, Tamil Nadu, IN</span>
    <span>© <?= date('Y') ?> Drawlead. All rights reserved.</span>
    <?php if ($footLegal = footer_menu_links($pdo, 'legal')): ?><nav class="foot__legal" aria-label="Legal"><?= $footLegal ?></nav><?php endif; ?>
  </div>
</footer>
</div>

<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
<script src="<?= asset_url('/assets/dcom.js') ?>" defer></script>
