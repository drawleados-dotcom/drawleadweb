"""Drawlead illustration library.
Every illustration shares one visual grammar (CSS-variable driven, see styles.css `.il`):
  .pn panel   .c card   .k/.k2 skeleton bars   .g solid accent   .gs soft accent
  .gl accent stroke   .ln neutral stroke   .flow animated dash   .float/.pulse/.spin/.rise motion
"""
import math

def R(x, y, w, h, r=10, c='c', ex=''):
    return f'<rect class="{c}" x="{x}" y="{y}" width="{w}" height="{h}" rx="{r}" {ex}/>'

def K(x, y, w, h=6, c='k', ex=''):
    return f'<rect class="{c}" x="{x}" y="{y}" width="{w}" height="{h}" rx="{h/2}" {ex}/>'

def C(cx, cy, r, c='c', ex=''):
    return f'<circle class="{c}" cx="{cx}" cy="{cy}" r="{r}" {ex}/>'

def P(d, c='ln', ex=''):
    return f'<path class="{c}" d="{d}" {ex}/>'

def T(x, y, s, c='', anchor='start', ex=''):
    return f'<text class="{c}" x="{x}" y="{y}" text-anchor="{anchor}" {ex}>{s}</text>'

def IC(name, x, y, s=20, color='currentColor', ex=''):
    return f'<svg x="{x}" y="{y}" width="{s}" height="{s}" style="color:{color}" {ex}><use href="#i-{name}"/></svg>'

def G(inner, c='', ex=''):
    return f'<g class="{c}" {ex}>{inner}</g>'

def D(delay):
    return f'style="--d:{delay}s"'

def wrap(vb, body, defs=''):
    return (f'<svg class="il-svg" viewBox="{vb}" aria-hidden="true" focusable="false" '
            f'xmlns="http://www.w3.org/2000/svg"><defs>{defs}</defs>{body}</svg>')

def grad(id_, c1='#4ad08c', c2='#27a363', vertical=True):
    d = 'x1="0" y1="0" x2="0" y2="1"' if vertical else 'x1="0" y1="0" x2="1" y2="1"'
    return f'<linearGradient id="{id_}" {d}><stop offset="0" stop-color="{c1}"/><stop offset="1" stop-color="{c2}"/></linearGradient>'

def dots(id_, step=16):
    return (f'<pattern id="{id_}" width="{step}" height="{step}" patternUnits="userSpaceOnUse">'
            f'<circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern>')

def panel(w, h, pid):
    return f'<rect class="pn" width="{w}" height="{h}"/><rect width="{w}" height="{h}" fill="url(#{pid})"/>'

# --------------------------------------------------------------------------- HERO
def hero_net():
    cx, cy = 280, 260
    labels = [('ERP', 'layers'), ('CRM', 'users'), ('AI Agents', 'bot'), ('Automation', 'workflow'),
              ('WhatsApp API', 'chat'), ('Data', 'db'), ('E-Commerce', 'cart'), ('Secure', 'shield')]
    out = [f'<circle cx="{cx}" cy="{cy}" r="250" fill="url(#hn-glow)"/>',
           f'<circle class="ring spin" cx="{cx}" cy="{cy}" r="150" fill="none"/>',
           f'<circle class="ring" cx="{cx}" cy="{cy}" r="205" fill="none" style="opacity:.5"/>']
    lines, nodes, pulses = [], [], []
    for i, (lab, ic) in enumerate(labels):
        a = math.radians(-90 + i * 45 + 22.5)
        nx, ny = cx + 218 * math.cos(a), cy + 188 * math.sin(a)
        w = 132 if len(lab) > 9 else 112
        d = f'M{nx:.1f} {ny:.1f} L{cx} {cy}'
        lines.append(f'<path class="ln flow" d="{d}" style="--d:{i*0.18:.2f}s"/>')
        pulses.append(f'<circle r="3.2" class="g"><animateMotion dur="{3.2+i*0.15:.2f}s" begin="-{i*0.4:.1f}s" repeatCount="indefinite" path="{d}"/></circle>')
        nodes.append(
            f'<g class="float node" style="--d:{i*0.5:.1f}s"><g transform="translate({nx-w/2:.1f} {ny-22:.1f})">'
            f'{R(0,0,w,44,14)}{C(24,22,14,"gs")}{IC(ic,15,13,18,"var(--green)")}'
            f'{T(46,26,lab,"lbl")}</g></g>')
    core = (f'<g transform="translate({cx-56} {cy-56})"><rect class="pulse" x="0" y="0" width="112" height="112" rx="32" fill="none" stroke="var(--green)" stroke-width="2" style="--d:0s"/>'
            f'<rect class="pulse" x="0" y="0" width="112" height="112" rx="32" fill="none" stroke="var(--green)" stroke-width="2" style="--d:1.2s"/>'
            f'<rect class="c coreshadow" x="0" y="0" width="112" height="112" rx="32"/>'
            f'<image href="assets/logos/drawlead-mark.webp" x="22" y="22" width="68" height="68" preserveAspectRatio="xMidYMid meet"/></g>')
    defs = ('<radialGradient id="hn-glow"><stop offset="0" stop-color="#32B46F" stop-opacity=".22"/>'
            '<stop offset="1" stop-color="#32B46F" stop-opacity="0"/></radialGradient>')
    return wrap('0 0 560 520', ''.join(out) + ''.join(lines) + ''.join(pulses) + core + ''.join(nodes), defs)

def hero_mobile():
    """compact, icon-only variant of the hero network for phones (legible at small sizes)"""
    cx, cy = 200, 200
    items = ['layers', 'users', 'bot', 'workflow', 'chat', 'db', 'cart', 'shield']
    out = [f'<circle cx="{cx}" cy="{cy}" r="190" fill="url(#hm-glow)"/>',
           f'<circle class="ring spin" cx="{cx}" cy="{cy}" r="120" fill="none"/>']
    lines, nodes, pulses = [], [], []
    for i, ic in enumerate(items):
        a = math.radians(-90 + i * 45)
        nx, ny = cx + 150 * math.cos(a), cy + 150 * math.sin(a)
        d = f'M{nx:.1f} {ny:.1f} L{cx} {cy}'
        lines.append(f'<path class="ln flow" d="{d}" style="--d:{i*0.18:.2f}s"/>')
        pulses.append(f'<circle r="3" class="g"><animateMotion dur="{3+i*.15:.2f}s" begin="-{i*.4:.1f}s" repeatCount="indefinite" path="{d}"/></circle>')
        nodes.append(f'<g class="float" style="--d:{i*.5:.1f}s"><g transform="translate({nx-27:.1f} {ny-27:.1f})">{R(0,0,54,54,18)}{IC(ic,13,13,28,"var(--green)")}</g></g>')
    core = (f'<g transform="translate({cx-46} {cy-46})"><rect class="pulse" width="92" height="92" rx="26" fill="none" stroke="var(--green)" stroke-width="2"/>'
            f'<rect class="c coreshadow" width="92" height="92" rx="26"/><image href="assets/logos/drawlead-mark.webp" x="18" y="18" width="56" height="56"/></g>')
    defs = ('<radialGradient id="hm-glow"><stop offset="0" stop-color="#32B46F" stop-opacity=".22"/><stop offset="1" stop-color="#32B46F" stop-opacity="0"/></radialGradient>')
    return wrap('0 0 400 400', ''.join(out) + ''.join(lines) + ''.join(pulses) + core + ''.join(nodes), defs)

# --------------------------------------------------------------------------- CAPABILITY CARDS
def cap_os():
    defs = grad('os-g', '#46cf8a', '#239a5c') + dots('os-d')
    b = [panel(480, 300, 'os-d')]
    for i, d in enumerate(['M178 76 C196 76 186 130 198 130', 'M302 76 C284 76 294 130 282 130',
                           'M178 224 C196 224 186 170 198 170', 'M302 224 C284 224 294 170 282 170']):
        b.append(f'<path class="ln flow" d="{d}" style="--d:{i*0.2}s"/>')
        b.append(f'<circle r="3.4" class="g"><animateMotion dur="2.6s" begin="-{i*0.5}s" repeatCount="indefinite" path="{d}" keyPoints="1;0" keyTimes="0;1" calcMode="linear"/></circle>')
    # ERP
    b.append(G(R(28, 28, 150, 96, 16) + C(48, 50, 9, 'gs') + IC('layers', 42, 44, 12, 'var(--green)') + T(64, 54, 'ERP', 'lbl')
               + K(44, 72, 96, 6) + K(44, 86, 64, 6)
               + ''.join(f'<rect class="g rise" x="{112+i*12}" y="{112-h}" width="8" height="{h}" rx="2" style="--d:{i*.12}s;opacity:{.35+i*.16}"/>' for i, h in enumerate([14, 22, 18, 30]))
               , 'float hovlift', D(0)))
    # CRM
    b.append(G(R(302, 28, 150, 96, 16) + C(322, 50, 9, 'gs') + IC('users', 316, 44, 12, 'var(--green)') + T(338, 54, 'CRM', 'lbl')
               + C(322, 78, 6, 'k2') + K(336, 75, 90, 6) + C(322, 98, 6, 'k2') + K(336, 95, 66, 6) + R(404, 90, 34, 16, 8, 'g')
               + P('M413 98l3 3 6-6', 'tick'), 'float hovlift', D(.6)))
    # Projects
    b.append(G(R(28, 176, 150, 96, 16) + C(48, 198, 9, 'gs') + IC('workflow', 42, 192, 12, 'var(--green)') + T(64, 202, 'Projects', 'lbl')
               + K(44, 220, 70, 7, 'g') + K(70, 234, 80, 7, 'gs2') + K(54, 248, 60, 7, 'g', 'style="opacity:.55"'), 'float hovlift', D(1.1)))
    # Departments
    b.append(G(R(302, 176, 150, 96, 16) + C(322, 198, 9, 'gs') + IC('hierarchy', 316, 192, 12, 'var(--green)') + T(338, 202, 'Departments', 'lbl')
               + R(364, 216, 26, 12, 4, 'k2') + P('M377 228v6M340 234h74M340 234v6M377 234v6M414 234v6', 'ln')
               + R(328, 240, 24, 12, 4, 'k') + R(365, 240, 24, 12, 4, 'k') + R(402, 240, 24, 12, 4, 'k'), 'float hovlift', D(1.6)))
    # core
    b.append('<g transform="translate(198 108)"><rect class="pulse" width="84" height="84" rx="24" fill="none" stroke="var(--green)" stroke-width="2"/>'
             '<rect width="84" height="84" rx="24" fill="url(#os-g)" class="coreshadow"/>'
             + IC('layers', 24, 24, 36, '#fff') + '</g>')
    b.append(G(R(190, 250, 100, 30, 15) + C(208, 265, 4, 'g blink') + T(220, 269, 'Unified', 'lbl'), 'float', D(.3)))
    return wrap('0 0 480 300', ''.join(b), defs)


def cap_commerce():
    defs = grad('ec-a', '#d9f5e6', '#b4ebcd') + grad('ec-b', '#46cf8a', '#239a5c') + dots('ec-d')
    b = [panel(480, 300, 'ec-d')]
    b.append(R(36, 30, 330, 244, 18))
    b.append(C(56, 48, 4, 'k2') + C(70, 48, 4, 'k2') + C(84, 48, 4, 'k2') + R(110, 41, 170, 14, 7, 'k'))
    b.append('<rect x="52" y="68" width="298" height="62" rx="12" fill="url(#ec-a)"/>')
    b.append(K(68, 84, 120, 9, 'g') + K(68, 100, 84, 6, 'k2', 'style="opacity:.4"') + R(262, 90, 70, 24, 12, 'g') + K(276, 99, 42, 6, 'wh'))
    tiles = [(52, 'tri'), (158, 'circ'), (264, 'sq')]
    for i, (x, sh) in enumerate(tiles):
        art = {'tri': '<path d="M70 196l22-38 22 38z" class="g" opacity=".9"/>',
               'circ': '<circle cx="211" cy="176" r="19" class="g" opacity=".9"/>',
               'sq': '<rect x="298" y="158" width="36" height="36" rx="9" class="g" opacity=".9"/>'}[sh]
        b.append(G(R(x, 142, 98, 120, 12, 'c2') + R(x + 6, 148, 86, 56, 8, 'gs') + art
                   + K(x + 10, 214, 56, 6) + K(x + 10, 226, 36, 5, 'k2') + R(x + 10, 238, 34, 14, 7, 'g')
                   , 'float hovlift', D(i * .5)))
    b.append(G(R(336, 58, 128, 116, 16) + T(352, 84, 'Cart', 'lbl') + K(352, 96, 70, 6) + K(352, 110, 50, 6, 'k2')
               + R(352, 134, 96, 26, 13, 'g') + P('M374 147l5 5 9-10', 'tick') + K(394, 144, 36, 6, 'wh'), 'float', D(.4)))
    b.append(G(R(352, 190, 112, 84, 16) + ''.join(f'<rect class="g rise" x="{368+i*18}" y="{258-h}" width="11" height="{h}" rx="3" style="--d:{i*.15}s;opacity:{.4+i*.15}"/>' for i, h in enumerate([16, 26, 36, 50]))
               + P('M366 232 L384 222 L402 214 L430 198', 'gl') + C(430, 198, 4, 'g'), 'float', D(1)))
    return wrap('0 0 480 300', ''.join(b), defs)


def cap_ai():
    defs = grad('ai-g', '#46cf8a', '#239a5c') + dots('ai-d')
    b = [panel(480, 300, 'ai-d')]
    ai = (232, 150)
    outs = [(358, 46), (358, 126), (358, 206)]
    # lines
    b.append(f'<path class="ln flow" d="M138 150 L{ai[0]-44} 150"/>')
    for i, (x, y) in enumerate(outs):
        d = f'M{ai[0]+44} {ai[1]} C {ai[0]+90} {ai[1]}, {x-40} {y+24}, {x} {y+24}'
        b.append(f'<path class="ln flow" d="{d}" style="--d:{i*.25}s"/>')
        b.append(f'<circle r="3.4" class="g"><animateMotion dur="2.4s" begin="-{i*.5}s" repeatCount="indefinite" path="{d}"/></circle>')
    b.append('<circle r="3.4" class="g"><animateMotion dur="1.8s" repeatCount="indefinite" path="M138 150 L188 150"/></circle>')
    # trigger
    b.append(G(R(26, 100, 112, 100, 18) + R(38, 114, 70, 22, 11, 'gs') + K(48, 122, 40, 6, 'g') + R(54, 144, 72, 22, 11, 'k')
               + K(64, 152, 42, 6, 'wh') + ''.join(f'<circle class="g typing" cx="{44+i*10}" cy="184" r="2.6" style="--d:{i*.18}s"/>' for i in range(3)), 'float', D(0)))
    b.append(G(C(26, 100, 15, 'g') + IC('chat', 18, 92, 16, '#fff'), 'float', D(.8)))
    # AI core
    b.append(f'<circle class="ring spin" cx="{ai[0]}" cy="{ai[1]}" r="62" fill="none"/>')
    b.append(f'<circle class="g" cx="{ai[0]+62}" cy="{ai[1]}" r="4"><animateTransform attributeName="transform" type="rotate" from="0 {ai[0]} {ai[1]}" to="360 {ai[0]} {ai[1]}" dur="9s" repeatCount="indefinite"/></circle>')
    b.append(f'<circle class="pulse" cx="{ai[0]}" cy="{ai[1]}" r="44" fill="none" stroke="var(--green)" stroke-width="2"/>')
    b.append(f'<circle cx="{ai[0]}" cy="{ai[1]}" r="44" fill="url(#ai-g)" class="coreshadow"/>')
    b.append(IC('sparkle', ai[0]-20, ai[1]-20, 40, '#fff'))
    icons = ['chart', 'calendar', 'db']
    for i, ((x, y), ic) in enumerate(zip(outs, icons)):
        b.append(G(R(x, y, 100, 48, 14) + C(x+26, y+24, 15, 'gs') + IC(ic, x+17, y+15, 18, 'var(--green)') + K(x+50, y+17, 36, 6) + K(x+50, y+28, 24, 5, 'k2')
                   + C(x+92, y+6, 7, 'g') + P(f'M{x+88.5} {y+6}l2.5 2.5 4-4.5', 'tick'), 'float hovlift', D(i*.4)))
    return wrap('0 0 480 300', ''.join(b), defs)


def cap_growth():
    defs = grad('gr-g', '#46cf8a', '#239a5c') + dots('gr-d') + \
        '<linearGradient id="gr-area" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#32B46F" stop-opacity=".35"/><stop offset="1" stop-color="#32B46F" stop-opacity="0"/></linearGradient>'
    b = [panel(480, 300, 'gr-d')]
    # funnel
    for i, (d, op) in enumerate([('M34 40 H196 L180 92 H50 Z', .22), ('M54 102 H176 L164 152 H66 Z', .4), ('M72 162 H158 L148 206 H82 Z', 1)]):
        b.append(f'<path d="{d}" class="g" style="opacity:{op}"/>')
    b.append(T(115, 70, 'Reach', 'lbl onfun', 'middle') + T(115, 132, 'Leads', 'lbl onfun', 'middle') + T(115, 190, 'Sales', 'lbl wht', 'middle'))
    for i in range(5):
        b.append(f'<circle class="g drop" cx="{60+i*28}" cy="30" r="3.5" style="--d:{i*.35}s"/>')
    b.append(G(R(84, 222, 62, 28, 14) + IC('users', 92, 228, 16, 'var(--green)') + K(112, 233, 24, 6), 'float', D(.5)))
    # chart card
    b.append(R(228, 34, 224, 150, 18))
    b.append(T(244, 60, 'SEO', 'chip') + T(280, 60, 'AEO', 'chip') + T(316, 60, 'GEO', 'chip'))
    b.append('<path d="M244 160 C270 154 280 140 304 136 S340 120 362 108 S412 84 436 64 V170 H244Z" fill="url(#gr-area)"/>')
    b.append('<path class="gl draw" d="M244 160 C270 154 280 140 304 136 S340 120 362 108 S412 84 436 64" pathLength="1"/>')
    b.append(P('M244 170 H436', 'ln') + P('M244 150 H436 M244 130 H436 M244 110 H436 M244 90 H436', 'ln faint'))
    b.append(C(436, 64, 5, 'g') + f'<circle class="pulse" cx="436" cy="64" r="8" fill="none" stroke="var(--green)" stroke-width="2"/>')
    # search card
    b.append(G(R(228, 202, 224, 72, 18) + C(252, 226, 12, 'gs') + IC('search', 245, 219, 14, 'var(--green)') + R(272, 218, 120, 16, 8, 'k')
               + K(244, 248, 160, 6) + K(244, 260, 110, 5, 'k2') + R(400, 244, 40, 20, 10, 'g') + IC('sparkle', 410, 247, 14, '#fff'), 'float hovlift', D(.2)))
    return wrap('0 0 480 300', ''.join(b), defs)

# --------------------------------------------------------------------------- HOW WE WORK (200x140)
def how(kind):
    defs = dots('hw-' + kind, 14)
    b = [panel(200, 140, 'hw-' + kind)]
    if kind == 'discover':
        b.append(P('M100 40 V64 M100 64 H52 V84 M100 64 H148 V84', 'ln'))
        b.append(R(78, 22, 44, 22, 8) + K(88, 30, 24, 6, 'g'))
        for x in (30, 126):
            b.append(R(x, 84, 44, 22, 8) + K(x + 10, 92, 24, 6, 'k2'))
        b.append('<g class="scan"><circle cx="112" cy="84" r="26" class="lens"/>' + P('M131 103 L152 124', 'gl', 'stroke-width="6"') + '</g>')
    elif kind == 'engineer':
        b.append(R(22, 18, 156, 104, 14))
        b.append(P('M22 44 H178 M22 70 H178 M22 96 H178 M56 18 V122 M100 18 V122 M144 18 V122', 'ln faint'))
        b.append(R(36, 28, 34, 22, 6, 'gs2') + R(130, 28, 34, 22, 6, 'g') + R(84, 84, 34, 22, 6, 'gs2'))
        b.append(P('M70 39 H130 M147 50 V66 H101 V84', 'gl flow', 'stroke-dasharray="4 5"'))
        b.append(C(147, 66, 4, 'g'))
    elif kind == 'build':
        b.append(R(26, 18, 120, 96, 14) + C(42, 32, 3.5, 'k2') + C(54, 32, 3.5, 'k2') + C(66, 32, 3.5, 'k2'))
        for i, (x, w, c) in enumerate([(40, 40, 'g'), (48, 60, 'k2'), (48, 32, 'k2'), (40, 52, 'g'), (48, 70, 'k2')]):
            b.append(K(x, 46 + i * 12, w, 6, c + ' typeln', f'style="--d:{i*.25}s"'))
        b.append(G(R(128, 66, 54, 40, 12) + IC('plug', 142, 74, 26, 'var(--green)'), 'float', D(.3)))
    else:  # deploy
        b.append(f'<circle cx="100" cy="70" r="42" class="ring spin" fill="none"/>')
        b.append(f'<circle cx="100" cy="70" r="30" class="g"/>' + IC('rocket', 84, 54, 32, '#fff'))
        b.append(G(R(24, 92, 44, 28, 8) + ''.join(f'<rect class="g rise" x="{32+i*10}" y="{112-h}" width="6" height="{h}" rx="2" style="--d:{i*.15}s"/>' for i, h in enumerate([6, 12, 18])), 'float', D(.2)))
        b.append(G(R(134, 22, 44, 26, 13) + C(148, 35, 4, 'g blink') + K(156, 32, 16, 6), 'float', D(.7)))
    return wrap('0 0 200 140', ''.join(b), defs)

# --------------------------------------------------------------------------- 8 WEEKS (320x200)
def week(n):
    pid = f'wk{n}'
    defs = dots(pid, 14)
    b = [panel(320, 200, pid)]
    if n == 1:   # audit: hierarchy + magnifier
        b.append(P('M160 56 V84 M160 84 H80 V108 M160 84 H240 V108 M160 84 V108', 'ln'))
        b.append(R(130, 30, 60, 28, 9) + K(142, 40, 36, 7, 'g'))
        for x in (50, 130, 210):
            b.append(R(x, 108, 60, 28, 9) + K(x + 12, 118, 36, 7, 'k2'))
        b.append('<g class="scan"><circle cx="196" cy="128" r="30" class="lens"/>' + P('M218 150 L244 176', 'gl', 'stroke-width="7"') + '</g>')
    elif n == 2:  # design: flow redesign
        b.append(R(30, 40, 70, 34, 10) + K(44, 54, 42, 7, 'k2') + R(124, 40, 70, 34, 10) + K(138, 54, 42, 7, 'k2'))
        b.append(P('M100 57 H124', 'ln'))
        b.append(R(30, 122, 70, 34, 10, 'c g-outline') + K(44, 136, 42, 7, 'g') + R(124, 122, 70, 34, 10, 'c g-outline') + K(138, 136, 42, 7, 'g') + R(218, 82, 70, 34, 10, 'c g-outline') + K(232, 96, 42, 7, 'g'))
        b.append(P('M100 139 H124 M194 139 C210 139 206 99 218 99', 'gl flow', 'stroke-dasharray="4 5"'))
        b.append(P('M194 57 C230 57 230 70 253 82', 'ln'))
        b.append(IC('workflow', 240, 36, 28, 'var(--green)'))
    elif n == 3:  # build
        b.append(R(32, 28, 170, 144, 16) + C(50, 44, 4, 'k2') + C(63, 44, 4, 'k2') + C(76, 44, 4, 'k2'))
        for i, (x, w, c) in enumerate([(48, 50, 'g'), (60, 86, 'k2'), (60, 60, 'k2'), (48, 74, 'g'), (60, 98, 'k2'), (60, 44, 'k2')]):
            b.append(K(x, 64 + i * 16, w, 7, c + ' typeln', f'style="--d:{i*.2}s"'))
        b.append(G(R(220, 74, 72, 74, 16) + '<g class="spin">' + IC('gear', 236, 90, 40, 'var(--green)') + '</g>', 'float', D(.3)))
    elif n == 4:  # integrate
        b.append(R(26, 50, 96, 100, 14) + K(40, 66, 56, 7) + K(40, 82, 40, 6, 'k2') + R(40, 104, 68, 26, 8, 'gs'))
        b.append(R(198, 50, 96, 100, 14) + K(212, 66, 56, 7) + K(212, 82, 40, 6, 'k2') + R(212, 104, 68, 26, 8, 'gs'))
        b.append(P('M122 100 H198', 'gl flow', 'stroke-dasharray="4 5"') + C(160, 100, 22, 'g') + IC('plug', 148, 88, 24, '#fff'))
        b.append(C(280, 56, 9, 'g') + P('M275.5 56l3 3 5.5-6', 'tick'))
    elif n == 5:  # live data
        b.append(R(30, 40, 80, 120, 14))
        for i in range(5):
            b.append(K(42, 56 + i * 20, 56, 7, 'k2' if i % 2 else 'k'))
        for i in range(3):
            b.append(f'<rect class="g moverow" x="130" y="{70+i*26}" width="40" height="8" rx="4" style="--d:{i*.35}s"/>')
        b.append(P('M118 100 H200', 'ln', 'stroke-dasharray="3 5"'))
        b.append(G('<ellipse class="c" cx="250" cy="58" rx="46" ry="16"/><path class="c" d="M204 58v70c0 9 21 16 46 16s46-7 46-16V58"/><path class="ln" d="M204 93c0 9 21 16 46 16s46-7 46-16"/><ellipse class="gs" cx="250" cy="58" rx="46" ry="16" style="opacity:.6"/>' + C(250, 124, 6, 'g'), 'float', D(.2)))
    elif n == 6:  # workflow implementation: departments
        pos = [(160, 40), (258, 100), (160, 160), (62, 100)]
        b.append(f'<circle class="ring spin" cx="160" cy="100" r="62" fill="none"/>')
        for i, (x, y) in enumerate(pos):
            nx, ny = pos[(i + 1) % 4]
            b.append(P(f'M{x} {y} L{nx} {ny}', 'ln flow', f'style="--d:{i*.2}s"'))
        for i, (x, y) in enumerate(pos):
            b.append(G(R(x-30, y-16, 60, 32, 10) + K(x-18, y-3, 36, 7, 'g' if i == 0 else 'k2'), 'float', D(i*.3)))
        b.append(C(160, 100, 22, 'g') + IC('gear', 148, 88, 24, '#fff'))
    elif n == 7:  # training: people + screen
        b.append(R(80, 34, 160, 96, 14) + K(96, 50, 80, 8, 'g') + K(96, 68, 128, 6) + K(96, 82, 100, 6, 'k2') + R(96, 98, 50, 18, 9, 'gs'))
        b.append(P('M160 130 V148 M130 148 H190', 'ln'))
        for i, x in enumerate((60, 160, 260)):
            b.append(G(C(x, 172, 12, 'gs') + C(x, 168, 5, 'g') + f'<path class="g" d="M{x-9} 180 a9 7 0 0 1 18 0z"/>', 'float', D(i*.35)))
        b.append(R(226, 20, 62, 22, 11, 'g') + IC('check', 236, 23, 16, '#fff') + K(256, 28, 24, 6, 'wh'))
    else:  # go live
        stages = [(28, 'Leads'), (118, 'Projects'), (208, 'Profit')]
        for i, (x, lab) in enumerate(stages):
            b.append(G(R(x, 56, 84, 66, 14) + T(x+42, 84, lab, 'lbl', 'middle') + ''.join(f'<rect class="g rise" x="{x+16+j*14}" y="{112-h}" width="8" height="{h}" rx="2" style="--d:{(i*3+j)*.08}s;opacity:{.35+j*.2}"/>' for j, h in enumerate([8, 14, 20, 26][:4])), 'float', D(i*.3)))
        b.append(P('M112 89 H118 M202 89 H208', 'gl flow'))
        b.append(R(104, 18, 112, 26, 13, 'g') + C(122, 31, 4, 'wh blink') + T(134, 35, 'Live', 'lbl wht'))
        b.append(P('M40 156 C90 150 120 138 160 140 S250 128 288 112', 'gl draw', 'pathLength="1"'))
    return wrap('0 0 320 200', ''.join(b), defs)
