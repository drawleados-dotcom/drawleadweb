<?php
/**
 * Static content for the 7 Platform module pages (Management, Sales,
 * Marketing, Operations, Finance, HR, Inventory Management) and the
 * Platform mega menu. Hand-maintained, like Home/About Us — not
 * admin-editable content. Icons and core descriptions for the first 6
 * mirror the homepage's "7 Functions" section (templates/home-body.php
 * #functions) so the two stay consistent.
 */

/** @return array<string,array> keyed by URL slug segment, e.g. 'sales' → /platform-sales */
function platform_modules(): array
{
    static $modules = null;
    if ($modules !== null) {
        return $modules;
    }

    $modules = [
        'management' => [
            'number' => '01',
            'name' => 'Management',
            'tagline' => 'One dashboard for how the business is actually doing',
            'description' => 'Centralized dashboards and operational visibility for faster, smarter business decisions — pulled live from every other module, not a weekly export.',
            'color' => '#32b46f',
            'icon' => '<svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="4" y="25" width="7" height="11" rx="1.5" fill="rgba(255,255,255,.5)"/><rect x="14" y="17" width="7" height="19" rx="1.5" fill="rgba(255,255,255,.75)"/><rect x="24" y="9" width="7" height="27" rx="1.5" fill="white"/><polyline points="6,21 17,13 27,5" fill="none" stroke="rgba(255,255,255,.6)" stroke-width="2" stroke-linecap="round"/><polygon points="27,2 33,8 21,8" fill="rgba(255,255,255,.7)"/></svg>',
            'features' => ['KPI Tracking', 'Real-time Analytics', 'Approval Workflows', 'Custom Dashboards', 'Role-based Visibility', 'Audit Trails'],
            'pain_points' => [
                'Decisions made on gut feel because the real numbers live in five different spreadsheets',
                "Approvals stuck in someone's inbox for days with no visibility into where they're stuck",
                'No single view of how the business actually performed this week',
            ],
            'connects' => [
                ['module' => 'Sales', 'text' => 'Pipeline and revenue numbers roll straight into your dashboards — no manual copy-paste.'],
                ['module' => 'Finance', 'text' => 'Expense and billing data feeds the same KPI view finance already trusts.'],
                ['module' => 'Operations', 'text' => 'Workflow bottlenecks show up on the dashboard the moment they happen.'],
            ],
            'board1_stats' => [['v' => '₹18.4L', 'l' => 'Revenue'], ['v' => '12', 'l' => 'Approvals Pending'], ['v' => '96%', 'l' => 'On-Time']],
            'board1_bars' => [45, 65, 40, 85, 60, 95],
            'board2_rows' => [
                ['label' => 'Q3 Budget Review', 'status' => 'Approved', 'tone' => 'good'],
                ['label' => 'Regional Sales Report', 'status' => 'In Review', 'tone' => 'pending'],
                ['label' => 'Vendor Contract Renewal', 'status' => 'Pending', 'tone' => 'pending'],
                ['label' => 'Hiring Plan FY26', 'status' => 'Approved', 'tone' => 'good'],
            ],
        ],
        'sales' => [
            'number' => '02',
            'name' => 'Sales',
            'tagline' => 'Every lead, pipeline, and invoice in one place',
            'description' => 'Manage leads, pipelines, customers, and revenue operations from one unified platform — instead of a CRM that never quite matches what finance is seeing.',
            'color' => '#32b46f',
            'icon' => '<svg width="26" height="26" viewBox="0 0 40 40" fill="none"><polyline points="4,30 14,18 22,23 36,8" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><polyline points="4,35 14,23 22,28 36,13" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="36" cy="13" r="4" fill="white"/></svg>',
            'features' => ['CRM', 'Pipeline Management', 'Invoicing', 'Lead Scoring', 'Follow-up Automation', 'Customer History'],
            'pain_points' => [
                'Leads going cold because follow-up depends on someone remembering to call back',
                "No visibility into where a deal actually is until it's already lost",
                'Invoices and customer history scattered across chat, email, and spreadsheets',
            ],
            'connects' => [
                ['module' => 'Marketing', 'text' => 'Leads captured by campaigns land directly in the pipeline — no manual handoff.'],
                ['module' => 'Finance', 'text' => 'Won deals become invoices automatically, with no re-entry.'],
                ['module' => 'Management', 'text' => "Every rep's pipeline rolls up into one dashboard, not seven exports."],
            ],
            'board1_stats' => [['v' => '₹24.6L', 'l' => 'Pipeline Value'], ['v' => '186', 'l' => 'Open Deals'], ['v' => '42%', 'l' => 'Win Rate']],
            'board1_bars' => [55, 40, 70, 50, 90, 65],
            'board2_rows' => [
                ['label' => 'Acme Textiles', 'status' => 'Proposal Sent', 'tone' => 'pending'],
                ['label' => 'Sunrise Retail', 'status' => 'Negotiation', 'tone' => 'pending'],
                ['label' => 'Kiran Traders', 'status' => 'Won', 'tone' => 'good'],
                ['label' => 'Metro Foods', 'status' => 'Follow-up Due', 'tone' => 'warn'],
            ],
        ],
        'marketing' => [
            'number' => '03',
            'name' => 'Marketing',
            'tagline' => 'Campaigns, WhatsApp, and email — one nurturing engine',
            'description' => 'Track campaigns, automate WhatsApp & email, and improve customer engagement at scale, with every lead tagged back to the campaign that produced it.',
            'color' => '#14855a',
            'icon' => '<svg width="26" height="26" viewBox="0 0 40 40" fill="none"><path d="M5 14 L5 26 L11 26 L11 14 Z" fill="rgba(255,255,255,.6)"/><path d="M11 14 L30 6 L30 34 L11 26 Z" fill="white"/><path d="M11 18 L11 22 L8 28 L5 28 L5 22" fill="rgba(255,255,255,.4)"/><path d="M32 15 Q38 20 32 25" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2.5" stroke-linecap="round"/></svg>',
            'features' => ['Campaign Tracking', 'WhatsApp Automation', 'Email Nurturing', 'Lead Attribution', 'Engagement Analytics', 'Response Time Tracking'],
            'pain_points' => [
                "Leads reply on WhatsApp and sit unanswered for hours because there's no shared inbox",
                'No way to tell which campaign actually produced a paying customer',
                'Nurture sequences run from memory instead of an actual system',
            ],
            'connects' => [
                ['module' => 'Sales', 'text' => "Every campaign lead drops straight into a rep's pipeline, tagged with its source."],
                ['module' => 'Operations', 'text' => 'Automated WhatsApp replies use the same customer record operations already has.'],
                ['module' => 'Management', 'text' => 'Campaign performance sits on the same dashboard as revenue, not a separate ads login.'],
            ],
            'board1_stats' => [['v' => '342', 'l' => 'New Leads'], ['v' => '<5 min', 'l' => 'Response Time'], ['v' => '18%', 'l' => 'Conversion']],
            'board1_bars' => [35, 55, 45, 75, 60, 80],
            'board2_rows' => [
                ['label' => 'Diwali Campaign', 'status' => 'Live', 'tone' => 'good'],
                ['label' => 'WhatsApp Broadcast', 'status' => 'Scheduled', 'tone' => 'pending'],
                ['label' => 'Landing Page A/B Test', 'status' => 'Running', 'tone' => 'good'],
                ['label' => 'Email Sequence', 'status' => 'Draft', 'tone' => 'pending'],
            ],
        ],
        'operations' => [
            'number' => '04',
            'name' => 'Operations',
            'tagline' => 'Workflows, inventory, and vendors that run themselves',
            'description' => 'Streamline activities, inventory, and vendor management with intelligent process automation — so the same job runs the same way every time, for everyone.',
            'color' => '#14855a',
            'icon' => '<svg width="26" height="26" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="7" fill="white"/><circle cx="20" cy="20" r="3.5" fill="rgba(124,45,18,.85)"/><rect x="18" y="3" width="4" height="7" rx="2" fill="rgba(255,255,255,.85)"/><rect x="18" y="30" width="4" height="7" rx="2" fill="rgba(255,255,255,.85)"/><rect x="3" y="18" width="7" height="4" rx="2" fill="rgba(255,255,255,.85)"/><rect x="30" y="18" width="7" height="4" rx="2" fill="rgba(255,255,255,.85)"/><rect x="7.5" y="7.5" width="4" height="7" rx="2" transform="rotate(45 9.5 11)" fill="rgba(255,255,255,.55)"/><rect x="28.5" y="7.5" width="4" height="7" rx="2" transform="rotate(-45 30.5 11)" fill="rgba(255,255,255,.55)"/></svg>',
            'features' => ['Workflow Automation', 'Inventory Tracking', 'Vendor Management', 'Purchase Orders', 'Stock Alerts', 'Process Templates'],
            'pain_points' => [
                'Stock counts that are only accurate the day someone last did a manual count',
                "Vendor orders tracked in a notebook or a chat thread that's easy to lose",
                'The same process done a different way by every person who runs it',
            ],
            'connects' => [
                ['module' => 'Sales', 'text' => 'Stock availability is visible before a rep promises a delivery date.'],
                ['module' => 'Finance', 'text' => 'Purchase orders and vendor bills flow straight into expense tracking.'],
                ['module' => 'Inventory Management', 'text' => 'Every purchase order updates stock the moment it arrives.'],
            ],
            'board1_stats' => [['v' => '842', 'l' => 'Orders'], ['v' => '6', 'l' => 'Stock Alerts'], ['v' => '96%', 'l' => 'On-Time']],
            'board1_bars' => [60, 45, 80, 55, 70, 90],
            'board2_rows' => [
                ['label' => 'PO #2291', 'status' => 'Awaiting Vendor', 'tone' => 'pending'],
                ['label' => 'Stock Recount — Warehouse B', 'status' => 'Scheduled', 'tone' => 'pending'],
                ['label' => 'Vendor SLA Review', 'status' => 'This Week', 'tone' => 'warn'],
                ['label' => 'Process Template v3', 'status' => 'Published', 'tone' => 'good'],
            ],
        ],
        'finance' => [
            'number' => '05',
            'name' => 'Finance',
            'tagline' => 'Billing, expenses, and reporting that reconcile themselves',
            'description' => 'Centralize billing, expenses, financial reporting, and accounting integrations seamlessly — with one number everyone in the business agrees on.',
            'color' => '#32b46f',
            'icon' => '<svg width="26" height="26" viewBox="0 0 40 40" fill="none"><ellipse cx="20" cy="11" rx="13" ry="5" fill="white"/><path d="M7 11 Q7 18 20 18 Q33 18 33 11" fill="rgba(255,255,255,.75)"/><path d="M7 18 Q7 25 20 25 Q33 25 33 18" fill="rgba(255,255,255,.5)"/><path d="M7 25 Q7 32 20 32 Q33 32 33 25" fill="rgba(255,255,255,.3)"/></svg>',
            'features' => ['Billing', 'Expense Tracking', 'Financial Reporting', 'Accounting Integrations', 'GST Workflows', 'Payment Reminders'],
            'pain_points' => [
                'Month-end close that takes a week because numbers live in three different tools',
                'Expenses submitted on paper, WhatsApp, or not submitted at all',
                "No real-time view of cash position — just last month's export",
            ],
            'connects' => [
                ['module' => 'Sales', 'text' => 'Invoices generate themselves the moment a deal is marked won.'],
                ['module' => 'Operations', 'text' => 'Vendor bills and purchase orders reconcile automatically.'],
                ['module' => 'Management', 'text' => 'Every finance number is the same number leadership sees on the dashboard.'],
            ],
            'board1_stats' => [['v' => '₹18.4L', 'l' => 'Revenue'], ['v' => '₹6.2L', 'l' => 'Expenses'], ['v' => '₹3.1L', 'l' => 'Outstanding']],
            'board1_bars' => [50, 70, 45, 85, 55, 75],
            'board2_rows' => [
                ['label' => 'Invoice #4471', 'status' => 'Paid', 'tone' => 'good'],
                ['label' => 'Vendor Bill #118', 'status' => 'Due in 3 Days', 'tone' => 'warn'],
                ['label' => 'GST Filing', 'status' => 'Draft', 'tone' => 'pending'],
                ['label' => 'Expense Report', 'status' => 'Awaiting Approval', 'tone' => 'pending'],
            ],
        ],
        'hr' => [
            'number' => '06',
            'name' => 'HR',
            'tagline' => 'Payroll, attendance, and leave without the spreadsheet',
            'description' => 'Manage employees, attendance, payroll workflows, and leave management efficiently — with one record per employee instead of five.',
            'color' => '#14855a',
            'icon' => '<svg width="26" height="26" viewBox="0 0 40 40" fill="none"><circle cx="14" cy="12" r="7" fill="white"/><circle cx="28" cy="14" r="5" fill="rgba(255,255,255,.6)"/><path d="M2 34 C2 25 8 22 14 22 C20 22 26 25 26 34 Z" fill="rgba(255,255,255,.8)"/><path d="M26 28 C26 24 29 22 32 22 C35 22 38 24 38 28 L38 34 L26 34 Z" fill="rgba(255,255,255,.4)"/></svg>',
            'features' => ['Payroll Processing', 'Attendance Tracking', 'Leave Management', 'Employee Records', 'Onboarding Workflows', 'Compliance Documentation'],
            'pain_points' => [
                'Attendance tracked on a register that someone has to manually total every month',
                'Leave requests approved over WhatsApp with no record anywhere else',
                'Payroll recalculated by hand every cycle, with room for costly mistakes',
            ],
            'connects' => [
                ['module' => 'Finance', 'text' => 'Approved payroll flows straight into monthly expense reporting.'],
                ['module' => 'Management', 'text' => 'Headcount and attendance trends sit on the same dashboard as revenue.'],
                ['module' => 'Operations', 'text' => "Attendance data lines up with the shifts people are actually running."],
            ],
            'board1_stats' => [['v' => '84', 'l' => 'Employees'], ['v' => '5', 'l' => 'On Leave'], ['v' => '97%', 'l' => 'Attendance']],
            'board1_bars' => [70, 65, 80, 60, 85, 75],
            'board2_rows' => [
                ['label' => 'Leave Request — Priya S.', 'status' => 'Approved', 'tone' => 'good'],
                ['label' => 'Payroll Run — November', 'status' => 'Processing', 'tone' => 'pending'],
                ['label' => 'Onboarding — New Hire', 'status' => 'Day 2', 'tone' => 'pending'],
                ['label' => 'Compliance Document', 'status' => 'Renewed', 'tone' => 'good'],
            ],
        ],
        'inventory' => [
            'number' => '07',
            'name' => 'Inventory Management',
            'tagline' => 'Stock levels you can trust, updated in real time',
            'description' => 'Track stock across every warehouse and channel, get alerted before you run out, and stop guessing what you actually have on hand.',
            'color' => '#23a065',
            'icon' => '<svg width="26" height="26" viewBox="0 0 40 40" fill="none"><rect x="5" y="18" width="13" height="14" rx="1.5" fill="rgba(255,255,255,.6)"/><rect x="21" y="18" width="14" height="14" rx="1.5" fill="white"/><rect x="12" y="6" width="16" height="13" rx="1.5" fill="rgba(255,255,255,.85)"/><path d="M12 12 L28 12" stroke="rgba(20,133,90,.55)" stroke-width="1.6"/><path d="M5 24 L18 24" stroke="rgba(20,133,90,.4)" stroke-width="1.6"/><path d="M21 24 L35 24" stroke="rgba(20,133,90,.4)" stroke-width="1.6"/></svg>',
            'features' => ['Real-time Stock Levels', 'Multi-warehouse Tracking', 'Reorder Point Alerts', 'Barcode & SKU Management', 'Batch/Lot Tracking', 'Stock Transfer Workflows'],
            'pain_points' => [
                "Selling what's already out of stock because the count online doesn't match the shelf",
                "Reordering too late, or too early, because nobody's watching reorder points",
                'Stock spread across two warehouses with no single view of total quantity',
            ],
            'connects' => [
                ['module' => 'Sales', 'text' => "Real stock counts stop reps from promising deliveries you can't make."],
                ['module' => 'Operations', 'text' => 'Purchase orders and vendor lead times feed straight into reorder alerts.'],
                ['module' => 'Finance', 'text' => 'Stock value updates the books without a manual recount.'],
            ],
            'board1_stats' => [['v' => '6,240', 'l' => 'Units in Stock'], ['v' => '14', 'l' => 'Reorder Alerts'], ['v' => '3', 'l' => 'Warehouses']],
            'board1_bars' => [55, 70, 40, 85, 60, 75],
            'board2_rows' => [
                ['label' => 'SKU-1042', 'status' => 'Reorder Threshold Hit', 'tone' => 'warn'],
                ['label' => 'Warehouse B Transfer', 'status' => 'In Transit', 'tone' => 'pending'],
                ['label' => 'Batch #2291', 'status' => 'Received', 'tone' => 'good'],
                ['label' => 'Stock Audit — Warehouse A', 'status' => 'Scheduled', 'tone' => 'pending'],
            ],
        ],
    ];

    return $modules;
}

/** Ordered list of [key, module] pairs — used by both the mega menu and the sitemap-ish loops. */
function platform_modules_ordered(): array
{
    $modules = platform_modules();
    uasort($modules, fn ($a, $b) => $a['number'] <=> $b['number']);
    $out = [];
    foreach ($modules as $key => $m) {
        $out[] = ['key' => $key, 'module' => $m];
    }
    return $out;
}
