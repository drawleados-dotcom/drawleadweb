#!/usr/bin/env python3
"""Drawlead website builder.
Source of truth for approved content (Drawlead Website V.100) + markup templates.
Run:  python3 build.py   ->  writes ../site/index.html  (CSS/JS/assets are plain files in ../site)
"""
import html, os, random
from svglib import *
import svglib2 as S2
import svglib as S

OUT = os.path.join(os.path.dirname(__file__), '..', 'index.html')
e = html.escape

def ic(name, cls='ic', size=None):
    s = f' width="{size}" height="{size}"' if size else ''
    return f'<svg class="{cls}"{s} aria-hidden="true" focusable="false"><use href="#i-{name}"/></svg>'

def btn(label, href, kind='green', arrow=True, extra=''):
    a = ic('arrow', 'ic btn__arr') if arrow else ''
    return f'<a class="btn btn--{kind}" href="{href}" {extra}><span>{label}</span>{a}</a>'

def pill(text, icon=None, cls=''):
    i = ic(icon, 'ic') if icon else '<span class="pill__dot" aria-hidden="true"></span>'
    return f'<span class="pill {cls}">{i}{text}</span>'

def tags(items, cls='tags'):
    return f'<ul class="{cls}">' + ''.join(f'<li>{e(t)}</li>' for t in items) + '</ul>'

def rep(html_, n):
    """repeat a marquee set n times; copies are hidden from assistive tech"""
    return html_ + html_.replace('<li', '<li aria-hidden="true"') * (n - 1)

def reveal(d=0):
    return f'class="reveal" style="--d:{d}s"'

# ----------------------------------------------------------------------------- ICON SPRITE
ICONS = {
 'arrow': '<path d="M5 12h14M13 6l6 6-6 6"/>',
 'check': '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
 'sparkle': '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z" fill="currentColor" stroke="none"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z" fill="currentColor" stroke="none"/>',
 'shield': '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2.2 2.2L15 10"/>',
 'layers': '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 12.5l9 5 9-5"/><path d="M3 17l9 5 9-5" opacity=".55"/>',
 'briefcase': '<rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7M3 13h18"/>',
 'calendar': '<rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>',
 'building': '<rect x="5" y="3" width="10" height="18" rx="1.5"/><path d="M15 9h4v12h-4M8 7h4M8 11h4M8 15h4"/>',
 'cart': '<path d="M3 4h2.2l2.4 11h10.4L20 8H6.3"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>',
 'bot': '<rect x="4" y="8" width="16" height="11" rx="3.5"/><path d="M12 8V5M9.5 13v1.6M14.5 13v1.6M2 13v3M22 13v3"/><circle cx="12" cy="4" r="1"/>',
 'megaphone': '<path d="M4 10v4l12 5V5z"/><path d="M16 9.5a3 3 0 010 5M7 14.5V19"/>',
 'users': '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/>',
 'lock': '<rect x="5" y="11" width="14" height="10" rx="2.5"/><path d="M8 11V8a4 4 0 018 0v3"/>',
 'db': '<ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/>',
 'chat': '<path d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v9a1.5 1.5 0 01-1.5 1.5H10l-4.5 4v-4h0A1.5 1.5 0 014 14.5z"/><path d="M8 9h8M8 12h5"/>',
 'chart': '<path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/>',
 'workflow': '<rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/>',
 'search': '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>',
 'code': '<path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/>',
 'rocket': '<path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/>',
 'x': '<path d="M6 6l12 12M18 6L6 18"/>',
 'menu': '<path d="M4 7h16M4 12h16M4 17h16"/>',
 'hierarchy': '<rect x="9" y="3" width="6" height="5" rx="1.2"/><rect x="3" y="16" width="6" height="5" rx="1.2"/><rect x="15" y="16" width="6" height="5" rx="1.2"/><path d="M12 8v4M6 16v-4h12v4"/>',
 'heart': '<path d="M12 20s-8-4.6-8-10.2A4.3 4.3 0 0112 7.2a4.3 4.3 0 018 2.6C20 15.4 12 20 12 20z"/><path d="M7 12.2h2.6l1.4-2.4 2 4.4 1.4-2H17"/>',
 'gem': '<path d="M6 4h12l4 5-10 11L2 9z"/><path d="M2 9h20M9 4L7 9l5 11M15 4l2 5-5 11"/>',
 'factory': '<path d="M3 21V10l6 3.5V10l6 3.5V5h4v16z"/><path d="M8 17h2M13 17h2"/>',
 'plug': '<path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/>',
 'gear': '<circle cx="12" cy="12" r="3.2"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/>',
 'target': '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/>',
 'quote': '<path d="M7 7h4v4.2c0 3-1.5 5-4 6M15 7h4v4.2c0 3-1.5 5-4 6"/>',
 'pause': '<path d="M9 5v14M15 5v14"/>',
 'play': '<path d="M8 5l11 7-11 7z" fill="currentColor"/>',
 'left': '<path d="M15 5l-7 7 7 7"/>',
 'right': '<path d="M9 5l7 7-7 7"/>',
 'up': '<path d="M12 19V6M6 12l6-6 6 6"/>',
 'mail': '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/>',
}
SPRITE = ('<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>'
          + ''.join(f'<symbol id="i-{k}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{v}</symbol>' for k, v in ICONS.items())
          + '</defs></svg>')

# ----------------------------------------------------------------------------- CONTENT
NAV = [('Home', '#top'), ('Solutions', '#solutions'), ('Case Studies', '#case-studies')]

PILLARS = [
 ('layers', 'No Generic Software', 'Solutions engineered around your business.'),
 ('target', 'No One-Size-Fits-All', 'Every business gets a tailored approach.'),
 ('hierarchy', 'Hierarchy-Based Systems', 'Structured roles, permissions and workflows.'),
 ('shield', 'Security-First Engineering', 'Security considered throughout implementation.'),
]
STRIP = ['ERP Development', 'CRM Solutions', 'AI Agents', 'Workflow Automation', 'AI Integration', 'WhatsApp API Integration',
         'Data Integration', 'E-Commerce for Conversions', 'Secure Engineering']
STATS = [('layers', 20, '+', 'Industries'), ('briefcase', 50, '+', 'Projects Delivered'), ('calendar', 8, ' Weeks', 'Implementation Program')]

CLIENTS = [('avm.webp', 'Dr. Velumani', 88, 32), ('baskin-robbins.webp', 'Baskin Robbins', 150, 20), ('venkateshwara.webp', 'Venkateshwara Hospital', 34, 36),
           (None, 'Kenney Alpay', 0, 0), ('goat-media.webp', 'Goat Media', 36, 36), ('vaahaa.webp', 'Vaahaa', 54, 36), ('palagarakoodai.webp', 'Palagarakoodai', 80, 38)]

CAPS_SUMMARY = [
 ('01', 'Business Systems', 'Custom software and operating systems built around how your business works.'),
 ('02', 'Digital Commerce', 'High-performing ecommerce platforms and connected shopping experiences.'),
 ('03', 'AI & Automation', 'Intelligent workflows that reduce manual work and improve business efficiency.'),
 ('04', 'Marketing & Growth', 'Data-driven digital marketing strategies that attract customers and generate business opportunities.'),
]
STACK_LABEL = 'Technologies we engineer with'
CAPS = [
 dict(n=1, art=S.cap_os, pill='Next-Gen Business OS', h='Still running your growing business on spreadsheets, disconnected tools and manual updates?',
      p='Unify your departments, projects, people and processes with a custom-built Business OS designed around your workflows',
      items=['Custom ERP & Business OS', 'Project & Operations Management', 'CRM & Workflow Management', 'Department-Wise Systems & Automation'],
      tags=['Construction', 'Healthcare', 'Manufacturing', 'Real Estate', 'Professional Services'],
      stack_label=STACK_LABEL, stack=['azure', 'aws', 'mongodb', 'supabase', 'react', 'nodejs', 'python', 'docker', 'github', 'postgresql', 'redis', 'cloudflare'],
      cta="Let's Build a Solution, Not Just Software"),
 dict(n=2, art=S.cap_commerce, pill='High-Performance E-Commerce', h='Ready to turn your ecommerce vision into a high-performing online store?',
      p='Create conversion-focused ecommerce experiences around your brand story, with custom storefronts and customer journeys designed to increase sales and scale.',
      items=['Brand Story-Driven Storefronts', 'Custom Ecommerce Development', 'App-Free Functionality', 'Customer-Centric & Scalable Experiences'],
      tags=['D2C & Lifestyle', 'Fashion & Apparel', 'Jewellery', 'Beauty & Wellness', 'Food & Beverages', 'Electronics'],
      stack_label=STACK_LABEL, stack=['shopify', 'razorpay', 'stripe', 'shiprocket'],
      cta="Let's Build a High-Converting Ecommerce Store"),
 dict(n=3, art=S.cap_ai, pill='AI-Powered Business Solutions', h='Are manual tasks and repetitive workflows slowing your business growth?',
      p='Transform repetitive business processes with intelligent AI solutions and automation designed to reduce manual work, improve efficiency and scale operations.',
      items=['AI-Powered Business Solutions', 'Intelligent Workflow Automation', 'WhatsApp & Communication Automation', 'Custom AI Applications & Agents'],
      tags=[], stack_label=STACK_LABEL, stack=['openai', 'claude', 'gemini', 'elevenlabs', 'n8n', 'zapier', 'make', 'power-automate'],
      cta="Let's Automate the Way You Work"),
 dict(n=4, art=S.cap_growth, pill='Data-Driven Marketing & Growth', h='Are you reaching the right audience but struggling to generate quality leads and sales?',
      p='Drive growth through performance marketing, qualified leads, and strategies that increase visibility across search and AI platforms.',
      items=['Performance Marketing & Paid Campaigns', 'Lead Generation & Customer Acquisition', 'SEO, AEO & GEO', 'Conversion Rate Optimization & Marketing Analytics'],
      tags=[], stack_label='Marketing & Growth Technology Stack',
      stack=['meta-ads', 'google-ads', 'google-analytics', 'search-console', 'tag-manager', 'semrush', 'ahrefs', 'hotjar'],
      cta="Let's Build Your Growth Engine"),
]
TECH_NAMES = {'azure': 'Microsoft Azure', 'aws': 'AWS', 'mongodb': 'MongoDB', 'supabase': 'Supabase', 'react': 'React', 'nodejs': 'Node.js', 'python': 'Python',
              'docker': 'Docker', 'github': 'GitHub', 'postgresql': 'PostgreSQL', 'redis': 'Redis', 'cloudflare': 'Cloudflare', 'shopify': 'Shopify', 'razorpay': 'Razorpay',
              'stripe': 'Stripe', 'shiprocket': 'Shiprocket', 'openai': 'OpenAI', 'claude': 'Claude', 'gemini': 'Gemini', 'elevenlabs': 'ElevenLabs', 'n8n': 'n8n',
              'zapier': 'Zapier', 'make': 'Make', 'power-automate': 'Microsoft Power Automate', 'meta-ads': 'Meta Ads', 'google-ads': 'Google Ads',
              'google-analytics': 'Google Analytics 4', 'search-console': 'Google Search Console', 'tag-manager': 'Google Tag Manager', 'semrush': 'Semrush', 'ahrefs': 'Ahrefs', 'hotjar': 'Hotjar'}

PROBLEMS = [
 ('Disconnected Software', 'Your business tools operate in silos without sharing information.', 'Connected Business Systems', 'Connect your applications, departments and workflows in one ecosystem.', 'Software', 'layers'),
 ('Scattered Business Data', 'Important information is spread across spreadsheets and multiple platforms.', 'Centralized Business Data', 'Bring business information together for better access and decisions.', 'Data', 'db'),
 ('Manual & Repetitive Work', 'Teams waste valuable time on repetitive tasks and manual updates.', 'Intelligent Workflow Automation', 'Automate repetitive tasks and simplify everyday business operations.', 'Tasks', 'workflow'),
 ('Lack of Real-Time Visibility', 'You lack a clear view of business performance and progress.', 'Real-Time Business Intelligence', 'Monitor business performance through centralized dashboards and reports.', 'Visibility', 'chart'),
 ('Disconnected Teams & Processes', 'Departments struggle with communication gaps and uncoordinated workflows.', 'Unified Business Operations', 'Align teams, responsibilities and processes through connected workflows.', 'Teams', 'users'),
 ('Difficult to Scale', 'Existing systems cannot keep up with your growing business.', 'Scalable Technology Solutions', 'Build flexible systems that adapt as your business grows.', 'Scale', 'rocket'),
 ('Scattered Marketing & Lead Sources', 'Leads arrive from multiple channels but remain scattered across platforms.', 'Unified Lead Management', 'Capture, organize and track leads from every marketing channel in one place.', 'Leads', 'megaphone'),
]

STEPS = [
 ('01', 'Discover the Business', 'Understand how your business actually operates.', 'We study your existing processes, teams, tools, workflows and operational challenges to identify where technology can create meaningful value.', ['Business Discovery', 'Process Analysis', 'Workflow Mapping'], 'discover'),
 ('02', 'Engineer the Workflows', 'Design technology around real business requirements.', 'We map processes, redesign workflows and architect the right combination of custom software, existing platforms and AI capabilities.', ['Workflow Engineering', 'System Architecture', 'Solution Design'], 'engineer'),
 ('03', 'Build & Integrate', 'Develop and connect technology within your business environment.', 'We build custom applications, integrate existing systems and implement AI-powered workflows that solve practical business problems.', ['Custom Development', 'AI Implementation', 'API Integration'], 'build'),
 ('04', 'Deploy & Evolve', 'Make technology work in everyday business operations.', 'We deploy solutions, work with your teams, support adoption and continuously improve systems based on real operational feedback.', ['Deployment', 'Team Enablement', 'Optimization', 'Continuous Improvement'], 'deploy'),
]

PHASES = [
 dict(n='01', name='Engineer & Build', weeks='Week 01–04', tag='Understand. Design. Build.', deliver='Business Systems Ready for Implementation'),
 dict(n='02', name='Implement & Go Live', weeks='Week 05–08', tag='Connect. Deploy. Train.', deliver='Live Systems & Connected Workflows'),
]
WEEKS = [
 ('Discover & Audit', 'Understand Your Business from the Inside.', ['Existing Workflows', 'Organizational Hierarchy', 'Work Culture', 'Existing Systems & Software'], None),
 ('Design & Engineer', 'Design Technology Around Your Business.', ['Workflow Mapping', 'Process Redesign', 'System Architecture', 'Solution Planning'], None),
 ('Build & Configure', 'Build Solutions Around Real Business Needs.', ['Custom Development', 'System Configuration', 'Feature Engineering', 'Application Development'], None),
 ('Integrate & Prepare', 'Connect Systems and Prepare for Implementation.', ['API Integration', 'Data Structuring', 'System Testing', 'Deployment Preparation'], None),
 ('Live Data Integration', 'Bring Your Business Data into the System.', ['Lead Migration', 'Customer Records', 'Project Data', 'Financial Records'], None),
 ('Workflow Implementation', 'Connect Your Teams, Systems and Processes.', ['Department Integration', 'Workflow Automation', 'Task Coordination', 'Process Activation'], None),
 ('Team Deployment & Training', 'Prepare Your People to Work with the New Systems.', ['Employee Onboarding', 'Role Assignment', 'Hands-on Training', 'User Enablement'], None),
 ('Go Live & Optimization', 'Run Your Business Through Connected Systems.', [], 'A connected business environment where everyday operations, from leads to profit, are tracked through structured workflows.'),
]

DATA_CARDS = [
 ('01.', 'Customer & Sales Data', 'Bring every customer interaction and sales record together.', ['Customer Profiles & Contact Details', 'Leads & Enquiries', 'WhatsApp & Email Conversations', 'Sales Pipeline & Follow-ups', 'Customer Purchase History', 'Call & Interaction Records'], 'users'),
 ('02.', 'Marketing & Campaign Data', 'Connect marketing information from multiple channels to understand campaign performance.', ['Meta & Google Ads Data', 'Website Traffic & Analytics', 'Social Media Engagement', 'Campaign Performance', 'Lead Sources & Conversion Data', 'SEO & Search Performance'], 'megaphone'),
 ('03.', 'Business Operations Data', 'Bring operational information from departments, teams and business systems into one connected view.', ['Project & Task Records', 'Employee & Team Activities', 'Workflow & Process Logs', 'Department-Wise Reports', 'Inventory & Resource Data', 'Vendor & Procurement Records'], 'workflow'),
 ('', 'Financial & Performance Data', 'Connect financial records and business performance metrics to understand revenue, costs and profitability.', ['Sales & Revenue Records', 'Expenses & Transactions', 'Invoices & Payments', 'Budget & Cash Flow', 'Project-Wise Costs', 'Profitability & Financial Reports'], 'chart'),
]

IND_SEL = [  # section 07
 ('construction', 'Construction & Real Estate', 'Are your projects, site teams and materials difficult to manage?',
  'Connect project management, site operations, procurement, materials, contractors and financial workflows through integrated construction business systems.',
  ['Construction ERP', 'Project Management', 'Procurement Management', 'Site Operations', 'Business Automation'], 'Stop Profit Leakage in Your Projects'),
 ('healthcare', 'Healthcare', 'Are disconnected systems making patient and clinic management harder?',
  'Simplify clinic and hospital operations with digital systems that connect patients, appointments, branches, teams and business workflows.',
  ['Healthcare Management Systems', 'Clinic Management', 'Patient Workflows', 'Appointment Management', 'Automation'], 'Stop Losing Revenue Through Operational Gaps'),
 ('ecommerce', 'Ecommerce & D2C', 'Are you struggling to turn website visitors into paying customers?',
  'Build high-performing ecommerce platforms that connect storefronts, products, orders, inventory, customer experiences and marketing.',
  ['Shopify Development', 'Custom Ecommerce', 'Conversion Optimization', 'Ecommerce Integrations', 'Performance Marketing'], 'Turn More Visitors into Paying Customers'),
 ('jewellery', 'Jewellery & Retail', 'Are inventory, customer data and sales spread across different systems?',
  'Improve retail operations through connected digital commerce, inventory visibility, customer management and business automation.',
  ['Retail Management', 'Inventory Systems', 'Shopify', 'Customer Management', 'Automation'], 'Stop Losing Sales Through Inventory Gaps'),
 ('manufacturing', 'Manufacturing & Distribution', 'Are production, inventory and supply chain operations difficult to coordinate?',
  'Improve operational visibility by connecting inventory, purchasing, production workflows, vendors and business processes.',
  ['Manufacturing ERP', 'Inventory Management', 'Procurement', 'Vendor Management', 'Operations Automation'], 'Reduce Production Waste. Protect Your Profits'),
]

CASES = [
 dict(n='01', kind='Construction & Real Estate', h='Multiple Construction Operations. One Connected Business System.',
      p='A custom-engineered Construction Business OS connecting project management, site operations, procurement, materials, contractors and finance, bringing greater visibility and control across everyday construction workflows.',
      lab='Solutions', items=['Project Management', 'Site Operations', 'Procurement', 'Material Management', 'Contractor Management', 'Finance (Profit Per Project)'], cta='Read Case Study', art=S2.case_construction),
 dict(n='02', kind='Ecommerce Solutions', h='Beyond Standard Shopify. 100+ Custom Product Pages Built to Convert.',
      p='A custom-developed Shopify ecommerce experience featuring 100+ uniquely designed product pages, intuitive navigation and tailored functionality. Engineered to simplify product discovery, create a seamless shopping journey and support higher conversion rates.',
      lab='Capabilities', items=['Custom Shopify Development', '100+ Unique Product Pages', 'Custom Functionality', 'User Experience Design', 'Conversion Rate Optimization'], cta='Explore Project', art=S2.case_shopify),
 dict(n='03', kind='Healthcare', h='Connected Digital Solutions for Multi-Branch Healthcare Operations.',
      p='Digital solutions supporting a multi-branch physiotherapy business through its online presence, digital marketing and operational technology initiatives. Bringing together patient-focused digital experiences and business growth solutions.',
      lab='Capabilities', items=['Healthcare Digital Solutions', 'SEO', 'Performance Marketing', 'Business Systems', 'Digital Growth'], cta='Explore Project', art=S2.case_health),
 dict(n='04', kind='D2C & Ecommerce', h='A Better Ecommerce Experience. Built Around the Customer Journey.',
      p='A tailored ecommerce experience designed to improve product discovery, simplify navigation and create a smoother online shopping journey. Focused on delivering a user-friendly storefront that supports the needs of a growing D2C business.',
      lab='Capabilities', items=['Shopify Development', 'Ecommerce Design', 'User Experience', 'Store Optimization'], cta='Explore Project', art=S2.case_d2c),
 dict(n='05', kind='AI & Business Automation', h='Smarter Customer Communication Through WhatsApp Automation.',
      p='A business communication automation solution designed to streamline customer interactions and connect WhatsApp communication with structured business workflows, helping businesses manage conversations more efficiently.',
      lab='Capabilities', items=['WhatsApp Automation', 'Business Communication', 'Workflow Automation', 'System Integration'], cta='Explore Project', art=S2.case_whatsapp),
]

IND_FOCUS = [
 ('construction', 'Construction & Real Estate', 'Bring Every Project. Every Team. Every Operation Together.', 'Connect project management, site operations, procurement, materials, contractors and financial workflows through integrated construction business systems.', ['Construction ERP', 'Project Management', 'Procurement Management', 'Site Operations', 'Business Automation']),
 ('healthcare', 'Healthcare', 'Build Better Connected Healthcare Operations.', 'Simplify clinic and hospital operations with digital systems that connect patients, appointments, branches, teams and business workflows.', ['Healthcare Management Systems', 'Clinic Management', 'Patient Workflows', 'Appointment Management', 'Automation']),
 ('ecommerce', 'Ecommerce & D2C', 'Create Commerce Experiences Built for Growth.', 'Build high-performing ecommerce platforms that connect storefronts, products, orders, inventory, customer experiences and marketing.', ['Shopify Development', 'Custom Ecommerce', 'Conversion Optimization', 'Ecommerce Integrations', 'Performance Marketing']),
 ('jewellery', 'Jewellery & Retail', 'Connect Your Retail Business from Inventory to Customer.', 'Improve retail operations through connected digital commerce, inventory visibility, customer management and business automation.', ['Retail Management', 'Inventory Systems', 'Shopify', 'Customer Management', 'Automation']),
 ('manufacturing', 'Manufacturing & Distribution', 'Connect Production, Procurement and Business Operations.', 'Improve operational visibility by connecting inventory, purchasing, production workflows, vendors and business processes.', ['Manufacturing ERP', 'Inventory Management', 'Procurement', 'Vendor Management', 'Operations Automation']),
 ('professional', 'Professional Services', 'Build More Structured and Efficient Service Businesses.', 'Connect customer acquisition, sales pipelines, projects, teams, delivery workflows and business performance.', ['CRM', 'Project Management', 'Business Automation', 'Workflow Management', 'Business Intelligence']),
]

# ----------------------------------------------------------------------------- SECTIONS
def header():
    links = ''.join(f'<a href="{h}" data-nav>{t}</a>' for t, h in [(a, b) for a, b in NAV])
    return f'''
<header class="nav" id="nav">
  <div class="nav__bar">
    <a class="nav__brand" href="#top" aria-label="Drawlead home"><img src="assets/logos/drawlead-dark.webp" alt="Drawlead – Digital Transformation Company" width="153" height="30"></a>
    <nav class="nav__links" aria-label="Primary">{links}</nav>
    <a class="btn btn--dark nav__cta" href="#contact"><span>Free Consultation Call</span></a>
    <button class="nav__burger" type="button" aria-expanded="false" aria-controls="drawer" aria-label="Open menu">{ic('menu')}{ic('x')}</button>
  </div>
  <div class="nav__drawer" id="drawer" hidden>
    <nav aria-label="Mobile">{links}<a class="btn btn--green" href="#contact"><span>Free Consultation Call</span></a></nav>
  </div>
</header>'''

def hero():
    pillars = ''.join(f'<li {reveal(.05*i)}><span class="pillar__ic">{ic(i_)}</span><h3>{t}</h3><p>{d}</p></li>' for i, (i_, t, d) in enumerate(PILLARS))
    strip = ''.join(f'<li>{e(s)}</li>' for s in STRIP)
    return f'''
<section class="hero" id="top">
  <div class="hero__bg" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i><i class="grid"></i></div>
  <div class="wrap">
    <div class="hero__top">
      <div class="hero__copy">
        <span class="pill pill--outline reveal">{ic('sparkle')}Turning Business Data into Intelligent AI &amp; Automation Solutions</span>
        <h1 class="hero__title">
          <span class="strike reveal" style="--d:.08s"><s>Stop Managing Business Complexity.</s></span>
          <span class="hero__start reveal" style="--d:.2s">Start Building <em>Smarter Business Solutions.</em></span>
        </h1>
      </div>
      <div class="hero__art il reveal" style="--d:.25s" aria-hidden="true"><div class="hero__art-d">{S.hero_net()}</div><div class="hero__art-m">{S.hero_mobile()}</div></div>
    </div>
    <div class="hero__mid">
      <div class="ctacard reveal" style="--d:.1s">
        <p>Looking for a structured business transformation?</p>
        {btn('Explore Our 8-Week Program', '#program', 'green')}
      </div>
      <p class="hero__lede reveal" style="--d:.18s">We work closely with your teams to understand your business, identify challenges and build technology around your needs. From custom business systems and software to AI, automation and digital commerce, we create connected solutions that simplify operations and drive growth.</p>
    </div>
    <ul class="pillars" role="list">{pillars}</ul>
  </div>
  <div class="strip" aria-label="Services">
    <div class="marquee marquee--strip"><ul class="marquee__track" role="list">{rep(strip, 4)}</ul></div>
  </div>
</section>'''

def client_chip(c):
    img, label, w, h = c
    if img:
        mark = f'<img src="assets/logos/{img}" alt="" height="{h}" loading="lazy" style="height:{h}px;width:auto">'
    else:
        mark = ''
    cls = 'logo logo--text' if not img else 'logo'
    return f'<li class="{cls}">{mark}<span>{e(label)}</span></li>'

def credibility():
    stats = ''.join(
        f'<li class="stat reveal" style="--d:{.08*i}s"><span class="stat__ic">{ic(i_)}</span><div><b class="stat__n"><span data-count="{n}">{n}</span>{suf}</b><span class="stat__l">{l}</span></div></li>'
        for i, (i_, n, suf, l) in enumerate(STATS))
    row = ''.join(client_chip(c) for c in CLIENTS)
    row2 = ''.join(client_chip(c) for c in reversed(CLIENTS))
    return f'''
<section class="cred" id="credibility">
  <div class="wrap">
    <div class="cred__head">
      <div>
        {pill('Trusted Technology Partner', 'shield', 'reveal')}
        <h2 class="h2 reveal" style="--d:.06s">Engineering Partnerships.<br><span class="hl">Building Business Solutions.</span></h2>
      </div>
      <ul class="stats" role="list">{stats}</ul>
    </div>
  </div>
  <div class="logorows reveal" style="--d:.1s" aria-label="Client logos">
    <div class="marquee marquee--logos"><ul class="marquee__track" role="list">{rep(row, 3)}</ul></div>
    <div class="marquee marquee--logos marquee--rev"><ul class="marquee__track" role="list">{rep(row2, 3)}</ul></div>
  </div>
  <p class="cred__note reveal">Client identities remain confidential where protected by NDA</p>
</section>'''

def tech_row(names):
    chips = ''.join(f'<li class="tech"><img src="assets/tech/{n}.webp" alt="{e(TECH_NAMES[n])}" height="26" loading="lazy"></li>' for n in names)
    return f'<div class="marquee marquee--tech"><ul class="marquee__track" role="list">{rep(chips, 2)}</ul></div>'

def capabilities():
    summ = ''.join(f'<li><button type="button" class="capsum" data-target="cap-{i+1}" {reveal(.06*i)}><span class="capsum__n">{n}</span><b>{t}</b><span>{d}</span>{ic("arrow", "ic capsum__arr")}</button></li>'
                   for i, (n, t, d) in enumerate(CAPS_SUMMARY))
    cards = ''
    for i, c in enumerate(CAPS):
        items = ''.join(f'<li>{ic("check")}{e(x)}</li>' for x in c['items'])
        tg = tags(c['tags'], 'tags tags--dark') if c['tags'] else ''
        cards += f'''
    <article class="cap spot reveal" id="cap-{c['n']}" style="--d:{.07*(i%2)}s" tabindex="-1">
      <div class="cap__art il on-dark">{c['art']()}</div>
      <div class="cap__body">
        {pill(c['pill'], None, 'pill--mint')}
        <h3 class="cap__h">{e(c['h'])}</h3>
        <p class="cap__p">{e(c['p'])}</p>
        <ul class="checks" role="list">{items}</ul>
        {tg}
        <div class="stack"><span class="stack__l">{e(c['stack_label'])}</span>{tech_row(c['stack'])}</div>
        {btn(e(c['cta']), '#contact', 'light')}
      </div>
    </article>'''
    return f'''
<section class="caps" id="solutions">
  <div class="wrap">
    <div class="caps__head">
      <div>
        {pill('Our Core Capabilities', None, 'pill--mint reveal')}
        <h2 class="h2 h2--light reveal" style="--d:.06s">Better Systems.<br>Smoother Operations.<br>Stronger Business Growth.</h2>
      </div>
      <p class="lede lede--light reveal" style="--d:.12s">We bring customer-centric development, intelligent business systems, workflow automation and strategic marketing together to help businesses streamline operations, create better customer experiences and accelerate growth. Every solution is designed around your business needs, your customers and your long-term goals.</p>
    </div>
    <ul class="capsums" role="list">{summ}</ul>
    <div class="capgrid">{cards}</div>
  </div>
</section>'''

def morph_svg():
    rnd = [(60, 6, -12), (430, 104, 9), (250, 18, 7), (700, 4, -8), (150, 106, 11), (560, 84, -10), (820, 98, 6)]
    tiles, stubs = '', ''
    for i, (lab, ic_) in enumerate([(p[4], p[5]) for p in PROBLEMS]):
        ex, ey = 20 + i * 132, 57
        sx, sy, sr = rnd[i]
        tiles += (f'<g class="mt" data-sx="{sx}" data-sy="{sy}" data-sr="{sr}" data-ex="{ex}" data-ey="{ey}" transform="translate({ex} {ey})">'
                  f'<rect class="mt__r" width="112" height="56" rx="16"/><g class="mt__i">{IC(ic_, 14, 16, 24, "currentColor")}</g>'
                  f'<text x="48" y="33" class="mt__t">{lab}</text></g>')
    for a, b in [(0, 3), (2, 5), (1, 6), (4, 2)]:
        stubs += f'<path class="stub" data-a="{a}" data-b="{b}" d="M0 0"/>'
    return (f'<svg viewBox="0 0 960 170" class="morph__svg" aria-hidden="true" focusable="false"><path class="bus" d="M70 85 H890" pathLength="1"/>{stubs}{tiles}</svg>')

def problems():
    rows = ''
    for i, (pt, pd, st, sd, _, _i) in enumerate(PROBLEMS):
        rows += f'''
      <li class="pair" data-pair="{i}">
        <div class="pcard pcard--p" tabindex="0"><span class="pcard__n">{i+1:02d}.</span><div><h3>{pt}</h3><p>{pd}</p></div><span class="pcard__x">{ic('x')}</span></div>
        <span class="pair__link" aria-hidden="true"><i></i></span>
        <div class="pcard pcard--s" tabindex="0"><span class="pcard__n">{i+1:02d}.</span><div><h3>{st}</h3><p>{sd}</p></div><span class="pcard__x">{ic('check')}</span></div>
      </li>'''
    return f'''
<section class="prob" id="problems">
  <div class="wrap">
    <div class="center">
      <h2 class="h2 reveal">More Tools. More Complexity.<br>Less Control.</h2>
      <p class="lede reveal" style="--d:.08s">As your business grows, disconnected software, scattered data and manual processes make operations harder to manage. We help connect your teams, systems and workflows into one streamlined business ecosystem.</p>
    </div>
    <div class="scene">
      <div class="morph" data-morph>{morph_svg()}</div>
      <div class="panels">
        <div class="panels__head"><span class="plabel plabel--p">{ic('x')}The Problems</span><span class="plabel plabel--s">{ic('check')}The Solutions</span></div>
        <ol class="pairs" role="list">{rows}</ol>
      </div>
    </div>
    <div class="center reveal">{btn("Let’s Build What Your Business Needs", '#contact', 'green')}</div>
  </div>
</section>'''

def approach():
    steps = ''
    for i, (n, t, h, p, tg, kind) in enumerate(STEPS):
        steps += f'''
      <li class="step spot reveal" style="--d:{.1*i}s">
        <span class="step__node" aria-hidden="true"><b>{n}</b></span>
        <div class="step__card">
          <div class="step__art il">{S.how(kind)}</div>
          <span class="step__k">{n} — {t}</span>
          <h3>{h}</h3>
          <p>{p}</p>
          {tags(tg)}
        </div>
      </li>'''
    return f'''
<section class="appr" id="approach">
  <div class="wrap">
    <div class="appr__head">
      <div>
        {pill('Working With Your Team. Building for Your Business.', None, 'reveal')}
        <h2 class="h2 reveal" style="--d:.06s">Your Business Has Its Own Way of Working. Your Technology Should Too.</h2>
      </div>
      <div class="appr__side reveal" style="--d:.12s">
        <p class="lede">We work directly with your teams to understand how your business operates, identify challenges and uncover opportunities. Our engineers collaborate with your people to design, build and implement technology that fits your actual workflows — from custom software and system integrations to AI and automation.</p>
        {btn('Explore Our Engineering Approach', '#program', 'dark')}
      </div>
    </div>
    <div class="flow" data-flow>
      <div class="flow__rail" aria-hidden="true"><i class="flow__fill"></i><i class="flow__dot"></i></div>
      <ol class="steps" role="list">{steps}</ol>
    </div>
  </div>
</section>'''

def program():
    ph = ''.join(f'''<div class="phase" data-phase="{i}"><span class="phase__k">Phase {p['n']} · {p['weeks']}</span><b>{p['name']}</b>
      <span class="phase__t">{p['tag']}</span><span class="phase__d">{ic('check')}<em>Deliverable</em> {p['deliver']}</span></div>''' for i, p in enumerate(PHASES))
    rail = ''.join(f'''<button type="button" role="tab" class="wk" id="wk-tab-{i+1}" aria-controls="wk-panel-{i+1}" aria-selected="{'true' if i == 0 else 'false'}" tabindex="{0 if i == 0 else -1}" data-i="{i}">
        <span class="wk__n">{i+1:02d}</span><span class="wk__l">Week {i+1:02d}</span><i class="wk__bar"><u></u></i></button>''' for i in range(8))
    panels = ''
    for i, (t, h, tg, body) in enumerate(WEEKS):
        phase = 0 if i < 4 else 1
        content = tags(tg, 'tags tags--dark') if tg else f'<p class="wkp__body">{e(body)}</p>'
        panels += f'''
      <div class="wkp" role="tabpanel" id="wk-panel-{i+1}" aria-labelledby="wk-tab-{i+1}" {'hidden' if i else ''} tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">{i+1:02d}</span>
          <span class="wkp__k">{i+1:02d} — Week {i+1:02d} <i>|</i> {t}</span>
          <h3>{h}</h3>
          {content}
          <span class="wkp__phase">Phase {PHASES[phase]['n']} — {PHASES[phase]['name']}</span>
        </div>
        <div class="wkp__art il on-dark">{S.week(i+1)}</div>
      </div>'''
    return f'''
<section class="prog" id="program">
  <div class="prog__glow" aria-hidden="true"></div>
  <div class="wrap">
    <div class="prog__head">
      {pill('A Structured Business Implementation Program', None, 'pill--mint reveal')}
      <h2 class="h2 h2--light reveal" style="--d:.06s">Transform the Way Your Business Works.<br><span class="grn">In 8 Weeks.</span></h2>
      <p class="lede lede--light reveal" style="--d:.1s">Move beyond disconnected tools, manual processes and operational bottlenecks.</p>
      <p class="lede lede--light reveal" style="--d:.14s">Our structured 8-week transformation program helps businesses understand their challenges, redesign workflows, implement intelligent technology and establish connected business operations.</p>
    </div>
    <div class="timeline reveal" data-timeline>
      <div class="phases">{ph}</div>
      <div class="wkrail" role="tablist" aria-label="8-week program timeline">{rail}<i class="wkrail__line" aria-hidden="true"><u></u></i></div>
      <div class="wkstage">{panels}</div>
      <div class="wkctl">
        <button type="button" class="iconbtn" data-prev aria-label="Previous week">{ic('left')}</button>
        <button type="button" class="iconbtn" data-play aria-label="Pause automatic progression" aria-pressed="false">{ic('pause')}</button>
        <button type="button" class="iconbtn" data-next aria-label="Next week">{ic('right')}</button>
      </div>
    </div>
    <div class="prog__cta reveal">
      {btn('Begin Your 8-Week Implementation Journey', '#contact', 'green')}
      <p class="fine">The 8-week timeline is based on agreed scope, system complexity, data readiness and client participation. Additional integrations or larger requirements may require an extended implementation period.</p>
    </div>
  </div>
</section>'''

def data():
    cards = ''
    for i, (n, t, d, items, i_) in enumerate(DATA_CARDS):
        li = ''.join(f'<li>{e(x)}</li>' for x in items)
        cards += f'''
      <article class="dcard spot reveal" data-src="{i+1}" style="--d:{.06*i}s" tabindex="0">
        <div class="dcard__top"><span class="dcard__ic">{ic(i_)}</span><h3>{(n + ' ') if n else ''}{t}</h3></div>
        <p>{d}</p>
        <ul class="dlist" role="list">{li}</ul>
      </article>'''
    return f'''
<section class="data" id="data">
  <div class="wrap">
    <div class="data__head center">
      {pill('From Scattered Information to Smarter Decisions', None, 'reveal')}
      <h2 class="h2 reveal" style="--d:.06s">Your Business Generates Data.<br>We Turn It Into Intelligence!</h2>
      <ul class="ticker reveal" role="list" style="--d:.1s"><li>Scattered Data</li><li>Missed Activity Logs</li><li>Disconnected Customer Records</li></ul>
      <p class="lede reveal" style="--d:.12s">We bring scattered business data together from multiple platforms, conversations, spreadsheets and existing systems. By structuring, connecting and analysing this information, we help businesses gain clear visibility, track activities and make smarter decisions.</p>
    </div>
    <div class="data__grid">
      <div class="data__viz il reveal" data-viz>{S2.data_viz()}</div>
      <div class="data__cards">{cards}</div>
    </div>
    <div class="databanner reveal">
      <div><h3>Stop Looking at Scattered Reports.<br>Start Understanding Your Business.</h3><p>Connect your business data and turn information into actionable intelligence.</p></div>
      {btn('Explore Data Intelligence', '#contact', 'green')}
    </div>
  </div>
</section>'''

def industries_select():
    tabs, panels = '', ''
    for i, (k, t, q, p, sol, cta) in enumerate(IND_SEL):
        tabs += f'''<button type="button" role="tab" class="itab" id="it-{i+1}" aria-controls="ip-{i+1}" aria-selected="{'true' if i == 0 else 'false'}" tabindex="{0 if i == 0 else -1}"><span class="itab__n">{i+1:02d}</span><span class="itab__t">{t}</span>{ic('arrow')}</button>'''
        panels += f'''
      <div class="ipanel" role="tabpanel" id="ip-{i+1}" aria-labelledby="it-{i+1}" {'hidden' if i else ''} tabindex="0">
        <div class="ipanel__art il">{S2.ind(k)}</div>
        <div class="ipanel__body">
          <span class="ipanel__k">{i+1:02d} — {t}</span>
          <h3>{q}</h3>
          <p>{p}</p>
          <div class="sol"><b>Solutions:</b>{tags(sol)}</div>
          {btn(cta, '#contact', 'dark')}
        </div>
      </div>'''
    return f'''
<section class="indsel" id="industries">
  <div class="wrap">
    <div class="indsel__head">
      <div>
        {pill('Same Industry ≠ Same Business Challenges', None, 'reveal')}
        <h2 class="h2 reveal" style="--d:.06s">We Don't Build for Industries.<br>We Build for Businesses.</h2>
      </div>
      <div class="indsel__text reveal" style="--d:.1s">
        <p class="lede">Every business operates differently. Even two companies in the same industry can have completely different business models, workflows, team structures and operational challenges.</p>
        <p class="lede">A construction company managing multiple sites may need an entirely different system from another construction company focused on property sales and customer management.</p>
        <p class="lede lede--strong">That's why we don't believe in one-size-fits-all software.</p>
        <p class="lede">We understand your industry, study how your business operates and engineer technology around your specific processes, people and business requirements.</p>
      </div>
    </div>
    <div class="itabs reveal" data-itabs>
      <div class="itablist" role="tablist" aria-label="Industry challenges" aria-orientation="vertical">{tabs}</div>
      <div class="ipanels">{panels}</div>
    </div>
  </div>
</section>'''

def cases():
    out = ''
    for i, c in enumerate(CASES):
        its = tags(c['items'])
        out += f'''
    <article class="case case--{i+1} spot reveal" style="--d:{.06*(i%2)}s">
      <div class="case__art il{' on-dark' if i == 0 else ''}">{c['art']()}</div>
      <div class="case__body">
        <span class="case__k"><b>Project {c['n']}</b>{c['kind']}</span>
        <h3>{c['h']}</h3>
        <p>{c['p']}</p>
        <div class="sol"><b>{c['lab']}</b>{its}</div>
        <a class="link" href="#contact"><span>{c['cta']}</span>{ic('arrow')}</a>
      </div>
    </article>'''
    return f'''
<section class="cases" id="case-studies">
  <div class="wrap">
    <div class="cases__head">
      <div>
        {pill('Selected Work. Proven Implementation', None, 'reveal')}
        <h2 class="h2 reveal" style="--d:.06s">We Understood.<br>We Engineered. We Made It Work</h2>
      </div>
      <div class="reveal" style="--d:.1s">
        <p class="lede">We work closely with businesses to understand their challenges, engineer tailored technology solutions and implement systems that make everyday operations more connected, efficient and scalable.</p>
        <p class="nda">{ic('lock')}Client names and identities remain confidential, as every engagement is governed by a NDA.</p>
      </div>
    </div>
    <div class="casegrid">{out}</div>
    <div class="more reveal">
      <div>{pill('Explore More', None, 'pill--mint')}<h3>Technology for Every Stage of Your Business.</h3>
      <p>From custom business systems and software engineering to digital commerce, AI implementation, automation and marketing, discover the complete range of solutions we build to help businesses move forward.</p></div>
      {btn('Explore Our Service Portfolio', '#solutions', 'green')}
    </div>
  </div>
</section>'''

def industry_focus():
    cards = ''
    for i, (k, t, h, p, sol) in enumerate(IND_FOCUS):
        cards += f'''
      <article class="icard spot reveal" style="--d:{.06*(i%3)}s">
        <div class="icard__art il on-dark">{S2.ind(k)}</div>
        <div class="icard__body">
          <span class="icard__k">{i+1:02d} — {t}</span>
          <h3>{h}</h3>
          <p>{p}</p>
          <div class="sol"><b>Solutions:</b>{tags(sol, 'tags tags--dark')}</div>
        </div>
      </article>'''
    return f'''
<section class="indf" id="industry-focus">
  <div class="wrap">
    <div class="indf__head">
      <div>
        {pill('Industry-Focused Engineering', None, 'pill--mint reveal')}
        <h2 class="h2 h2--light reveal" style="--d:.06s">Different Industries. Different Challenges. Technology Built to Fit.</h2>
      </div>
      <div class="reveal" style="--d:.1s">
        <p class="lede lede--light">Every industry has its own way of operating. From managing construction projects and healthcare operations to scaling ecommerce and manufacturing businesses, we understand that one-size-fits-all software doesn't solve every business challenge.</p>
        <p class="lede lede--light">Drawlead combines business understanding, custom engineering, AI and digital technology to build solutions around the unique requirements of each industry.</p>
      </div>
    </div>
    <div class="igrid">{cards}</div>
    <div class="bcta reveal">
      <div><h3>Your Industry. Your Challenges.<br>Our Engineering.</h3><p>Let's understand your business and identify the technology solutions that will move it forward.</p></div>
      {btn('Discuss Your Industry Requirements', '#contact', 'green')}
    </div>
  </div>
</section>'''

def testimonials():
    cards = ''
    ph = [('[Client Name / Business Name]', '[Insert the client\'s actual feedback about their experience working with Drawlead.]', '[Client Name], [Designation]'),
          ('[Client Name / Business Name]', '[Insert genuine feedback about the solution, collaboration or project delivery.]', '[Client Name], [Designation]'),
          ('[Client Name / Business Name]', '[Insert genuine feedback about the business impact or overall working relationship.]', '[Client Name], [Designation]')]
    for i, (n, q, a) in enumerate(ph):
        cards += f'''
      <figure class="tcard spot reveal" style="--d:{.08*i}s" data-testimonial-slot="{i+1}">
        <span class="tcard__q">{ic('quote')}</span>
        <figcaption class="tcard__who"><span class="tcard__av" aria-hidden="true">{ic('users')}</span><b>{n}</b></figcaption>
        <blockquote>“{q}”</blockquote>
        <p class="tcard__by">— {a}</p>
      </figure>'''
    return f'''
<section class="test" id="testimonials">
  <div class="wrap">
    <div class="center">
      {pill('Client Experiences', None, 'reveal')}
      <h2 class="h2 reveal" style="--d:.06s">Built on Trust.<br>Strengthened by Results.</h2>
      <p class="lede reveal" style="--d:.1s">Great technology is built through great partnerships. We work closely with our clients to understand their business, solve complex challenges and deliver solutions that create lasting value.</p>
      <p class="lede reveal" style="--d:.14s">Here's what our clients have to say about working with Drawlead.</p>
    </div>
    <div class="tgrid">{cards}</div>
    <div class="bcta bcta--light reveal">
      <div><h3>Your Business Could Be Our Next Success Story.</h3><p>Let's build something meaningful together.</p></div>
      {btn('Start a Conversation', '#contact', 'dark')}
    </div>
  </div>
</section>'''

def final_cta():
    return f'''
<section class="final" id="contact">
  <div class="final__bg" aria-hidden="true"><i class="orb o1"></i><i class="orb o2"></i><i class="grid"></i></div>
  <div class="wrap final__in">
    {pill("Let's Build Something Better", None, 'pill--mint reveal')}
    <h2 class="h1x reveal" style="--d:.06s">Your Next Business Transformation <span class="grn">Starts Here.</span></h2>
    <p class="lede lede--light reveal" style="--d:.1s">Whether you're looking to build a custom business system, develop a high-performing ecommerce platform, implement AI-powered automation or accelerate digital growth, we're ready to understand your requirements and engineer the right solution.</p>
    <p class="final__line reveal" style="--d:.14s">Let's turn your business challenges into technology opportunities.</p>
    <div class="final__ctas reveal" style="--d:.18s">{btn('Book a Consultation', '#contact', 'green')}{btn('Explore Our Services', '#solutions', 'ghost')}</div>
  </div>
</section>
<footer class="foot">
  <div class="wrap foot__in">
    <a href="#top" aria-label="Drawlead – back to top"><img src="assets/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <nav aria-label="Footer"><a href="#solutions">Solutions</a><a href="#program">8-Week Program</a><a href="#case-studies">Case Studies</a><a href="#industry-focus">Industries</a></nav>
  </div>
</footer>'''

def page():
    body = ''.join([header(), '<main>', hero(), credibility(), capabilities(), problems(), approach(), program(), data(),
                    industries_select(), cases(), industry_focus(), testimonials(), final_cta().replace('<footer', '</main><footer', 1), ])
    return f'''<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Drawlead</title>
<meta name="description" content="Business systems, digital commerce, AI and automation. Drawlead engineers connected technology around your business.">
<meta name="theme-color" content="#32B46F">
<link rel="icon" href="assets/logos/drawlead-mark.webp" type="image/webp">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/styles.css">
<script>document.documentElement.classList.add('js')</script>
</head>
<body>
{SPRITE}
<a class="skip" href="#top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div>
{body}
<script src="js/main.js" defer></script>
</body>
</html>'''

def uniquify_svg_ids(doc):
    """SVG illustrations are reused in several sections; give every repeated id a unique suffix."""
    import re
    seen, n = set(), [0]
    def fix(m):
        block = m.group(0)
        ids = re.findall(r'\bid="([^"]+)"', block)
        for i_ in ids:
            if i_ in seen:
                n[0] += 1
                new = f'{i_}-{n[0]}'
                block = block.replace(f'id="{i_}"', f'id="{new}"').replace(f'url(#{i_})', f'url(#{new})')
            else:
                seen.add(i_)
        return block
    return re.sub(r'<svg class="il-svg".*?</svg>', fix, doc, flags=re.S)

if __name__ == '__main__':
    out = uniquify_svg_ids(page())
    with open(OUT, 'w') as f:
        f.write(out)
    print('built', len(out) // 1024, 'KB')
