<?php
/**
 * Business Systems landing page (/business-systems, template "business-systems").
 *
 * Built in the home page's design language: it loads /assets/home10.css and
 * home10.js and wraps everything in .h10, so buttons, eyebrows, word reveals,
 * the palette and the footer are the same components. What is particular to
 * this page is /assets/bizsys.css (scoped under .bs) and /assets/bizsys.js
 * (the hero system board and the approach timeline).
 *
 * Copy is editable in Admin → Pages → Business Systems → Page Content.
 * Every animation stands down under prefers-reduced-motion and the page
 * reads in full with JavaScript off.
 */
$activePage = 'business-systems';
include __DIR__ . '/partials/nav.php';

$bsIc = fn (string $id, string $class = 'ic'): string => '<svg class="' . $class . '" aria-hidden="true" focusable="false"><use href="#i-' . $id . '"/></svg>';
$bsArrow = $bsIc('arrow', 'ic h10-btn__arr');
$bsCase = function (string $slug, string $fallback) use ($pdo): string {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM case_studies WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() ? '/case-studies/' . $slug : $fallback;
    } catch (PDOException $e) {
        return $fallback;
    }
};

// Engineering principles: [icon, tint, label]
$bsPrinciples = [
    ['puzzle', 'mint', 'No Generic Software'],
    ['target', 'sky', 'Business-Specific Engineering'],
    ['workflow', 'lilac', 'Connected Workflows'],
    ['shield', 'peach', 'Security-First Development'],
];

// The business problem: [icon, title, text]
$bsProblems = [
    ['plug', 'Disconnected Software', 'Different departments use different applications that don\'t communicate with each other.'],
    ['db', 'Scattered Business Data', 'Customer records, project information and important business data remain spread across multiple platforms.'],
    ['gear', 'Manual Workflows', 'Employees spend valuable time on repetitive tasks, manual updates and follow-ups.'],
    ['search', 'Limited Business Visibility', 'Management struggles to understand what\'s happening across departments and operations.'],
    ['puzzle', 'Software That Doesn\'t Fit', 'Existing software cannot accommodate your specific workflows, hierarchy and business requirements.'],
    ['trend', 'Difficult to Scale', 'As your teams and operations expand, managing existing systems becomes increasingly complicated.'],
];

// What we engineer: [icon, tint, title, text, size]
$bsBuild = [
    ['layers', 'mint', 'Custom ERP & Business Operating Systems', 'Connect your departments, people, processes and business activities through one unified operating system.', 'lg'],
    ['users', 'sky', 'CRM & Customer Management', 'Manage leads, customer relationships, sales pipelines, follow-ups and customer interactions.', ''],
    ['briefcase', 'lilac', 'Project & Operations Management', 'Manage projects, tasks, resources, timelines, responsibilities and operational activities.', ''],
    ['calendar', 'peach', 'HR & Workforce Management', 'Organize employees, attendance, responsibilities, payroll workflows and team activities.', ''],
    ['wallet', 'lime', 'Finance & Business Management', 'Connect expenses, invoices, payments, budgets, revenue and project-wise financial tracking.', ''],
    ['chip', 'rose', 'AI, Automation & System Integration', 'Integrate applications, automate repetitive processes and implement AI capabilities across business workflows.', 'lg'],
];

// Connected ecosystem: departments either side of the core [label, tint]
$bsDeptsL = [['Sales & Marketing', 'mint'], ['Lead Management', 'sky'], ['CRM & Customer Relations', 'lilac'], ['Project Management', 'aqua'], ['Planning & Scheduling', 'peach'], ['Procurement & Purchasing', 'rose']];
$bsDeptsR = [['HR & Payroll', 'lime'], ['Finance & Accounts', 'peri'], ['Inventory & Materials', 'mint'], ['Vendor & Contractor Management', 'sky'], ['Operations & Administration', 'lilac'], ['Management & Business Intelligence', 'aqua']];
$bsFlow = ['Leads', 'Sales', 'Projects', 'Teams', 'Expenses', 'Profit'];

// Engineering approach: [number, label, title, text, tags]
$bsSteps = [
    ['01', 'Discover & Audit', 'Understand how your business really works.', 'We study your existing workflows, organizational hierarchy, teams, work culture, software and operational challenges.', 'Business Discovery · Process Analysis · Workflow Mapping'],
    ['02', 'Design & Engineer', 'Design the right solution for your business.', 'We redesign workflows, define system requirements and architect technology around your operational needs.', 'Workflow Engineering · System Architecture · Solution Design'],
    ['03', 'Build & Integrate', 'Build technology around your workflows.', 'We develop custom applications, configure business systems and integrate existing platforms.', 'Custom Development · API Integration · System Configuration'],
    ['04', 'Deploy & Evolve', 'Make technology work in everyday operations.', 'We deploy systems, onboard employees, train teams and continuously improve workflows based on real operational feedback.', 'Deployment · Team Enablement · Optimization'],
];

// Industries: [icon, tint, industry, question, text, cta]
$bsInd = [
    ['hardhat', 'peach', 'Construction & Real Estate', 'Are Your Projects Losing Profit Through Disconnected Operations?', 'Connect project management, procurement, materials, contractors, site activities and project finances through custom construction systems.', 'Stop Profit Leakage in Your Projects'],
    ['stetho', 'mint', 'Healthcare', 'Are Operational Gaps Affecting Your Patient Experience?', 'Connect patient management, appointments, clinic operations, branch management and administrative workflows.', 'Improve Your Healthcare Operations'],
    ['bag', 'sky', 'Ecommerce & D2C', 'Are You Losing Potential Customers Before They Complete a Purchase?', 'Build custom ecommerce experiences with optimized storefronts, intuitive shopping journeys and connected commerce operations.', 'Turn More Visitors Into Customers'],
    ['gem', 'rose', 'Jewellery & Retail', 'Are Inventory Gaps and Disconnected Sales Affecting Your Business?', 'Connect product catalogues, inventory, customer management, sales channels and retail workflows.', 'Take Control of Your Inventory & Sales'],
    ['factory', 'lime', 'Manufacturing & Distribution', 'Are Production Delays and Operational Waste Reducing Your Profits?', 'Connect production planning, procurement, inventory, vendors and supply chain operations.', 'Reduce Production Waste & Protect Profits'],
    ['briefcase', 'lilac', 'Professional Services', 'Are You Losing Valuable Time Managing Clients and Team Workflows?', 'Connect CRM, project delivery, employee responsibilities, resource allocation and financial workflows.', 'Stop Losing Billable Hours'],
];

// Selected projects: [tint, label, title, text, chips label, chips, from, to, href]
$bsProjects = [
    ['peach', 'Construction & Real Estate', 'Multiple Construction Operations. One Connected Business System.', 'A custom-engineered Construction Business OS connecting project management, site operations, procurement, materials, contractors and finance.', 'Connected operations', ['Project Management', 'Site Operations', 'Procurement', 'Material Management', 'Contractor Management', 'Finance'], 'Disconnected departments, spreadsheets and manual site updates.', 'One connected Construction Business Operating System.', $bsCase('from-manual-construction-operations-to-a-fully-integrated-erp-ecosystem', '/case-studies')],
    ['sky', 'Ecommerce & D2C', 'Beyond Standard Shopify. Custom Shopping Experiences Built to Convert.', 'A custom Shopify experience featuring over 100 unique product pages, intuitive navigation and tailored functionality designed around the brand and its customers.', 'Connected capabilities', ['Custom Shopify Development', 'Product Pages', 'Storefront Engineering', 'Customer Experience', 'Conversion Optimization'], 'Standard ecommerce experiences and limited customization.', 'A custom-built shopping experience designed around the brand and its products.', '/case-studies?service=Ecommerce+Solutions'],
    ['mint', 'Healthcare', 'Connecting Multi-Branch Healthcare Operations Through Technology.', 'Digital and operational solutions supporting a multi-branch physiotherapy business through online presence, marketing and operational technology initiatives.', 'Connected capabilities', ['Healthcare Systems', 'Patient Management', 'Multi-Branch Operations', 'Digital Presence', 'Workflow Solutions'], 'Separate operational and digital activities.', 'More connected digital and operational workflows.', $bsCase('from-fragmented-clinic-operations-to-a-unified-multi-branch-physiotherapy-management-system', '/case-studies')],
];
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/bizsys.css') ?>">
<script>document.documentElement.classList.add('js')</script>

<div class="h10 bs">
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol><symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol><symbol id="i-sparkle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z" fill="currentColor" stroke="none"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z" fill="currentColor" stroke="none"/></symbol><symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2.2 2.2L15 10"/></symbol><symbol id="i-layers" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 12.5l9 5 9-5"/><path d="M3 17l9 5 9-5" opacity=".55"/></symbol><symbol id="i-briefcase" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7M3 13h18"/></symbol><symbol id="i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></symbol><symbol id="i-building" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="10" height="18" rx="1.5"/><path d="M15 9h4v12h-4M8 7h4M8 11h4M8 15h4"/></symbol><symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2.2l2.4 11h10.4L20 8H6.3"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></symbol><symbol id="i-puzzle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h4a2 2 0 1 1 4 0h4v4a2 2 0 1 1 0 4v4H4v-4a2 2 0 1 0 0-4z"/></symbol><symbol id="i-store" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9l1.5-5h13L20 9M4 9h16v1.5a2.7 2.7 0 0 1-5.3.7 2.7 2.7 0 0 1-5.4 0A2.7 2.7 0 0 1 4 10.5zM5.5 13v7h13v-7M10 20v-4h4v4"/></symbol><symbol id="i-chip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4M10.5 12h3"/></symbol><symbol id="i-trend" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol><symbol id="i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="3"/><path d="M3 10h18M16.5 14.5h1"/></symbol><symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></symbol><symbol id="i-verified" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5l2.3 1.8 2.9-.4 1.1 2.7 2.7 1.1-.4 2.9 1.8 2.3-1.8 2.3.4 2.9-2.7 1.1-1.1 2.7-2.9-.4-2.3 1.8-2.3-1.8-2.9.4-1.1-2.7-2.7-1.1.4-2.9L2.5 12l1.8-2.3-.4-2.9 2.7-1.1 1.1-2.7 2.9.4z"/><path d="M8.5 12.2l2.4 2.4 4.6-4.6"/></symbol><symbol id="i-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></symbol><symbol id="i-hardhat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17h18v2.5H3zM5 17a7 7 0 0 1 14 0M12 10V6.5M9 11V8.2M15 11V8.2"/></symbol><symbol id="i-stetho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v6a4 4 0 0 0 8 0V3M10 13v2.5a4 4 0 0 0 8 0V14"/><circle cx="18" cy="12" r="2"/></symbol><symbol id="i-wa" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5z"/><path d="M9.4 8.6c.3 2.2 2.5 4.6 5 5.2l1.1-1.2-1.7-1-1 .8c-1-.4-1.9-1.3-2.3-2.3l.8-1-1-1.7z"/></symbol><symbol id="i-bot" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="11" rx="3.5"/><path d="M12 8V5M9.5 13v1.6M14.5 13v1.6M2 13v3M22 13v3"/><circle cx="12" cy="4" r="1"/></symbol><symbol id="i-megaphone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10v4l12 5V5z"/><path d="M16 9.5a3 3 0 010 5M7 14.5V19"/></symbol><symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol><symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2.5"/><path d="M8 11V8a4 4 0 018 0v3"/></symbol><symbol id="i-db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></symbol><symbol id="i-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v9a1.5 1.5 0 01-1.5 1.5H10l-4.5 4v-4h0A1.5 1.5 0 014 14.5z"/><path d="M8 9h8M8 12h5"/></symbol><symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol><symbol id="i-workflow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/></symbol><symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></symbol><symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/></symbol><symbol id="i-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol><symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></symbol><symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h16"/></symbol><symbol id="i-hierarchy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="3" width="6" height="5" rx="1.2"/><rect x="3" y="16" width="6" height="5" rx="1.2"/><rect x="15" y="16" width="6" height="5" rx="1.2"/><path d="M12 8v4M6 16v-4h12v4"/></symbol><symbol id="i-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-8-4.6-8-10.2A4.3 4.3 0 0112 7.2a4.3 4.3 0 018 2.6C20 15.4 12 20 12 20z"/><path d="M7 12.2h2.6l1.4-2.4 2 4.4 1.4-2H17"/></symbol><symbol id="i-gem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12l4 5-10 11L2 9z"/><path d="M2 9h20M9 4L7 9l5 11M15 4l2 5-5 11"/></symbol><symbol id="i-factory" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l6 3.5V10l6 3.5V5h4v16z"/><path d="M8 17h2M13 17h2"/></symbol><symbol id="i-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol><symbol id="i-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></symbol><symbol id="i-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/></symbol><symbol id="i-quote" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h4v4.2c0 3-1.5 5-4 6M15 7h4v4.2c0 3-1.5 5-4 6"/></symbol><symbol id="i-pause" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5v14M15 5v14"/></symbol><symbol id="i-play" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 5l11 7-11 7z" fill="currentColor"/></symbol><symbol id="i-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></symbol><symbol id="i-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></symbol><symbol id="i-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V6M6 12l6-6 6 6"/></symbol><symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/></symbol></defs></svg>
<a class="skip" href="#bs-top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>

<!-- 01 · Hero -->
<section class="bsh" id="bs-top">
  <div class="wrap bsh__grid">
    <div class="bsh__copy">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Business Systems · Custom Software · Business OS</span>
      <h1 class="bsh__h1">Your Business Is Unique. <span class="bsh__hl">Your Systems Should Work That Way.</span></h1>
      <p class="bsh__lede reveal" style="--d:.2s">Every business has its own workflows, departments, people and operational challenges. We understand how your business works and engineer custom technology that connects your operations, simplifies processes and gives you better control.</p>
      <p class="bsh__lede bsh__lede--2 reveal" style="--d:.26s">From custom ERP and Business Operating Systems to CRM, AI and workflow automation, we build technology around your actual business requirements.</p>
      <div class="bsh__ctas reveal" style="--d:.32s">
        <a class="h10-btn bs-btn" href="/book" data-book><span>Build Your Business System</span><?= $bsArrow ?></a>
        <a class="h10-btn bs-btn--ghost" href="#capabilities"><span>Explore Our Capabilities</span><?= $bsArrow ?></a>
      </div>
    </div>
    <div class="bsh__board reveal" style="--d:.24s" aria-hidden="true" data-bs-board>
      <div class="bsb__hd"><span class="bsb__logo">D</span><b>Business OS</b><span class="bsb__live"><i></i>Live</span></div>
      <div class="bsb__core"><span class="bsb__ring"></span><span class="bsb__dot"><?= $bsIc('layers') ?></span><small>One connected system</small></div>
      <ul class="bsb__mods">
        <li style="--t:#DFEAF9;--k:var(--ik-sky)"><?= $bsIc('users') ?><span>CRM</span><b data-bs-tick="128">128</b><em>leads</em></li>
        <li style="--t:#E8E2F5;--k:var(--ik-lilac)"><?= $bsIc('briefcase') ?><span>Projects</span><b data-bs-tick="24">24</b><em>active</em></li>
        <li style="--t:#F8E6D6;--k:var(--ik-peach)"><?= $bsIc('calendar') ?><span>HR</span><b data-bs-tick="86">86</b><em>on shift</em></li>
        <li style="--t:#EDF0D7;--k:var(--ik-lime)"><?= $bsIc('wallet') ?><span>Finance</span><b>₹18.6L</b><em>this month</em></li>
      </ul>
      <p class="bsb__feed" data-bs-feed>New lead synced to CRM · Sales</p>
    </div>
  </div>
  <div class="wrap">
    <div class="bsh__principles reveal" style="--d:.36s">
      <span class="bsh__pk">Our Engineering Principles</span>
      <ul role="list">
<?php foreach ($bsPrinciples as [$icon, $tint, $label]): ?>
        <li class="bs-tint--<?= $tint ?>"><span class="bs-tile"><?= $bsIc($icon) ?></span><?= h($label) ?></li>
<?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<!-- 02 · The business problem -->
<section class="bsp" id="challenges">
  <div class="wrap bsp__grid">
    <div class="bsp__head">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>The Challenges of Running a Growing Business</span>
      <h2 class="bs-h2">Growing Your Business Shouldn't Mean Managing More Complexity.</h2>
      <p class="bs-lede reveal" style="--d:.1s">As your business grows, managing operations through disconnected software, spreadsheets and manual processes becomes increasingly difficult.</p>
      <a class="h10-btn bs-btn reveal" style="--d:.16s" href="/book" data-book><span>Let's Fix Your Business Workflows</span><?= $bsArrow ?></a>
    </div>
    <ol class="bsp__list" role="list">
<?php foreach ($bsProblems as $i => [$icon, $title, $text]): ?>
      <li class="bsp__item reveal" style="--d:<?= $i * .05 ?>s"><span class="bsp__n"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span><span class="bsp__ic"><?= $bsIc($icon) ?></span><div><h3><?= h($title) ?></h3><p><?= h($text) ?></p></div></li>
<?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- 03 · What we engineer -->
<section class="bsc" id="capabilities">
  <div class="wrap">
    <div class="bs-head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Custom Business Systems &amp; Software Development</span>
        <h2 class="bs-h2">Everything Your Business Needs. Engineered Around You.</h2>
      </div>
      <p class="bs-lede reveal" style="--d:.1s">We design and develop technology solutions that connect your departments, automate workflows and simplify business operations.</p>
    </div>
    <div class="bsc__grid">
<?php foreach ($bsBuild as $i => [$icon, $tint, $title, $text, $size]): ?>
      <article class="bsc__card bs-tint--<?= $tint ?><?= $size === 'lg' ? ' bsc__card--lg' : '' ?> reveal" style="--d:<?= $i * .05 ?>s">
        <div class="bsc__top"><span class="bs-tile"><?= $bsIc($icon) ?></span><span class="bsc__n"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span></div>
        <h3><?= h($title) ?></h3>
        <p><?= h($text) ?></p>
      </article>
<?php endforeach; ?>
    </div>
    <div class="bs-cta-row reveal"><a class="h10-btn bs-btn" href="/book" data-book><span>Explore What We Can Build</span><?= $bsArrow ?></a></div>
  </div>
</section>

<!-- 04 · Connected ecosystem -->
<section class="bse" id="ecosystem">
  <div class="wrap">
    <div class="bs-head bs-head--center">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>One Connected Business. Every Department Working Together.</span>
      <h2 class="bs-h2">Connect Every Part of Your Business Through One System.</h2>
      <p class="bs-lede reveal" style="--d:.1s">Your business shouldn't operate through disconnected departments and isolated applications. We engineer connected systems that allow information, responsibilities and workflows to move across your organization.</p>
    </div>
    <div class="bse__map reveal">
      <span class="bse__k">Business Departments &amp; Operations</span>
      <ul class="bse__col bse__col--l" role="list">
<?php foreach ($bsDeptsL as $i => [$label, $tint]): ?>
        <li class="bs-tint--<?= $tint ?>" style="--i:<?= $i ?>"><i class="bse__dot" aria-hidden="true"></i><?= h($label) ?></li>
<?php endforeach; ?>
      </ul>
      <div class="bse__core" aria-hidden="true"><span class="bse__pulse"></span><span class="bse__logo">D</span><b>Business OS</b><small>Drawlead</small></div>
      <ul class="bse__col bse__col--r" role="list">
<?php foreach ($bsDeptsR as $i => [$label, $tint]): ?>
        <li class="bs-tint--<?= $tint ?>" style="--i:<?= $i ?>"><i class="bse__dot" aria-hidden="true"></i><?= h($label) ?></li>
<?php endforeach; ?>
      </ul>
    </div>
    <div class="bse__flow reveal">
      <div class="bse__flow-copy"><span class="bse__k">From Leads to Profit</span><p>Connect customer acquisition, sales, project execution, team activities, operational expenses and financial performance through structured business workflows.</p></div>
      <ol class="bse__steps" role="list">
<?php foreach ($bsFlow as $i => $step): ?>
        <li style="--i:<?= $i ?>"><span><?= h($step) ?></span></li>
<?php endforeach; ?>
      </ol>
    </div>
    <div class="bs-cta-row bs-cta-row--center reveal"><a class="h10-btn bs-btn" href="/book" data-book><span>Build Your Connected Business</span><?= $bsArrow ?></a></div>
  </div>
</section>

<!-- 05 · Engineering approach -->
<section class="bsa" id="approach">
  <div class="wrap bsa__grid">
    <div class="bsa__head">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Working With Your Team. Building for Your Business.</span>
      <h2 class="bs-h2">We Don't Adapt Your Business to Software. We Engineer Software Around Your Business.</h2>
      <p class="bs-lede reveal" style="--d:.1s">Every business operates differently. That's why we work directly with your team to understand existing processes, identify challenges and engineer technology around your actual business requirements.</p>
      <a class="h10-btn bs-btn reveal" style="--d:.16s" href="/book" data-book><span>Discuss Your Business Requirements</span><?= $bsArrow ?></a>
    </div>
    <ol class="bsa__steps" role="list" data-bs-steps>
      <i class="bsa__rail" aria-hidden="true"><u></u></i>
<?php foreach ($bsSteps as $i => [$n, $label, $title, $text, $tags]): ?>
      <li class="bsa__step reveal" style="--d:<?= $i * .05 ?>s">
        <span class="bsa__n"><?= $n ?></span>
        <div class="bsa__card">
          <span class="bsa__k"><?= h($label) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
          <p class="bsa__tags"><?= h($tags) ?></p>
        </div>
      </li>
<?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- 06 · Industry-specific engineering -->
<section class="bsi" id="industries">
  <div class="wrap">
    <div class="bs-head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Same Industry ≠ Same Business Challenges</span>
        <h2 class="bs-h2">We Don't Build for Industries. We Build for Businesses.</h2>
      </div>
      <div class="bs-lede reveal" style="--d:.1s"><p>Even two businesses within the same industry can have completely different workflows, organizational structures and operational challenges.</p><p>We understand your business, study your processes and engineer technology around your specific requirements.</p></div>
    </div>
    <div class="bsi__grid">
<?php foreach ($bsInd as $i => [$icon, $tint, $ind, $q, $text, $cta]): ?>
      <article class="bsi__card bs-tint--<?= $tint ?> reveal" style="--d:<?= ($i % 3) * .06 ?>s">
        <div class="bsi__top"><span class="bs-tile"><?= $bsIc($icon) ?></span><span class="bsi__ind"><b><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></b><?= h($ind) ?></span></div>
        <h3><?= h($q) ?></h3>
        <p><?= h($text) ?></p>
        <a class="bsi__cta" href="/book" data-book><span><?= h($cta) ?></span><?= $bsIc('arrow') ?></a>
      </article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 07 · Selected projects -->
<section class="bsw" id="projects">
  <div class="wrap">
    <div class="bs-head bs-head--center">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Real Businesses. Real Challenges. Real Solutions.</span>
      <h2 class="bs-h2">We Understood. We Engineered. We Made It Work.</h2>
      <p class="bs-lede reveal" style="--d:.1s">Explore how we've understood different business requirements and engineered technology solutions that work in real business environments.</p>
    </div>
    <div class="bsw__list">
<?php foreach ($bsProjects as $i => [$tint, $label, $title, $text, $chipsLabel, $chips, $from, $to, $href]): ?>
      <article class="bsw__card bs-tint--<?= $tint ?> reveal">
        <div class="bsw__main">
          <span class="bsw__k">Project <?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?> · <?= h($label) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
          <span class="bsw__cl"><?= h($chipsLabel) ?></span>
          <ul class="bsw__chips" role="list"><?php foreach ($chips as $chip): ?><li><?= h($chip) ?></li><?php endforeach; ?></ul>
        </div>
        <div class="bsw__shift">
          <div class="bsw__from"><span>From</span><p><?= h($from) ?></p></div>
          <span class="bsw__arrow" aria-hidden="true"><?= $bsIc('arrow') ?></span>
          <div class="bsw__to"><span>To</span><p><?= h($to) ?></p></div>
          <a class="bsw__cta" href="<?= h($href) ?>"><span>Explore Project</span><?= $bsIc('arrow') ?></a>
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="bsw__nda reveal"><?= $bsIc('lock') ?><b>Client confidentiality.</b> Client names and identities remain confidential, as every engagement runs under applicable Non-Disclosure Agreements (NDAs).</p>
  </div>
</section>

<!-- 08 · Final CTA -->
<section class="final final--card bsf" id="contact">
  <div class="wrap">
    <div class="final__card">
      <div class="final__in">
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Let's Build Your Business System</span>
        <h2 class="h1x">Your Business Deserves More Than <span class="grn">Off-the-Shelf Software.</span></h2>
        <p class="lede reveal" style="--d:.1s">Tell us how your business operates, what challenges your teams face and what you want to improve.</p>
        <p class="final__line reveal" style="--d:.14s">Our engineers will work with you to understand your requirements and explore the right technology solution for your business.</p>
        <div class="final__ctas reveal" style="--d:.18s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Build Your Business System</span><?= $bsArrow ?></a><a class="h10-btn h10-btn--ghost" href="/book" data-book><span>Book a Strategy Call</span><?= $bsArrow ?></a></div>
        <p class="bsf__sign">Drawlead — Business Systems &amp; Technology Company</p>
      </div>
    </div>
  </div>
</section>
</main><footer class="foot">
  <?php $signClass = 'wrap'; include __DIR__ . '/partials/footer-signoff.php'; ?>
  <div class="wrap foot__in">
    <a href="#bs-top" aria-label="Drawlead – back to top"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <nav aria-label="Footer"><a href="<?= h($homeUrl) ?>#solutions">Solutions</a><a href="<?= h($homeUrl) ?>#program">8-Week Program</a><a href="/case-studies">Case Studies</a><a href="<?= h($homeUrl) ?>#industries">Industries</a><a href="/about-us">About Us</a><a href="/blog">Blog</a></nav>
  </div>
  <div class="wrap foot__base">
    <span>official@drawlead.co.in &nbsp;·&nbsp; www.drawlead.co.in &nbsp;·&nbsp; Chennai, Tamil Nadu, IN</span>
    <span>© <?= date('Y') ?> Drawlead. All rights reserved.</span>
    <nav class="foot__legal" aria-label="Legal"><a href="/privacy-policy">Privacy Policy</a><a href="/refund-policy">Refund Policy</a><a href="/terms-and-conditions">Terms &amp; Conditions</a></nav>
  </div>
</footer>
</div>

<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
<script src="<?= asset_url('/assets/bizsys.js') ?>" defer></script>
