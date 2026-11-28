<?php
/**
 * Business Systems landing page (/business-systems, template "business-systems").
 *
 * Built on the home page's components and palette: /assets/home10.css and
 * home10.js load first and the page sits in .h10, so the buttons, pills, word
 * reveal on headings, tints and footer are shared with Home and About. What is
 * particular to this page is /assets/bizsys.css (everything under .bz) and
 * /assets/bizsys.js. Each section's layout follows what it says:
 *
 *   01 hero          the statement, beside a "business fingerprint": ridges no two
 *                    businesses share, with the six modules on one green workflow
 *   02 problems      bento: the disconnected scene beside six tinted symptom
 *                    cards, each with the sign you would notice day to day
 *   03 capabilities  light mint stage holding one app window: the six capabilities as
 *                    its sidebar, each with its own small product screen
 *   04 ecosystem     twelve departments round a ring with the connected hub in
 *                    the middle; picking one lights what it shares. Then Leads →
 *                    Profit as a line of type
 *   05 approach      four tinted cards that stack up as the page scrolls
 *   06 industries    two businesses in one industry side by side (≠), then six cards
 *   07 projects      zig-zag rows: work photo with a From → To card, story beside
 *   08 final CTA     the home page's light full-width close
 *
 * All copy is server-rendered inside <section>s, so Admin → Pages → Page
 * Content can edit it. Everything reads in full without JavaScript, and
 * stands still under prefers-reduced-motion.
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

// 01 · the modules that sit on the fingerprint's workflow ridge, and in the ERP screen [icon, tint, label, value, unit]
$bsMods = [
    ['users', 'sky', 'CRM', '128', 'open leads'],
    ['briefcase', 'lilac', 'Projects', '24', 'active'],
    ['calendar', 'peach', 'HR', '86', 'on shift'],
    ['wallet', 'lime', 'Finance', '₹18.6L', 'this month'],
    ['grid', 'aqua', 'Inventory', '1,240', 'SKUs'],
    ['chart', 'rose', 'Reports', '12', 'live boards'],
];
$bsPrinciples = [['puzzle', 'mint', 'No Generic Software'], ['target', 'sky', 'Business-Specific Engineering'], ['workflow', 'lilac', 'Connected Workflows'], ['shield', 'peach', 'Security-First Development']];

// 02 · problems [icon, title, text, tint, symptom]
$bsProblems = [
    ['plug', 'Disconnected Software', 'Different departments use different applications that don\'t communicate with each other.', 'rose', 'Not in sync'],
    ['db', 'Scattered Business Data', 'Customer records, project information and important business data remain spread across multiple platforms.', 'peach', 'Five versions of the truth'],
    ['gear', 'Manual Workflows', 'Employees spend valuable time on repetitive tasks, manual updates and follow-ups.', 'lilac', 'Done by hand'],
    ['search', 'Limited Business Visibility', 'Management struggles to understand what\'s happening across departments and operations.', 'sky', 'No live view'],
    ['puzzle', 'Software That Doesn\'t Fit', 'Existing software cannot accommodate your specific workflows, hierarchy and business requirements.', 'aqua', 'Workarounds everywhere'],
    ['trend', 'Difficult to Scale', 'As your teams and operations expand, managing existing systems becomes increasingly complicated.', 'lime', 'Breaks as you grow'],
];

// 03 · capabilities [icon, tint, title, text, screen]
$bsBuild = [
    ['layers', 'mint', 'Custom ERP & Business Operating Systems', 'Connect your departments, people, processes and business activities through one unified operating system.', 'os'],
    ['users', 'sky', 'CRM & Customer Management', 'Manage leads, customer relationships, sales pipelines, follow-ups and customer interactions.', 'crm'],
    ['briefcase', 'lilac', 'Project & Operations Management', 'Manage projects, tasks, resources, timelines, responsibilities and operational activities.', 'gantt'],
    ['calendar', 'peach', 'HR & Workforce Management', 'Organize employees, attendance, responsibilities, payroll workflows and team activities.', 'hr'],
    ['wallet', 'lime', 'Finance & Business Management', 'Connect expenses, invoices, payments, budgets, revenue and project-wise financial tracking.', 'fin'],
    ['chip', 'rose', 'AI, Automation & System Integration', 'Integrate applications, automate repetitive processes and implement AI capabilities across business workflows.', 'ai'],
];

// 04 · departments [key, label, tint] and what each shares (related keys, caption)
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
$bsStepIc = ['search', 'workflow', 'code', 'rocket'];
$bsStepTint = ['peach', 'sky', 'lilac', 'lime'];

// 06 · industries [icon, tint, industry, question, text, cta]
$bsInd = [
    ['hardhat', 'peach', 'Construction & Real Estate', 'Are Your Projects Losing Profit Through Disconnected Operations?', 'Connect project management, procurement, materials, contractors, site activities and project finances through custom construction systems.', 'Stop Profit Leakage in Your Projects'],
    ['stetho', 'mint', 'Healthcare', 'Are Operational Gaps Affecting Your Patient Experience?', 'Connect patient management, appointments, clinic operations, branch management and administrative workflows.', 'Improve Your Healthcare Operations'],
    ['bag', 'sky', 'Ecommerce & D2C', 'Are You Losing Potential Customers Before They Complete a Purchase?', 'Build custom ecommerce experiences with optimized storefronts, intuitive shopping journeys and connected commerce operations.', 'Turn More Visitors Into Customers'],
    ['gem', 'rose', 'Jewellery & Retail', 'Are Inventory Gaps and Disconnected Sales Affecting Your Business?', 'Connect product catalogues, inventory, customer management, sales channels and retail workflows.', 'Take Control of Your Inventory & Sales'],
    ['factory', 'lime', 'Manufacturing & Distribution', 'Are Production Delays and Operational Waste Reducing Your Profits?', 'Connect production planning, procurement, inventory, vendors and supply chain operations.', 'Reduce Production Waste & Protect Profits'],
    ['briefcase', 'lilac', 'Professional Services', 'Are You Losing Valuable Time Managing Clients and Team Workflows?', 'Connect CRM, project delivery, employee responsibilities, resource allocation and financial workflows.', 'Stop Losing Billable Hours'],
];

// 07 · projects [tint, label, title, text, chips label, chips, from, to, href, from tiles, to modules, photo, alt]
$bsProjects = [
    ['peach', 'Construction & Real Estate', 'Multiple Construction Operations. One Connected Business System.', 'A custom-engineered Construction Business OS connecting project management, site operations, procurement, materials, contractors and finance.', 'Connected operations', ['Project Management', 'Site Operations', 'Procurement', 'Material Management', 'Contractor Management', 'Finance'], 'Disconnected departments, spreadsheets and manual site updates.', 'One connected Construction Business Operating System.', $bsCase('from-manual-construction-operations-to-a-fully-integrated-erp-ecosystem', '/case-studies'), ['Excel sheets', 'Site diary', 'Phone calls', 'Paper bills'], ['Projects', 'Site Ops', 'Procurement', 'Materials', 'Contractors', 'Finance'], 'construction', 'Two workers cutting and laying steel bars on a construction site'],
    ['sky', 'Ecommerce & D2C', 'Beyond Standard Shopify. Custom Shopping Experiences Built to Convert.', 'A custom Shopify experience featuring over 100 unique product pages, intuitive navigation and tailored functionality designed around the brand and its customers.', 'Connected capabilities', ['Custom Shopify Development', 'Product Pages', 'Storefront Engineering', 'Customer Experience', 'Conversion Optimization'], 'Standard ecommerce experiences and limited customization.', 'A custom-built shopping experience designed around the brand and its products.', '/case-studies?service=Ecommerce+Solutions', ['Stock theme', 'Plugins', 'Generic pages', 'Slow checkout'], ['Storefront', '100+ Product Pages', 'Navigation', 'Brand Story', 'Checkout', 'Analytics'], 'ecommerce', 'A rail of black, grey and white T-shirts on hangers'],
    ['mint', 'Healthcare', 'Connecting Multi-Branch Healthcare Operations Through Technology.', 'Digital and operational solutions supporting a multi-branch physiotherapy business through online presence, marketing and operational technology initiatives.', 'Connected capabilities', ['Healthcare Systems', 'Patient Management', 'Multi-Branch Operations', 'Digital Presence', 'Workflow Solutions'], 'Separate operational and digital activities.', 'More connected digital and operational workflows.', $bsCase('from-fragmented-clinic-operations-to-a-unified-multi-branch-physiotherapy-management-system', '/case-studies'), ['Branch A', 'Branch B', 'Separate ads', 'Paper records'], ['Patients', 'Appointments', 'Branches', 'Digital Presence', 'Marketing', 'Workflows'], 'healthcare', 'A physiotherapist fitting a wrist brace on a patient\'s arm'],
];

// 08 · composer prompts
$bsPrompts = ['We use too many disconnected tools', 'We need a custom ERP', 'Automate our follow-ups', 'Connect our departments and branches'];

/*
 * The business fingerprint (01 and 08). Eighteen ridges round a core, each a
 * slightly warped loop broken in one or two places, clipped to a fingertip
 * oval. Seeded, so it is the same drawing on every visit. Ridge 8 is left out
 * of the grey set and drawn whole in green: the workflow the six modules sit on.
 * Coordinates are in a 400 × 480 box.
 */
$bzPrint = (function (): array {
    $cx = 200; $cy = 236; $ox = 184; $oy = 228; $steps = 96;
    $rnd = fn (float $s): float => fmod(abs(sin($s * 12.9898) * 43758.5453), 1.0);
    $pt = function (int $i, float $t) use ($cx, $cy): array {
        $r = 10 + $i * 12.4;
        $f = 1 + 0.055 * sin(2 * $t + 0.33 * $i) + 0.03 * sin(3 * $t - 0.21 * $i);
        return [$cx + $r * 0.9 * $f * cos($t), $cy + $i * 1.4 + $r * 1.1 * $f * sin($t)];
    };
    $f1 = fn (float $v): string => rtrim(rtrim(number_format($v, 1, '.', ''), '0'), '.');
    $ridges = [];
    for ($i = 0; $i < 18; $i++) {
        if ($i === 8) {
            continue;
        }
        $gaps = [[$rnd($i + 1.3) * 2 * M_PI, 0.14 + $rnd($i + 7.1) * 0.2]];
        if ($i % 3 === 1) {
            $gaps[] = [$rnd($i + 13.7) * 2 * M_PI, 0.1 + $rnd($i + 3.3) * 0.14];
        }
        $seg = [];
        for ($k = 0; $k <= $steps; $k++) {
            $t = -M_PI / 2 + $k * 2 * M_PI / $steps;
            [$x, $y] = $pt($i, $t);
            $skip = (($x - $cx) / $ox) ** 2 + (($y - $cy) / $oy) ** 2 > 1;
            foreach ($gaps as [$g, $w]) {
                if (abs(fmod($t - $g + 3 * M_PI, 2 * M_PI) - M_PI) < $w / 2) $skip = true;
            }
            if ($skip) {
                if (count($seg) > 2) $ridges[] = 'M' . implode('L', $seg);
                $seg = [];
                continue;
            }
            $seg[] = $f1($x) . ' ' . $f1($y);
        }
        if (count($seg) > 2) $ridges[] = 'M' . implode('L', $seg);
    }
    $hi = [];
    for ($k = 0; $k < $steps; $k++) {
        [$x, $y] = $pt(8, -M_PI / 2 + $k * 2 * M_PI / $steps);
        $hi[] = $f1($x) . ' ' . $f1($y);
    }
    $nodes = [];
    for ($n = 0; $n < 6; $n++) {
        [$x, $y] = $pt(8, -M_PI / 2 + $n * M_PI / 3);
        $nodes[] = [round($x / 4, 2), round($y / 4.8, 2)];
    }
    return ['ridges' => $ridges, 'hi' => 'M' . implode('L', $hi) . 'Z', 'nodes' => $nodes];
})();
$bzRidges = function (bool $draw) use ($bzPrint): string {
    $out = '';
    foreach ($bzPrint['ridges'] as $i => $d) {
        $out .= '<path d="' . $d . '"' . ($draw ? ' pathLength="1" style="--i:' . $i . '"' : '') . '/>';
    }
    return $out;
};

/*
 * The department ring (04): twelve points round a circle in a 1200 × 800 box,
 * labels a little further out, and a chord for every pair that shares work.
 */
$bzRing = [];
foreach ($bsDepts as $k => [$key]) {
    $a = deg2rad(-90 + $k * 30);
    $bzRing[$key] = ['k' => $k, 'x' => 600 + 300 * cos($a), 'y' => 400 + 300 * sin($a), 'lx' => 600 + 336 * cos($a), 'ly' => 400 + 336 * sin($a), 'c' => cos($a)];
}
$bzChords = [];
foreach ($bsLinks as $key => [$rel]) {
    foreach (explode(' ', $rel) as $to) {
        $pair = [$key, $to];
        sort($pair);
        $bzChords[implode('|', $pair)] = $pair;
    }
}
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/bizsys.css') ?>">
<script>document.documentElement.classList.add('js')</script>

<div class="h10 bz">
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol><symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol><symbol id="i-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></symbol><symbol id="i-sparkle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z" fill="currentColor" stroke="none"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z" fill="currentColor" stroke="none"/></symbol><symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2.2 2.2L15 10"/></symbol><symbol id="i-layers" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 12.5l9 5 9-5"/><path d="M3 17l9 5 9-5" opacity=".55"/></symbol><symbol id="i-briefcase" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7M3 13h18"/></symbol><symbol id="i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></symbol><symbol id="i-puzzle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h4a2 2 0 1 1 4 0h4v4a2 2 0 1 1 0 4v4H4v-4a2 2 0 1 0 0-4z"/></symbol><symbol id="i-chip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4M10.5 12h3"/></symbol><symbol id="i-trend" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol><symbol id="i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="3"/><path d="M3 10h18M16.5 14.5h1"/></symbol><symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></symbol><symbol id="i-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></symbol><symbol id="i-hardhat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17h18v2.5H3zM5 17a7 7 0 0 1 14 0M12 10V6.5M9 11V8.2M15 11V8.2"/></symbol><symbol id="i-stetho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v6a4 4 0 0 0 8 0V3M10 13v2.5a4 4 0 0 0 8 0V14"/><circle cx="18" cy="12" r="2"/></symbol><symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol><symbol id="i-db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></symbol><symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol><symbol id="i-workflow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/></symbol><symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></symbol><symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/></symbol><symbol id="i-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol><symbol id="i-gem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12l4 5-10 11L2 9z"/><path d="M2 9h20M9 4L7 9l5 11M15 4l2 5-5 11"/></symbol><symbol id="i-factory" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l6 3.5V10l6 3.5V5h4v16z"/><path d="M8 17h2M13 17h2"/></symbol><symbol id="i-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol><symbol id="i-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></symbol><symbol id="i-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/></symbol><symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/></symbol><symbol id="i-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v9a1.5 1.5 0 01-1.5 1.5H10l-4.5 4v-4h0A1.5 1.5 0 014 14.5z"/><path d="M8 9h8M8 12h5"/></symbol></defs></svg>
<a class="skip" href="#bs-top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>

<!-- 01 · Hero: the statement and the business fingerprint -->
<section class="bsh bz-hero" id="bs-top">
  <div class="wrap">
    <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Business Systems · Custom Software · Business OS</span>
    <h1 class="bz-hero__h1">Your Business Is Unique. <span class="bz-hero__h1b">Your Systems Should Work That Way.</span></h1>
    <div class="bz-hero__grid">
      <figure class="bz-print" aria-hidden="true">
        <svg class="bz-print__svg" viewBox="0 0 400 480" focusable="false">
          <g class="bz-print__ridges"><?= $bzRidges(true) ?></g>
          <path class="bz-print__hi" id="bz-flow" d="<?= $bzPrint['hi'] ?>" pathLength="1"/>
          <circle class="bz-print__dot" r="5"><animateMotion dur="14s" repeatCount="indefinite"><mpath href="#bz-flow"/></animateMotion></circle>
        </svg>
<?php foreach ($bsMods as $i => [$icon, $tint, $label]): [$x, $y] = $bzPrint['nodes'][$i]; ?>
        <span class="bz-print__mod t-<?= $tint ?>" style="left:<?= $x ?>%;top:<?= $y ?>%;--i:<?= $i ?>"><i><?= $bsIc($icon) ?></i><?= h($label) ?></span>
<?php endforeach; ?>
      </figure>
      <div class="bz-hero__copy">
        <p class="bz-hero__lede reveal" style="--d:.15s">Every business has its own workflows, departments, people and operational challenges. We understand how your business works and engineer custom technology that connects your operations, simplifies processes and gives you better control.</p>
        <p class="bz-hero__sub reveal" style="--d:.22s">From custom ERP and Business Operating Systems to CRM, AI and workflow automation, we build technology around your actual business requirements.</p>
        <div class="bz-hero__ctas reveal" style="--d:.28s">
          <a class="h10-btn h10-btn--dark" href="/book" data-book><span>Build Your Business System</span><?= $bsArrow ?></a>
          <a class="h10-btn bz-btn-line" href="#capabilities"><span>Explore Our Capabilities</span><?= $bsArrow ?></a>
        </div>
        <ul class="bz-stats reveal" style="--d:.34s" role="list">
          <li><b><span data-count="50">50</span>+</b><span>projects delivered</span></li>
          <li><b><span data-count="20">20</span>+</b><span>industries</span></li>
          <li><b>8-week</b><span>implementation program</span></li>
        </ul>
        <ul class="bz-principles reveal" style="--d:.4s" role="list" aria-label="Our engineering principles">
<?php foreach ($bsPrinciples as [$icon, $tint, $label]): ?>
          <li class="t-<?= $tint ?>"><i><?= $bsIc($icon) ?></i><?= h($label) ?></li>
<?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- 02 · The business problem: a bento, the disconnected scene beside six symptom cards -->
<section class="bz-prob" id="challenges">
  <div class="wrap">
    <div class="bz-head">
      <div>
        <span class="pill bz-pill t-rose reveal"><span class="pill__dot" aria-hidden="true"></span>The Challenges of Running a Growing Business</span>
        <h2 class="h2">Growing Your Business Shouldn't Mean Managing More Complexity.</h2>
      </div>
      <div class="bz-head__txt reveal" style="--d:.1s">
        <p class="lede">As your business grows, managing operations through disconnected software, spreadsheets and manual processes becomes increasingly difficult.</p>
        <a class="h10-btn h10-btn--dark" href="/book" data-book><span>Let's Fix Your Business Workflows</span><?= $bsArrow ?></a>
      </div>
    </div>
    <div class="bz-pains">
      <figure class="bz-pains__art reveal">
        <figcaption><span class="bz-pains__tag"><i aria-hidden="true"></i>How most growing businesses run today</span><b>Every team on its own island. Nothing connected.</b></figcaption>
        <img src="/assets/about/ai-before.webp" alt="Five people on separate islands, each with their own tool, and cables that connect to nothing" width="1500" height="1200" loading="lazy" decoding="async">
      </figure>
<?php foreach ($bsProblems as $i => [$icon, $title, $text, $tint, $sym]): ?>
      <article class="bz-pain t-<?= $tint ?> reveal" style="--d:<?= ($i % 2) * .08 ?>s">
        <div class="bz-pain__top"><span class="bz-pain__ic"><?= $bsIc($icon) ?></span><span class="bz-pain__n" aria-hidden="true"><?= $bsN($i) ?></span></div>
        <h3><?= h($title) ?></h3>
        <p><?= h($text) ?></p>
        <span class="bz-pain__sym"><i aria-hidden="true"></i><?= h($sym) ?></span>
      </article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 03 · What we engineer: one app window, the capabilities as its sidebar -->
<section class="bz-caps" id="capabilities">
  <div class="bz-stage">
    <div class="wrap">
      <div class="bz-head bz-head--center">
        <span class="pill bz-pill t-mint reveal"><span class="pill__dot" aria-hidden="true"></span>Custom Business Systems &amp; Software Development</span>
        <h2 class="h2">Everything Your Business Needs. <span class="grn">Engineered Around You.</span></h2>
        <p class="lede reveal" style="--d:.1s">We design and develop technology solutions that connect your departments, automate workflows and simplify business operations.</p>
      </div>
      <div class="bz-win reveal" data-bz-tour>
        <div class="bz-win__bar" aria-hidden="true"><span class="bz-win__dots"><i></i><i></i><i></i></span><b>Business OS</b><span class="bz-win__live"><i></i>Live</span></div>
        <div class="bz-win__body">
          <div class="bz-win__nav" role="tablist" aria-label="What we engineer" aria-orientation="vertical">
<?php foreach ($bsBuild as $i => [$icon, $tint, $title]): ?>
            <button type="button" role="tab" class="bz-win__tab t-<?= $tint ?>" id="bzc-t<?= $i ?>" aria-controls="bzc-p<?= $i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i ? ' tabindex="-1"' : '' ?>><i><?= $bsIc($icon) ?></i><span><?= h($title) ?></span><em aria-hidden="true"><?= $bsN($i) ?></em></button>
<?php endforeach; ?>
          </div>
<?php foreach ($bsBuild as $i => [$icon, $tint, $title, $text, $scr]): ?>
          <article class="bz-panel t-<?= $tint ?><?= $i === 0 ? ' is-on' : '' ?>" role="tabpanel" id="bzc-p<?= $i ?>" aria-labelledby="bzc-t<?= $i ?>">
            <div class="bz-scr bz-scr--<?= $scr ?>" aria-hidden="true">
<?php if ($scr === 'os'): ?>
              <div class="bz-scr__hub"><?= $bsIc('layers') ?><b>Business OS</b><small>6 modules connected</small></div>
<?php foreach ($bsMods as [$mi, $mt, $ml, $mv, $mu]): ?>
              <div class="bz-scr__mod t-<?= $mt ?>"><i><?= $bsIc($mi) ?></i><small><?= h($ml) ?></small><b><?= h($mv) ?></b><em><?= h($mu) ?></em></div>
<?php endforeach; ?>
<?php elseif ($scr === 'crm'): ?>
<?php foreach ([['New', 4, ['Construction', 'Retail', 'Clinic']], ['Qualified', 3, ['Manufacturing', 'D2C brand']], ['Won', 2, ['Jewellery']]] as [$col, $cnt, $cards]): ?>
              <div class="bz-scr__col"><small><?= $col ?> <em><?= $cnt ?></em></small><?php foreach ($cards as $c): ?><span><b><?= $c ?></b><i></i></span><?php endforeach; ?></div>
<?php endforeach; ?>
<?php elseif ($scr === 'gantt'): ?>
              <div class="bz-scr__weeks"><span></span><?php for ($w = 1; $w <= 6; $w++): ?><small>W<?= $w ?></small><?php endfor; ?></div>
<?php foreach ([['Site survey', 0, 22], ['Procurement', 14, 34], ['Structure', 30, 40], ['Interiors', 56, 30], ['Handover', 80, 18]] as [$task, $s, $w]): ?>
              <div class="bz-scr__row"><small><?= $task ?></small><span><i style="left:<?= $s ?>%;width:<?= $w ?>%"></i></span></div>
<?php endforeach; ?>
<?php elseif ($scr === 'hr'): ?>
              <div class="bz-scr__cal"><?php foreach (['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $d): ?><small><?= $d ?></small><?php endforeach; ?><?php for ($d = 0; $d < 28; $d++): ?><i class="<?= $d % 7 === 6 ? 'off' : (in_array($d, [9, 17], true) ? 'leave' : '') ?>"></i><?php endfor; ?></div>
              <div class="bz-scr__kpis"><span><b>86</b>on shift</span><span><b>4</b>on leave</span><span><b>98%</b>attendance</span></div>
<?php elseif ($scr === 'fin'): ?>
<?php foreach ([['INV-1042', '₹48,200', 'Due'], ['INV-1041', '₹1,26,000', 'Paid'], ['INV-1040', '₹32,750', 'Paid'], ['INV-1039', '₹64,500', 'Paid']] as [$inv, $amt, $st]): ?>
              <div class="bz-scr__inv"><small><?= $inv ?></small><b><?= $amt ?></b><em class="<?= $st === 'Due' ? 'due' : '' ?>"><?= $st ?></em></div>
<?php endforeach; ?>
              <div class="bz-scr__bar"><small>This month</small><span><i style="width:72%"></i></span><b>₹18.6L</b></div>
<?php else: ?>
              <div class="bz-scr__flow">
                <span class="bz-scr__node"><?= $bsIc('mail') ?>New enquiry</span>
                <span class="bz-scr__node bz-scr__node--ai"><?= $bsIc('sparkle') ?>AI qualifies</span>
                <span class="bz-scr__outs"><span class="bz-scr__node"><?= $bsIc('users') ?>CRM record</span><span class="bz-scr__node"><?= $bsIc('chat') ?>WhatsApp reply</span><span class="bz-scr__node"><?= $bsIc('check') ?>Task for sales</span></span>
              </div>
<?php endif; ?>
            </div>
            <div class="bz-panel__txt">
              <span class="bz-panel__n"><?= $bsN($i) ?> / <?= $bsN(count($bsBuild) - 1) ?></span>
              <h3><?= h($title) ?></h3>
              <p><?= h($text) ?></p>
            </div>
          </article>
<?php endforeach; ?>
        </div>
      </div>
      <div class="bz-caps__foot reveal">
        <p class="bz-fine">Product screens use sample data.</p>
        <a class="h10-btn h10-btn--green" href="#projects"><span>Explore What We Can Build</span><?= $bsArrow ?></a>
      </div>
    </div>
  </div>
</section>

<!-- 04 · Connected ecosystem: the department ring, then Leads → Profit -->
<section class="bz-eco" id="ecosystem">
  <div class="wrap">
    <div class="bz-head bz-head--center">
      <span class="pill bz-pill t-peri reveal"><span class="pill__dot" aria-hidden="true"></span>One Connected Business. Every Department Working Together.</span>
      <h2 class="h2">Connect Every Part of Your Business Through One System.</h2>
      <p class="lede reveal" style="--d:.1s">Your business shouldn't operate through disconnected departments and isolated applications. We engineer connected systems that allow information, responsibilities and workflows to move across your organization.</p>
    </div>
    <div class="bz-ring reveal" data-bz-ring>
      <svg class="bz-ring__svg" viewBox="0 0 1200 800" aria-hidden="true" focusable="false">
        <circle class="bz-ring__orbit" cx="600" cy="400" r="300"/>
<?php foreach ($bzChords as [$a, $b]): $p = $bzRing[$a]; $q = $bzRing[$b]; $mx = 600 + (($p['x'] + $q['x']) / 2 - 600) * .14; $my = 400 + (($p['y'] + $q['y']) / 2 - 400) * .14; ?>
        <path class="bz-ring__chord" data-a="<?= $a ?>" data-b="<?= $b ?>" d="M<?= round($p['x'], 1) ?> <?= round($p['y'], 1) ?>Q<?= round($mx, 1) ?> <?= round($my, 1) ?> <?= round($q['x'], 1) ?> <?= round($q['y'], 1) ?>"/>
<?php endforeach; ?>
<?php foreach ($bzRing as $key => $p): ?>
        <circle class="bz-ring__pt" data-k="<?= $key ?>" cx="<?= round($p['x'], 1) ?>" cy="<?= round($p['y'], 1) ?>" r="7"/>
<?php endforeach; ?>
      </svg>
      <div class="bz-ring__hub">
        <img src="/assets/about/ai-after.webp" alt="The same five people with green bridges joining every island to one shared hub" width="1500" height="1200" loading="lazy" decoding="async">
        <b>Business OS</b>
        <p class="bz-ring__cap" data-bz-cap aria-live="polite">Pick a department to see what it shares.</p>
      </div>
      <span class="bz-ring__k">Business Departments &amp; Operations</span>
      <ul class="bz-ring__depts" role="list">
<?php foreach ($bsDepts as [$key, $label, $tint]): $p = $bzRing[$key]; $side = $p['c'] > .2 ? 'r' : ($p['c'] < -.2 ? 'l' : 'c'); ?>
        <li class="bz-ring__d bz-ring__d--<?= $side ?>" style="left:<?= round($p['lx'] / 12, 2) ?>%;top:<?= round($p['ly'] / 8, 2) ?>%"><button type="button" class="t-<?= $tint ?>" data-dept="<?= $key ?>" data-rel="<?= h($bsLinks[$key][0]) ?>" data-cap="<?= h($bsLinks[$key][1]) ?>" aria-pressed="false"><i aria-hidden="true"></i><?= h($label) ?></button></li>
<?php endforeach; ?>
      </ul>
    </div>
    <div class="bz-flow reveal">
      <div class="bz-flow__copy"><span class="bz-kick">From Leads to Profit</span><p>Connect customer acquisition, sales, project execution, team activities, operational expenses and financial performance through structured business workflows.</p></div>
      <ol class="bz-flow__line" role="list">
<?php foreach ($bsFlow as $i => $step): ?>
        <li style="--i:<?= $i ?>"><span><?= h($step) ?></span></li>
<?php endforeach; ?>
      </ol>
    </div>
    <div class="bz-center reveal"><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Build Your Connected Business</span><?= $bsArrow ?></a></div>
  </div>
</section>

<!-- 05 · Engineering approach: four cards that stack as you scroll -->
<section class="bz-appr" id="approach">
  <div class="wrap">
    <div class="bz-head">
      <div>
        <span class="pill bz-pill t-lilac reveal"><span class="pill__dot" aria-hidden="true"></span>Working With Your Team. Building for Your Business.</span>
        <h2 class="h2">We Don't Adapt Your Business to Software. We Engineer Software Around Your Business.</h2>
      </div>
      <div class="bz-head__txt reveal" style="--d:.1s"><p class="lede">Every business operates differently. That's why we work directly with your team to understand existing processes, identify challenges and engineer technology around your actual business requirements.</p><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Discuss Your Business Requirements</span><?= $bsArrow ?></a></div>
    </div>
    <ol class="bz-stack" role="list">
<?php foreach ($bsSteps as $i => [$label, $title, $text, $tags]): ?>
      <li class="bz-card t-<?= $bsStepTint[$i] ?>" style="--i:<?= $i ?>">
        <div class="bz-card__side">
          <span class="bz-card__n" aria-hidden="true"><?= $bsN($i) ?></span>
          <span class="bz-card__k"><i><?= $bsIc($bsStepIc[$i]) ?></i><?= h($label) ?></span>
        </div>
        <div class="bz-card__main">
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
          <ul class="bz-card__tags" role="list"><?php foreach (explode(' · ', $tags) as $tag): ?><li><?= h($tag) ?></li><?php endforeach; ?></ul>
        </div>
      </li>
<?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- 06 · Industries: two businesses in one industry side by side (≠), then the six industry cards -->
<section class="bz-ind" id="industries">
  <div class="wrap">
    <div class="bz-head">
      <div>
        <span class="pill bz-pill t-peach reveal"><span class="pill__dot" aria-hidden="true"></span>Same Industry ≠ Same Business Challenges</span>
        <h2 class="h2">We Don't Build for Industries. We Build for Businesses.</h2>
      </div>
      <div class="bz-head__txt reveal" style="--d:.1s">
        <p class="lede">Even two businesses within the same industry can have completely different workflows, organizational structures and operational challenges.</p>
        <p class="lede lede--strong">We understand your business, study your processes and engineer technology around your specific requirements.</p>
      </div>
    </div>
    <div class="bz-neq reveal" role="img" aria-label="Example: two construction companies. One runs three live sites with sub-contractors and weekly billing; the other runs from one head office with an in-house crew and milestone billing. Same industry, different workflows.">
<?php foreach ([['sky', 'Business A', '3 live sites, 40 people', ['Site-wise budgets', 'Sub-contractors', 'Weekly billing', 'Tally + Excel']], ['lilac', 'Business B', '1 head office, 120 people', ['Fixed-price bids', 'In-house crew', 'Milestone billing', 'Zoho + WhatsApp']]] as $n => [$tint, $name, $meta, $chips]): ?>
<?php if ($n): ?>
      <span class="bz-neq__sign" aria-hidden="true">≠</span>
<?php endif; ?>
      <div class="bz-neq__biz t-<?= $tint ?>" aria-hidden="true">
        <div class="bz-neq__hd"><i><?= $bsIc('hardhat') ?></i><span><b><?= $name ?></b><small>Construction · <?= $meta ?></small></span></div>
        <ul role="list"><?php foreach ($chips as $c): ?><li><?= $c ?></li><?php endforeach; ?></ul>
      </div>
<?php endforeach; ?>
      <p class="bz-neq__cap" aria-hidden="true"><span>Example</span>Same industry. Different workflows. Different system.</p>
    </div>
    <ul class="bz-inds" role="list">
<?php foreach ($bsInd as $i => [$icon, $tint, $ind, $q, $text, $cta]): ?>
      <li class="bz-indc t-<?= $tint ?> reveal" style="--d:<?= ($i % 3) * .07 ?>s">
        <div class="bz-indc__top"><i class="bz-indc__ic"><?= $bsIc($icon) ?></i><small><?= h($ind) ?></small><span class="bz-indc__n" aria-hidden="true"><?= $bsN($i) ?></span></div>
        <h3><?= h($q) ?></h3>
        <p><?= h($text) ?></p>
        <a class="bz-indc__cta" href="/book" data-book><span><?= h($cta) ?></span><i><?= $bsIc('arrow') ?></i></a>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- 07 · Selected projects: zig-zag rows, photo with From → To beside the story -->
<section class="bz-work" id="projects">
  <div class="wrap">
    <div class="bz-head bz-head--center">
      <span class="pill bz-pill t-sky reveal"><span class="pill__dot" aria-hidden="true"></span>Real Businesses. Real Challenges. Real Solutions.</span>
      <h2 class="h2">We Understood. We Engineered. We Made It Work.</h2>
      <p class="lede reveal" style="--d:.1s">Explore how we've understood different business requirements and engineered technology solutions that work in real business environments.</p>
    </div>
    <div class="bz-cases">
<?php foreach ($bsProjects as $i => [$tint, $label, $title, $text, $chipsLabel, $chips, $from, $to, $href, $fromTiles, $toMods, $photo, $alt]): ?>
      <article class="bz-case t-<?= $tint ?><?= $i % 2 ? ' bz-case--flip' : '' ?>">
        <figure class="bz-case__fig reveal">
          <img src="/assets/home10/work/<?= $photo ?>.webp" alt="<?= h($alt) ?>" width="1000" height="1000" loading="lazy" decoding="async">
          <div class="bz-ft" aria-hidden="true">
            <div class="bz-ft__from"><small>From</small><?php foreach ($fromTiles as $t): ?><s><?= h($t) ?></s><?php endforeach; ?></div>
            <span class="bz-ft__arr"><?= $bsIc('arrow') ?></span>
            <div class="bz-ft__to"><small>To</small><?php foreach ($toMods as $t): ?><span><?= h($t) ?></span><?php endforeach; ?></div>
          </div>
        </figure>
        <div class="bz-case__copy reveal" style="--d:.08s">
          <span class="bz-kick">Project <?= $bsN($i) ?> · <?= h($label) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
          <dl class="bz-case__ft">
            <div><dt>From</dt><dd><?= h($from) ?></dd></div>
            <div><dt>To</dt><dd><?= h($to) ?></dd></div>
          </dl>
          <span class="bz-case__cl"><?= h($chipsLabel) ?></span>
          <ul class="bz-case__chips" role="list"><?php foreach ($chips as $chip): ?><li><?= h($chip) ?></li><?php endforeach; ?></ul>
          <a class="h10-btn h10-btn--dark" href="<?= h($href) ?>"><span>Explore Project</span><?= $bsArrow ?></a>
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="bz-nda reveal"><span>Confidential · NDA</span>Client names and identities remain confidential, as every engagement runs under applicable Non-Disclosure Agreements (NDAs).</p>
  </div>
</section>

<!-- 08 · Final CTA: the same light full-width close as the home page -->
<section class="final final--card" id="contact">
  <div class="wrap">
    <div class="final__card">
      <div class="final__in">
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Let's Build Your Business System</span>
        <h2 class="h1x reveal" style="--d:.06s">Your Business Deserves More Than <span class="grn">Off-the-Shelf Software.</span></h2>
        <p class="lede lede--light reveal" style="--d:.1s">Tell us how your business operates, what challenges your teams face and what you want to improve.</p>
        <p class="final__line reveal" style="--d:.14s">Our engineers will work with you to understand your requirements and explore the right technology solution for your business.</p>
        <div class="final__ctas reveal" style="--d:.18s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Build Your Business System</span><?= $bsArrow ?></a><a class="h10-btn h10-btn--ghost" href="/book" data-book><span>Book a Strategy Call</span><?= $bsArrow ?></a></div>
        <p class="bz-sign">Drawlead — Business Systems &amp; Technology Company</p>
      </div>
    </div>
  </div>
</section>
</main><footer class="foot">
  <?php $signClass = 'wrap'; include __DIR__ . '/partials/footer-signoff.php'; ?>
  <div class="wrap foot__in">
    <a href="#bs-top" aria-label="Drawlead – back to top"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
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
<script src="<?= asset_url('/assets/bizsys.js') ?>" defer></script>
