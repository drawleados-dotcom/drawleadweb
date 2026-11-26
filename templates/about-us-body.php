<?php
/**
 * About Us: the story page, in the home page's design language.
 *
 * Loads the home page's stylesheet and script (/assets/home10.css, .js) and
 * wraps everything in .h10, so buttons, pills, headings, reveals and the close
 * are the same components. What is particular to this page is /assets/about.css
 * (scoped under .ab) and /assets/about.js. The eight tints come from
 * /assets/palette.css.
 *
 * Copy and images are editable in Admin → Pages → About Us → Page Content.
 * Images live in /assets/about/. The illustrations are original renders made
 * for this page. The six photographs under "people" are stand-ins that show no
 * faces: replace them with real team photographs from Page Content.
 *
 * Motion is small on purpose: things ease in once, the story section travels
 * sideways like a long newspaper from 2022 to 2026 (wide screens only; phones,
 * reduced motion and the editor get the same pages stacked), and the AI
 * illustration joins up when the belief is reached. All of it stands down under prefers-reduced-motion,
 * and the page reads in full with JavaScript off.
 */
$activePage = 'about-us';
include __DIR__ . '/partials/nav.php';

$abIcon = fn (string $name, string $class = ''): string =>
    '<svg class="ic' . ($class !== '' ? ' ' . $class : '') . '" aria-hidden="true" focusable="false"><use href="#i-' . $name . '"/></svg>';

$abImg = fn (string $file, string $alt, int $w, int $h, string $attrs = 'loading="lazy"'): string =>
    '<img src="' . asset_url('/assets/about/' . $file) . '" alt="' . h($alt) . '" width="' . $w . '" height="' . $h . '" decoding="async" ' . $attrs . '>';

$abDelay = fn (int $i, float $step = .06): string => 'style="--d:' . round($i * $step, 2) . 's"';

// The gap we see: [tint, icon, first clause, rest]
$abGaps = [
    ['peach', 'sparkle',  'AI tools everywhere,', 'but no clear implementation strategy.'],
    ['sky',   'users',    'Employees using AI individually,', 'without connected business workflows.'],
    ['lilac', 'plug',     'Multiple AI subscriptions,', 'but no integration with existing systems.'],
    ['rose',  'rocket',   'Exciting AI experiments,', 'but limited impact on everyday operations.'],
    ['lime',  'db',       'Business data scattered across platforms,', 'inaccessible to intelligent systems.'],
];

// Operating rhythms: [label, tint, path]
$abRhythms = [
    ['Construction',  'peach', 'M2 60H34V46H70V60H84V30H126V60H140V16H190V60H204V38H238V60H278'],
    ['Hospital',      'rose',  'M2 40H48l9-9 9 9H82l6 7 9-37 11 52 9-22H150l9-9 9 9H184l6 7 9-37 11 52 9-22H278'],
    ['Ecommerce',     'sky',   'M2 50C24 50 28 18 52 18S84 62 112 62 148 24 172 24 204 52 228 52 258 12 278 12'],
    ['Manufacturing', 'aqua',  'M2 58H20V26H42V58H60V26H82V58H100V26H122V58H140V26H162V58H180V26H202V58H220V26H242V58H278'],
];

// Capabilities: [tint, image, alt, title, text]
$abCaps = [
    ['sky',   'cap-systems.webp',  'Department blocks stacked into one building and joined by a single green line', 'Business Systems', 'We engineer custom software, ERP and Business Operating Systems that connect departments, teams, projects and everyday operations.'],
    ['peach', 'cap-commerce.webp', 'A small shop with a green awning, a shopping bag and parcels ready to go', 'Digital Commerce', 'We create digital shopping experiences that bring brands closer to their customers and support better online commerce.'],
    ['lilac', 'cap-ai.webp',       'A robot turning mixed-up boxes on a conveyor into neat, checked ones', 'AI & Automation', 'We use intelligent technology to simplify repetitive work, connect workflows and help businesses operate more efficiently.'],
    ['lime',  'cap-growth.webp',   'Rising bars with an arrow above them and a magnet drawing an audience in', 'Marketing & Growth', 'We help businesses improve digital visibility, attract customers, generate opportunities and optimize their growth.'],
];

// Culture: [tint, image, title, text]
$abCulture = [
    ['sky',   'cul-curiosity.webp',  'Curiosity Over Assumptions', 'We ask questions before jumping to conclusions. We explore how things work, challenge existing approaches and stay curious about better possibilities.'],
    ['peach', 'cul-beyond.webp',     'Think Beyond the Task', 'We encourage our team to understand the bigger picture, not just complete the assigned requirement. Every detail contributes to the larger solution.'],
    ['lilac', 'cul-ownership.webp',  'Ownership Over Excuses', 'We believe in taking responsibility, communicating openly and finding ways forward when challenges arise.'],
    ['aqua',  'cul-collab.webp',     'Collaboration Over Silos', 'Good solutions don\'t come from one person working in isolation. We share ideas, exchange perspectives and work together across disciplines.'],
    ['lime',  'cul-experiment.webp', 'Experiment, Learn & Improve', 'Technology keeps changing. We encourage continuous learning, experimentation and the confidence to try better approaches.'],
    ['rose',  'cul-purpose.webp',    'Build With Purpose', 'We care about what we build and why we build it. The objective is not just to finish a project, but to create something useful.'],
];

// Photographs: [image, alt, tint, width, height]
$abPhotos = [
    ['people-desk.webp',    'Laptops, notebooks and phones around a shared work table, seen from above', 'peach', 1400, 933],
    ['people-sketch.webp',  'A hand sketching screen layouts on paper', 'sky', 1200, 800],
    ['people-code.webp',    'A laptop with code on screen on a bright desk', 'lilac', 1200, 800],
    ['people-board.webp',   'A planning board covered in notes about problems to solve', 'lime', 1200, 800],
    ['people-talk.webp',    'People taking notes around a table during a project discussion', 'aqua', 1200, 800],
    ['people-hands.webp',   'A team stacking their hands together', 'rose', 1200, 800],
];

// Way of working: [tint, title, text, illustration]
$abSteps = [
    ['peach', 'Listen & Understand', 'We spend time understanding the business, its people, challenges and expectations.',
        '<rect class="c" x="22" y="30" width="92" height="52" rx="16"/><path class="c" d="M40 80l-6 18 22-17z" style="stroke:none"/><rect class="k2" x="38" y="46" width="60" height="6" rx="3"/><rect class="k" x="38" y="59" width="40" height="6" rx="3"/><g class="float"><rect class="g" x="96" y="62" width="82" height="46" rx="15"/><path class="g" d="M158 106l8 16-24-15z"/><circle class="wh typing" cx="120" cy="85" r="4"/><circle class="wh typing" cx="137" cy="85" r="4" style="--d:.15s"/><circle class="wh typing" cx="154" cy="85" r="4" style="--d:.3s"/></g>'],
    ['sky', 'Question & Explore', 'We challenge assumptions, explore possibilities and identify the right approach.',
        '<path class="ln flow" d="M34 106C64 106 62 40 98 40"/><path class="ln flow" d="M34 106C80 106 84 74 120 74" style="--d:.2s"/><path class="ln flow" d="M34 106C84 106 96 112 140 112" style="--d:.4s"/><circle class="c" cx="34" cy="106" r="12"/><circle class="g" cx="34" cy="106" r="5"/><circle class="c" cx="104" cy="38" r="9"/><circle class="c" cx="148" cy="112" r="9"/><g class="scan"><circle class="lens" cx="132" cy="70" r="24"/><path class="gl" d="M150 88l17 17" style="stroke-width:6"/></g><circle class="g pulse" cx="132" cy="70" r="6"/><circle class="g" cx="132" cy="70" r="6"/>'],
    ['lilac', 'Design & Engineer', 'We translate business requirements into practical technology solutions.',
        '<rect class="c" x="26" y="24" width="148" height="94" rx="14"/><path class="ln faint" d="M26 48H174M76 48V118M124 48V118M26 83H174"/><rect class="gs2" x="33" y="55" width="36" height="22" rx="6"/><rect class="g tile" x="83" y="55" width="36" height="22" rx="6"/><rect class="gs2" x="131" y="90" width="36" height="22" rx="6"/><path class="ln flow" d="M69 66H83M119 66C136 66 149 72 149 90"/><circle class="k2" cx="40" cy="36" r="3.5"/><circle class="k2" cx="52" cy="36" r="3.5"/><circle class="k2" cx="64" cy="36" r="3.5"/>'],
    ['aqua', 'Build & Implement', 'We develop, integrate, test and implement the solution alongside the people who will use it.',
        '<rect class="c" x="36" y="84" width="46" height="32" rx="8"/><rect class="c" x="86" y="84" width="46" height="32" rx="8"/><rect class="g" x="61" y="48" width="46" height="32" rx="8"/><path class="tick" d="M77 64l5 5 10-10"/><g class="float"><rect class="gs2" x="112" y="34" width="46" height="32" rx="8" transform="rotate(9 135 50)"/></g><circle class="c" cx="164" cy="80" r="10"/><path class="c" d="M146 116c0-12 8-19 18-19s18 7 18 19z"/><circle class="g pulse" cx="109" cy="100" r="4"/><circle class="gs2" cx="109" cy="100" r="4"/><path class="ln" d="M24 118H182"/>'],
    ['lime', 'Learn & Evolve', 'We gather feedback, improve what we\'ve built and adapt as the business grows.',
        '<path class="ln flow" d="M58 70a42 42 0 0 1 72-29"/><path class="ln flow" d="M142 70a42 42 0 0 1-72 29" style="--d:.3s"/><path class="gl" d="M128.4 31.1L130 41L120.2 39.1"/><path class="gl" d="M71.6 108.9L70 99L79.8 100.9"/><rect class="c" x="72" y="48" width="56" height="46" rx="13"/><rect class="gs2 rise" x="82" y="72" width="8" height="14" rx="2.5"/><rect class="gs2 rise" x="96" y="66" width="8" height="20" rx="2.5" style="--d:.2s"/><rect class="g rise" x="110" y="57" width="8" height="29" rx="2.5" style="--d:.4s"/>'],
];
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/about.css') ?>">
<script>document.documentElement.classList.add('js')</script>

<div class="h10 ab">
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol><symbol id="i-down" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M6 13l6 6 6-6"/></symbol><symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol><symbol id="i-sparkle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z" fill="currentColor" stroke="none"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z" fill="currentColor" stroke="none"/></symbol><symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol><symbol id="i-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol><symbol id="i-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol><symbol id="i-db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></symbol></defs></svg>
<a class="skip" href="#about-top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>

<!-- 01 · The opening story -->
<section class="ahero" id="about-top">
  <div class="hero__bg" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="grid"></i></div>
  <div class="wrap">
    <div class="ahero__head">
      <span class="pill pill--outline reveal"><?= $abIcon('sparkle') ?>We are Drawlead · Business Systems &amp; Technology</span>
      <h1 class="ahero__title"><span class="reveal" style="--d:.06s">Businesses Are Built by People.</span> <em class="reveal" style="--d:.16s">We Believe Technology Should Work for Them.</em></h1>
      <p class="lede ahero__lede reveal" style="--d:.26s">Behind every growing business are people making decisions, solving problems, managing teams and keeping things moving.</p>
    </div>
    <figure class="ahero__stage reveal" style="--d:.34s">
      <?= $abImg('hero.webp', 'People standing together under a green magnet arch, with scattered software on one side and the same tools lined up and connected on the other', 2000, 940, 'fetchpriority="high" data-drift') ?>

      <figcaption class="ahero__noise">
        <p>But somewhere along the way, technology can make things more complicated.</p>
        <ul role="list"><li>More software.</li><li>More spreadsheets.</li><li>More manual work.</li><li>More disconnected processes.</li></ul>
      </figcaption>
    </figure>
    <div class="ahero__q">
      <p class="ahero__lead reveal">We started Drawlead with a simple thought:</p>
      <div>
        <p class="ahero__big reveal" style="--d:.08s">What if technology actually <span class="hl">worked around the way businesses work?</span></p>
        <div class="ahero__end reveal" style="--d:.16s">
          <p>Today, that's the thinking behind everything we build.</p>
          <a class="h10-btn h10-btn--dark" href="#our-story"><span>Get to Know Us</span><?= $abIcon('arrow', 'h10-btn__arr') ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 02 · The AI reality -->
<section class="aireal" id="ai-reality">
  <div class="wrap">
    <div class="ahead ahead--top">
      <div>
        <span class="pill pill--sky reveal"><span class="pill__dot" aria-hidden="true"></span>The world is adopting AI. But what are businesses actually solving?</span>
        <h2 class="h2 reveal" style="--d:.06s">AI Is in Every Hand.<br>But Where Are the Real Solutions?</h2>
      </div>
      <div class="ahead__txt reveal" style="--d:.12s">
        <p class="lede">Today, almost everyone has access to AI. Employees use it. Business owners experiment with it. Teams explore different tools, generate content, automate small tasks and discover new possibilities.</p>
        <p class="lede lede--strong">But having access to AI and implementing AI into a business are two very different things.</p>
        <p class="lede">Businesses are still struggling with disconnected systems, manual workflows, scattered information and operational challenges.</p>
        <p class="lede">Using AI to generate an answer is easy. Making AI understand your business, work with your existing systems and solve an actual operational problem is a different challenge.</p>
      </div>
    </div>

    <div class="aiflow" data-aiflow>
      <div class="aiflow__fig">
        <div class="aiflow__stage reveal">
          <?= $abImg('ai-before.webp', 'Five people on separate islands, each holding their own AI tool, with cables that connect to nothing', 1500, 1200) ?>

          <?= $abImg('ai-after.webp', 'The same five people with green bridges joining every island to one shared hub', 1500, 1200, 'loading="lazy" class="aiflow__after"') ?>

        </div>
      </div>
      <div class="aiflow__txt">
        <div class="gap">
          <h3 class="kick reveal">The gap we see</h3>
          <ul class="gaplist" role="list">
<?php foreach ($abGaps as $i => [$tint, $icon, $lead, $rest]): ?>
            <li class="reveal t-<?= $tint ?>" <?= $abDelay($i) ?>><span class="gaplist__ic"><?= $abIcon($icon) ?></span><p><b><?= $lead ?></b> <?= $rest ?></p></li>
<?php endforeach; ?>
          </ul>
        </div>
        <?= $abImg('ai-after.webp', 'The same five people with green bridges joining every island to one shared hub', 1500, 1200, 'loading="lazy" class="aiflow__m"') ?>

        <div class="belief reveal" data-belief>
          <h3 class="kick">Our belief</h3>
          <p class="belief__a">AI should not remain another tool your employees open separately.</p>
          <p class="belief__b">It should become part of how your business works.</p>
          <p class="belief__c">That's where business understanding, engineering and implementation matter.</p>
        </div>
      </div>
    </div>

    <p class="bigline" data-fill>We don't just bring AI into your business. We engineer ways to make it work for your business.</p>
  </div>
</section>

<!-- 03 · Our story: one long newspaper that travels from 2022 to 2026 -->
<section class="story" id="our-story" data-story data-at="0">
  <div class="story__run" data-run>
    <div class="story__stage" data-stage>
      <div class="story__track" data-track>
        <div class="story__paper" aria-hidden="true"></div>

        <header class="pg pg--front">
          <span class="pill pill--white"><span class="pill__dot" aria-hidden="true"></span>Our story · Since 2022</span>
          <h2 class="story__h">It Started With Building Websites.<br>It Became Something Much&nbsp;Bigger.</h2>
        </header>

        <article class="pg pg--a" id="story-2022" data-yr>
          <h3 class="yr">2022</h3>
          <figure class="cut"><?= $abImg('press-2022.webp', 'One website on a small plinth with two people beside it, printed in black ink like a newspaper picture', 1300, 964) ?></figure>
          <div class="pg__txt">
            <p class="np">When we started Drawlead in 2022, our journey began with helping businesses build their digital presence.</p>
            <ul class="ads" role="list"><li>Websites.</li><li>Digital experiences.</li><li>Online growth.</li></ul>
          </div>
        </article>

        <article class="pg pg--b" id="story-2023" data-yr>
          <div class="pg__txt">
            <h3 class="yr">2023</h3>
            <p class="np">But as we worked with more businesses, we started noticing something beyond the projects we were delivering.</p>
          </div>
          <div class="pg__mid">
            <figure class="cut"><?= $abImg('press-2023.webp', 'A website, an ERP, a CRM, spreadsheets and a phone, each on its own island with loose cables between them', 1400, 826) ?></figure>
            <div class="cols">
              <p>Businesses were investing in technology, but their everyday challenges weren't disappearing.</p>
              <p>A company could have a website, an ERP, a CRM, multiple spreadsheets and several other tools—and still struggle to manage its operations.</p>
            </div>
          </div>
          <ul class="briefs" role="list"><li>Departments weren't connected.</li><li>Information was scattered.</li><li>Teams depended on manual coordination.</li><li>Business owners struggled to get a clear picture of what was happening.</li></ul>
        </article>

        <article class="pg pg--c" id="story-2024" data-yr>
          <h3 class="yr">2024</h3>
          <p class="np np--l">We realized the problem wasn't always the absence of technology.</p>
          <figure class="cut"><?= $abImg('press-2024.webp', 'People on one side, working systems on the other, and a bridge between them with the middle span missing', 1400, 761) ?></figure>
          <p class="pull">Sometimes, the problem was <span class="mark">how technology was being implemented.</span></p>
        </article>

        <article class="pg pg--d" id="story-2025" data-yr>
          <figure class="cut"><?= $abImg('press-2025.webp', 'Website, charts, data and people on one shared platform, joined by a green line under a magnet arch, partly printed in colour', 1400, 1013) ?></figure>
          <div class="pg__txt">
            <h3 class="yr">2025</h3>
            <p class="np">That changed the way we looked at our work.</p>
            <p class="drop">We moved beyond building individual digital solutions and started looking at the bigger picture: the business itself, its people, its processes and how technology could bring everything together.</p>
          </div>
        </article>

        <article class="pg pg--e" id="story-2026" data-yr>
          <div class="pg__txt">
            <h3 class="yr yr--g">2026</h3>
            <p class="np np--xl">That became the direction of Drawlead.</p>
          </div>
          <figure class="cut"><?= $abImg('press-2026.webp', 'A large green compass whose needle points to the Drawlead magnet, with people walking along a green path towards it, printed in full colour', 1600, 1280) ?></figure>
        </article>
      </div>
      <div class="route" data-route aria-hidden="true"><?= $abImg('press-plane.webp', '', 360, 134, 'class="route__plane" loading="lazy"') ?></div>
    </div>
  </div>
</section>

<!-- 04 · The realization that shaped us -->
<section class="realz" id="realization">
  <div class="wrap">
    <div class="realz__panel">
      <div class="realz__head">
        <span class="pill pill--white reveal"><span class="pill__dot" aria-hidden="true"></span>A different way of thinking</span>
        <h2 class="realz__title">
          <span class="strike reveal" style="--d:.08s"><s>Don't Ask What Software a Business Needs.</s></span>
          <span class="realz__ask reveal" style="--d:.2s">Ask What the Business Is <em>Trying to Solve.</em></span>
        </h2>
        <p class="lede reveal" style="--d:.28s">We believe every business has its own operating rhythm.</p>
      </div>
      <ul class="rhythms" role="list">
<?php foreach ($abRhythms as $i => [$label, $tint, $path]): ?>
        <li class="rhythm reveal" <?= $abDelay($i, .1) ?>>
          <svg viewBox="0 0 280 72" fill="none" aria-hidden="true" focusable="false" preserveAspectRatio="none"><path class="rhythm__bg" d="<?= $path ?>"/><path class="rhythm__ln" pathLength="1" d="<?= $path ?>"/></svg>
          <span><?= $label ?></span>
        </li>
<?php endforeach; ?>
      </ul>
      <p class="realz__p reveal">The way a construction company manages projects is different from how a hospital manages patients. An ecommerce brand has different customer journeys from a manufacturing business.</p>
    </div>
  </div>
</section>

<!-- 04b · Our approach: start from the challenge, not the product -->
<section class="fit" id="approach">
  <div class="wrap">
    <div class="fit__top">
      <figure class="fit__art reveal"><?= $abImg('fit.webp', 'A board with four differently shaped openings, each filled by the piece made for it, with one green piece being lowered into place', 1800, 1000, 'loading="lazy" data-drift') ?></figure>
      <div class="fit__copy">
        <h2 class="fit__h reveal" style="--d:.06s">Even two companies in the same industry may work completely differently.</h2>
        <p class="fit__p reveal" style="--d:.12s">Yet technology is often sold as a predefined product, expecting businesses to adjust their processes around it.</p>
        <div class="fit__turn reveal" style="--d:.18s">
          <h3>We question that approach.</h3>
          <p>We believe the starting point should always be the business challenge.</p>
        </div>
      </div>
    </div>
    <ol class="fsteps" role="list">
<?php foreach (['Understand the people.', 'Understand the process.', 'Understand the problem.', 'Then engineer the technology.'] as $i => $step): ?>
      <li class="fstep reveal" <?= $abDelay($i, .12) ?>><span class="fstep__n"><?= sprintf('%02d', $i + 1) ?></span><span class="fstep__t"><?= $step ?></span></li>
<?php endforeach; ?>
    </ol>
    <p class="fit__end reveal">That's the difference between simply delivering software and <span class="hl">making technology work for a business.</span></p>
  </div>
</section>

<!-- 05 · What we are building -->
<section class="build sw" id="capabilities">
  <div class="wrap">
    <div class="ahead">
      <div>
        <span class="pill pill--lilac reveal"><span class="pill__dot" aria-hidden="true"></span>Our evolving capabilities</span>
        <h2 class="h2 reveal" style="--d:.06s">Different Technologies.<br>One Purpose: Making Businesses Work Better.</h2>
      </div>
      <p class="lede reveal" style="--d:.12s">Our work brings four technology capabilities together.</p>
    </div>
    <div class="sw-rows">
<?php foreach ($abCaps as $i => [$tint, $file, $alt, $title, $text]): ?>
      <article class="sw-row<?= $i % 2 === 0 ? ' sw-row--media-first' : '' ?>" data-accent="<?= $tint ?>">
        <div class="sw-text reveal">
          <span class="sw-cat"><b><?= sprintf('%02d', $i + 1) ?></b></span>
          <h3 class="sw-h3"><?= h($title) ?></h3>
          <p class="sw-p"><?= h($text) ?></p>
        </div>
        <div class="sw-media reveal" style="--d:.08s"><?= $abImg($file, $alt, 1400, 1100) ?></div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="build__end reveal">These aren't isolated services. They are different ways of <span class="hl">solving business challenges</span> through technology.</p>
  </div>
</section>

<!-- 06 · Our culture -->
<section class="cult dark-ctx" id="culture">
  <div class="wrap">
    <div class="ahead ahead--top">
      <div>
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Life at Drawlead · How we think · How we build</span>
        <h2 class="h2 h2--light reveal" style="--d:.06s">We Are Curious by Nature.<br>We Build by Thinking.<br>We Grow by Doing.</h2>
      </div>
      <div class="ahead__txt reveal" style="--d:.12s">
        <p class="lede lede--light">We don't want to be a workplace where people simply receive tasks, complete them and move on.</p>
        <p class="lede lede--light">We want to build a culture where people understand why something is being built, question how it can be better and take ownership of making it happen.</p>
        <p class="lede lede--light">Our culture is shaped by the way we approach problems, work with each other and learn through every project.</p>
      </div>
    </div>
    <ul class="cugrid" role="list">
<?php foreach ($abCulture as $i => [$tint, $file, $title, $text]): ?>
      <li class="cu t-<?= $tint ?> reveal" <?= $abDelay($i % 3, .07) ?>>
        <div class="cu__top"><span class="cu__n"><?= sprintf('%02d', $i + 1) ?></span><?= $abImg($file, '', 720, 720) ?></div>
        <h3><?= h($title) ?></h3>
        <p><?= h($text) ?></p>
      </li>
<?php endforeach; ?>
    </ul>
  </div>
  <div class="oneline">
    <p class="oneline__k">Our culture in one line</p>
    <div class="marquee marquee--line"><ul class="marquee__track" role="list"><?php for ($r = 0; $r < 4; $r++): foreach (['Think independently.', 'Work collaboratively.', 'Take ownership.', 'Keep building.'] as $s): ?><li<?= $r ? ' aria-hidden="true"' : '' ?>><?= $s ?></li><?php endforeach; endfor; ?></ul></div>
  </div>
</section>

<!-- 07 · The people behind the work -->
<section class="people" id="people">
  <div class="wrap">
    <div class="ahead ahead--top">
      <div>
        <span class="pill pill--rose reveal"><span class="pill__dot" aria-hidden="true"></span>People make the technology.</span>
        <h2 class="h2 reveal" style="--d:.06s">Different Minds.<br>Shared Ownership.<br>One Team.</h2>
      </div>
      <div class="ahead__txt reveal" style="--d:.12s">
        <p class="lede">Every solution we build is shaped by people with different perspectives and skills.</p>
        <p class="lede">From strategy and design to development, marketing, automation and implementation, our team brings different disciplines together to solve business challenges.</p>
        <p class="lede">We believe great work happens when people can share ideas, ask questions, learn from mistakes and take ownership of the outcome.</p>
      </div>
    </div>
    <div class="mosaic">
<?php foreach ($abPhotos as $i => [$file, $alt, $tint, $w, $h]): ?>
      <figure class="ph ph--<?= $i + 1 ?> t-<?= $tint ?> reveal" <?= $abDelay($i % 3, .08) ?>><span><?= $abImg($file, $alt, $w, $h, 'loading="lazy" data-drift') ?></span></figure>
<?php endforeach; ?>
    </div>
    <p class="people__end reveal">At Drawlead, we are not just building technology projects. We are building a team that <span class="hl">enjoys solving meaningful problems.</span></p>
  </div>
</section>

<!-- 08 · How we turn ideas into reality -->
<section class="way" id="way-of-working">
  <div class="wrap">
    <div class="way__head">
      <span class="pill pill--white reveal"><span class="pill__dot" aria-hidden="true"></span>Our way of working</span>
      <h2 class="h2 reveal" style="--d:.06s">From Understanding a Challenge<br>to Making It Work.</h2>
      <p class="lede reveal" style="--d:.12s">Our approach is simple in principle, but thoughtful in execution.</p>
    </div>
    <ol class="tix" role="list">
<?php foreach ($abSteps as $i => [$tint, $title, $text, $art]): ?>
      <li class="tix__item reveal" <?= $abDelay($i, .08) ?>>
        <article class="tik t-<?= $tint ?>">
          <div class="tik__stub">
            <p class="tik__no"><span class="tik__k">Step</span> <b><?= sprintf('%02d', $i + 1) ?></b></p>
            <div class="tik__trip">
              <span class="tik__k">of <?= sprintf('%02d', count($abSteps)) ?></span>
              <span class="tik__route" aria-hidden="true"><?php foreach ($abSteps as $k => [$stopTint]): ?><i class="<?= $k < $i ? 'is-done' : ($k === $i ? 'is-here' : '') ?>" style="--c:var(--w-<?= $stopTint ?>)"></i><?php endforeach; ?></span>
            </div>
          </div>
          <i class="tik__tear" aria-hidden="true"></i>
          <div class="tik__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><?= $art ?></svg></div>
          <h3><?= h($title) ?></h3>
          <p class="tik__p"><?= h($text) ?></p>
        </article>
      </li>
<?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- 09 · Our vision -->
<section class="vis" id="vision">
  <div class="wrap vis__head">
    <span class="pill pill--white reveal"><span class="pill__dot" aria-hidden="true"></span>Where we are heading</span>
    <h2 class="h2 reveal" style="--d:.06s">A Future Where Businesses Don't Have to Adapt to Technology. <span class="vis__g">Technology Adapts to Businesses.</span></h2>
    <p class="lede reveal" style="--d:.12s">We see a future where businesses can operate with connected systems, intelligent workflows and technology designed around their unique requirements.</p>
  </div>
  <figure class="vis__fig"><?= $abImg('vision.webp', 'A landscape of differently shaped businesses joined by one green road that leads towards the sun', 2400, 1272, 'loading="lazy" data-drift') ?></figure>
  <div class="wrap vis__foot">
    <div class="vis__cards">
      <p class="vcard reveal">Where employees spend less time managing disconnected tools and <b>more time doing meaningful work.</b></p>
      <p class="vcard reveal" style="--d:.08s">Where business owners have better visibility, teams work together and <b>technology becomes an actual advantage.</b></p>
    </div>
    <p class="vis__a reveal">That's the future we want to help build.</p>
    <p class="vis__b reveal" style="--d:.08s">And we're building towards it, one business challenge at a time.</p>
  </div>
</section>

<!-- 10 · Let's build what's next -->
<section class="final afinal" id="start">
  <div class="final__bg" aria-hidden="true"><i class="orb o1"></i><i class="orb o2"></i><i class="grid"></i></div>
  <div class="wrap final__in">
    <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Every great solution starts with a conversation.</span>
    <h2 class="h1x reveal" style="--d:.06s">Have a Business Challenge? <span class="grn">We'd Love to Understand It.</span></h2>
    <p class="lede lede--light reveal" style="--d:.1s">Maybe you need a system that connects your departments. Maybe you're building an ecommerce brand. Maybe you want to bring AI into your operations.</p>
    <p class="final__line reveal" style="--d:.14s">Whatever the challenge, let's start by understanding it.</p>
    <div class="final__ctas reveal" style="--d:.18s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Start a Conversation</span><?= $abIcon('arrow', 'h10-btn__arr') ?></a><a class="h10-btn h10-btn--ghost" href="/case-studies"><span>Explore Our Work</span><?= $abIcon('arrow', 'h10-btn__arr') ?></a></div>
  </div>
  <div class="wrap signoff reveal">
    <p class="signoff__w">Drawlead</p>
    <p class="signoff__l"><b>Business Systems &amp; Technology.</b> <span>Built around the way businesses work.</span></p>
  </div>
</section>
</main><footer class="foot">
  <div class="wrap foot__in">
    <a href="#about-top" aria-label="Drawlead – back to top"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <nav aria-label="Footer"><a href="<?= h($homeUrl) ?>#solutions">Solutions</a><a href="<?= h($homeUrl) ?>#program">8-Week Program</a><a href="/case-studies">Case Studies</a><a href="<?= h($homeUrl) ?>#industries">Industries</a><a href="/about-us">About Us</a><a href="/blog">Blog</a></nav>
  </div>
  <div class="wrap foot__base">
    <span>official@drawlead.co.in &nbsp;·&nbsp; www.drawlead.co.in &nbsp;·&nbsp; Chennai, Tamil Nadu, IN</span>
    <span>© <?= date('Y') ?> Drawlead. All rights reserved.</span>
  </div>
</footer>
</div>

<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
<script src="<?= asset_url('/assets/about.js') ?>" defer></script>
