<?php
/**
 * Business Systems landing page (/business-systems, template "business-systems").
 *
 * In the home page's design language: it loads /assets/home10.css and
 * home10.js and wraps everything in .h10, so buttons, eyebrows, the word
 * reveal on headings, the palette and the footer are shared. What is
 * particular to this page is /assets/bizsys.css (scoped under .bs) and
 * /assets/bizsys.js. Each section has its own layout and interaction:
 *   01 hero          a "Business OS" console that boots module by module
 *   02 problems      chaos-to-clarity: app tiles react to each problem, then snap into order
 *   03 capabilities  bento of live mini product previews
 *   04 ecosystem     department map: pick one to see what it shares; leads-to-profit pipeline
 *   05 approach      a track of paper tickets that tear off their stubs as you scroll
 *   06 industries    case-file folders switched by tabs
 *   07 projects      before/after case files with a drag-to-compare slider
 *   08 final CTA     a message composer whose prompts open the booking calendar
 * All copy is server-rendered so Admin → Pages → Page Content can edit it.
 * Everything reads in full without JavaScript and stands still under
 * prefers-reduced-motion.
 */
$activePage = 'business-systems';
include __DIR__ . '/partials/nav.php';

$bsIc = fn (string $id, string $class = 'ic'): string => '<svg class="' . $class . '" aria-hidden="true" focusable="false"><use href="#i-' . $id . '"/></svg>';
$bsArrow = $bsIc('arrow', 'ic h10-btn__arr');
$bsN = fn (int $i): string => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
$bsCase = function (string $slug, string $fallback) use ($pdo): string {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM case_studies WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() ? '/case-studies/' . $slug : $fallback;
    } catch (PDOException $e) {
        return $fallback;
    }
};

// 01 · console modules [icon, tint, label, value, unit]
$bsMods = [
    ['users', 'sky', 'CRM', '128', 'open leads'],
    ['briefcase', 'lilac', 'Projects', '24', 'active'],
    ['calendar', 'peach', 'HR', '86', 'on shift'],
    ['wallet', 'lime', 'Finance', '₹18.6L', 'this month'],
    ['grid', 'aqua', 'Inventory', '1,240', 'SKUs'],
    ['chart', 'rose', 'Reports', '12', 'live boards'],
];
$bsPrinciples = [['puzzle', 'mint', 'No Generic Software'], ['target', 'sky', 'Business-Specific Engineering'], ['workflow', 'lilac', 'Connected Workflows'], ['shield', 'peach', 'Security-First Development']];

// 02 · problems [icon, title, text] and the app tile each one shakes [label, glyph, messy x/y/rot, tidy x/y]
$bsProblems = [
    ['plug', 'Disconnected Software', 'Different departments use different applications that don\'t communicate with each other.'],
    ['db', 'Scattered Business Data', 'Customer records, project information and important business data remain spread across multiple platforms.'],
    ['gear', 'Manual Workflows', 'Employees spend valuable time on repetitive tasks, manual updates and follow-ups.'],
    ['search', 'Limited Business Visibility', 'Management struggles to understand what\'s happening across departments and operations.'],
    ['puzzle', 'Software That Doesn\'t Fit', 'Existing software cannot accommodate your specific workflows, hierarchy and business requirements.'],
    ['trend', 'Difficult to Scale', 'As your teams and operations expand, managing existing systems becomes increasingly complicated.'],
];
$bsTiles = [
    ['Billing App', 'wallet', '6%', '8%', '-9deg', '4%', '14%'],
    ['Spreadsheets', 'grid', '54%', '4%', '7deg', '36%', '14%'],
    ['Sticky Notes', 'briefcase', '30%', '34%', '-4deg', '68%', '14%'],
    ['Email Threads', 'mail', '62%', '40%', '11deg', '4%', '52%'],
    ['Old ERP', 'layers', '4%', '58%', '6deg', '36%', '52%'],
    ['WhatsApp Groups', 'chat', '46%', '68%', '-8deg', '68%', '52%'],
];

// 03 · capabilities [icon, tint, title, text, size, preview]
$bsBuild = [
    ['layers', 'mint', 'Custom ERP & Business Operating Systems', 'Connect your departments, people, processes and business activities through one unified operating system.', 'lg', 'os'],
    ['users', 'sky', 'CRM & Customer Management', 'Manage leads, customer relationships, sales pipelines, follow-ups and customer interactions.', '', 'crm'],
    ['briefcase', 'lilac', 'Project & Operations Management', 'Manage projects, tasks, resources, timelines, responsibilities and operational activities.', '', 'gantt'],
    ['calendar', 'peach', 'HR & Workforce Management', 'Organize employees, attendance, responsibilities, payroll workflows and team activities.', '', 'hr'],
    ['wallet', 'lime', 'Finance & Business Management', 'Connect expenses, invoices, payments, budgets, revenue and project-wise financial tracking.', '', 'fin'],
    ['chip', 'rose', 'AI, Automation & System Integration', 'Integrate applications, automate repetitive processes and implement AI capabilities across business workflows.', 'lg', 'ai'],
];

// 04 · departments [key, label, tint] with what each shares (related keys, caption)
$bsDepts = [
    ['sales', 'Sales & Marketing', 'mint'], ['leads', 'Lead Management', 'sky'], ['crm', 'CRM & Customer Relations', 'lilac'],
    ['projects', 'Project Management', 'aqua'], ['planning', 'Planning & Scheduling', 'peach'], ['procurement', 'Procurement & Purchasing', 'rose'],
    ['hr', 'HR & Payroll', 'lime'], ['finance', 'Finance & Accounts', 'peri'], ['inventory', 'Inventory & Materials', 'mint'],
    ['vendors', 'Vendor & Contractor Management', 'sky'], ['ops', 'Operations & Administration', 'lilac'], ['mgmt', 'Management & Business Intelligence', 'aqua'],
];
$bsLinks = [
    'sales' => ['leads crm finance', 'Campaign spend and won deals roll straight into revenue.'],
    'leads' => ['sales crm', 'Every enquiry lands in the CRM with its source attached.'],
    'crm' => ['leads sales projects', 'Won customers turn into projects without re-entering data.'],
    'projects' => ['planning hr finance', 'Tasks, people and costs stay tied to each project.'],
    'planning' => ['projects procurement hr', 'Schedules drive purchasing and staffing automatically.'],
    'procurement' => ['inventory vendors finance', 'Purchase orders update stock and payables together.'],
    'hr' => ['projects finance ops', 'Attendance flows into payroll and project costs.'],
    'finance' => ['sales procurement mgmt', 'Revenue and expenses meet in one live ledger.'],
    'inventory' => ['procurement ops finance', 'Stock levels trigger reorders before you run out.'],
    'vendors' => ['procurement finance projects', 'Contractor work and bills link back to the job.'],
    'ops' => ['hr inventory mgmt', 'Daily operations report up without manual updates.'],
    'mgmt' => ['finance sales projects', 'Live dashboards built from every department.'],
];
$bsFlow = ['Leads', 'Sales', 'Projects', 'Teams', 'Expenses', 'Profit'];

// 05 · approach [label, title, text, tags]
$bsSteps = [
    ['Discover & Audit', 'Understand how your business really works.', 'We study your existing workflows, organizational hierarchy, teams, work culture, software and operational challenges.', 'Business Discovery · Process Analysis · Workflow Mapping'],
    ['Design & Engineer', 'Design the right solution for your business.', 'We redesign workflows, define system requirements and architect technology around your operational needs.', 'Workflow Engineering · System Architecture · Solution Design'],
    ['Build & Integrate', 'Build technology around your workflows.', 'We develop custom applications, configure business systems and integrate existing platforms.', 'Custom Development · API Integration · System Configuration'],
    ['Deploy & Evolve', 'Make technology work in everyday operations.', 'We deploy systems, onboard employees, train teams and continuously improve workflows based on real operational feedback.', 'Deployment · Team Enablement · Optimization'],
];

// 06 · industries [icon, tint, industry, question, text, cta]
$bsInd = [
    ['hardhat', 'peach', 'Construction & Real Estate', 'Are Your Projects Losing Profit Through Disconnected Operations?', 'Connect project management, procurement, materials, contractors, site activities and project finances through custom construction systems.', 'Stop Profit Leakage in Your Projects'],
    ['stetho', 'mint', 'Healthcare', 'Are Operational Gaps Affecting Your Patient Experience?', 'Connect patient management, appointments, clinic operations, branch management and administrative workflows.', 'Improve Your Healthcare Operations'],
    ['bag', 'sky', 'Ecommerce & D2C', 'Are You Losing Potential Customers Before They Complete a Purchase?', 'Build custom ecommerce experiences with optimized storefronts, intuitive shopping journeys and connected commerce operations.', 'Turn More Visitors Into Customers'],
    ['gem', 'rose', 'Jewellery & Retail', 'Are Inventory Gaps and Disconnected Sales Affecting Your Business?', 'Connect product catalogues, inventory, customer management, sales channels and retail workflows.', 'Take Control of Your Inventory & Sales'],
    ['factory', 'lime', 'Manufacturing & Distribution', 'Are Production Delays and Operational Waste Reducing Your Profits?', 'Connect production planning, procurement, inventory, vendors and supply chain operations.', 'Reduce Production Waste & Protect Profits'],
    ['briefcase', 'lilac', 'Professional Services', 'Are You Losing Valuable Time Managing Clients and Team Workflows?', 'Connect CRM, project delivery, employee responsibilities, resource allocation and financial workflows.', 'Stop Losing Billable Hours'],
];

// 07 · projects [tint, label, title, text, chips label, chips, from, to, href, from tiles, to modules]
$bsProjects = [
    ['peach', 'Construction & Real Estate', 'Multiple Construction Operations. One Connected Business System.', 'A custom-engineered Construction Business OS connecting project management, site operations, procurement, materials, contractors and finance.', 'Connected operations', ['Project Management', 'Site Operations', 'Procurement', 'Material Management', 'Contractor Management', 'Finance'], 'Disconnected departments, spreadsheets and manual site updates.', 'One connected Construction Business Operating System.', $bsCase('from-manual-construction-operations-to-a-fully-integrated-erp-ecosystem', '/case-studies'), ['Excel sheets', 'Site diary', 'Phone calls', 'Paper bills'], ['Projects', 'Site Ops', 'Procurement', 'Materials', 'Contractors', 'Finance']],
    ['sky', 'Ecommerce & D2C', 'Beyond Standard Shopify. Custom Shopping Experiences Built to Convert.', 'A custom Shopify experience featuring over 100 unique product pages, intuitive navigation and tailored functionality designed around the brand and its customers.', 'Connected capabilities', ['Custom Shopify Development', 'Product Pages', 'Storefront Engineering', 'Customer Experience', 'Conversion Optimization'], 'Standard ecommerce experiences and limited customization.', 'A custom-built shopping experience designed around the brand and its products.', '/case-studies?service=Ecommerce+Solutions', ['Stock theme', 'Plugins', 'Generic pages', 'Slow checkout'], ['Storefront', '100+ Product Pages', 'Navigation', 'Brand Story', 'Checkout', 'Analytics']],
    ['mint', 'Healthcare', 'Connecting Multi-Branch Healthcare Operations Through Technology.', 'Digital and operational solutions supporting a multi-branch physiotherapy business through online presence, marketing and operational technology initiatives.', 'Connected capabilities', ['Healthcare Systems', 'Patient Management', 'Multi-Branch Operations', 'Digital Presence', 'Workflow Solutions'], 'Separate operational and digital activities.', 'More connected digital and operational workflows.', $bsCase('from-fragmented-clinic-operations-to-a-unified-multi-branch-physiotherapy-management-system', '/case-studies'), ['Branch A', 'Branch B', 'Separate ads', 'Paper records'], ['Patients', 'Appointments', 'Branches', 'Digital Presence', 'Marketing', 'Workflows']],
];

// 08 · composer prompts
$bsPrompts = ['We use too many disconnected tools', 'We need a custom ERP', 'Automate our follow-ups', 'Connect our departments and branches'];
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/bizsys.css') ?>">
<script>document.documentElement.classList.add('js')</script>

<div class="h10 bs">
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol><symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol><symbol id="i-sparkle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z" fill="currentColor" stroke="none"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z" fill="currentColor" stroke="none"/></symbol><symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2.2 2.2L15 10"/></symbol><symbol id="i-layers" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 12.5l9 5 9-5"/><path d="M3 17l9 5 9-5" opacity=".55"/></symbol><symbol id="i-briefcase" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7M3 13h18"/></symbol><symbol id="i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></symbol><symbol id="i-building" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="10" height="18" rx="1.5"/><path d="M15 9h4v12h-4M8 7h4M8 11h4M8 15h4"/></symbol><symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2.2l2.4 11h10.4L20 8H6.3"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></symbol><symbol id="i-puzzle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h4a2 2 0 1 1 4 0h4v4a2 2 0 1 1 0 4v4H4v-4a2 2 0 1 0 0-4z"/></symbol><symbol id="i-store" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9l1.5-5h13L20 9M4 9h16v1.5a2.7 2.7 0 0 1-5.3.7 2.7 2.7 0 0 1-5.4 0A2.7 2.7 0 0 1 4 10.5zM5.5 13v7h13v-7M10 20v-4h4v4"/></symbol><symbol id="i-chip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4M10.5 12h3"/></symbol><symbol id="i-trend" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol><symbol id="i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="3"/><path d="M3 10h18M16.5 14.5h1"/></symbol><symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></symbol><symbol id="i-verified" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5l2.3 1.8 2.9-.4 1.1 2.7 2.7 1.1-.4 2.9 1.8 2.3-1.8 2.3.4 2.9-2.7 1.1-1.1 2.7-2.9-.4-2.3 1.8-2.3-1.8-2.9.4-1.1-2.7-2.7-1.1.4-2.9L2.5 12l1.8-2.3-.4-2.9 2.7-1.1 1.1-2.7 2.9.4z"/><path d="M8.5 12.2l2.4 2.4 4.6-4.6"/></symbol><symbol id="i-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></symbol><symbol id="i-hardhat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17h18v2.5H3zM5 17a7 7 0 0 1 14 0M12 10V6.5M9 11V8.2M15 11V8.2"/></symbol><symbol id="i-stetho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v6a4 4 0 0 0 8 0V3M10 13v2.5a4 4 0 0 0 8 0V14"/><circle cx="18" cy="12" r="2"/></symbol><symbol id="i-wa" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5z"/><path d="M9.4 8.6c.3 2.2 2.5 4.6 5 5.2l1.1-1.2-1.7-1-1 .8c-1-.4-1.9-1.3-2.3-2.3l.8-1-1-1.7z"/></symbol><symbol id="i-bot" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="11" rx="3.5"/><path d="M12 8V5M9.5 13v1.6M14.5 13v1.6M2 13v3M22 13v3"/><circle cx="12" cy="4" r="1"/></symbol><symbol id="i-megaphone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10v4l12 5V5z"/><path d="M16 9.5a3 3 0 010 5M7 14.5V19"/></symbol><symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol><symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2.5"/><path d="M8 11V8a4 4 0 018 0v3"/></symbol><symbol id="i-db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></symbol><symbol id="i-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v9a1.5 1.5 0 01-1.5 1.5H10l-4.5 4v-4h0A1.5 1.5 0 014 14.5z"/><path d="M8 9h8M8 12h5"/></symbol><symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol><symbol id="i-workflow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/></symbol><symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></symbol><symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/></symbol><symbol id="i-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol><symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></symbol><symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h16"/></symbol><symbol id="i-hierarchy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="3" width="6" height="5" rx="1.2"/><rect x="3" y="16" width="6" height="5" rx="1.2"/><rect x="15" y="16" width="6" height="5" rx="1.2"/><path d="M12 8v4M6 16v-4h12v4"/></symbol><symbol id="i-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-8-4.6-8-10.2A4.3 4.3 0 0112 7.2a4.3 4.3 0 018 2.6C20 15.4 12 20 12 20z"/><path d="M7 12.2h2.6l1.4-2.4 2 4.4 1.4-2H17"/></symbol><symbol id="i-gem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12l4 5-10 11L2 9z"/><path d="M2 9h20M9 4L7 9l5 11M15 4l2 5-5 11"/></symbol><symbol id="i-factory" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l6 3.5V10l6 3.5V5h4v16z"/><path d="M8 17h2M13 17h2"/></symbol><symbol id="i-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol><symbol id="i-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></symbol><symbol id="i-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/></symbol><symbol id="i-quote" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h4v4.2c0 3-1.5 5-4 6M15 7h4v4.2c0 3-1.5 5-4 6"/></symbol><symbol id="i-pause" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5v14M15 5v14"/></symbol><symbol id="i-play" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 5l11 7-11 7z" fill="currentColor"/></symbol><symbol id="i-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></symbol><symbol id="i-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></symbol><symbol id="i-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V6M6 12l6-6 6 6"/></symbol><symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/></symbol></defs></svg>
<a class="skip" href="#bs-top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>

<!-- 01 · Hero: the Business OS boots -->
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
      <ul class="bsh__proof reveal" style="--d:.38s" role="list"><li><b>50+</b> projects delivered</li><li><b>20+</b> industries</li><li><b>8-week</b> implementation program</li></ul>
    </div>
    <div class="cs reveal" style="--d:.2s" aria-hidden="true" data-bs-console>
      <div class="cs__bar"><span class="cs__dots"><i></i><i></i><i></i></span><b>Business OS</b><span class="cs__live"><i></i>Live</span></div>
      <div class="cs__boot"><span data-bs-boot>6 of 6 modules connected</span><i class="cs__prog"><u></u></i></div>
      <div class="cs__stage">
        <div class="cs__core"><span class="cs__ring"></span><span class="cs__dot"><?= $bsIc('layers') ?></span><small>One connected system</small></div>
        <ul class="cs__mods">
<?php foreach ($bsMods as $i => [$icon, $tint, $label, $val, $unit]): ?>
          <li class="bs-tint--<?= $tint ?>" style="--i:<?= $i ?>"><span class="cs__ic"><?= $bsIc($icon) ?></span><span class="cs__lb"><?= h($label) ?></span><b<?= ctype_digit($val) ? ' data-bs-tick' : '' ?>><?= h($val) ?></b><em><?= h($unit) ?></em></li>
<?php endforeach; ?>
        </ul>
      </div>
      <p class="cs__feed" data-bs-feed>New lead synced to CRM · Sales</p>
    </div>
  </div>
  <div class="wrap">
    <div class="ptix reveal" style="--d:.4s">
      <span class="ptix__label" aria-hidden="true">Principles</span>
      <ul class="ptix__row" role="list" aria-label="Our engineering principles">
<?php foreach ($bsPrinciples as [$icon, $tint, $label]): ?>
        <li class="ptix__s bs-tint--<?= $tint ?>"><span class="bs-tile"><?= $bsIc($icon) ?></span><span><?= h($label) ?></span></li>
<?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<!-- 02 · The business problem: chaos to clarity -->
<section class="bsp" id="challenges">
  <div class="wrap">
    <div class="bs-head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>The Challenges of Running a Growing Business</span>
        <h2 class="bs-h2">Growing Your Business Shouldn't Mean Managing More Complexity.</h2>
      </div>
      <p class="bs-lede reveal" style="--d:.1s">As your business grows, managing operations through disconnected software, spreadsheets and manual processes becomes increasingly difficult.</p>
    </div>
    <div class="bsp__grid" data-bs-chaos>
      <div class="bsp__stage" aria-hidden="true">
        <div class="bsp__desk">
<?php foreach ($bsTiles as $i => [$label, $glyph, $x, $y, $r, $gx, $gy]): ?>
          <div class="bsp__tile" data-i="<?= $i ?>" style="--x:<?= $x ?>;--y:<?= $y ?>;--r:<?= $r ?>;--gx:<?= $gx ?>;--gy:<?= $gy ?>"><span class="bsp__tg"><?= $bsIc($glyph) ?></span><b><?= h($label) ?></b><i class="bsp__pin"></i><i class="bsp__ok"><?= $bsIc('check') ?></i></div>
<?php endforeach; ?>
        </div>
        <p class="bsp__cap"><span class="bsp__cap-a">6 tools · 0 connections</span><span class="bsp__cap-b">1 system · everything connected</span></p>
      </div>
      <div class="bsp__side">
        <ol class="bsp__list" role="list">
<?php foreach ($bsProblems as $i => [$icon, $title, $text]): ?>
          <li class="bsp__item reveal" data-i="<?= $i ?>"><span class="bsp__n"><?= $bsN($i) ?></span><span class="bsp__ic"><?= $bsIc($icon) ?></span><div><h3><?= h($title) ?></h3><p><?= h($text) ?></p></div></li>
<?php endforeach; ?>
        </ol>
        <a class="h10-btn bs-btn reveal" href="/book" data-book><span>Let's Fix Your Business Workflows</span><?= $bsArrow ?></a>
      </div>
    </div>
  </div>
</section>

<!-- 03 · What we engineer: bento of live previews -->
<section class="bsc" id="capabilities">
  <div class="wrap">
    <div class="bs-head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Custom Business Systems &amp; Software Development</span>
        <h2 class="bs-h2">Everything Your Business Needs. Engineered Around You.</h2>
      </div>
      <p class="bs-lede reveal" style="--d:.1s">We design and develop technology solutions that connect your departments, automate workflows and simplify business operations.</p>
    </div>
    <div class="bento">
<?php foreach ($bsBuild as $i => [$icon, $tint, $title, $text, $size, $pv]): ?>
      <article class="bento__card bs-tint--<?= $tint ?><?= $size === 'lg' ? ' bento__card--lg' : '' ?> reveal" style="--d:<?= $i * .05 ?>s">
        <div class="pv pv--<?= $pv ?>" aria-hidden="true">
<?php if ($pv === 'os'): ?>
          <span class="pv-os__core">OS</span><span class="pv-os__m" style="--a:0">Sales</span><span class="pv-os__m" style="--a:1">Projects</span><span class="pv-os__m" style="--a:2">HR</span><span class="pv-os__m" style="--a:3">Finance</span><span class="pv-os__m" style="--a:4">Stock</span><span class="pv-os__m" style="--a:5">Reports</span>
<?php elseif ($pv === 'crm'): ?>
          <div class="pv-crm__col"><small>New</small><i></i><i class="mv"></i></div><div class="pv-crm__col"><small>Qualified</small><i></i></div><div class="pv-crm__col"><small>Won</small><i class="won"></i></div>
<?php elseif ($pv === 'gantt'): ?>
          <i style="--s:0;--w:46%"></i><i style="--s:18%;--w:40%"></i><i style="--s:36%;--w:34%"></i><i style="--s:52%;--w:44%"></i>
<?php elseif ($pv === 'hr'): ?>
<?php for ($d = 0; $d < 21; $d++): ?><i class="<?= in_array($d, [4, 11, 17], true) ? 'off' : '' ?>" style="--d:<?= $d ?>"></i><?php endfor; ?>
<?php elseif ($pv === 'fin'): ?>
          <div><span>INV-1042</span><b>₹48,200</b><em class="due">Due</em></div><div><span>INV-1041</span><b>₹1,26,000</b><em>Paid</em></div><div><span>INV-1040</span><b>₹32,750</b><em>Paid</em></div>
<?php else: ?>
          <svg viewBox="0 0 240 110" focusable="false"><path class="pv-ai__ln" d="M30 55H90M90 55L150 25M90 55L150 85M150 25H210M150 85H210"/><circle cx="30" cy="55" r="9"/><circle cx="90" cy="55" r="12" class="hub"/><circle cx="150" cy="25" r="9"/><circle cx="150" cy="85" r="9"/><circle cx="210" cy="25" r="9"/><circle cx="210" cy="85" r="9"/></svg>
<?php endif; ?>
        </div>
        <div class="bento__txt">
          <span class="bento__n"><?= $bsN($i) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <div class="bs-cta-row reveal"><a class="h10-btn bs-btn" href="/book" data-book><span>Explore What We Can Build</span><?= $bsArrow ?></a></div>
  </div>
</section>

<!-- 04 · Connected ecosystem: the department map -->
<section class="bse" id="ecosystem">
  <div class="wrap">
    <div class="bs-head bs-head--center">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>One Connected Business. Every Department Working Together.</span>
      <h2 class="bs-h2">Connect Every Part of Your Business Through One System.</h2>
      <p class="bs-lede reveal" style="--d:.1s">Your business shouldn't operate through disconnected departments and isolated applications. We engineer connected systems that allow information, responsibilities and workflows to move across your organization.</p>
    </div>
    <div class="map reveal" data-bs-map>
      <span class="map__k">Business Departments &amp; Operations</span>
      <ul class="map__col map__col--l" role="list">
<?php foreach (array_slice($bsDepts, 0, 6) as [$key, $label, $tint]): ?>
        <li><button type="button" class="map__d bs-tint--<?= $tint ?>" data-dept="<?= $key ?>" data-rel="<?= h($bsLinks[$key][0]) ?>" data-cap="<?= h($bsLinks[$key][1]) ?>"><i class="map__dot" aria-hidden="true"></i><?= h($label) ?></button></li>
<?php endforeach; ?>
      </ul>
      <div class="map__core"><span class="map__pulse" aria-hidden="true"></span><span class="map__logo" aria-hidden="true">D</span><b>Business OS</b><p class="map__cap" data-bs-cap aria-live="polite">Pick a department to see what it shares.</p></div>
      <ul class="map__col map__col--r" role="list">
<?php foreach (array_slice($bsDepts, 6) as [$key, $label, $tint]): ?>
        <li><button type="button" class="map__d bs-tint--<?= $tint ?>" data-dept="<?= $key ?>" data-rel="<?= h($bsLinks[$key][0]) ?>" data-cap="<?= h($bsLinks[$key][1]) ?>"><i class="map__dot" aria-hidden="true"></i><?= h($label) ?></button></li>
<?php endforeach; ?>
      </ul>
    </div>
    <div class="pipe reveal" data-bs-pipe>
      <div class="pipe__copy"><span class="map__k">From Leads to Profit</span><p>Connect customer acquisition, sales, project execution, team activities, operational expenses and financial performance through structured business workflows.</p></div>
      <div class="pipe__track">
        <i class="pipe__line" aria-hidden="true"><u></u></i><i class="pipe__token" aria-hidden="true"></i>
        <ol class="pipe__steps" role="list">
<?php foreach ($bsFlow as $i => $step): ?>
          <li style="--i:<?= $i ?>"><span class="pipe__dot" aria-hidden="true"></span><?= h($step) ?></li>
<?php endforeach; ?>
        </ol>
      </div>
    </div>
    <div class="bs-cta-row bs-cta-row--center reveal"><a class="h10-btn bs-btn" href="/book" data-book><span>Build Your Connected Business</span><?= $bsArrow ?></a></div>
  </div>
</section>

<!-- 05 · Engineering approach: tear-off ticket track -->
<section class="bsa" id="approach" data-bs-track>
  <div class="bsa__pin">
    <div class="wrap bsa__in">
      <div class="bs-head bsa__head">
        <div>
          <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Working With Your Team. Building for Your Business.</span>
          <h2 class="bs-h2">We Don't Adapt Your Business to Software. We Engineer Software Around Your Business.</h2>
        </div>
        <div class="bs-lede reveal" style="--d:.1s"><p>Every business operates differently. That's why we work directly with your team to understand existing processes, identify challenges and engineer technology around your actual business requirements.</p><a class="h10-btn bs-btn" href="/book" data-book><span>Discuss Your Business Requirements</span><?= $bsArrow ?></a></div>
      </div>
      <div class="tkts">
        <ol class="tkts__row" role="list">
<?php foreach ($bsSteps as $i => [$label, $title, $text, $tags]): ?>
          <li class="tkt reveal" style="--d:<?= $i * .06 ?>s">
            <div class="tkt__stub"><span>Step</span><b><?= $bsN($i) ?></b><small><?= h($label) ?></small></div>
            <div class="tkt__body">
              <h3><?= h($title) ?></h3>
              <p><?= h($text) ?></p>
              <p class="tkt__tags"><?= h($tags) ?></p>
            </div>
          </li>
<?php endforeach; ?>
        </ol>
        <i class="tkts__bar" aria-hidden="true"><u></u></i>
      </div>
    </div>
  </div>
</section>

<!-- 06 · Industries: case-file folders -->
<section class="bsi" id="industries">
  <div class="wrap">
    <div class="bs-head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Same Industry ≠ Same Business Challenges</span>
        <h2 class="bs-h2">We Don't Build for Industries. We Build for Businesses.</h2>
      </div>
      <div class="bs-lede reveal" style="--d:.1s"><p>Even two businesses within the same industry can have completely different workflows, organizational structures and operational challenges.</p><p>We understand your business, study your processes and engineer technology around your specific requirements.</p></div>
    </div>
    <div class="files reveal" data-bs-files>
      <div class="files__tabs" role="tablist" aria-label="Industries" aria-orientation="vertical">
<?php foreach ($bsInd as $i => [$icon, $tint, $ind]): ?>
        <button type="button" role="tab" class="files__tab bs-tint--<?= $tint ?>" id="bsi-t<?= $i ?>" aria-controls="bsi-p<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i ? ' tabindex="-1"' : '' ?>><b><?= $bsN($i) ?></b><?= h($ind) ?></button>
<?php endforeach; ?>
      </div>
      <div class="files__stack">
<?php foreach ($bsInd as $i => [$icon, $tint, $ind, $q, $text, $cta]): ?>
        <article class="file bs-tint--<?= $tint ?>" role="tabpanel" id="bsi-p<?= $i ?>" aria-labelledby="bsi-t<?= $i ?>">
          <span class="file__tab" aria-hidden="true"><?= $bsN($i) ?> · <?= h($ind) ?></span>
          <div class="file__in">
            <span class="file__art" aria-hidden="true"><?= $bsIc($icon) ?></span>
            <h3><?= h($q) ?></h3>
            <p><?= h($text) ?></p>
            <a class="file__cta" href="/book" data-book><span><?= h($cta) ?></span><?= $bsIc('arrow') ?></a>
          </div>
        </article>
<?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- 07 · Selected projects: before / after case files -->
<section class="bsw" id="projects">
  <div class="wrap">
    <div class="bs-head bs-head--center">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Real Businesses. Real Challenges. Real Solutions.</span>
      <h2 class="bs-h2">We Understood. We Engineered. We Made It Work.</h2>
      <p class="bs-lede reveal" style="--d:.1s">Explore how we've understood different business requirements and engineered technology solutions that work in real business environments.</p>
    </div>
    <div class="cases reveal" data-bs-cases>
      <div class="cases__nav" role="tablist" aria-label="Projects">
<?php foreach ($bsProjects as $i => $pr): ?>
        <button type="button" role="tab" class="cases__tab" id="bsw-t<?= $i ?>" aria-controls="bsw-p<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i ? ' tabindex="-1"' : '' ?>><b><?= $bsN($i) ?></b><?= h($pr[1]) ?></button>
<?php endforeach; ?>
      </div>
<?php foreach ($bsProjects as $i => [$tint, $label, $title, $text, $chipsLabel, $chips, $from, $to, $href, $fromTiles, $toMods]): ?>
      <article class="case bs-tint--<?= $tint ?>" role="tabpanel" id="bsw-p<?= $i ?>" aria-labelledby="bsw-t<?= $i ?>">
        <div class="case__copy">
          <span class="case__k">Project <?= $bsN($i) ?> · <?= h($label) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
          <span class="case__cl"><?= h($chipsLabel) ?></span>
          <ul class="case__chips" role="list"><?php foreach ($chips as $chip): ?><li><?= h($chip) ?></li><?php endforeach; ?></ul>
          <a class="h10-btn bs-btn case__cta" href="<?= h($href) ?>"><span>Explore Project</span><?= $bsArrow ?></a>
        </div>
        <div class="cmp" style="--pos:50%">
          <div class="cmp__from">
            <span class="cmp__k">From</span>
            <div class="cmp__mess" aria-hidden="true"><?php foreach ($fromTiles as $k => $t): ?><span style="--k:<?= $k ?>"><?= h($t) ?></span><?php endforeach; ?></div>
            <p><?= h($from) ?></p>
          </div>
          <div class="cmp__to">
            <span class="cmp__k">To</span>
            <div class="cmp__sys" aria-hidden="true"><b>Business OS</b><?php foreach ($toMods as $t): ?><span><?= h($t) ?></span><?php endforeach; ?></div>
            <p><?= h($to) ?></p>
          </div>
          <i class="cmp__handle" aria-hidden="true"><?= $bsIc('left') ?><?= $bsIc('right') ?></i>
          <input class="cmp__range" type="range" min="0" max="100" value="50" aria-label="Compare before and after for project <?= $bsN($i) ?>">
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="nda reveal"><span class="nda__stamp">Confidential · NDA</span>Client names and identities remain confidential, as every engagement runs under applicable Non-Disclosure Agreements (NDAs).</p>
  </div>
</section>

<!-- 08 · Final CTA: tell us how your business works -->
<section class="final final--card bsf" id="contact">
  <div class="wrap">
    <div class="final__card">
      <div class="final__in">
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Let's Build Your Business System</span>
        <h2 class="h1x">Your Business Deserves More Than <span class="grn">Off-the-Shelf Software.</span></h2>
        <p class="lede reveal" style="--d:.1s">Tell us how your business operates, what challenges your teams face and what you want to improve.</p>
        <p class="final__line reveal" style="--d:.14s">Our engineers will work with you to understand your requirements and explore the right technology solution for your business.</p>
        <div class="composer reveal" style="--d:.18s">
          <p class="composer__msg" data-bs-msg>Hi Drawlead, we'd like to talk about our business…</p>
          <div class="composer__chips">
<?php foreach ($bsPrompts as $prompt): ?>
            <button type="button" class="composer__chip" data-book data-prompt="<?= h($prompt) ?>"><?= h($prompt) ?></button>
<?php endforeach; ?>
          </div>
        </div>
        <div class="final__ctas reveal" style="--d:.22s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Build Your Business System</span><?= $bsArrow ?></a><a class="h10-btn h10-btn--ghost" href="/book" data-book><span>Book a Strategy Call</span><?= $bsArrow ?></a></div>
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
