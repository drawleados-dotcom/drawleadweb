use strict; use warnings;
#
# Regenerates templates/home9-body.php from templates/home8-body.php.
#
# Home 9 is Home 8's page with its sections in a different order:
#
#     1  Hero (with the marquee strip under it)     8  The Problem
#     2  Solutions                                  9  Platform Dashboards
#     3  Case Studies                              10  Testimonials  (new, placeholder)
#     4  How We Work                               11  Industries
#     5  How We Work  (process row, Home 9's own)  12  Why Drawlead
#     6  How We Work  (phases, Home 9's own)       13  CTA  (letter train + card)
#     7  CTA  (horizontal, Home 9's own)
#
# Core Platform (the 7 Functions) and Tech Stack are left out. Every section
# but 5, 6, 7 and 10 is copied from Home 8 exactly as it stands, so any change
# to Home 8 reaches Home 9 on the next run; 5, 6, 7 and 10 come from
# tools/home9-process.html, tools/home9-phases.html, tools/home9-cta-mid.html
# and tools/home9-testimonials.html. A few copied
# sections get Home 9's own edits (section 3b), and Solutions gains the hover
# accordion driver in tools/home9-solutions.js. sync-home8.pl runs this
# script at its end, so the usual workflow needs nothing extra; run it by hand
# after editing any of those tools/home9-* files:
#
#     perl tools/sync-home9.pl
#
# Like sync-home8.pl it fails loudly the moment Home 8's markup stops matching
# what it expects, rather than writing a page with a section missing or doubled.
#
# Testing: `perl tools/sync-home9.pl --rendered IN OUT` runs the same section
# reordering over a rendered copy of Home 8 (HTML, PHP already executed) and
# skips the PHP-only header delta.
#
sub slurp { my $f=shift; open(my $h,'<:raw',$f) or die "$f: $!"; local $/; my $s=<$h>; close $h; $s }
sub spew  { my ($f,$s)=@_; open(my $h,'>:raw',$f) or die "$f: $!"; print $h $s; close $h }

my $rendered = 0;
my ($src, $dst) = ('templates/home8-body.php', 'templates/home9-body.php');
if (@ARGV && $ARGV[0] eq '--rendered') {
  $rendered = 1;
  (undef, $src, $dst) = @ARGV;
  die "usage: perl tools/sync-home9.pl [--rendered IN OUT]\n" unless defined $dst;
}

my $s = slurp($src);
my $crlf = ($s =~ /\r\n/) ? 1 : 0;
$s =~ s/\r\n/\n/g;

# ── 1 · cut Home 8 into its sections ─────────────────────────────────────────
# Each block runs from its marker to the next block's marker, so it carries
# its lead-in comment and PHP preamble along with the <section>. The hero's
# block also holds the marquee strip that follows it. The last block ends at
# its own </section>; what follows (footer include, libraries, page scripts)
# is kept as the page's tail.
my @home8 = (
  [hero       => '<section id="hero">'],
  [dashboards => "<!-- DASHBOARDS -->\n"],
  [functions  => "<!-- 7 FUNCTIONS -->\n"],
  [unify      => '<!-- APP CHAOS '],
  [method     => "<!-- METHODOLOGY -->\n"],
  [solutions  => "<!-- SOLUTIONS -->\n"],
  [tech       => "<!-- TECH STACK -->\n"],
  [cases      => "<!-- CASE STUDIES -->\n"],
  [industries => "<!-- INDUSTRIES -->\n"],
  [why        => "<!-- WHY Drawlead -->\n"],
  [ctaintro   => $rendered ? '<section id="cta-intro">' : "<?php\n// CTA INTRO"],
  [cta        => "<!-- CTA -->\n"],
);
# the id each block's one <section> must carry
my %sid = (hero => 'hero', ctaintro => 'cta-intro', map { $_ => $_ }
           qw(dashboards functions unify method solutions tech cases industries why cta));

my @at;
for my $b (@home8) {
  my ($name, $marker) = @$b;
  my $i = index($s, $marker);
  die "sync-home9: marker for '$name' not found\n" if $i < 0;
  die "sync-home9: marker for '$name' appears more than once\n" if index($s, $marker, $i + 1) >= 0;
  die "sync-home9: '$name' is out of Home 8's order\n" if @at && $i <= $at[-1];
  push @at, $i;
}
my $close = index($s, "</section>\n", $at[-1]);
die "sync-home9: the closing CTA never closes\n" if $close < 0;
my $end = $close + length("</section>\n");

my $head = substr($s, 0, $at[0]);
my $tail = substr($s, $end);
my %blk;
for my $k (0 .. $#home8) {
  my $name = $home8[$k][0];
  my $to   = $k < $#home8 ? $at[$k + 1] : $end;
  my $t    = substr($s, $at[$k], $to - $at[$k]);
  my $opens  = () = $t =~ /<section[\s>]/g;
  my $closes = () = $t =~ m{</section>}g;
  die "sync-home9: '$name' should hold exactly one section, found $opens open / $closes close\n"
    unless $opens == 1 && $closes == 1;
  die "sync-home9: '$name' does not hold <section id=\"$sid{$name}\">\n"
    unless $t =~ /<section id="\Q$sid{$name}\E"[\s>]/;
  $blk{$name} = $t;
}
# nothing between the sections and the page tail may go missing
die "sync-home9: unexpected markup before the hero\n" if $head =~ /<section[\s>]/;
die "sync-home9: unexpected section after the closing CTA\n" if $tail =~ /<section[\s>]/;

# ── 2 · the two new blocks, from their own files ────────────────────────────
# Mid-page CTA: a horizontal layout of the closing CTA's card, under its own
# id (the closing CTA's letter train finds its card by #cta). Testimonials:
# placeholder content.
sub block_file {
  my ($file, $id) = @_;
  my $t = slurp($file);
  $t =~ s/\r\n/\n/g;
  $t =~ s/\n*\z/\n\n/;
  my $opens = () = $t =~ /<section[\s>]/g;
  die "sync-home9: $file should hold exactly one section, <section id=\"$id\">\n"
    unless $opens == 1 && $t =~ /<section id="\Q$id\E"[\s>]/ && $t =~ m{</section>\n\n\z};
  return $t;
}
my $mid = block_file('tools/home9-cta-mid.html', 'cta-mid');
my $tm  = block_file('tools/home9-testimonials.html', 'testimonials');
# How We Work, a second time: the same copy with a split header and the four
# steps as a staggered card row instead of the card grid and panel.
my $proc = block_file('tools/home9-process.html', 'process');
# And a third time: a two-column header over the audit's three phases.
my $phases = block_file('tools/home9-phases.html', 'phases');

# Solutions: a horizontal hover accordion instead of Home 8's sticky stack.
# Its driver follows the section; the stack's driver is removed in 3b.
{
  my $js = slurp('tools/home9-solutions.js');
  $js =~ s/\r\n/\n/g;
  $js =~ s/\n*\z/\n/;
  die "sync-home9: tools/home9-solutions.js should be one <script> block\n"
    unless $js =~ /\A\s*<script>/ && $js =~ m{</script>\n\z};
  $blk{solutions} =~ s{</section>\n}{</section>\n$js}
    or die "sync-home9: the Solutions section does not close\n";

  # No metrics row (100% Ownership, 3x Faster launch, 4x Organic traffic) on
  # Home 9's cards: the block goes from all three, exactly three.
  my $gone = $blk{solutions} =~ s{\n  <div class="sol-metrics">\n(?:   <div>.*?</div></div>\n){2}  </div>(?=\n)}{}g;
  die "sync-home9: expected to remove 3 Solutions metrics rows, removed " . ($gone || 0) . "\n"
    unless $gone == 3;
  die "sync-home9: a Solutions metrics row survived\n" if $blk{solutions} =~ /sol-metric/;
}

# Case Studies: the heading (eyebrow, heading, subline) and the CTA move
# inside the pinned box, so the whole section holds still on one screen while
# the cards travel sideways (assets/home9.css, section 9). Home 8 pins only
# the card track, and its heading scrolls away above it. The ids the scroll
# driver looks up (casesScroll, casesTrack) and the .cases-pin it measures are
# unchanged.
{
  my $n = $blk{cases} =~ s{
      (<section\ id="cases">\n)
      (.*?)                                              # the heading block
      (\ <div\ class="cases-scroll"\ id="casesScroll">\n
       \ \ <div\ class="cases-pin">\n)
      (.*?\ \ \ </div><!--\ /cases-track\ -->\n)         # the track and its cards
      (\ \ </div>\n
       \ </div><!--\ /cases-scroll\ -->\n)
      (.*?)                                              # the CTA
      (</section>\n)
    }{$1$3   <div class="cases-head">\n$2   </div>\n$4$6$5$7}sx;
  die "sync-home9: Case Studies markup not as expected (heading / pin / CTA)\n" unless $n;
  die "sync-home9: Case Studies heading did not move into the pin\n"
    unless $blk{cases} =~ m{<div class="cases-pin">\n   <div class="cases-head">\n <div class="eyebrow rv">.*?class="sec-h rv".*?class="sec-sub rv".*?\n   </div>\n   <div class="cases-track"}s;
  die "sync-home9: Case Studies CTA did not move into the pin\n"
    unless $blk{cases} =~ m{</div><!-- /cases-track -->\n <div class="sec-cta rv">.*?</div>\n  </div>\n </div><!-- /cases-scroll -->\n</section>\n}s;
}

# ── 3 · Home 9's order ───────────────────────────────────────────────────────
my @dropped = qw(functions tech);
my @order   = qw(hero solutions cases method PROCESS PHASES MID unify dashboards TESTIMONIALS industries why ctaintro cta);
my %used = map { $_ => 1 } @order, @dropped;
for (map { $_->[0] } @home8) { die "sync-home9: Home 8 section '$_' is neither placed nor dropped\n" unless $used{$_} }

my $body = '';
for (@order) {
  $body .= $_ eq 'MID'          ? $mid
         : $_ eq 'PROCESS'      ? $proc
         : $_ eq 'PHASES'       ? $phases
         : $_ eq 'TESTIMONIALS' ? $tm
         :                        $blk{$_};
}
$s = $head . $body . $tail;

# ── 3b · Home 9's own edits to sections it copies ───────────────────────────
# Each must match exactly once, or Home 8's markup has moved and the edit
# needs looking at.
sub edit_once {
  my ($from, $to, $what) = @_;
  my $c = () = $s =~ /\Q$from\E/g;
  die "sync-home9: $what: expected 1 match, found $c\n" unless $c == 1;
  $s =~ s/\Q$from\E/$to/;
}
# How We Work: a break between the heading's two sentences. assets/home9.css
# hides it from 900px up, where the heading fits on one line; below that it
# splits the heading at the sentence instead of leaving "first." alone on the
# second line. The space before it separates the sentences while it is hidden.
edit_once(q{<h2 class="sec-h rv">We don't build first. <span class="g">We understand first.</span></h2>},
          q{<h2 class="sec-h rv">We don't build first. <br><span class="g">We understand first.</span></h2>},
          'How We Work heading');
# Solutions: the cards are a hover accordion here (tools/home9-solutions.js),
# so Home 8's sticky-stack driver must not dim and scale them on scroll.
edit_once(qq{ initStickyStack('#solutions .sol-card', { dimTo: 0.30, shrink: 0.04 });\n},
          qq{ // Home 9: Solutions is a hover accordion (the script after #solutions), not a sticky stack.\n},
          'Solutions sticky-stack driver');

# ── 4 · the page's own stylesheet, after Home 8's base sheet ────────────────
my $css = $rendered ? '/assets/home9.css' : "<?= asset_url('/assets/home9.css') ?>";
die "sync-home9: Home 8's base stylesheet link not found\n"
  unless $s =~ s{(<link rel="stylesheet" href="[^"]*/assets/home8-base\.css[^"]*">\n)}
                {$1<!-- Home 9 only: the Testimonials section and the mid-page CTA's frame -->\n<link rel="stylesheet" href="$css">\n};

# ── 5 · header: docblock and page key (PHP only) ────────────────────────────
unless ($rendered) {
  my $a = index($s, "/**\n * Home 8: ");
  my $k = "\$activePage = 'home8';\n";
  my $b = index($s, $k);
  die "sync-home9: Home 8's docblock / page key not found\n" if $a < 0 || $b < $a;
  substr($s, $a, $b + length($k) - $a) = <<'PHP';
/**
 * Home 9: Home 8's sections in a different order, served at /home-9 as a
 * DB-managed page (Admin -> Pages).
 *
 * GENERATED FILE — do not edit by hand. It is built from
 * templates/home8-body.php by tools/sync-home9.pl, which tools/sync-home8.pl
 * runs at its end, so a change to Home 7 or Home 8 reaches this page on the
 * next sync. The order and the two sections left out (Core Platform and Tech
 * Stack) are defined in that script; the horizontal mid-page CTA and the
 * placeholder Testimonials come from tools/home9-cta-mid.html and
 * tools/home9-testimonials.html.
 *
 * Styling is Home 8's: /assets/home7.css, /assets/home8-base.css and the dark
 * sheet /assets/home8.css, which the header toggle switches off for light.
 * /assets/home9.css adds only the Testimonials cards and the mid-page CTA.
 */
$activePage = 'home9';
PHP
}

# ── 6 · checks on the result ─────────────────────────────────────────────────
my @ids = $s =~ /<section id="([^"]+)"/g;
my $want = 'hero solutions cases method process phases cta-mid unify dashboards testimonials industries why cta-intro cta';
die "sync-home9: sections came out as '@ids', expected '$want'\n" unless "@ids" eq $want;
my %seen; $seen{$_}++ for @ids;
die "sync-home9: duplicate section id\n" if grep { $_ > 1 } values %seen;
my $o = () = $s =~ /<section[\s>]/g;
my $c = () = $s =~ m{</section>}g;
die "sync-home9: $o sections opened, $c closed\n" unless $o == $c;

$s =~ s/\n/\r\n/g if $crlf;
spew($dst, $s);
print "sync-home9: ", scalar(@ids), " sections in Home 9's order, $dst regenerated (", length($s), " bytes)\n";
