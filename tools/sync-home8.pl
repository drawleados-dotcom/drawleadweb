use strict; use warnings;
#
# Regenerates templates/home8-body.php from templates/home7-body.php.
#
# Home 8 is Home 7's page in a dark theme. Its markup is a copy rather than an
# include, so the two pages stay independent — but that copy goes stale the
# moment Home 7's markup moves. Run this from the repo root after any change to
# Home 7's body and commit the result:
#
#     perl tools/sync-home8.pl
#
# It applies exactly seven deltas and fails loudly if any of them no longer
# matches, which is the signal that the change needs looking at by hand rather
# than being copied across blind.
#
sub slurp { my $f=shift; open(my $h,'<:raw',$f) or die "$f: $!"; local $/; my $s=<$h>; close $h; $s }
sub spew  { my ($f,$s)=@_; open(my $h,'>:raw',$f) or die "$f: $!"; print $h $s; close $h }

my $src = 'templates/home7-body.php';
my $dst = 'templates/home8-body.php';
my $s = slurp($src);
my $crlf = ($s =~ /\r\n/) ? 1 : 0;
$s =~ s/\r\n/\n/g;

my $n = 0;
sub rep {
  my ($r,$from,$to,$want) = @_;
  my ($c,$k) = (0,0);
  while (($k = index($$r,$from,$k)) >= 0) { substr($$r,$k,length($from)) = $to; $k += length($to); $c++ }
  die "sync-home8: expected $want of \"" . substr($from,0,54) . "...\", found $c\n" unless $c == $want;
  $n += $c;
}

# 1 · the docblock and the page key
rep(\$s, <<'OLD', <<'NEW', 1);
/**
 * Home 7: exact copy of the standalone redesigned homepage bundle
 * (home (1)/home/), served at /home-7 as a DB-managed page (Admin -> Pages).
 * Its stylesheet is /assets/home7.css and its animation libraries live in
 * /assets/ (matter.min.js, gsap.min.js, ScrollTrigger.min.js), all copied
 * from that bundle. The live "/" homepage is untouched.
 */
$activePage = 'home7';
OLD
/**
 * Home 8: Home 7's page in a dark theme, served at /home-8 as a DB-managed
 * page (Admin -> Pages).
 *
 * GENERATED FILE — do not edit by hand. It is a copy of
 * templates/home7-body.php with four theme deltas applied. After any change to
 * Home 7's body, run `perl tools/sync-home8.pl` from the repo root and commit
 * the result, or the two pages drift apart.
 *
 * Only the theme differs. This page loads /assets/home7.css for the layout,
 * type, spacing and animation, then /assets/home8.css on top of it, which
 * carries nothing but colour. That is deliberate: a layout fix to Home 7
 * reaches Home 8 as well, and the dark theme stays readable as its own file
 * instead of being diffused through a 165KB copy.
 */
$activePage = 'home8';
NEW

# 2 · the theme sheet, after Home 7's
rep(\$s,
  '<link rel="stylesheet" href="<?= asset_url(\'/assets/home7.css\') ?>">',
  '<link rel="stylesheet" href="<?= asset_url(\'/assets/home7.css\') ?>">' . "\n"
  . '<!-- colour only; everything structural comes from home7.css above -->' . "\n"
  . '<link rel="stylesheet" href="<?= asset_url(\'/assets/home8.css\') ?>">',
  1);


# 4 · the About Us hero's cursor glow, lifted onto this hero.
#
#     The .ah-fx / .ah-blob / .ah-lines styles already live in
#     partials/style.php, which is inlined on every page, so only the markup and
#     the driver travel; home8.css adds the stacking and the dark-ground tuning.
#
#     The <script> sits immediately after the element it drives, inside the open
#     <section>: a classic inline script runs during parse, so #ahFx is already
#     in the tree and closest('section') resolves to #hero.
rep(\$s, qq{ <div class="hero-stars" aria-hidden="true">\n}, <<'INS' . qq{ <div class="hero-stars" aria-hidden="true">\n}, 1);
 <!-- Cursor glow, the same effect as the About Us hero: nothing at rest, then a
      soft green light trails the pointer and a green grid lights up beneath it.
      Decorative only — aria-hidden, pointer-events:none, and pinned behind
      .hero-grid, so it never obstructs the copy or the dashboard. -->
 <div class="ah-fx" id="ahFx" aria-hidden="true">
  <span class="ah-blob"></span>
  <span class="ah-lines"></span>
 </div>
 <script>
 // Verbatim copy of the About Us hero driver (templates/about-us-body.php). It
 // writes the pointer position into two custom properties and lets CSS place the
 // light and the grid mask; the element is never re-laid-out, only composited.
 //
 // The light eases toward the pointer instead of snapping to it — that trailing
 // is what makes it read as a soft light rather than a cursor attachment. The
 // rAF loop only runs while the pointer is inside and stops once it catches up.
 (function(){
  const fx = document.getElementById('ahFx');
  if(!fx) return;
  const hero = fx.closest('section');
  if(!hero) return;
  // hover-capable, fine pointers only: on touch there is no hover to fade out of
  if(!window.matchMedia('(hover:hover) and (pointer:fine)').matches) return;

  const snap = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let tx = 0, ty = 0, x = 0, y = 0, raf = 0, seeded = false;

  function paint(){
   fx.style.setProperty('--ah-x', x.toFixed(1) + 'px');
   fx.style.setProperty('--ah-y', y.toFixed(1) + 'px');
  }
  function step(){
   raf = 0;
   const dx = tx - x, dy = ty - y;
   if(Math.abs(dx) < 0.5 && Math.abs(dy) < 0.5){ x = tx; y = ty; paint(); return; }
   x += dx * 0.12;            // ease factor: lower trails further behind
   y += dy * 0.12;
   paint();
   raf = requestAnimationFrame(step);
  }
  function queue(){ if(!raf) raf = requestAnimationFrame(step); }

  hero.addEventListener('pointermove', function(e){
   const r = hero.getBoundingClientRect();
   tx = e.clientX - r.left;
   ty = e.clientY - r.top;
   if(!seeded){ seeded = true; x = tx; y = ty; paint(); fx.classList.add('is-on'); return; }
   if(snap){ x = tx; y = ty; paint(); return; }
   queue();
  }, { passive: true });

  hero.addEventListener('pointerenter', function(){ if(seeded) fx.classList.add('is-on'); }, { passive: true });
  hero.addEventListener('pointerleave', function(){
   fx.classList.remove('is-on');
   if(raf){ cancelAnimationFrame(raf); raf = 0; }
  }, { passive: true });
 })();
 </script>

INS
# 5 · Home 7 inverts these two buttons inline, because #tech and #why are
#     already dark there. Every section is dark on this page, so they match the
#     rest of the buttons instead.
rep(\$s, 'style="background:#fff;color:#0a1310"', 'style="background:#32b46f;color:#04110a"', 2);


# 6 · Platform Dashboards moves up to sit directly after the hero.
#
#     The section cannot travel on its own: the <?php ?> block above it defines
#     the $dIco / $dKpi / $dSec / $dHead helpers that build every card, so the
#     comment, that block and the section move as one piece. Moving them EARLIER
#     is always safe for PHP scope — anything later that wanted the helpers
#     still finds them defined.
#
#     It lands before the 7 Functions section rather than hard against the
#     hero's </section>, because the marquee strip in between is the hero's own
#     base rather than a section of its own, and splitting the two would strand
#     it as an introduction to the wrong block.
{
 my $from = "<!-- DASHBOARDS -->\n";
 my $to   = "<?php\n// CTA INTRO: continuous letter train\n";
 my $at   = "<!-- 7 FUNCTIONS -->\n";

 my $a = index($s, $from);  die "sync-home8: dashboards block not found\n"      if $a < 0;
 my $b = index($s, $to, $a); die "sync-home8: cta-intro marker not found\n"      if $b < 0;
 my $chunk = substr($s, $a, $b - $a);
 die "sync-home8: the block to move looks wrong\n"
   unless $chunk =~ /<section id="dashboards"/ && $chunk =~ /Every Module/ && $chunk =~ /\$dHead = function/;

 substr($s, $a, length($chunk)) = '';

 my $k = index($s, $at);                     die "sync-home8: functions marker not found\n" if $k < 0;
 die "sync-home8: functions marker is not unique\n" if index($s, $at, $k + 1) >= 0;
 substr($s, $k, 0) = $chunk;

 # the whole point of the delta: prove the order actually changed
 die "sync-home8: dashboards did not end up before functions\n"
   unless index($s, '<section id="dashboards"') < index($s, '<section id="functions">');
 $n++;
}

# 7 · Industries becomes an overlapping stack of tall panels.
#
#     Home 7 keeps its sliding row. The whole .ind-hscroll block is swapped for
#     the stack markup and its own driver; the PHP loop over $indStackOrder /
#     $indByKey is rebuilt inside it, so the same six industries render with the
#     same content and nothing is renamed or dropped.
#
#     The ids change too (isOuter/isSticky/isStack), which is what retires the
#     old row driver: it opens with a guard on indHOuter/indHSticky/indRow and
#     returns when they are absent, so it simply does nothing on this page.
#
#     Styles live in assets/home8.css, which only this page loads.
{
 my $from = qq{ <div class="ind-hscroll" id="indHOuter">\n};
 my $to   = qq{ </div><!-- /ind-hscroll -->\n};

 my $a = index($s, $from);   die "sync-home8: industries row not found\n"     if $a < 0;
 my $b = index($s, $to, $a); die "sync-home8: industries row end not found\n" if $b < 0;
 my $old = substr($s, $a, $b + length($to) - $a);
 die "sync-home8: the industries block to replace looks wrong\n"
   unless $old =~ /\$indStackOrder/ && $old =~ /ind-scard/ && $old =~ /Built for/;

 my $new = slurp('tools/home8-industries.html') . slurp('tools/home8-industries.js');
 $new =~ s/\r\n/\n/g;
 substr($s, $a, length($old)) = $new;

 die "sync-home8: the stack did not land\n"
   unless $s =~ /id="isStack"/ && $s =~ /indStackOrder/;
 die "sync-home8: the old row survived\n" if $s =~ /id="indRow"/;
 $n++;
}
$s =~ s/\n/\r\n/g if $crlf;
spew($dst, $s);
print "sync-home8: $n deltas applied, $dst regenerated (", length($s), " bytes)\n";
