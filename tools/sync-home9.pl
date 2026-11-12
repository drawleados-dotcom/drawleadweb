use strict; use warnings;
#
# Regenerates templates/home9-body.php from templates/home8-body.php.
#
# Home 9 is Home 8's page with its sections in a different order:
#
#     1  Hero (with the marquee strip under it)     7  Platform Dashboards
#     2  Solutions                                  8  Testimonials  (new, placeholder)
#     3  Case Studies                               9  Industries
#     4  How We Work                               10  Why Drawlead
#     5  CTA  (the closing CTA's card, repeated)   11  CTA  (letter train + card)
#     6  The Problem
#
# Core Platform (the 7 Functions) and Tech Stack are left out. Every section
# is copied from Home 8 exactly as it stands, so any change to Home 8 reaches
# Home 9 on the next run. sync-home8.pl runs this script at its end, so the
# usual workflow needs nothing extra; run it by hand after editing
# tools/home9-testimonials.html:
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

# ── 2 · the two new blocks ───────────────────────────────────────────────────
# Mid-page CTA: the closing CTA's card under its own id. home7.css styles the
# closing one through #cta.cta-framed and the letter train finds its card by
# #cta, so the copy must not share that id; assets/home9.css frames #cta-mid.
my $mid = $blk{cta};
die "sync-home9: the CTA block's opening tag changed\n"
  unless $mid =~ s{^<!-- CTA -->\n<section id="cta" class="cta-framed">}
                  {<!-- CTA (mid-page: the closing CTA's card, repeated) -->\n<section id="cta-mid" class="cta-framed">};
$mid .= "\n";

my $tm = slurp('tools/home9-testimonials.html');
$tm =~ s/\r\n/\n/g;
$tm =~ s/\n*\z/\n\n/;
die "sync-home9: testimonials file does not hold the section\n" unless $tm =~ /<section id="testimonials">/;

# ── 3 · Home 9's order ───────────────────────────────────────────────────────
my @dropped = qw(functions tech);
my @order   = qw(hero solutions cases method MID unify dashboards TESTIMONIALS industries why ctaintro cta);
my %used = map { $_ => 1 } @order, @dropped;
for (map { $_->[0] } @home8) { die "sync-home9: Home 8 section '$_' is neither placed nor dropped\n" unless $used{$_} }

my $body = '';
for (@order) {
  $body .= $_ eq 'MID'          ? $mid
         : $_ eq 'TESTIMONIALS' ? $tm
         :                        $blk{$_};
}
$s = $head . $body . $tail;

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
 * next sync. The order, the two sections left out (Core Platform and Tech
 * Stack), the placeholder Testimonials section and the mid-page CTA are all
 * defined in that script.
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
my $want = 'hero solutions cases method cta-mid unify dashboards testimonials industries why cta-intro cta';
die "sync-home9: sections came out as '@ids', expected '$want'\n" unless "@ids" eq $want;
my %seen; $seen{$_}++ for @ids;
die "sync-home9: duplicate section id\n" if grep { $_ > 1 } values %seen;
my $o = () = $s =~ /<section[\s>]/g;
my $c = () = $s =~ m{</section>}g;
die "sync-home9: $o sections opened, $c closed\n" unless $o == $c;

$s =~ s/\n/\r\n/g if $crlf;
spew($dst, $s);
print "sync-home9: ", scalar(@ids), " sections in Home 9's order, $dst regenerated (", length($s), " bytes)\n";
