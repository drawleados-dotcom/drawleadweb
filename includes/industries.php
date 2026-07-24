<?php
/**
 * Static content for the 20 Industries pages and the homepage #industries
 * grid. Hand-maintained, like Home/About Us — not admin-editable content.
 * The first 6 mirror the existing homepage cards (templates/home-body.php
 * #industries) so the two stay consistent; the section itself now loops
 * over this file instead of hardcoding each card.
 */

/** @return array<string,array> keyed by URL slug segment, e.g. 'construction' → /industry-construction */
function industries(): array
{
    static $all = null;
    if ($all !== null) {
        return $all;
    }

    $all = [
        'construction' => [
            'number' => '01',
            'name' => 'Construction & Real Estate',
            'tag' => 'Projects · Sites · Billing',
            'tagline' => 'One dashboard for every site, every project',
            'description' => 'Run multi-site construction and real estate operations from a single system — instead of a different spreadsheet for every project.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="4" y="28" width="32" height="8" rx="2" fill="rgba(255,255,255,.9)"/><rect x="8" y="16" width="10" height="12" rx="1" fill="rgba(255,255,255,.7)"/><rect x="22" y="20" width="10" height="8" rx="1" fill="rgba(255,255,255,.5)"/><polygon points="2,28 20,8 38,28" fill="rgba(255,255,255,.3)"/><polygon points="8,28 20,14 32,28" fill="rgba(255,255,255,.2)"/></svg>',
            'problems' => ['Project overruns with no visibility', 'Manual contractor billing errors', 'Siloed site & inventory data'],
            'solutions' => ['Real-time multi-site project dashboard', 'Automated contractor & billing workflows', 'Unified inventory & vendor management'],
            'outcomes' => ['Faster Site Reporting', 'Fewer Billing Errors', 'One View Across All Sites'],
        ],
        'healthcare' => [
            'number' => '02',
            'name' => 'Healthcare & Wellness',
            'tag' => 'Clinics · Appointments · Billing',
            'tagline' => 'Every clinic, every appointment, in sync',
            'description' => 'Run clinics and wellness centers where scheduling, billing, and patient follow-ups never depend on a phone call.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="4" y="4" width="32" height="32" rx="6" fill="rgba(255,255,255,.15)"/><rect x="16" y="8" width="8" height="24" rx="2" fill="rgba(255,255,255,.9)"/><rect x="8" y="16" width="24" height="8" rx="2" fill="rgba(255,255,255,.9)"/></svg>',
            'problems' => ['Scheduling conflicts across branches', 'Manual patient billing & follow-ups', 'No centralized therapist management'],
            'solutions' => ['Smart appointment scheduling system', 'Automated billing & WhatsApp reminders', 'Multi-branch centralized reporting'],
            'outcomes' => ['Fewer Scheduling Conflicts', 'Faster Patient Billing', 'One View Across Branches'],
        ],
        'manufacturing' => [
            'number' => '03',
            'name' => 'Manufacturing',
            'tag' => 'Production · Inventory · Quality',
            'tagline' => 'Production, inventory, and quality — one system',
            'description' => 'Track production runs, raw material stock, and quality checks from a single dashboard instead of a factory floor full of paper logs.',
            'color' => '#14855a',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="3" y="28" width="34" height="8" rx="2" fill="rgba(255,255,255,.8)"/><rect x="6" y="18" width="8" height="10" rx="1" fill="rgba(255,255,255,.6)"/><rect x="18" y="14" width="8" height="14" rx="1" fill="rgba(255,255,255,.7)"/><rect x="30" y="10" width="4" height="18" rx="1" fill="rgba(255,255,255,.5)"/><circle cx="10" cy="22" r="2" fill="rgba(255,255,255,.3)"/><rect x="14" y="6" width="3" height="8" rx="1.5" fill="rgba(255,255,255,.4)"/></svg>',
            'problems' => ['Production delays with no alerts', 'Raw material stockout surprises', 'Manual quality control records'],
            'solutions' => ['AI-powered production monitoring', 'Predictive inventory restocking alerts', 'Digital QC workflows & reporting'],
            'outcomes' => ['Fewer Production Delays', 'Zero Stockout Surprises', 'Faster QC Turnaround'],
        ],
        'agencies' => [
            'number' => '04',
            'name' => 'Marketing Agencies',
            'tag' => 'Projects · Clients · Delivery',
            'tagline' => 'Every client, every project, one hub',
            'description' => 'Run an agency where client projects, leads, and delivery timelines all live in one place instead of six different tools.',
            'color' => '#14855a',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><circle cx="20" cy="12" r="8" fill="rgba(255,255,255,.9)"/><circle cx="10" cy="28" r="6" fill="rgba(255,255,255,.65)"/><circle cx="30" cy="28" r="6" fill="rgba(255,255,255,.65)"/><line x1="14" y1="18" x2="12" y2="23" stroke="rgba(255,255,255,.6)" stroke-width="2"/><line x1="26" y1="18" x2="28" y2="23" stroke="rgba(255,255,255,.6)" stroke-width="2"/></svg>',
            'problems' => ['Client projects slipping deadlines', 'No single view of leads & revenue', 'Team scattered across tools'],
            'solutions' => ['Agency OS — all-in-one client hub', 'Unified CRM + project + billing view', 'Team tasks, timelines & reports'],
            'outcomes' => ['Fewer Missed Deadlines', 'One View of Leads & Revenue', 'Better Team Utilization'],
        ],
        'retail' => [
            'number' => '05',
            'name' => 'Retail & E-Commerce',
            'tag' => 'Orders · Inventory · CRM',
            'tagline' => 'Every order, every channel, one inventory',
            'description' => 'Sell across stores and online channels with stock, orders, and customers synced in real time — not reconciled at the end of the day.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><path d="M6 10 L10 4 L30 4 L34 10 Z" fill="rgba(255,255,255,.9)"/><path d="M6 10 L6 34 Q6 36 8 36 L32 36 Q34 36 34 34 L34 10 Z" fill="rgba(255,255,255,.7)"/><path d="M15 10 Q15 18 20 18 Q25 18 25 10" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linecap="round"/></svg>',
            'problems' => ['Inventory mismatches across stores', 'No customer retention system', 'Order tracking is fully manual'],
            'solutions' => ['Live inventory sync across all channels', 'Customer CRM + loyalty automation', 'End-to-end order management'],
            'outcomes' => ['Zero Channel Mismatches', 'Higher Repeat Purchase Rate', 'Faster Order Fulfilment'],
        ],
        'logistics' => [
            'number' => '06',
            'name' => 'Logistics & Transport',
            'tag' => 'Fleet · Delivery · Compliance',
            'tagline' => 'Every vehicle, every delivery, tracked',
            'description' => 'Track fleet, deliveries, and compliance documents from one dashboard instead of a driver group chat and a filing cabinet.',
            'color' => '#14855a',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="2" y="16" width="22" height="14" rx="2" fill="rgba(255,255,255,.85)"/><path d="M24 20 L34 20 L38 28 L38 30 L24 30 Z" fill="rgba(255,255,255,.65)"/><circle cx="10" cy="32" r="4" fill="rgba(255,255,255,.5)" stroke="rgba(255,255,255,.9)" stroke-width="2"/><circle cx="30" cy="32" r="4" fill="rgba(255,255,255,.5)" stroke="rgba(255,255,255,.9)" stroke-width="2"/><rect x="6" y="10" width="10" height="6" rx="1" fill="rgba(255,255,255,.4)"/></svg>',
            'problems' => ['No real-time fleet visibility', 'Manual delivery proof & billing', 'Compliance docs always missing'],
            'solutions' => ['Live fleet & delivery tracking dashboard', 'Digital POD & auto-invoicing', 'Compliance document automation'],
            'outcomes' => ['Full Fleet Visibility', 'Faster Delivery Billing', 'Zero Missing Compliance Docs'],
        ],
        'jewellery' => [
            'number' => '07',
            'name' => 'Jewellery & Gems',
            'tag' => 'Sales · Stock · Hallmarking',
            'tagline' => 'Every piece tracked, from vault to sale',
            'description' => 'Run a jewellery business where stock, purity, and billing are never a guessing game — synced across every counter and branch.',
            'color' => '#23a065',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><polygon points="20,4 30,14 20,36 10,14" fill="white"/><polygon points="10,14 20,4 30,14" fill="rgba(255,255,255,.6)"/><line x1="10" y1="14" x2="30" y2="14" stroke="rgba(20,133,90,.4)" stroke-width="1.5"/></svg>',
            'problems' => ["Stock counts that don't match what's in the vault", 'Manual hallmarking and purity records', 'No single view of fast-moving designs across branches'],
            'solutions' => ['Real-time inventory by weight, purity & design', 'Digital hallmarking and certificate records', 'Multi-branch stock and sales dashboard'],
            'outcomes' => ['Full Stock Traceability', 'Faster Billing at Every Counter', 'One View Across All Branches'],
        ],
        'education' => [
            'number' => '08',
            'name' => 'Education & Training',
            'tag' => 'Admissions · Fees · Attendance',
            'tagline' => 'Run the institute, not just the classroom',
            'description' => 'From admissions to fee collection to attendance, manage every part of running a school or training institute in one system.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="6" y="8" width="28" height="24" rx="2" fill="rgba(255,255,255,.85)"/><rect x="6" y="8" width="13" height="24" rx="2" fill="white"/><line x1="19" y1="8" x2="19" y2="32" stroke="rgba(20,133,90,.4)" stroke-width="1.5"/></svg>',
            'problems' => ['Fee collection tracked across registers and spreadsheets', 'Attendance recorded on paper, reconciled by hand', 'No visibility into admissions pipeline or dropouts'],
            'solutions' => ['Automated fee tracking and payment reminders', 'Digital attendance with instant parent notifications', 'Admissions pipeline with enquiry-to-enrollment tracking'],
            'outcomes' => ['Faster Fee Collection', 'Real-time Attendance Visibility', 'Higher Admission Conversion'],
        ],
        'hospitality' => [
            'number' => '09',
            'name' => 'Hospitality & Restaurants',
            'tag' => 'Orders · Tables · Inventory',
            'tagline' => 'Every table, order, and ingredient in sync',
            'description' => 'Manage orders, table turnover, and kitchen inventory from one dashboard instead of a POS, a notebook, and a supplier call sheet.',
            'color' => '#14855a',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><circle cx="20" cy="20" r="15" fill="rgba(255,255,255,.5)"/><circle cx="20" cy="20" r="9" fill="white"/><rect x="8" y="8" width="3" height="14" rx="1.5" fill="rgba(255,255,255,.9)"/><rect x="29" y="8" width="3" height="14" rx="1.5" fill="rgba(255,255,255,.9)"/></svg>',
            'problems' => ['Orders and billing handled on disconnected systems', 'Ingredient stock running out mid-service with no warning', 'No visibility into which items or tables perform best'],
            'solutions' => ['Unified order-to-billing workflow', 'Live kitchen inventory with reorder alerts', 'Table and menu performance reporting'],
            'outcomes' => ['Faster Table Turnover', 'Fewer Mid-Service Stockouts', 'Clear View of Best-Sellers'],
        ],
        'automotive' => [
            'number' => '10',
            'name' => 'Automotive & Auto Services',
            'tag' => 'Service · Parts · Billing',
            'tagline' => 'Every service bay and part, accounted for',
            'description' => 'Run a dealership or service center where job cards, spare parts, and billing all live in one place — not three.',
            'color' => '#23a065',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="4" y="18" width="32" height="10" rx="3" fill="white"/><path d="M9 18 L13 9 L27 9 L31 18" fill="rgba(255,255,255,.7)"/><circle cx="12" cy="30" r="4" fill="rgba(255,255,255,.9)"/><circle cx="28" cy="30" r="4" fill="rgba(255,255,255,.9)"/></svg>',
            'problems' => ["Spare parts stock that's never accurate when a job comes in", 'Job cards tracked on paper, easy to lose', "No record of a customer's full service history"],
            'solutions' => ['Real-time spare parts inventory', 'Digital job cards from check-in to billing', 'Complete customer and vehicle service history'],
            'outcomes' => ['Faster Turnaround per Vehicle', 'Fewer Parts Stockouts', 'Higher Repeat Service Visits'],
        ],
        'textile' => [
            'number' => '11',
            'name' => 'Textile & Apparel',
            'tag' => 'Production · Stock · Orders',
            'tagline' => 'From fabric to finished order, one system',
            'description' => 'Track raw material, production stages, and finished goods stock across every unit and showroom without a separate spreadsheet for each.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><path d="M14 6 L20 10 L26 6 L34 12 L29 18 L26 15 L26 34 L14 34 L14 15 L11 18 L6 12 Z" fill="white"/></svg>',
            'problems' => ['Raw material and finished stock tracked in different systems', 'No visibility into which production stage an order is at', "Showroom stock counts that don't match the warehouse"],
            'solutions' => ['End-to-end raw material to finished goods tracking', 'Production stage visibility on every order', 'Synced stock across warehouse and showrooms'],
            'outcomes' => ['Faster Order Fulfilment', 'Accurate Stock Everywhere', 'Fewer Production Delays'],
        ],
        'pharma' => [
            'number' => '12',
            'name' => 'Pharmaceuticals & Distribution',
            'tag' => 'Batches · Expiry · Compliance',
            'tagline' => 'Every batch tracked, every expiry flagged',
            'description' => 'Manage batch tracking, expiry alerts, and regulatory compliance across your entire distribution network from one system.',
            'color' => '#14855a',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="4" y="16" width="32" height="10" rx="5" fill="rgba(255,255,255,.85)"/><rect x="4" y="16" width="16" height="10" rx="5" fill="white"/><circle cx="30" cy="21" r="2" fill="rgba(20,133,90,.5)"/></svg>',
            'problems' => ['Expired stock discovered only during a physical audit', 'Batch and compliance records scattered across paper files', 'No real-time view of stock across distributors'],
            'solutions' => ['Automated batch and expiry tracking with alerts', 'Digital compliance and licensing records', 'Real-time distributor and stockist inventory view'],
            'outcomes' => ['Zero Expired Stock Surprises', 'Faster Compliance Audits', 'Full Distributor Visibility'],
        ],
        'professional' => [
            'number' => '13',
            'name' => 'Professional Services',
            'tag' => 'Clients · Billing · Delivery',
            'tagline' => 'Every client engagement, tracked end to end',
            'description' => 'Run a law firm, accounting practice, or consultancy where client work, billing, and deadlines are never scattered across inboxes.',
            'color' => '#23a065',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="5" y="14" width="30" height="20" rx="3" fill="white"/><path d="M15 14 L15 9 Q15 7 17 7 L23 7 Q25 7 25 9 L25 14" fill="none" stroke="rgba(255,255,255,.85)" stroke-width="2.5"/><rect x="5" y="20" width="30" height="4" fill="rgba(20,133,90,.35)"/></svg>',
            'problems' => ["Billable hours tracked in someone's memory or a notebook", "Client deadlines missed because nothing's centrally tracked", 'Invoicing delayed because work records are scattered'],
            'solutions' => ['Time and billing tracked against every engagement', 'Centralized deadline and deliverable tracking', 'Invoicing generated straight from tracked work'],
            'outcomes' => ['Faster Invoice Turnaround', 'Zero Missed Deadlines', 'Clear View of Firm Utilization'],
        ],
        'food-beverage' => [
            'number' => '14',
            'name' => 'Food & Beverage Manufacturing',
            'tag' => 'Batches · Quality · Inventory',
            'tagline' => 'Every batch, every ingredient, accounted for',
            'description' => 'Track raw ingredients, batch production, and quality checks across every shift, with full traceability from ingredient to finished product.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="15" y="4" width="10" height="8" rx="1" fill="rgba(255,255,255,.7)"/><path d="M14 12 L26 12 L28 18 L28 34 Q28 36 26 36 L14 36 Q12 36 12 34 L12 18 Z" fill="white"/></svg>',
            'problems' => ['Ingredient stock running low with no advance warning', 'Batch and quality records kept on paper checklists', 'No traceability from finished product back to ingredients'],
            'solutions' => ['Real-time ingredient stock with reorder alerts', 'Digital batch production and QC records', 'Full ingredient-to-product traceability'],
            'outcomes' => ['Fewer Production Stoppages', 'Faster Quality Audits', 'Full Batch Traceability'],
        ],
        'it-software' => [
            'number' => '15',
            'name' => 'IT & Software Services',
            'tag' => 'Projects · Resourcing · Billing',
            'tagline' => 'Every project and every hour, accounted for',
            'description' => 'Run a software or IT services company where project timelines, resource allocation, and client billing all live in one connected system.',
            'color' => '#14855a',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><path d="M14 10 L4 20 L14 30" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M26 10 L36 20 L26 30" fill="none" stroke="rgba(255,255,255,.75)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'problems' => ['Resource allocation tracked across spreadsheets per project', 'Project timelines slip with no early warning', "Billing delayed because hours aren't tracked centrally"],
            'solutions' => ['Centralized resource allocation across projects', 'Real-time project timeline and milestone tracking', 'Billing generated straight from tracked hours'],
            'outcomes' => ['Better Resource Utilization', 'Fewer Missed Milestones', 'Faster Client Billing'],
        ],
        'financial' => [
            'number' => '16',
            'name' => 'Financial Services & NBFCs',
            'tag' => 'Accounts · Compliance · Collections',
            'tagline' => 'Every account and every collection, tracked',
            'description' => 'Manage loan accounts, repayment tracking, and regulatory compliance from one system built for how NBFCs and financial services actually operate.',
            'color' => '#23a065',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><ellipse cx="20" cy="10" rx="14" ry="5" fill="white"/><path d="M6 10 L6 30 Q6 35 20 35 Q34 35 34 30 L34 10" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2.5"/><ellipse cx="20" cy="20" rx="14" ry="5" fill="rgba(255,255,255,.5)"/></svg>',
            'problems' => ['Repayment tracking scattered across spreadsheets and calls', 'Compliance documentation hard to pull together for audits', 'No single view of overdue accounts across branches'],
            'solutions' => ['Automated repayment tracking and reminders', 'Centralized compliance and audit documentation', 'Real-time overdue account dashboard across branches'],
            'outcomes' => ['Lower Overdue Rates', 'Faster Compliance Reporting', 'Full Branch-wise Visibility'],
        ],
        'agriculture' => [
            'number' => '17',
            'name' => 'Agriculture & Agri-Business',
            'tag' => 'Procurement · Stock · Distribution',
            'tagline' => 'From farm gate to warehouse, one view',
            'description' => 'Track procurement, storage, and distribution of agricultural produce across every warehouse and season without losing visibility between harvests.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><path d="M8 32 Q8 12 32 8 Q28 32 8 32 Z" fill="white"/><path d="M8 32 Q18 26 28 12" fill="none" stroke="rgba(20,133,90,.4)" stroke-width="1.5"/></svg>',
            'problems' => ['Procurement records tracked separately per season', 'Storage and spoilage losses with no early warning', 'No unified view of stock across warehouses'],
            'solutions' => ['Centralized procurement and farmer payment tracking', 'Storage condition and spoilage alerts', 'Unified warehouse stock and distribution dashboard'],
            'outcomes' => ['Lower Spoilage Losses', 'Faster Procurement Settlements', 'Full Warehouse Visibility'],
        ],
        'events' => [
            'number' => '18',
            'name' => 'Event Management',
            'tag' => 'Bookings · Vendors · Budgets',
            'tagline' => 'Every event, every vendor, on one timeline',
            'description' => 'Run an event management business where bookings, vendor coordination, and budgets are tracked in one place, not across a dozen chat threads.',
            'color' => '#14855a',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="6" y="8" width="28" height="26" rx="3" fill="white"/><rect x="6" y="8" width="28" height="7" fill="rgba(20,133,90,.55)"/><rect x="12" y="20" width="5" height="5" fill="rgba(20,133,90,.4)"/><rect x="22" y="20" width="5" height="5" fill="rgba(20,133,90,.4)"/></svg>',
            'problems' => ['Vendor coordination scattered across calls and chats', 'Event budgets tracked in spreadsheets that go stale fast', 'No central view of upcoming bookings and deadlines'],
            'solutions' => ['Centralized vendor and task coordination per event', 'Live budget tracking against every event', 'Unified booking calendar and deadline tracking'],
            'outcomes' => ['Fewer Vendor Miscommunications', 'Tighter Budget Control', 'Zero Missed Event Deadlines'],
        ],
        'beauty' => [
            'number' => '19',
            'name' => 'Beauty & Salon Chains',
            'tag' => 'Appointments · Stock · Staff',
            'tagline' => 'Every appointment and every product, tracked',
            'description' => 'Manage appointments, staff schedules, and product inventory across every branch of your salon or spa chain from one dashboard.',
            'color' => '#23a065',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><circle cx="12" cy="12" r="6" fill="none" stroke="white" stroke-width="3"/><circle cx="12" cy="28" r="6" fill="none" stroke="white" stroke-width="3"/><line x1="16" y1="16" x2="34" y2="34" stroke="white" stroke-width="3" stroke-linecap="round"/><line x1="16" y1="24" x2="34" y2="6" stroke="rgba(255,255,255,.75)" stroke-width="3" stroke-linecap="round"/></svg>',
            'problems' => ['Appointment bookings clash across branches and staff', 'Product stock running out mid-service with no warning', 'No visibility into staff performance across branches'],
            'solutions' => ['Unified appointment booking across all branches', 'Real-time product inventory with reorder alerts', 'Staff performance and revenue tracking per branch'],
            'outcomes' => ['Fewer Booking Conflicts', 'Fewer Mid-Service Stockouts', 'Clear View of Branch Performance'],
        ],
        'wholesale' => [
            'number' => '20',
            'name' => 'Wholesale & Distribution',
            'tag' => 'Orders · Stock · Dealers',
            'tagline' => 'Every dealer order and every unit, tracked',
            'description' => 'Manage dealer orders, stock allocation, and distribution logistics from one system instead of juggling order books and phone calls.',
            'color' => '#32b46f',
            'icon' => '<svg width="28" height="28" fill="none" viewBox="0 0 40 40"><rect x="4" y="18" width="32" height="16" fill="white"/><polygon points="2,18 20,6 38,18" fill="rgba(255,255,255,.75)"/><rect x="16" y="24" width="8" height="10" fill="rgba(20,133,90,.4)"/></svg>',
            'problems' => ['Dealer orders tracked over phone calls and notebooks', 'Stock allocation decisions made without real-time visibility', 'No clear view of dealer-wise sales and outstanding dues'],
            'solutions' => ['Digital dealer ordering and order tracking', 'Real-time stock allocation across warehouses', 'Dealer-wise sales and outstanding dashboard'],
            'outcomes' => ['Faster Order Processing', 'Better Stock Allocation', 'Clear View of Dealer Outstanding'],
        ],
    ];

    return $all;
}

/** Ordered list of [key, industry] pairs — used by both the homepage grid and the mega menu. */
function industries_ordered(): array
{
    $all = industries();
    uasort($all, fn ($a, $b) => $a['number'] <=> $b['number']);
    $out = [];
    foreach ($all as $key => $i) {
        $out[] = ['key' => $key, 'industry' => $i];
    }
    return $out;
}
