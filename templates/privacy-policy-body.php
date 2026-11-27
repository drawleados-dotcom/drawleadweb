<?php
/**
 * Privacy Policy (/privacy-policy). Migration 033 adds the page; layout and
 * notes are in partials/legal-top.php.
 *
 * Written from what the site actually does (Nov 2026): the booking form
 * (api/booking-submit.php, confirmation email to the visitor), the chat
 * assistant (api/whatsapp-submit.php, stays on the site), the free analysis
 * (includes/analyze.php), how-you-arrived tracking (assets/lead-source.js),
 * Microsoft Clarity and optional Google Analytics (layout-start.php), the
 * session cookie and Google Fonts. Update it when any of those change, and
 * move $legalUpdated with it.
 */
$legalTitle = 'Privacy Policy';
$legalLede = 'How Drawlead collects, uses and protects your personal data when you visit drawlead.com, book a consultation or chat with us.';
$legalUpdated = '27 November 2026';
include __DIR__ . '/partials/legal-top.php';
?>
    <section id="summary" class="legal-summary">
      <h2>The short version</h2>
      <ul>
        <li>We collect the details you give us when you book a consultation, chat with us or use our free website analysis, plus basic information about how you use the site.</li>
        <li>We use them to reply to you, run your consultation, deliver our services and improve the website.</li>
        <li>We do not sell or rent your personal data.</li>
        <li>You can ask us to see, correct or delete your data at any time by emailing <a href="mailto:official@drawlead.co.in">official@drawlead.co.in</a>.</li>
      </ul>
    </section>

    <section id="who-we-are">
      <h2>Who we are</h2>
      <p>Drawlead (&ldquo;Drawlead&rdquo;, &ldquo;we&rdquo;, &ldquo;us&rdquo;) is a technology company based in Chennai, Tamil Nadu, India. We build business systems, digital commerce platforms and growth programmes for businesses.</p>
      <p>This policy covers the personal data we collect through drawlead.com, including its forms, chat assistant and free tools. When we build or run systems for a client, the data inside those systems is handled under our agreement with that client, not under this policy.</p>
    </section>

    <section id="information-we-collect">
      <h2>Information we collect</h2>
      <h3>Information you give us</h3>
      <ul>
        <li><strong>Consultation bookings:</strong> your name, email address, phone number, company name, anything else you add to the booking form, and the date and time you choose.</li>
        <li><strong>Chat assistant:</strong> your answers to the assistant&rsquo;s questions, such as the service you are interested in, and the phone number you share so that we can reach you.</li>
        <li><strong>Free website analysis:</strong> the web address you submit. We fetch that public page to prepare your report.</li>
        <li><strong>Emails and calls:</strong> whatever you choose to tell us when you contact us directly.</li>
      </ul>
      <h3>Information collected automatically</h3>
      <ul>
        <li><strong>How you found us:</strong> the website that referred you, campaign tags in the link you followed (such as utm_source), advertising click identifiers from Google, Microsoft, Meta, LinkedIn or TikTok, and the page you first landed on. This is kept in your browser and reaches us only if you submit a booking or a chat enquiry, so that we know which channels bring enquiries.</li>
        <li><strong>How you use the site:</strong> we use Microsoft Clarity, which records how visitors move through our pages, including clicks, scrolling and mouse movement, along with device, browser and approximate location. We may also use Google Analytics to measure visits. We look at these insights to improve the website.</li>
        <li><strong>Technical data:</strong> like every website, our hosting servers log your IP address, browser type, the pages you request and the time of each request, to keep the site secure and running.</li>
      </ul>
    </section>

    <section id="how-we-use-it">
      <h2>How we use your information</h2>
      <ul>
        <li>To reply to your enquiry and to schedule, confirm and hold your consultation, including sending you a confirmation email.</li>
        <li>To prepare proposals and deliver the services you engage us for.</li>
        <li>To understand which pages, campaigns and channels bring enquiries, and to improve the website and our services.</li>
        <li>To keep the website secure and prevent misuse of our forms and tools.</li>
        <li>To meet our legal, tax and accounting obligations.</li>
      </ul>
      <p>We use the details you submit on the basis of the consent you give when you submit a form, and for other purposes that Indian law allows, such as meeting a legal obligation. You can withdraw your consent at any time (see Your rights). Withdrawing it does not affect anything we did before.</p>
      <p>We contact you about your enquiry using the details you gave us. If we send you marketing messages, each one lets you opt out.</p>
    </section>

    <section id="cookies">
      <h2>Cookies and similar technologies</h2>
      <p>Cookies are small files a website stores in your browser. We use:</p>
      <ul>
        <li><strong>An essential cookie:</strong> a session cookie that keeps our forms secure and the site working. The site cannot work properly without it.</li>
        <li><strong>Browser storage:</strong> small records of how you arrived (see above) and whether you have already seen a popup, so that we do not show it again.</li>
        <li><strong>Analytics cookies:</strong> set by Microsoft Clarity and, where we use it, Google Analytics, to understand how the site is used.</li>
      </ul>
      <p>You can block or delete cookies in your browser settings. Blocking analytics cookies does not stop the site from working. Our fonts load from Google Fonts, which means your browser connects to Google&rsquo;s servers when you open a page.</p>
    </section>

    <section id="sharing">
      <h2>Who we share it with</h2>
      <p>We do not sell or rent your personal data. We share it only with:</p>
      <ul>
        <li><strong>Service providers who run the website for us:</strong> our hosting and email provider (Hostinger), Microsoft (Clarity) and Google (Fonts and, where used, Analytics). They process data on our behalf and only for these purposes.</li>
        <li><strong>Our team:</strong> the Drawlead staff who need your details to respond to you or deliver your project.</li>
        <li><strong>Authorities, when the law requires it:</strong> for example, in response to a valid legal request, or to protect our rights or the safety of others.</li>
        <li><strong>A future owner:</strong> if Drawlead is merged or sold, your data may pass to the new owner, who must keep protecting it as this policy describes.</li>
      </ul>
    </section>

    <section id="where-it-is-stored">
      <h2>Where your data is stored</h2>
      <p>Your data is stored on servers run by our hosting provider. Some of our service providers may process data outside India. We transfer data outside India only where Indian law allows it, and we expect those providers to protect it at least as well as this policy describes.</p>
    </section>

    <section id="how-long-we-keep-it">
      <h2>How long we keep it</h2>
      <p>We keep enquiry and booking details for as long as we need them to respond to you and, if you become a client, for the length of our working relationship. After that we delete or anonymise them, unless the law requires us to keep certain records, such as invoices, for longer. Analytics data is kept by Microsoft and Google under their own retention settings.</p>
    </section>

    <section id="security">
      <h2>How we protect it</h2>
      <p>We follow reasonable security practices to protect your data. These include encrypted connections (HTTPS), access to our admin system only for authorised staff, and passwords stored in hashed form. No way of sending or storing data is completely secure, so we cannot guarantee absolute security. If a breach affects your data, we will tell you and the relevant authorities as the law requires.</p>
    </section>

    <section id="your-rights">
      <h2>Your rights</h2>
      <p>Under India&rsquo;s Digital Personal Data Protection Act, 2023 and other applicable laws, you can:</p>
      <ul>
        <li>ask for a summary of the personal data we hold about you and how we use it;</li>
        <li>ask us to correct, complete or update it;</li>
        <li>ask us to delete it, unless the law requires us to keep it;</li>
        <li>withdraw your consent at any time;</li>
        <li>nominate someone to exercise these rights for you if you die or become unable to; and</li>
        <li>raise a grievance with us and, if our response does not resolve it, with the Data Protection Board of India.</li>
      </ul>
      <p>To do any of these, email <a href="mailto:official@drawlead.co.in">official@drawlead.co.in</a>. We may need to confirm your identity first. We respond within 30 days.</p>
    </section>

    <section id="children">
      <h2>Children</h2>
      <p>Our website and services are meant for businesses and adults. We do not knowingly collect personal data from anyone under 18. If you believe a child has given us their details, contact us and we will delete them.</p>
    </section>

    <section id="other-websites">
      <h2>Links to other websites</h2>
      <p>Our website links to other websites, such as social networks. Their own privacy policies apply when you visit them, and we are not responsible for how they handle your data.</p>
    </section>

    <section id="changes">
      <h2>Changes to this policy</h2>
      <p>We may update this policy as our website or the law changes. The date at the top of this page shows when it last changed. If we make a significant change, we will say so on this page.</p>
    </section>

    <section id="contact">
      <h2>Contact and Grievance Officer</h2>
      <p>For questions, requests or complaints about your personal data, contact our Grievance Officer:</p>
      <p><strong>Grievance Officer, Drawlead</strong><br>Email: <a href="mailto:official@drawlead.co.in">official@drawlead.co.in</a><br>Chennai, Tamil Nadu, India</p>
      <p>We acknowledge every grievance and resolve it within 30 days of receiving it.</p>
    </section>
<?php include __DIR__ . '/partials/legal-bottom.php'; ?>
