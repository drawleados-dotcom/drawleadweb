from svglib import *

# --------------------------------------------------------------------------- DATA INTELLIGENCE
def data_viz():
    defs = grad('dv-g', '#46cf8a', '#239a5c') + dots('dv-d', 18) + \
        '<linearGradient id="dv-area" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#32B46F" stop-opacity=".35"/><stop offset="1" stop-color="#32B46F" stop-opacity="0"/></linearGradient>'
    b = [panel(680, 560, 'dv-d')]
    srcs = [('Customer & Sales', 'users', 38), ('Marketing & Campaign', 'megaphone', 168), ('Business Operations', 'workflow', 298), ('Financial & Performance', 'chart', 428)]
    core = (372, 280)
    for i, (lab, ic, y) in enumerate(srcs):
        d = f'M196 {y+46} C 280 {y+46}, 280 {core[1]}, {core[0]-40} {core[1]}'
        b.append(f'<g class="stream" data-stream="{i+1}"><path class="ln flow" d="{d}" style="--d:{i*.2}s"/>'
                 f'<circle r="4" class="g"><animateMotion dur="{2.6+i*.3}s" begin="-{i*.45}s" repeatCount="indefinite" path="{d}"/></circle>'
                 f'<circle r="4" class="g" opacity=".6"><animateMotion dur="{2.6+i*.3}s" begin="-{i*.45+1.3}s" repeatCount="indefinite" path="{d}"/></circle></g>')
    b.append('<path class="ln flow thick" d="M412 280 H452"/>')
    for i, (lab, ic, y) in enumerate(srcs):
        rows = ''.join(K(52, y + 62 + j * 11, [96, 70, 84][j], 5, 'k2' if j else 'k') for j in range(2))
        b.append(f'<g class="src" data-src="{i+1}">' + R(16, y, 180, 92, 18) + C(44, y + 28, 15, 'gs') + IC(ic, 35, y + 19, 18, 'var(--green)')
                 + T(68, y + 33, lab, 'lbl sm') + rows + R(150, y + 62, 32, 16, 8, 'g srcdot') + '</g>')
    # core
    b.append(f'<circle class="pulse" cx="{core[0]}" cy="{core[1]}" r="42" fill="none" stroke="var(--green)" stroke-width="2"/>'
             f'<circle class="pulse" cx="{core[0]}" cy="{core[1]}" r="42" fill="none" stroke="var(--green)" stroke-width="2" style="--d:1.2s"/>'
             f'<circle cx="{core[0]}" cy="{core[1]}" r="38" fill="url(#dv-g)" class="coreshadow"/>' + IC('sparkle', core[0]-18, core[1]-18, 36, '#fff'))
    # dashboard
    x0 = 452
    b.append(R(x0, 40, 212, 480, 22))
    b.append(C(x0+22, 64, 4, 'k2') + C(x0+34, 64, 4, 'k2') + C(x0+46, 64, 4, 'k2') + K(x0+120, 60, 72, 8))
    for i in range(3):
        xx = x0 + 16 + i * 64
        b.append(R(xx, 88, 58, 52, 12, 'c2') + K(xx+8, 98, 28, 5, 'k2') + K(xx+8, 110, 38, 9, 'g' if i == 0 else 'k', 'style="opacity:.9"')
                 + f'<path class="gl" d="M{xx+8} 132 l10 -6 l8 3 l10 -9 l8 4" style="stroke-width:1.6"/>')
    # line chart
    b.append(R(x0+16, 152, 180, 120, 14, 'c2'))
    b.append(K(x0+28, 164, 54, 6, 'k2'))
    b.append(f'<path d="M{x0+28} 250 C{x0+50} 244 {x0+64} 226 {x0+86} 222 S{x0+118} 200 {x0+140} 192 S{x0+168} 176 {x0+184} 170 V258 H{x0+28}Z" fill="url(#dv-area)"/>')
    b.append(f'<path class="gl draw" d="M{x0+28} 250 C{x0+50} 244 {x0+64} 226 {x0+86} 222 S{x0+118} 200 {x0+140} 192 S{x0+168} 176 {x0+184} 170" pathLength="1"/>')
    b.append(C(x0+184, 170, 4.5, 'g'))
    # bars + donut
    b.append(R(x0+16, 284, 100, 110, 14, 'c2') + K(x0+28, 296, 40, 6, 'k2'))
    for j, h in enumerate([30, 52, 40, 68, 48]):
        b.append(f'<rect class="g rise" x="{x0+28+j*16}" y="{380-h}" width="10" height="{h}" rx="3" style="--d:{j*.1}s;opacity:{.4+j*.12}"/>')
    b.append(R(x0+124, 284, 72, 110, 14, 'c2'))
    b.append(f'<circle cx="{x0+160}" cy="339" r="22" fill="none" class="donut-bg" stroke-width="9"/>'
             f'<circle class="donut" cx="{x0+160}" cy="339" r="22" fill="none" stroke="var(--green)" stroke-width="9" stroke-linecap="round" pathLength="100" stroke-dasharray="68 100" transform="rotate(-90 {x0+160} 339)"/>')
    # table
    b.append(R(x0+16, 406, 180, 98, 14, 'c2'))
    for j in range(4):
        b.append(K(x0+28, 420 + j * 20, 60, 6, 'k2') + K(x0+104, 420 + j * 20, 36 + (j % 2) * 16, 6) + R(x0+160, 417 + j * 20, 24, 12, 6, 'g' if j == 0 else 'gs'))
    return wrap('0 0 680 560', ''.join(b), defs)

# --------------------------------------------------------------------------- INDUSTRY SCENES (480x300)
def ind(kind):
    pid = 'in-' + kind
    defs = dots(pid) + grad(pid + 'g', '#46cf8a', '#239a5c')
    b = [panel(480, 300, pid)]
    if kind == 'construction':
        b.append(P('M40 262 H300', 'ln', 'stroke-width="2"'))
        # building floors
        for i in range(4):
            b.append(R(70, 230 - i * 38, 120, 34, 6, 'c') + ''.join(R(82 + j * 34, 238 - i * 38, 22, 16, 3, 'gs' if (i + j) % 2 else 'k') for j in range(3)))
        b.append(R(70, 78, 120, 34, 6, 'gs2', 'style="stroke-dasharray:4 4"'))
        # crane
        b.append(P('M232 262 V54 M232 54 H330 M232 54 L204 78 M300 54 V96', 'ln crane', 'stroke-width="4"'))
        b.append(R(222, 40, 20, 14, 3, 'g') + R(292, 96, 18, 14, 3, 'g'))
        b.append('<g class="swing"><path class="ln" d="M300 54 V110" stroke-width="1.6"/>' + R(284, 104, 32, 18, 3, 'g') + '</g>')
        # side cards
        b.append(G(R(330, 120, 130, 74, 14) + T(344, 144, 'Site', 'lbl') + K(344, 156, 84, 6) + K(344, 170, 56, 6, 'k2') + R(420, 160, 26, 14, 7, 'g'), 'float hovlift', D(.2)))
        b.append(G(R(330, 206, 130, 64, 14) + T(344, 230, 'Profit / project', 'lbl sm') + K(344, 242, 100, 8, 'gs2') + K(344, 242, 70, 8, 'g'), 'float hovlift', D(.7)))
        b.append(G(R(330, 28, 130, 76, 14) + ''.join(f'<rect class="g rise" x="{346+i*20}" y="{92-h}" width="12" height="{h}" rx="3" style="--d:{i*.12}s;opacity:{.4+i*.1}"/>' for i, h in enumerate([16, 28, 22, 40, 52])), 'float', D(.4)))
    elif kind == 'healthcare':
        b.append(R(40, 84, 150, 180, 16) + R(96, 48, 38, 38, 10, 'g') + P('M115 56v22M104 67h22', 'tick', 'stroke-width="4"'))
        for i in range(3):
            for j in range(2):
                b.append(R(58 + j * 66, 112 + i * 44, 50, 30, 7, 'gs' if (i + j) % 2 == 0 else 'k'))
        b.append(R(105, 232, 30, 32, 6, 'g2'))
        b.append(G(R(220, 34, 240, 96, 16) + P('M236 96 H282 L296 66 L318 110 L336 78 L350 96 H440', 'gl heartline', 'pathLength="1"') + T(236, 56, 'Patients', 'lbl') + R(398, 42, 46, 18, 9, 'gs'), 'float', D(.1)))
        b.append(G(R(220, 146, 116, 120, 16) + T(236, 170, 'Appointments', 'lbl sm') + ''.join(R(236 + (j % 3) * 28, 184 + (j // 3) * 26, 22, 20, 6, 'g' if j in (1, 4) else 'k') for j in range(6)) + K(236, 244, 70, 6, 'k2'), 'float hovlift', D(.5)))
        b.append(G(R(350, 146, 110, 120, 16) + T(364, 170, 'Branches', 'lbl sm') + C(380, 210, 6, 'g') + C(430, 196, 6, 'g') + C(412, 240, 6, 'g') + P('M380 210 L430 196 L412 240 Z', 'ln') + C(412, 214, 3.5, 'k2'), 'float hovlift', D(.9)))
    elif kind == 'ecommerce':
        b.append(R(40, 40, 250, 224, 18))
        b.append(C(60, 58, 4, 'k2') + C(73, 58, 4, 'k2') + C(86, 58, 4, 'k2') + R(106, 51, 130, 14, 7, 'k'))
        b.append('<rect x="56" y="78" width="218" height="54" rx="10" fill="#d9f5e6" class="mintfill"/>' + K(70, 92, 90, 8, 'g') + K(70, 108, 60, 6, 'k2'))
        for i in range(3):
            b.append(G(R(56 + i * 74, 144, 66, 106, 10, 'c2') + R(62 + i * 74, 150, 54, 46, 7, 'gs') + K(62 + i * 74, 204, 40, 6) + R(62 + i * 74, 222, 28, 14, 7, 'g'), 'float hovlift', D(i * .4)))
        b.append(G(R(310, 40, 150, 100, 16) + T(326, 66, 'Orders', 'lbl') + ''.join(K(326, 78 + j * 16, 96 - j * 10, 6, 'k2' if j else 'g') for j in range(3)), 'float', D(.3)))
        b.append(G(R(310, 156, 150, 108, 16) + T(326, 182, 'Conversion', 'lbl') + '<path d="M326 238 L360 222 L394 228 L430 200 L444 192" class="gl draw" pathLength="1"/>' + C(444, 192, 4, 'g'), 'float', D(.8)))
    elif kind == 'jewellery':
        # gem
        b.append('<g class="float" style="--d:0s"><path d="M110 70 H210 L250 118 L160 220 L70 118 Z" class="gem"/><path d="M70 118 H250 M110 70 L128 118 L160 220 M210 70 L192 118 L160 220 M128 118 L160 70 L192 118" class="ln gemln"/><path d="M110 70 H210 L250 118 H70Z" class="g" opacity=".75"/></g>')
        b.append(f'<g class="sparkle"><path d="M258 62 l4 12 l12 4 l-12 4 l-4 12 l-4 -12 l-12 -4 l12 -4z" class="g"/></g>')
        b.append(G(R(300, 34, 160, 96, 16) + T(316, 60, 'Inventory', 'lbl') + ''.join(R(316 + j * 36, 74, 28, 40, 6, 'gs' if j != 2 else 'g') for j in range(4)), 'float hovlift', D(.3)))
        b.append(G(R(300, 146, 160, 56, 16) + C(326, 174, 13, 'gs') + IC('users', 318, 166, 16, 'var(--green)') + K(348, 166, 80, 6) + K(348, 180, 54, 6, 'k2'), 'float hovlift', D(.6)))
        b.append(G(R(300, 214, 160, 56, 16) + '<path d="M316 252 L346 238 L376 246 L410 226 L444 230" class="gl draw" pathLength="1"/>', 'float', D(.9)))
        b.append(G(R(40, 238, 150, 36, 12) + K(54, 252, 100, 8, 'k2') + R(160, 246, 22, 20, 6, 'g'), 'float', D(.5)))
    elif kind == 'manufacturing':
        b.append(P('M30 244 H450', 'ln', 'stroke-width="2"'))
        b.append(R(40, 108, 160, 136, 8) + '<path class="c" d="M40 108 l40 -26 v26 l40 -26 v26 l40 -26 v26"/>')
        b.append(R(160, 54, 26, 54, 4, 'k2') + R(120, 76, 22, 32, 4, 'k2'))
        for j in range(4):
            b.append(R(58 + j * 34, 138, 22, 26, 5, 'gs' if j % 2 else 'k'))
        b.append(R(90, 196, 36, 48, 4, 'g2'))
        # chimney smoke
        b.append('<circle class="smoke" cx="173" cy="40" r="8"/><circle class="smoke" cx="182" cy="26" r="6" style="--d:1s"/>')
        # conveyor
        b.append(R(222, 206, 238, 22, 11) + ''.join(f'<rect class="g belt" x="{236+i*50}" y="176" width="30" height="26" rx="5" style="--d:{i*.5}s"/>' for i in range(4)))
        b.append(C(240, 217, 6, 'k2') + C(330, 217, 6, 'k2') + C(420, 217, 6, 'k2'))
        b.append(G(R(222, 44, 114, 110, 16) + T(238, 70, 'Inventory', 'lbl sm') + ''.join(f'<rect class="g rise" x="{238+i*18}" y="{140-h}" width="12" height="{h}" rx="3" style="--d:{i*.12}s;opacity:{.4+i*.1}"/>' for i, h in enumerate([20, 36, 28, 48, 34])), 'float hovlift', D(.2)))
        b.append(G(R(350, 44, 110, 110, 16) + T(366, 70, 'Vendors', 'lbl sm') + C(380, 112, 8, 'gs') + C(424, 96, 8, 'gs') + C(424, 130, 8, 'gs') + P('M388 112 L416 98 M388 112 L416 128', 'ln') + C(380, 112, 4, 'g'), 'float hovlift', D(.7)))
    else:  # professional services
        for i, (x, lab) in enumerate([(40, 'Lead'), (176, 'Proposal'), (312, 'Delivery')]):
            b.append(G(R(x, 40, 128, 220, 16) + T(x + 14, 66, lab, 'lbl') + R(x + 12, 80, 104, 44, 10, 'c2') + K(x + 22, 92, 60, 6) + K(x + 22, 106, 40, 5, 'k2')
                       + (R(x + 12, 134, 104, 44, 10, 'c2') + K(x + 22, 146, 70, 6) + K(x + 22, 160, 44, 5, 'k2') if i != 2 else '')
                       + (R(x + 12, 188, 104, 44, 10, 'gs') + K(x + 22, 200, 52, 6, 'g') if i == 1 else ''), 'float', D(i * .4)))
        b.append('<g class="drift">' + R(52, 134, 104, 44, 10, 'c g-outline') + K(62, 146, 70, 6, 'g') + '</g>')
        b.append(G(R(342, 144, 118, 116, 16) + '<path d="M356 232 L384 214 L410 220 L440 188" class="gl draw" pathLength="1"/>' + C(440, 188, 4, 'g'), 'float', D(.9)))
    return wrap('0 0 480 300', ''.join(b), defs)

# --------------------------------------------------------------------------- CASE STUDIES
def case_construction():
    defs = dots('cc-d', 18) + grad('cc-g', '#46cf8a', '#239a5c')
    b = [panel(720, 440, 'cc-d')]
    # hub + modules
    mods = [('Project Management', 'workflow'), ('Site Operations', 'building'), ('Procurement', 'cart'), ('Material Management', 'layers'), ('Contractor Management', 'users'), ('Finance', 'chart')]
    cx, cy = 360, 220
    import math
    for i, (lab, ic) in enumerate(mods):
        a = math.radians(-90 + i * 60)
        nx, ny = cx + 250 * math.cos(a) * (1 if i % 3 else 1), cy + 150 * math.sin(a)
        d = f'M{nx:.0f} {ny:.0f} L{cx} {cy}'
        b.append(f'<path class="ln flow" d="{d}" style="--d:{i*.2}s"/><circle r="3.6" class="g"><animateMotion dur="{2.8+i*.2}s" begin="-{i*.4}s" repeatCount="indefinite" path="{d}"/></circle>')
    b.append(f'<circle class="ring spin" cx="{cx}" cy="{cy}" r="100" fill="none"/>')
    b.append(f'<g transform="translate({cx-70} {cy-48})">{R(0,0,140,96,20,"c coreshadow")}{R(14,14,112,14,7,"gs")}'
             + ''.join(f'<rect class="g rise" x="{22+i*18}" y="{82-h}" width="12" height="{h}" rx="3" style="--d:{i*.1}s;opacity:{.35+i*.12}"/>' for i, h in enumerate([16, 26, 20, 34, 44, 52])) + '</g>')
    for i, (lab, ic) in enumerate(mods):
        a = math.radians(-90 + i * 60)
        nx, ny = cx + 250 * math.cos(a), cy + 150 * math.sin(a)
        w = 170
        b.append(f'<g class="float hovlift" style="--d:{i*.45}s"><g transform="translate({nx-w/2:.0f} {ny-24:.0f})">{R(0,0,w,48,16)}{C(26,24,15,"gs")}{IC(ic,17,15,18,"var(--green)")}{T(50,28,lab,"lbl sm")}</g></g>')
    b.append(G(R(560, 366, 140, 52, 16) + T(576, 388, 'Profit per project', 'lbl sm') + K(576, 398, 108, 8, 'gs2') + K(576, 398, 78, 8, 'g'), 'float', D(.5)))
    return wrap('0 0 720 440', ''.join(b), defs)


def case_shopify():
    defs = dots('cs-d', 18)
    b = [panel(480, 300, 'cs-d')]
    import random
    random.seed(7)
    cols, rows = 7, 4
    for r in range(rows):
        for c in range(cols):
            x, y = 28 + c * 62, 30 + r * 58
            hi = (r, c) == (1, 3)
            shapes = ['<circle cx="{cx}" cy="{cy}" r="9" class="g" opacity=".8"/>', '<rect x="{x2}" y="{y2}" width="18" height="18" rx="5" class="g" opacity=".8"/>', '<path d="M{x0} {y0} l9 -16 l9 16z" class="g" opacity=".8"/>']
            s = shapes[(r + c) % 3].format(cx=x + 24, cy=y + 15, x2=x + 15, y2=y + 6, x0=x + 15, y0=y + 24)
            layout = (r * 3 + c) % 3
            body = R(x, y, 54, 48, 8, 'c2' if not hi else 'c g-outline')
            if layout == 0:
                body += R(x + 5, y + 5, 44, 24, 5, 'gs') + s + K(x + 8, y + 36, 30, 4)
            elif layout == 1:
                body += R(x + 5, y + 5, 20, 38, 5, 'gs') + K(x + 30, y + 10, 18, 4) + K(x + 30, y + 20, 14, 4, 'k2') + R(x + 30, y + 31, 18, 10, 5, 'g')
            else:
                body += K(x + 7, y + 8, 40, 4) + R(x + 5, y + 16, 44, 18, 5, 'gs') + K(x + 8, y + 40, 24, 4, 'k2')
            b.append(f'<g class="tile" style="--d:{(r*cols+c)*.04:.2f}s">{body}</g>')
    b.append(G(R(150, 78, 190, 140, 18, 'c coreshadow') + R(164, 92, 162, 70, 10, 'gs') + '<circle cx="245" cy="127" r="22" class="g"/>' + K(164, 174, 100, 8) + K(164, 190, 70, 6, 'k2') + R(262, 184, 64, 22, 11, 'g') + K(274, 192, 40, 6, 'wh'), 'float', D(0)))
    b.append(G(R(352, 24, 104, 40, 20, 'g') + T(404, 50, '100+', 'lbl wht big', 'middle'), 'float', D(.5)))
    return wrap('0 0 480 300', ''.join(b), defs)


def case_health():
    defs = dots('ch-d', 18) + grad('ch-g', '#46cf8a', '#239a5c')
    b = [panel(480, 300, 'ch-d')]
    hub = (240, 150)
    br = [(70, 70), (410, 70), (60, 230), (420, 232), (240, 262)]
    for i, (x, y) in enumerate(br):
        d = f'M{x} {y} L{hub[0]} {hub[1]}'
        b.append(f'<path class="ln flow" d="{d}" style="--d:{i*.2}s"/><circle r="3.4" class="g"><animateMotion dur="{2.6+i*.2}s" begin="-{i*.4}s" repeatCount="indefinite" path="{d}"/></circle>')
    b.append(f'<circle class="ring spin" cx="{hub[0]}" cy="{hub[1]}" r="86" fill="none"/>')
    b.append(f'<circle class="pulse" cx="{hub[0]}" cy="{hub[1]}" r="40" fill="none" stroke="var(--green)" stroke-width="2"/><circle cx="{hub[0]}" cy="{hub[1]}" r="38" fill="url(#ch-g)" class="coreshadow"/>' + IC('heart', hub[0]-18, hub[1]-18, 36, '#fff'))
    for i, (x, y) in enumerate(br):
        b.append(f'<g class="float" style="--d:{i*.4}s"><g transform="translate({x-24} {y-30})"><path class="g pin" d="M24 60 C 4 38 4 22 24 22 s 20 16 0 38z" transform="translate(0 -4)"/>{C(24,34,7,"wh")}</g></g>')
    b.append(G(R(150, 18, 180, 36, 18) + IC('search', 164, 27, 16, 'var(--green)') + K(188, 33, 110, 6), 'float', D(.3)))
    b.append(G(R(24, 134, 88, 32, 16) + IC('calendar', 36, 142, 16, 'var(--green)') + K(60, 148, 40, 6), 'float', D(.8)))
    b.append(G(R(368, 134, 88, 32, 16) + IC('megaphone', 380, 142, 16, 'var(--green)') + K(404, 148, 40, 6), 'float', D(1.1)))
    return wrap('0 0 480 300', ''.join(b), defs)


def case_d2c():
    defs = dots('cd-d', 18)
    b = [panel(480, 300, 'cd-d')]
    steps = [(30, 'Discover', 'search'), (146, 'Product', 'gem'), (262, 'Cart', 'cart'), (378, 'Checkout', 'check')]
    b.append(P('M80 82 H400', 'ln flow', 'stroke-width="2"'))
    for i, (x, lab, ic) in enumerate(steps):
        b.append(G(C(x + 24, 82, 24, 'g' if i == 3 else 'c') + IC(ic, x + 12, 70, 24, '#fff' if i == 3 else 'var(--green)') + T(x + 24, 126, lab, 'lbl sm', 'middle'), 'float', D(i * .3)))
    b.append('<circle class="g trav" r="6" cy="82"><animate attributeName="cx" values="54;170;286;402;54" keyTimes="0;.28;.55;.8;1" dur="7s" repeatCount="indefinite"/></circle>')
    # phone
    b.append(G(R(60, 150, 96, 140, 18, 'c coreshadow') + R(70, 164, 76, 50, 8, 'gs') + K(70, 224, 56, 6) + K(70, 236, 38, 5, 'k2') + R(70, 252, 76, 20, 10, 'g'), 'float hovlift', D(.2)))
    b.append(G(R(178, 150, 130, 140, 18) + R(190, 164, 50, 50, 8, 'gs') + R(248, 164, 50, 50, 8, 'k') + K(190, 226, 70, 6) + K(190, 240, 44, 5, 'k2') + R(190, 254, 52, 18, 9, 'g'), 'float hovlift', D(.6)))
    b.append(G(R(330, 150, 130, 140, 18) + T(346, 176, 'Journey', 'lbl') + '<path d="M346 250 L372 232 L396 238 L424 208 L444 198" class="gl draw" pathLength="1"/>' + C(444, 198, 4, 'g'), 'float', D(1)))
    return wrap('0 0 480 300', ''.join(b), defs)


def case_whatsapp():
    defs = dots('cw-d', 18)
    b = [panel(480, 300, 'cw-d')]
    # phone chat
    b.append(R(40, 22, 150, 256, 24, 'c coreshadow'))
    b.append(R(40, 22, 150, 40, 24, 'g') + C(64, 42, 10, 'wh') + K(82, 36, 60, 6, 'wh') + K(82, 47, 38, 4, 'wh', 'style="opacity:.6"'))
    b.append(R(54, 76, 96, 30, 14, 'c2') + K(64, 88, 66, 6, 'k2'))
    b.append(R(80, 118, 96, 30, 14, 'gs') + K(90, 130, 66, 6, 'g'))
    b.append(R(54, 160, 110, 30, 14, 'c2') + K(64, 172, 80, 6, 'k2'))
    b.append(R(80, 202, 96, 30, 14, 'g', 'class="g bubble"') + K(90, 214, 70, 6, 'wh'))
    b.append(''.join(f'<circle class="g typing" cx="{62+i*10}" cy="254" r="2.6" style="--d:{i*.18}s"/>' for i in range(3)))
    # automation
    nodes = [(270, 54, 'bot'), (270, 130, 'workflow'), (270, 206, 'db')]
    hubp = (190, 150)
    for i, (x, y, ic) in enumerate(nodes):
        d = f'M190 150 C 230 150 230 {y+22} {x} {y+22}'
        b.append(f'<path class="ln flow" d="{d}" style="--d:{i*.25}s"/><circle r="3.4" class="g"><animateMotion dur="2.4s" begin="-{i*.5}s" repeatCount="indefinite" path="{d}"/></circle>')
        b.append(G(R(x, y, 190, 44, 14) + C(x+26, y+22, 14, 'gs') + IC(ic, x+17, y+13, 18, 'var(--green)') + K(x+50, y+15, 100, 6) + K(x+50, y+26, 66, 5, 'k2') + C(x+172, y+22, 8, 'g') + P(f'M{x+168} {y+22}l3 3 5-6', 'tick'), 'float hovlift', D(i*.4)))
    b.append(G(R(270, 258, 190, 26, 13, 'gs2') + IC('chat', 282, 262, 18, 'var(--green)') + K(308, 268, 120, 6, 'g'), 'float', D(.9)))
    return wrap('0 0 480 300', ''.join(b), defs)
