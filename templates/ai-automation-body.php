<?php
/**
 * AI & Automation landing page (/ai-automation, template "ai-automation").
 *
 * Built on the home page's components and palette: /assets/home10.css and
 * home10.js load first and the page sits in .h10, so buttons, pills, the word
 * reveal on headings, the line illustrations (.il), the paper tickets (.tk),
 * the logo marquee and the footer are shared with Home and the other service
 * pages. What is particular to this page is /assets/aiauto.css (everything
 * under .ai) and /assets/aiauto.js. Every section has its own layout:
 *
 *   01 hero          the statement beside an AI agent answering a WhatsApp
 *                    enquiry, its workflow ticking through underneath; the four
 *                    differentiators as 3D chips
 *   02 challenge     where the week goes (a donut) beside a wall of pinned notes,
 *                    one per challenge
 *   03 engineer      the home page's 3D capability cards, stacking as you scroll
 *   04 connected     a workflow-builder canvas: five nodes wired in sequence
 *   05 approach      the home page's paper tickets, four steps
 *   06 industries    six tall panels; the one you point at opens out
 *   07 use cases     three solution examples, each on its own device
 *   08 final CTA     the home page's light full-width close
 *
 * All copy is server-rendered inside <section>s, so Admin → Pages → Page
 * Content can edit it. Everything reads in full without JavaScript, and stands
 * still under prefers-reduced-motion. Screens use sample data.
 */
$activePage = 'ai-automation';
include __DIR__ . '/partials/nav.php';

$aiIc = fn (string $id, string $class = 'ic'): string => '<svg class="' . $class . '" aria-hidden="true" focusable="false"><use href="#a-' . $id . '"/></svg>';
$aiArrow = $aiIc('arrow', 'ic h10-btn__arr');
$aiN = fn (int $i): string => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
// an icon placed inside an illustration
$aiIl = fn (string $id, int $x, int $y, int $s = 18, string $c = 'var(--green)'): string => '<svg x="' . $x . '" y="' . $y . '" width="' . $s . '" height="' . $s . '" style="color:' . $c . '"><use href="#a-' . $id . '"/></svg>';
$aiCase = function (string $slug, string $fallback) use ($pdo): string {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM case_studies WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() ? '/case-studies/' . $slug : $fallback;
    } catch (PDOException $e) {
        return $fallback;
    }
};
$aiTint = ['mint' => '#DAF1E5', 'sky' => '#DFEAF9', 'lilac' => '#E8E2F5', 'aqua' => '#DCEFF0', 'peach' => '#F8E6D6', 'peri' => '#E3E5F6', 'rose' => '#F5E1E9', 'lime' => '#EDF0D7'];

// 01 · differentiators [icon, tint, label]
$aiDiff = [['target', 'mint', 'Business-Driven AI Implementation'], ['bot', 'sky', 'Custom AI Agents & Applications'], ['workflow', 'lilac', 'Connected Workflow Automation'], ['heart', 'peach', 'Human-Centric AI Systems']];
// the agent's workflow, ticking through under the chat
$aiTrace = [['chat', 'Enquiry captured from WhatsApp'], ['target', 'Lead qualified · high intent'], ['db', 'CRM record created'], ['bell', 'Sales follow-up scheduled']];

// 02 · challenges [tint, title, text]
$aiProblems = [
    ['peach', 'Repetitive Manual Tasks', 'Your team spends valuable time doing the same routine work every day instead of focusing on important business activities.'],
    ['sky', 'Missed Customer Enquiries', 'Leads and customer messages arrive through multiple channels, making it difficult to respond and follow up consistently.'],
    ['lilac', 'Disconnected Business Applications', 'Your team moves information manually between CRM, spreadsheets, email, WhatsApp and other software.'],
    ['lime', 'Information Is Difficult to Find', 'Important business information is scattered across documents, conversations, spreadsheets and different platforms.'],
    ['rose', 'Delayed Decisions', 'Employees spend hours collecting, organizing and preparing information before management can understand what\'s happening.'],
    ['aqua', 'Processes Depend on Individuals', 'Important activities rely on employees remembering tasks, sending reminders and manually coordinating with other departments.'],
];
// where the week goes (illustrative shares) [label, share %, colour]
$aiWeek = [['Updating spreadsheets', 30, '#E8833A'], ['Chasing follow-ups', 25, '#5B7FD6'], ['Copying data between apps', 25, '#8A6AD0'], ['Preparing reports', 20, '#D6456F']];

// 03 · capabilities [icon, tint, tag, title, text, items]
$aiBuild = [
    ['bot', 'sky', 'AI Agents', 'AI Agents & Assistants', 'Build intelligent assistants that understand instructions, retrieve information and support business workflows.', ['Custom AI Agents', 'AI-Powered Business Assistants', 'Knowledge-Based AI Assistants', 'Customer Support Agents', 'Internal Team Assistants']],
    ['workflow', 'mint', 'Automation', 'Workflow & Process Automation', 'Connect everyday business activities and automate repetitive processes across departments.', ['Multi-Step Workflow Automation', 'Task & Approval Automation', 'Data Entry & Processing Automation', 'Automated Notifications & Reminders', 'Cross-Platform Workflow Integration']],
    ['chat', 'lime', 'WhatsApp', 'WhatsApp & Communication Automation', 'Transform customer communication into structured, automated business workflows.', ['WhatsApp Business API Integration', 'Automated Customer Responses', 'Lead Qualification & Routing', 'Appointment & Meeting Automation', 'Customer Follow-Up Workflows']],
    ['sparkle', 'lilac', 'AI Applications', 'Custom AI Applications', 'Build AI-powered applications designed around specific business requirements.', ['Custom AI Web Applications', 'AI-Powered Search & Knowledge Systems', 'Document Processing Applications', 'AI Content & Data Processing Tools', 'Custom Business AI Solutions']],
    ['plug', 'peach', 'Integration', 'System Integration & Data Automation', 'Connect existing software and eliminate unnecessary manual data movement.', ['CRM & ERP Integration', 'API Development & Integration', 'Database Synchronization', 'Third-Party Application Integration', 'Automated Data Collection & Processing']],
    ['chart', 'rose', 'Intelligence', 'AI-Powered Business Intelligence', 'Make business information easier to understand, access and use for decision-making.', ['AI-Powered Business Reporting', 'Data Extraction & Structuring', 'Intelligent Document Analysis', 'Conversational Data Queries', 'Automated Reports & Insights']],
];
$aiBuildArt = [
    // agents: a question, the agent answering with a source attached
    '<rect class="c" x="14" y="16" width="112" height="30" rx="12"/><rect class="k2" x="26" y="27" width="70" height="5" rx="2.5"/><rect class="k" x="26" y="36" width="44" height="4" rx="2"/><g class="float" style="--d:.2s"><rect class="c g-outline" x="56" y="56" width="130" height="50" rx="14"/><circle class="g" cx="76" cy="81" r="11"/>' . $aiIl('sparkle', 69, 74, 14, '#fff') . '<rect class="g typeln" x="94" y="70" width="76" height="5" rx="2.5" style="--d:0s"/><rect class="k2 typeln" x="94" y="80" width="62" height="5" rx="2.5" style="--d:.3s"/><rect class="k typeln" x="94" y="90" width="44" height="5" rx="2.5" style="--d:.6s"/></g><rect class="gs2" x="96" y="114" width="58" height="14" rx="7"/>' . $aiIl('doc', 101, 116, 10) . '<rect class="g" x="114" y="119" width="32" height="4" rx="2"/>',
    // automation: trigger, rule, actions, with a gear turning
    '<rect class="c" x="12" y="50" width="44" height="40" rx="11"/>' . $aiIl('bolt', 23, 59, 22) . '<path class="gl flow" d="M56 70H76M120 58C134 58 134 34 146 34M120 82C134 82 134 106 146 106" stroke-dasharray="4 5"/><circle class="g" cx="98" cy="70" r="22"/><g class="spin">' . $aiIl('gear', 84, 56, 28, '#fff') . '</g><rect class="c" x="146" y="18" width="42" height="32" rx="10"/>' . $aiIl('mail', 158, 25, 18) . '<rect class="c" x="146" y="90" width="42" height="32" rx="10"/>' . $aiIl('check', 158, 97, 18),
    // whatsapp: a phone, a message in, a reply and quick buttons out
    '<rect class="c" x="60" y="8" width="80" height="128" rx="16"/><rect class="gs2" x="60" y="8" width="80" height="20" rx="16"/><circle class="g" cx="74" cy="18" r="5"/><rect class="k" x="84" y="15" width="34" height="5" rx="2.5"/><rect class="k2" x="68" y="36" width="50" height="16" rx="8"/><rect class="gs2" x="82" y="58" width="50" height="22" rx="8"/><rect class="g" x="88" y="64" width="34" height="4" rx="2"/><rect class="g" x="88" y="71" width="24" height="4" rx="2"/><rect class="c g-outline" x="68" y="88" width="64" height="13" rx="6.5"/><rect class="c g-outline" x="68" y="105" width="64" height="13" rx="6.5"/><g class="float" style="--d:.3s"><rect class="c" x="148" y="40" width="40" height="30" rx="10"/>' . $aiIl('check', 159, 46, 18) . '</g><g class="float" style="--d:.8s"><rect class="c" x="12" y="76" width="40" height="30" rx="10"/>' . $aiIl('bell', 23, 82, 18) . '</g>',
    // ai applications: an app with a search box and an answer forming
    '<rect class="c" x="16" y="14" width="168" height="112" rx="12"/><circle class="k2" cx="30" cy="27" r="3"/><circle class="k2" cx="40" cy="27" r="3"/><circle class="k2" cx="50" cy="27" r="3"/><path class="ln" d="M16 38H184"/><rect class="c g-outline" x="30" y="48" width="140" height="20" rx="10"/>' . $aiIl('search', 37, 51, 14) . '<rect class="k2" x="56" y="56" width="64" height="4" rx="2"/><rect class="gs2" x="30" y="78" width="140" height="38" rx="9"/>' . $aiIl('sparkle', 38, 85, 14) . '<rect class="g typeln" x="58" y="86" width="96" height="5" rx="2.5" style="--d:0s"/><rect class="k2 typeln" x="58" y="96" width="80" height="5" rx="2.5" style="--d:.3s"/><rect class="k typeln" x="58" y="106" width="54" height="4" rx="2" style="--d:.6s"/>',
    // integration: two systems, an API between, records moving across
    '<g transform="translate(14 28)"><ellipse class="c" cx="26" cy="10" rx="26" ry="9"/><path class="c" d="M0 10v56c0 5 12 9 26 9s26-4 26-9V10"/><path class="ln" d="M0 30c0 5 12 9 26 9s26-4 26-9M0 50c0 5 12 9 26 9s26-4 26-9"/></g><g transform="translate(134 28)"><ellipse class="c" cx="26" cy="10" rx="26" ry="9"/><path class="c" d="M0 10v56c0 5 12 9 26 9s26-4 26-9V10"/><path class="ln" d="M0 30c0 5 12 9 26 9s26-4 26-9M0 50c0 5 12 9 26 9s26-4 26-9"/></g><path class="gl flow" d="M68 58H132M68 86H132" stroke-dasharray="4 5"/><rect class="g" x="80" y="56" width="40" height="34" rx="10"/><text class="wht" x="100" y="77" text-anchor="middle" style="font-size:10px;font-weight:800">API</text><g class="float" style="--d:.4s"><rect class="c" x="78" y="16" width="44" height="22" rx="7"/><rect class="g" x="86" y="25" width="28" height="4" rx="2"/></g>',
    // intelligence: ask a question of the data, the chart answers
    '<rect class="c" x="14" y="14" width="118" height="112" rx="12"/><path class="ln faint" d="M28 48H118M28 74H118M28 100H118"/><rect class="g rise" x="34" y="78" width="12" height="32" rx="3" style="--d:0s"/><rect class="gs2 rise" x="54" y="64" width="12" height="46" rx="3" style="--d:.15s"/><rect class="g rise" x="74" y="54" width="12" height="56" rx="3" style="--d:.3s"/><rect class="gs2 rise" x="94" y="36" width="12" height="74" rx="3" style="--d:.45s"/><g class="float" style="--d:.2s"><path class="c" d="M142 22h40a8 8 0 0 1 8 8v26a8 8 0 0 1-8 8h-24l-10 9v-9h-6a8 8 0 0 1-8-8V30a8 8 0 0 1 8-8z"/><text x="162" y="48" text-anchor="middle" style="font-size:20px;font-weight:800;fill:var(--green)">?</text></g><g class="float" style="--d:.7s"><rect class="c" x="140" y="84" width="50" height="32" rx="10"/>' . $aiIl('sparkle', 147, 91, 16) . '<rect class="g" x="166" y="96" width="18" height="5" rx="2.5"/></g>',
];

// 04 · connected automation [icon, tint, title, items]
$aiFlow = [
    ['target', 'sky', 'Lead & Sales Automation', ['Lead Capture', 'Lead Assignment', 'Lead Qualification', 'Follow-Up Reminders', 'Sales Pipeline Updates']],
    ['chat', 'mint', 'Customer Communication', ['WhatsApp Automation', 'Email Notifications', 'AI Customer Support', 'Appointment Scheduling', 'Customer Follow-Ups']],
    ['users', 'lilac', 'Team & Operations', ['Task Assignment', 'Approval Workflows', 'Employee Notifications', 'Project Updates', 'Process Automation']],
    ['doc', 'peach', 'Data & Documents', ['Document Extraction', 'Data Entry Automation', 'Information Classification', 'Database Updates', 'Document Summarization']],
    ['chart', 'rose', 'Management & Reporting', ['Automated Reports', 'Business Performance Summaries', 'AI-Powered Data Queries', 'Activity Tracking', 'Management Notifications']],
];
$aiFlowKind = ['Trigger', 'Action', 'Action', 'AI step', 'Output'];
$aiTech = [['n8n', 'n8n'], ['zapier', 'Zapier'], ['make', 'Make'], ['power-automate', 'Microsoft Power Automate'], ['openai', 'OpenAI'], ['claude', 'Claude'], ['gemini', 'Gemini'], ['elevenlabs', 'ElevenLabs'], ['python', 'Python'], ['supabase', 'Supabase'], ['postgresql', 'PostgreSQL'], ['nodejs', 'Node.js']];

// 05 · approach [ticket label, title, text, tags]
$aiSteps = [
    ['Discover', 'Discover & Identify', 'Understand your business processes, repetitive tasks, existing tools and operational challenges. Identify where AI or automation can make a meaningful difference.', 'Process Audit · Team Discussions · Workflow Analysis · Automation Opportunities'],
    ['Design', 'Design & Architect', 'Define the solution, map the workflow and select the appropriate AI models, integrations and automation architecture.', 'Solution Architecture · Workflow Mapping · AI Model Selection · Integration Planning'],
    ['Build', 'Build & Integrate', 'Develop custom AI applications, agents and automated workflows. Connect them with your existing platforms, databases and business systems.', 'AI Development · Agent Engineering · API Integration · Workflow Automation'],
    ['Deploy', 'Deploy & Optimize', 'Test real-world scenarios, deploy the solution, train your team and continuously improve its performance based on usage and feedback.', 'Testing · Deployment · Team Enablement · Monitoring & Optimization'],
];
$aiStepTint = ['peach', 'sky', 'lilac', 'lime'];
$aiStepArt = [
    // discover: the process on a whiteboard, a lens finding the repetitive step
    '<path class="ln" d="M44 40V60H100M100 40V60M156 40V60H100M100 60V80"/><rect class="c" x="22" y="20" width="44" height="22" rx="7"/><rect class="k2" x="32" y="29" width="24" height="5" rx="2.5"/><rect class="c" x="78" y="20" width="44" height="22" rx="7"/><rect class="k2" x="88" y="29" width="24" height="5" rx="2.5"/><rect class="c" x="134" y="20" width="44" height="22" rx="7"/><rect class="k2" x="144" y="29" width="24" height="5" rx="2.5"/><rect class="c g-outline" x="74" y="80" width="52" height="24" rx="8"/><g class="spin" style="transform-origin:100px 92px">' . $aiIl('gear', 91, 83, 18) . '</g><g class="scan"><circle cx="128" cy="98" r="20" class="lens"/><path class="gl" d="M142 112L158 128" stroke-width="6"/></g>',
    // design: the workflow drawn as nodes with the model picked
    '<rect class="c" x="16" y="18" width="168" height="104" rx="14"/><path class="ln faint" d="M16 46H184M16 74H184M16 102H184M58 18V122M100 18V122M142 18V122"/><rect class="gs2" x="28" y="28" width="38" height="22" rx="6"/><rect class="g" x="134" y="28" width="38" height="22" rx="6"/>' . $aiIl('sparkle', 144, 30, 18, '#fff') . '<rect class="gs2" x="80" y="84" width="40" height="22" rx="6"/><path class="gl flow" d="M66 39H134M153 50V70H100V84" stroke-dasharray="4 5"/><circle class="g" cx="153" cy="70" r="4"/>',
    // build: code typing, the agent plugged into existing systems
    '<rect class="c" x="22" y="18" width="116" height="96" rx="14"/><circle class="k2" cx="38" cy="32" r="3.5"/><circle class="k2" cx="50" cy="32" r="3.5"/><circle class="k2" cx="62" cy="32" r="3.5"/><rect class="g typeln" x="36" y="46" width="40" height="6" rx="3" style="--d:0s"/><rect class="k2 typeln" x="44" y="58" width="60" height="6" rx="3" style="--d:.25s"/><rect class="k2 typeln" x="44" y="70" width="32" height="6" rx="3" style="--d:.5s"/><rect class="g typeln" x="36" y="82" width="52" height="6" rx="3" style="--d:.75s"/><rect class="k2 typeln" x="44" y="94" width="70" height="6" rx="3" style="--d:1s"/><g class="float" style="--d:.3s"><rect class="c" x="126" y="66" width="56" height="42" rx="12"/>' . $aiIl('bot', 141, 74, 26) . '</g>',
    // deploy: live, monitored, improving
    '<circle cx="100" cy="70" r="42" class="ring spin" fill="none"/><circle class="g pulse" cx="100" cy="70" r="30"/><circle class="g" cx="100" cy="70" r="30"/>' . $aiIl('rocket', 84, 54, 32, '#fff') . '<g class="float" style="--d:.2s"><rect class="c" x="20" y="90" width="48" height="30" rx="8"/><rect class="g rise" x="29" y="106" width="6" height="8" rx="2" style="--d:0s"/><rect class="g rise" x="39" y="100" width="6" height="14" rx="2" style="--d:.15s"/><rect class="g rise" x="49" y="94" width="6" height="20" rx="2" style="--d:.3s"/></g><g class="float" style="--d:.7s"><rect class="c" x="132" y="20" width="50" height="26" rx="13"/><circle class="g blink" cx="146" cy="33" r="4"/><rect class="k" x="154" y="30" width="20" height="6" rx="3"/></g>',
];

// 06 · industries [icon, tint, name, question, items, cta]
$aiInd = [
    ['hardhat', 'peach', 'Construction & Real Estate', 'Are your teams spending too much time coordinating projects, site updates and documentation?', ['Project Update Automation', 'Document Processing', 'Lead & Customer Follow-Ups', 'Internal Workflow Automation'], 'Automate Your Construction Operations'],
    ['stetho', 'mint', 'Healthcare', 'Are repetitive administrative tasks taking time away from patient-focused activities?', ['Appointment & Reminder Automation', 'Patient Enquiry Management', 'Document & Data Processing', 'Communication Workflows'], 'Automate Your Healthcare Workflows'],
    ['bag', 'sky', 'Ecommerce & D2C', 'Are you struggling to manage customer enquiries, orders and repetitive ecommerce activities?', ['AI Customer Support', 'Order Communication Automation', 'Product Information Assistants', 'Customer Follow-Up Automation'], 'Automate Your Ecommerce Business'],
    ['factory', 'lime', 'Manufacturing & Distribution', 'Are manual updates and disconnected information slowing down your operations?', ['Data Entry Automation', 'Document Processing', 'Inventory Workflow Automation', 'Automated Reporting'], 'Streamline Your Business Operations'],
    ['briefcase', 'lilac', 'Professional Services', 'Are your employees spending more time managing administrative work than serving clients?', ['Client Enquiry Automation', 'Document & Proposal Assistance', 'Meeting & Follow-Up Automation', 'Internal Knowledge Assistants'], 'Automate Your Service Workflows'],
    ['megaphone', 'rose', 'Sales & Marketing Teams', 'Are leads, campaigns and customer conversations spread across multiple platforms?', ['Lead Qualification Agents', 'CRM Workflow Automation', 'WhatsApp Follow-Up Automation', 'Marketing Data Reporting'], 'Build Your AI-Powered Growth Engine'],
];

// 07 · use cases [tint, label, title, text, capabilities, from, to, href, device]
$aiUse = [
    ['mint', 'WhatsApp Business Automation', 'Turn Customer Conversations into Structured Business Workflows.', 'A WhatsApp automation solution designed to streamline customer communication, automate responses and connect conversations with business processes.', ['WhatsApp Business API Integration', 'Automated Message Workflows', 'Customer Communication Management', 'CRM & System Integration'], 'Manually managing customer conversations and follow-ups.', 'Structured communication and automated customer workflows.', $aiCase('whatsapp-business-automation', '/case-studies'), 'phone'],
    ['lilac', 'AI-Powered Business Assistant', 'Make Business Information Easier to Access.', 'An AI assistant concept designed to help teams interact with business information through natural-language queries and connected knowledge sources.', ['AI Assistant Development', 'Knowledge Base Integration', 'Information Retrieval', 'Conversational Interfaces'], 'Searching across multiple documents and information sources.', 'A conversational interface for accessing relevant information.', $aiCase('ai-powered-business-assistant', '/case-studies'), 'assistant'],
    ['sky', 'Business Process Automation', 'Reduce Manual Coordination Across Business Operations.', 'A workflow automation solution connecting business activities, task updates and notifications to improve process visibility and reduce repetitive coordination.', ['Workflow Automation', 'API Integrations', 'Automated Notifications', 'Data Synchronization'], 'Manual updates and disconnected processes.', 'Connected workflows and automated information flow.', $aiCase('business-process-automation', '/case-studies'), 'board'],
];

// the donut in 02: each share as a dash on one circle (r 70 → circumference 439.82)
$aiDonut = '';
$aiOff = 0.0;
foreach ($aiWeek as [$lbl, $pct, $col]) {
    $len = 439.82 * $pct / 100;
    $aiDonut .= '<circle cx="90" cy="90" r="70" fill="none" stroke="' . $col . '" stroke-width="22" stroke-dasharray="' . round($len - 3, 2) . ' ' . round(439.82 - $len + 3, 2) . '" stroke-dashoffset="' . round(-$aiOff, 2) . '"/>';
    $aiOff += $len;
}
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/aiauto.css') ?>">
<script>document.documentElement.classList.add('js')</script>

<div class="h10 ai">
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>
<symbol id="a-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
<symbol id="a-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>
<symbol id="a-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></symbol>
<symbol id="a-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/></symbol>
<symbol id="a-bot" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="4"/><path d="M12 4v4M9 13.5h.01M15 13.5h.01M9.5 17h5"/><circle cx="12" cy="3.5" r="1"/></symbol>
<symbol id="a-workflow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/></symbol>
<symbol id="a-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7.5-4.4-7.5-10A4.3 4.3 0 0112 7.3 4.3 4.3 0 0119.5 10c0 5.6-7.5 10-7.5 10z"/></symbol>
<symbol id="a-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v9a1.5 1.5 0 01-1.5 1.5H10l-4.5 4v-4h0A1.5 1.5 0 014 14.5z"/><path d="M8 9h8M8 12h5"/></symbol>
<symbol id="a-db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></symbol>
<symbol id="a-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 16V11a6 6 0 0112 0v5l1.5 2h-15z"/><path d="M10 20.5a2 2 0 004 0"/></symbol>
<symbol id="a-sparkle" viewBox="0 0 24 24"><path fill="currentColor" d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/><path fill="currentColor" d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/></symbol>
<symbol id="a-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol>
<symbol id="a-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol>
<symbol id="a-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol>
<symbol id="a-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 12h6M9 16h6"/></symbol>
<symbol id="a-bolt" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M13 3L5 13.5h6L10 21l8-10.5h-6z"/></symbol>
<symbol id="a-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></symbol>
<symbol id="a-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/></symbol>
<symbol id="a-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></symbol>
<symbol id="a-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol>
<symbol id="a-hardhat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17h18v2.5H3zM5 17a7 7 0 0 1 14 0M12 10V6.5M9 11V8.2M15 11V8.2"/></symbol>
<symbol id="a-stetho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v6a4 4 0 0 0 8 0V3M10 13v2.5a4 4 0 0 0 8 0V14"/><circle cx="18" cy="12" r="2"/></symbol>
<symbol id="a-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></symbol>
<symbol id="a-factory" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l6 3.5V10l6 3.5V5h4v16z"/><path d="M8 17h2M13 17h2"/></symbol>
<symbol id="a-briefcase" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7M3 13h18"/></symbol>
<symbol id="a-megaphone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10v4h3l8 4V6L7 10z"/><path d="M7 14l1.5 5h2.5L10 15M18 9.5a3.5 3.5 0 010 5"/></symbol>
<symbol id="a-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></symbol>
</defs></svg>
<a class="skip" href="#ai-top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>

<!-- 01 · Hero: the statement beside an AI agent at work; the differentiators as 3D chips -->
<section class="bsh ai-hero" id="ai-top">
  <div class="wrap ai-hero__grid">
    <div class="ai-hero__copy">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>AI Solutions · Business Automation · AI Agents</span>
      <h1 class="ai-hero__h1">Stop Repeating Work That <span class="ai-hero__em">Technology Can Handle.</span></h1>
      <p class="ai-hero__lede reveal" style="--d:.15s">Your business shouldn't depend on manual updates, repetitive tasks and disconnected tools to keep things moving. We engineer AI-powered solutions and intelligent automation that connect your workflows, reduce manual effort and help your teams work smarter.</p>
      <p class="ai-hero__sub reveal" style="--d:.2s">From AI agents and WhatsApp automation to custom AI applications and business process automation, we build solutions around how your business actually operates.</p>
      <div class="ai-hero__ctas reveal" style="--d:.26s">
        <a class="h10-btn h10-btn--dark" href="/book" data-book><span>Automate My Business</span><?= $aiArrow ?></a>
        <a class="h10-btn ai-btn-line" href="#solutions"><span>Explore Our AI Solutions</span><?= $aiArrow ?></a>
      </div>
    </div>
    <figure class="ai-agent reveal" style="--d:.1s" aria-hidden="true">
      <div class="ai-agent__win">
        <div class="ai-agent__bar"><span class="ai-agent__av"><?= $aiIc('bot') ?></span><span><b>Drawlead AI Agent</b><small><i></i>Online · replies instantly</small></span><span class="ai-agent__ch"><?= $aiIc('chat') ?>WhatsApp</span></div>
        <div class="ai-agent__chat">
          <p class="ai-msg ai-msg--in" style="--s:0">Hi, do you deliver to Coimbatore? I need 40 units by Friday.<small>10:42</small></p>
          <p class="ai-msg ai-msg--out" style="--s:1">Yes — 40 units can reach Coimbatore by Thursday. Shall I send you a quote and book a call with our sales team?<small>10:42 ✓✓</small></p>
          <p class="ai-msg ai-msg--in" style="--s:2">Yes please, tomorrow morning works.<small>10:43</small></p>
          <p class="ai-msg ai-msg--typing" style="--s:3"><i></i><i></i><i></i></p>
        </div>
        <ol class="ai-trace">
<?php foreach ($aiTrace as $i => [$icon, $label]): ?>
          <li style="--s:<?= $i ?>"><i><?= $aiIc($icon) ?></i><span><?= h($label) ?></span><em><?= $aiIc('check') ?></em></li>
<?php endforeach; ?>
        </ol>
      </div>
      <span class="ai-float"><i><?= $aiIc('sparkle') ?></i><span><b>Handled by AI</b><small>Your team picks it up from here</small></span></span>
    </figure>
  </div>
  <div class="wrap">
    <ul class="ai-diff" role="list">
<?php foreach ($aiDiff as $i => [$icon, $tint, $label]): ?>
      <li class="ai-3d reveal" style="--pt:<?= $aiTint[$tint] ?>;--d:<?= $i * .07 ?>s"><i><?= $aiIc($icon) ?></i><span><?= h($label) ?></span></li>
<?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- 02 · The challenge: where the week goes, beside a wall of pinned notes -->
<section class="ai-chal" id="challenges">
  <div class="wrap">
    <div class="ai-head">
      <div>
        <span class="pill ai-pill t-peach reveal"><span class="pill__dot" aria-hidden="true"></span>Your Team's Time Is Too Valuable for Repetitive Work</span>
        <h2 class="h2">You Don't Need More People to Manage More Manual Work.</h2>
      </div>
      <div class="ai-head__txt reveal" style="--d:.1s">
        <p class="lede">As businesses grow, repetitive tasks, scattered information and disconnected processes consume valuable time. Employees spend hours updating spreadsheets, following up with customers, moving information between platforms and managing routine activities.</p>
        <p class="lede lede--strong">AI and automation can help eliminate much of this operational friction.</p>
      </div>
    </div>
    <div class="ai-chal__grid">
      <aside class="ai-week reveal">
        <span class="ai-kick">Where the week goes</span>
        <div class="ai-week__donut" role="img" aria-label="Illustration: most of a team's routine week goes on updating spreadsheets, chasing follow-ups, copying data between apps and preparing reports.">
          <svg viewBox="0 0 180 180" aria-hidden="true" focusable="false"><circle cx="90" cy="90" r="70" fill="none" stroke="#eef2f0" stroke-width="22"/><g transform="rotate(-90 90 90)"><?= $aiDonut ?></g></svg>
          <span aria-hidden="true"><?= $aiIc('clock') ?><b>Routine work</b></span>
        </div>
        <ul class="ai-week__key" role="list" aria-hidden="true">
<?php foreach ($aiWeek as [$lbl, $pct, $col]): ?>
          <li><i style="background:<?= $col ?>"></i><?= h($lbl) ?></li>
<?php endforeach; ?>
        </ul>
        <p class="ai-week__cap"><span>Illustrative</span>Hours that automation can give back</p>
        <a class="h10-btn h10-btn--dark" href="/book" data-book><span>Identify What Your Business Can Automate</span><?= $aiArrow ?></a>
      </aside>
      <ol class="ai-notes" role="list">
<?php foreach ($aiProblems as $i => [$tint, $title, $text]): ?>
        <li class="ai-note reveal" style="--pt:<?= $aiTint[$tint] ?>;--tink:var(--ik-<?= $tint ?>);--r:<?= [-2.2, 1.6, -1.2, 2, -1.8, 1.2][$i] ?>deg;--d:<?= ($i % 2) * .08 ?>s">
          <span class="ai-note__pin" aria-hidden="true"></span>
          <span class="ai-note__n"><?= $aiN($i) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
        </li>
<?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>

<!-- 03 · What we engineer: the home page's 3D capability cards, stacking as you scroll -->
<section class="ai-caps" id="solutions">
  <div class="wrap">
    <div class="ai-head ai-head--center">
      <span class="pill ai-pill t-sky reveal"><span class="pill__dot" aria-hidden="true"></span>Practical AI. Intelligent Automation. Real Business Applications.</span>
      <h2 class="h2">From Everyday Automation to <span class="grn">Intelligent AI-Powered Systems.</span></h2>
      <p class="lede reveal" style="--d:.1s">We design and implement AI solutions that solve practical business problems. Whether you want to automate a repetitive workflow, build an AI agent or connect multiple business applications, we engineer the right solution around your requirements.</p>
    </div>
    <ol class="ai-deck" role="list">
<?php foreach ($aiBuild as $i => [$icon, $tint, $tag, $title, $text, $items]): ?>
      <li class="ai-deck__it" style="--i:<?= $i ?>">
        <article class="ai-card t-<?= $tint ?>" style="--pt:<?= $aiTint[$tint] ?>" data-ai-tilt>
          <div class="ai-card__txt">
            <span class="ai-card__tag"><?= $aiN($i) ?> · <?= h($tag) ?></span>
            <h3><?= h($title) ?></h3>
            <p><?= h($text) ?></p>
            <ul class="ai-card__checks" role="list"><?php foreach ($items as $t): ?><li><?= $aiIc('check') ?><?= h($t) ?></li><?php endforeach; ?></ul>
          </div>
          <div class="ai-card__art il" aria-hidden="true"><svg class="il-svg" viewBox="0 0 200 140" focusable="false"><rect class="pn" width="200" height="140"/><?= $aiBuildArt[$i] ?></svg></div>
        </article>
      </li>
<?php endforeach; ?>
    </ol>
    <div class="ai-center reveal"><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Let's Engineer Your AI Solution</span><?= $aiArrow ?></a></div>
  </div>
</section>

<!-- 04 · Connected automation: a workflow-builder canvas, five nodes wired in sequence -->
<section class="ai-conn" id="connected">
  <div class="ai-conn__stage">
    <div class="wrap">
      <div class="ai-head">
        <div>
          <span class="pill ai-pill t-lilac reveal"><span class="pill__dot" aria-hidden="true"></span>Connect Your Tools. Automate Your Workflows.</span>
          <h2 class="h2">One Connected Workflow. Less Manual Coordination.</h2>
        </div>
        <div class="ai-head__txt reveal" style="--d:.1s">
          <p class="lede">Your business already uses multiple platforms to manage customers, employees, projects, finance and operations. The challenge is making these systems work together.</p>
          <p class="lede lede--strong">We connect your existing applications, automate information flow and introduce AI where it creates practical value.</p>
        </div>
      </div>
      <div class="ai-canvas reveal" data-ai-canvas>
        <div class="ai-canvas__bar" aria-hidden="true"><span class="ai-canvas__name"><?= $aiIc('workflow') ?>Business workflow</span><span class="ai-canvas__run"><i></i>Running</span><span class="ai-canvas__meta">5 steps · 25 automations</span></div>
        <div class="ai-canvas__body">
          <svg class="ai-canvas__wires" aria-hidden="true" focusable="false" data-ai-wires></svg>
          <ol class="ai-nodes" role="list">
<?php foreach ($aiFlow as $i => [$icon, $tint, $title, $items]): ?>
            <li class="ai-node t-<?= $tint ?>" style="--pt:<?= $aiTint[$tint] ?>">
              <div class="ai-node__hd"><i><?= $aiIc($icon) ?></i><span><small><?= $aiN($i) ?> · <?= $aiFlowKind[$i] ?></small><h3><?= h($title) ?></h3></span></div>
              <ul role="list"><?php foreach ($items as $t): ?><li><span class="ai-node__sw" aria-hidden="true"></span><?= h($t) ?></li><?php endforeach; ?></ul>
            </li>
<?php endforeach; ?>
          </ol>
        </div>
      </div>
      <div class="ai-logos reveal">
        <span class="ai-kick">Platforms &amp; models we work with</span>
        <div class="marquee marquee--tech ai-logos__row"><ul class="marquee__track" role="list"><?php for ($r = 0; $r < 2; $r++): foreach ($aiTech as [$file, $name]): ?><li class="cx-logo"<?= $r ? ' aria-hidden="true"' : '' ?>><img src="/assets/home10/tech/<?= $file ?>.webp" alt="<?= $r ? '' : h($name) ?>" loading="lazy" decoding="async"></li><?php endforeach; endfor; ?></ul></div>
      </div>
      <div class="ai-center reveal"><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Connect and Automate Your Workflows</span><?= $aiArrow ?></a></div>
    </div>
  </div>
</section>

<!-- 05 · Approach: the home page's paper tickets, four steps -->
<section class="ai-appr" id="approach">
  <div class="wrap">
    <div class="ai-head">
      <div>
        <span class="pill ai-pill t-rose reveal"><span class="pill__dot" aria-hidden="true"></span>We Don't Implement AI Just Because It's Trending</span>
        <h2 class="h2">First Understand the Problem. Then Engineer the Right AI Solution.</h2>
      </div>
      <div class="ai-head__txt reveal" style="--d:.1s">
        <p class="lede">Not every business problem requires AI. Some need better workflows, some need system integration, and others benefit from intelligent automation.</p>
        <p class="lede lede--strong">We work closely with your team to identify the right opportunity and implement technology that delivers practical value.</p>
      </div>
    </div>
    <ol class="ai-tks" role="list">
<?php foreach ($aiSteps as $i => [$cls, $title, $text, $tags]): ?>
      <li class="step reveal" style="--tk:<?= $aiTint[$aiStepTint[$i]] ?>;--d:<?= $i * .1 ?>s">
        <div class="step__card tk">
          <div class="tk-a">
            <div class="tk-top"><span class="tk-cls"><?= h($cls) ?></span><span class="tk-adm">Step <?= $aiN($i) ?> · Your Team</span></div>
            <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false"><rect class="pn" width="200" height="140"/><?= $aiStepArt[$i] ?></svg></div>
          </div>
          <div class="tk-b">
            <h3 class="tk-h"><?= h($title) ?></h3>
            <p><?= h($text) ?></p>
            <ul class="ai-tk__tags" role="list"><?php foreach (explode(' · ', $tags) as $t): ?><li><?= h($t) ?></li><?php endforeach; ?></ul>
          </div>
        </div>
      </li>
<?php endforeach; ?>
    </ol>
    <div class="ai-center reveal"><a class="h10-btn h10-btn--dark" href="/book" data-book><span>Build Your AI Implementation Plan</span><?= $aiArrow ?></a></div>
  </div>
</section>

<!-- 06 · Industries: six tall panels; the one you point at opens out -->
<section class="ai-ind" id="industries">
  <div class="wrap">
    <div class="ai-head ai-head--center">
      <span class="pill ai-pill t-aqua reveal"><span class="pill__dot" aria-hidden="true"></span>AI That Understands Your Business Workflows</span>
      <h2 class="h2">Different Industries. Different Processes. Smarter Ways to Work.</h2>
      <p class="lede reveal" style="--d:.1s">AI becomes valuable when it solves problems that matter to your business. We engineer automation around the processes, people and systems within your industry.</p>
    </div>
    <div class="ai-acc reveal" data-ai-acc>
<?php foreach ($aiInd as $i => [$icon, $tint, $name, $q, $items, $cta]): ?>
      <article class="ai-acc__p t-<?= $tint ?><?= $i === 0 ? ' is-open' : '' ?>" style="--pt:<?= $aiTint[$tint] ?>">
        <button type="button" class="ai-acc__btn" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="aia-<?= $i ?>"><span class="ai-acc__n"><?= $aiN($i) ?></span><i><?= $aiIc($icon) ?></i><span class="ai-acc__name"><?= h($name) ?></span></button>
        <div class="ai-acc__body" id="aia-<?= $i ?>">
          <h3><?= h($q) ?></h3>
          <ul role="list"><?php foreach ($items as $t): ?><li><?= $aiIc('bolt') ?><?= h($t) ?></li><?php endforeach; ?></ul>
          <a class="ai-acc__cta" href="/book" data-book><span><?= h($cta) ?></span><i><?= $aiIc('arrow') ?></i></a>
        </div>
      </article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 07 · Use cases: three solution examples, each shown on its own device -->
<section class="ai-use" id="use-cases">
  <div class="wrap">
    <div class="ai-head ai-head--center">
      <span class="pill ai-pill t-mint reveal"><span class="pill__dot" aria-hidden="true"></span>From Business Challenges to Intelligent Solutions</span>
      <h2 class="h2">We Don't Just Connect Tools. We Make Them Work Together.</h2>
      <p class="lede reveal" style="--d:.1s">AI and automation deliver value when they are connected to real business processes. Explore examples of the solutions we engineer.</p>
    </div>
    <div class="ai-uses">
<?php foreach ($aiUse as $i => [$tint, $label, $title, $text, $caps, $from, $to, $href, $dev]): ?>
      <article class="ai-uc t-<?= $tint ?> reveal" style="--pt:<?= $aiTint[$tint] ?>;--d:<?= $i * .08 ?>s">
        <div class="ai-dev ai-dev--<?= $dev ?>" aria-hidden="true">
<?php if ($dev === 'phone'): ?>
          <div class="ai-phone">
            <div class="ai-phone__hd"><span class="ai-phone__av"><?= $aiIc('bag') ?></span><span><b>Your Brand</b><small>Business account</small></span></div>
            <p class="ai-b ai-b--in">I'd like to book a site visit this week.</p>
            <p class="ai-b ai-b--out">Sure! Pick a slot that suits you 👇</p>
            <span class="ai-phone__btns"><i>Wed · 11 AM</i><i>Thu · 4 PM</i><i>Talk to sales</i></span>
            <p class="ai-b ai-b--sys"><?= $aiIc('check') ?>Visit booked · added to CRM</p>
          </div>
<?php elseif ($dev === 'assistant'): ?>
          <div class="ai-asst">
            <p class="ai-asst__q">Which branch had the most open service requests last month?</p>
            <div class="ai-asst__a"><span class="ai-asst__av"><?= $aiIc('sparkle') ?></span><span>Branch B, with 42 open requests, mostly installation delays.<span class="ai-asst__src"><?= $aiIc('doc') ?>Service log · March.xlsx</span></span></div>
            <span class="ai-asst__in"><?= $aiIc('search') ?>Ask about your business…</span>
          </div>
<?php else: ?>
          <div class="ai-board">
<?php foreach ([['users', 'Task assigned', 'Site team', 'on'], ['check', 'Approval received', 'Manager', 'on'], ['bell', 'Customer notified', 'WhatsApp', 'on'], ['db', 'Records synced', 'CRM · ERP', 'run']] as [$bi, $bt, $bs, $st]): ?>
            <span class="ai-board__row ai-board__row--<?= $st ?>"><i><?= $aiIc($bi) ?></i><span><b><?= $bt ?></b><small><?= $bs ?></small></span><em><?= $st === 'on' ? 'Done' : 'Syncing' ?></em></span>
<?php endforeach; ?>
          </div>
<?php endif; ?>
        </div>
        <div class="ai-uc__txt">
          <span class="ai-uc__k"><span>Solution example</span><?= $aiN($i) ?> — <?= h($label) ?></span>
          <h3><?= h($title) ?></h3>
          <p><?= h($text) ?></p>
          <div class="ai-ft">
            <div><small>From</small><?= h($from) ?></div>
            <div><small>To</small><?= h($to) ?></div>
          </div>
          <ul class="ai-uc__chips" role="list"><?php foreach ($caps as $c): ?><li><?= h($c) ?></li><?php endforeach; ?></ul>
          <a class="ai-uc__cta" href="<?= h($href) ?>"><span>Explore Project</span><i><?= $aiIc('arrow') ?></i></a>
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="ai-fine reveal">Shown as solution examples. Screens use sample data.</p>
  </div>
</section>

<!-- 08 · Final CTA: the same light full-width close as the home page -->
<section class="final final--card" id="contact">
  <div class="wrap">
    <div class="final__card">
      <div class="final__in">
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Your Next Business Advantage Could Be Automation</span>
        <h2 class="h1x reveal" style="--d:.06s">Stop Spending Time on Work <span class="grn">That Can Be Automated.</span></h2>
        <p class="final__line reveal" style="--d:.1s">Your business doesn't need AI everywhere. It needs AI in the right places.</p>
        <p class="lede lede--light reveal" style="--d:.14s">Let's understand your operations, identify opportunities and engineer intelligent solutions that help your team save time, improve efficiency and focus on what matters.</p>
        <div class="final__ctas reveal" style="--d:.18s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Automate My Business</span><?= $aiArrow ?></a><a class="h10-btn h10-btn--ghost" href="/book" data-book><span>Book an AI Strategy Call</span><?= $aiArrow ?></a></div>
        <p class="ai-sign reveal" style="--d:.22s">From intelligent AI agents and custom applications to connected workflows and business automation — let's build a smarter way to work.</p>
      </div>
    </div>
  </div>
</section>
</main><footer class="foot">
  <?php $signClass = 'wrap'; include __DIR__ . '/partials/footer-signoff.php'; ?>
  <div class="wrap foot__in">
    <a href="#ai-top" aria-label="Drawlead – back to top"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
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
<script src="<?= asset_url('/assets/aiauto.js') ?>" defer></script>
