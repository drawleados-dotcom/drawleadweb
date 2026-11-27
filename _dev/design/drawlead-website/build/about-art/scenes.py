"""About Us illustrations (assets/about/*.webp), rendered with Blender's Python module.

    py -3.13 -m venv bl && bl/Scripts/pip install bpy==5.2.5 pillow numpy
    bl/Scripts/python scenes.py hero,fit final 1 100     # names, output folder, scale, samples
    python export.py                                     # final/*.png -> assets/about/*.webp

Use scale 0.4 and 24 samples for a quick draft."""
import sys, os, math, random
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from lib import *

SK = ['skin1', 'skin2', 'skin3', 'skin4']


# ------------------------------------------------------------------ 01 hero
def hero():
    """Left: more software, spreadsheets, manual work, disconnected. Centre: people under the magnet. Right: it lines up."""
    stage('mint')
    lights(1.3, (0, 0, 1.2))
    cyl((0, .2, 0), 4.15, .24, 'white', .1)
    cyl((0, .2, .24), 3.15, .2, 'cream', .08)
    magnet((0, 1.1, .44), R=1.95, r=1.14, leg=1.0, depth=1.0, tiplen=.6, scale=1.22)
    P = [(-1.62, -1.05, 1.06, 'peach', 0), (-.78, -.6, 1.42, 'sky', 1), (0, -1.25, 1.16, 'white', 2), (.8, -.55, 1.5, 'lilac', 3),
         (1.6, -1.08, 1.1, 'rose', 1), (0, .35, 1.64, 'lime', 0)]
    for x, y, h, k, s in P:
        person((x, y, .44), k, SK[s], h)
    # left: scattered, tilted, unplugged
    L = 1.15
    browser((-5.75 + L, 1.9, .1), 2.1, 1.55, bar='rose', rot=(-6, -10, 20), kind='site')
    browser((-8.55 + L, 2.3, .16), 2.2, 1.7, bar='lilac', rot=(-8, 9, -12), kind='sheet')
    browser((-5.55 + L, -2.1, .12), 1.55, 1.15, bar='peach', rot=(-60, 0, 30), kind='list')
    bubble((-7.3 + L, -.1, 1.5), 1.35, .92, 'sky', rot=(82, -14, 10))
    gear((-7.15 + L, -2.0, .6), .62, 9, 'peach', rot=(70, 18, -18))
    phone((-9.6 + L, -.5, .05), rot=(-10, 13, 26), screen='lilac')
    rbox((-7.0 + L, 3.3, .5), (1, 1, 1), .16, 'lime', (0, 0, 33))
    rbox((-8.9 + L, -2.4, .18), (1.25, .85, .32), .09, 'rose', (0, 6, -24))
    tube([(-9.55 + L, -1.75, .08), (-9.1 + L, -3.0, .08), (-8.1 + L, -1.5, .08), (-7.0 + L, -3.2, .08), (-6.2 + L, -.9, .08), (-5.6 + L, -.4, .08)], .075, 'ink')
    cyl((-5.6 + L, -.4, .08), .16, .4, 'ink', .05, rot=(0, 90, 40))
    # right: the same things, aligned and joined up
    X = -.35
    rbox((7.1 + X, .8, .15), (5.2, 3.2, .3), .15, 'white')
    browser((5.45 + X, 1.5, .3), 1.75, 1.5, bar='green', kind='site')
    browser((7.4 + X, 1.5, .3), 1.75, 1.5, bar='sky', kind='chart')
    browser((9.0 + X, 1.5, .3), 1.2, 1.5, bar='lilac', kind='list')
    for i, (k, n) in enumerate((('peach', 1), ('sky', 2), ('lilac', 1), ('mint', 2))):
        for q in range(n):
            rbox((5.0 + X + i * .8, .25, .3 + .28 + q * .56), (.62, .62, .52), .13, k)
    stack_db((8.75 + X, .15, .3), .48, 3, ('aqua', 'peri', 'rose'))
    tube([(3.1, -.1, .34), (3.7 + X, -.45, .46), (4.6 + X, -.55, .38), (6.6 + X, -.55, .38), (9.4 + X, -.55, .38)], .08, 'green')
    for x in (5.0, 5.8, 6.6, 7.4, 8.75):
        ball((x + X, -.55, .4), .14, 'green')
    tree((9.0 + X, -2.1, 0), 1.1, 'mint')
    tree((5.2 + X, -2.6, 0), .95, 'lime')
    person((6.7 + X, -2.0, 0), 'aqua', 'skin2', 1.16)
    person((7.8 + X, -2.25, 0), 'peach', 'skin4', 1.04)
    camera((0, -27, 7.4), (0, 0, 2.0), 51)
    return (2400, 1128), 'mint'


# ------------------------------------------------------------------ 02 AI: same camera, before and after
def _ai(connected):
    stage('sky', back=16, curve=9)
    lights(1.0, (0, 0, .5))
    cols = [('peach', 'skin1'), ('lilac', 'skin3'), ('mint', 'skin2'), ('rose', 'skin4'), ('lime', 'skin2')]
    R = 3.15
    for i, (c, s) in enumerate(cols):
        a = math.radians(-54 + 72 * i)
        x, y = R * math.cos(a), R * .92 * math.sin(a)
        cyl((x, y, 0), 1.12, .5, 'white', .14)
        cyl((x, y, .5), .88, .12, c, .05)
        px, py = x * 1.03, y * 1.03
        person((px, py, .62), c, SK[(i * 2 + 1) % 4], 1.02)
        # each has a tool of their own
        ang = math.degrees(a) + 90
        if not connected:
            sparkle((px + (0 if 0 < i < 4 else (.66 if x > 0 else -.66)), py - .05, 2.2 + (.12 if i % 2 else 0) - (0 if 0 < i < 4 else .5)), .36 if 0 < i < 4 else .32, 'ink' if i % 2 else 'green', rot=(86, (-14, 12, -8, 16, -10)[i], 0))
            rbox((px + (.55 if i % 2 else -.52), py - .28, .72), (.5, .36, .06), .03, 'ink', (0, 0, 22 if i % 2 else -18))
        else:
            # a bridge to the hub
            u = Vector((x, y, 0)).normalized()
            p = u * (R * .5)
            rbox((p.x, p.y * .95, .31), (1.75, .62, .2), .09, 'green', (0, 0, math.degrees(math.atan2(y, x))))
            rbox((px + (.52 if i % 2 else -.5), py - .25, .8), (.46, .08, .34), .04, 'white', (0, 0, 14 if i % 2 else -14))
    if connected:
        cyl((0, 0, 0), 1.35, .66, 'white', .16)
        cyl((0, 0, .66), 1.08, .16, 'green', .06)
        rbox((0, -.1, 1.28), (1.06, 1.06, .92), .26, 'white')
        sparkle((0, -.65, 1.3), .34, 'green', depth=.14)
        torus((0, 0, .84), 1.0, .05, 'white')
    else:
        # nothing in the middle, and cables that go nowhere
        torus((0, 0, .03), 1.1, .035, 'sky')
        for a0, l in ((-60, 1.5), (30, 1.25), (135, 1.5), (215, 1.2)):
            a = math.radians(a0)
            x0, y0 = 2.0 * math.cos(a), 1.9 * math.sin(a)
            tube([(x0, y0, .06), (x0 * .8 + .22, y0 * .8 - .2, .06), (x0 * .62 - .16, y0 * .62 + .16, .06), (x0 * .46, y0 * .46, .06)], .055, 'ink')
            cyl((x0 * .46, y0 * .46, .06), .11, .28, 'ink', .04, rot=(0, 90, math.degrees(math.atan2(-y0, -x0))))
    camera((0, -14.2, 10.2), (0, .12, .62), 66)
    return (1500, 1200), 'sky'


def ai_before():
    return _ai(False)


def ai_after():
    return _ai(True)


# ------------------------------------------------------------------ 03 story: four chapters
def story1():
    stage('peach', back=9)
    lights(.85, (0, 0, 1))
    cyl((0, 0, 0), 2.5, .24, 'white', .1)
    cyl((0, .1, .24), 1.95, .18, 'cream', .07)
    browser((0, .45, .42), 2.75, 2.1, bar='green', kind='site')
    person((-1.15, -.75, .42), 'sky', 'skin2', .95)
    person((1.2, -.8, .42), 'rose', 'skin1', .82)
    tree((2.0, .4, .24), .72, 'mint')
    camera((0, -10.5, 4.6), (0, 0, 1.02), 60)
    return (1200, 1200), 'peach'


def story2():
    stage('rose', back=10)
    lights(.95, (0, 0, 1))
    S = [(-2.1, 1.3), (1.95, 1.5), (-2.35, -1.35), (2.3, -1.2), (.05, .05)]
    for x, y in S:
        cyl((x, y, 0), 1.12, .2, 'white', .09)
    browser((-2.1, 1.4, .2), 1.6, 1.3, bar='peach', kind='site', rot=(0, 0, 16))
    for i, c in enumerate(('peri', 'lilac', 'sky')):           # ERP: drawers
        rbox((1.95, 1.55, .2 + .25 + i * .5), (1.25, .9, .46), .1, c)
        rbox((1.95, 1.09, .2 + .25 + i * .5), (.42, .06, .09), .03, 'white')
    browser((-2.35, -1.35, .2), 1.5, 1.2, bar='lilac', kind='sheet', rot=(0, 0, 12))
    for i, c in enumerate(('mint', 'lime', 'aqua')):           # CRM: cards
        rbox((2.3 + (i - 1) * .1, -1.15 + (1 - i) * -.22, .72), (1.15, .08, .86), .04, c, (-8, 0, -14))
    ball((2.22, -1.52, .92), .13, 'white')
    rbox((2.3, -1.5, .62), (.55, .05, .12), .025, 'white', (-8, 0, -14))
    phone((.05, .1, .2), rot=(0, 0, -6), screen='sky')
    person((-.62, -2.1, 0), 'mint', 'skin3', .92)
    person((.5, -2.35, 0), 'peach', 'skin1', .84)
    for p in ([(-1.95, .1, .06), (-1.5, -.15, .06), (-1.55, .3, .06), (-1.15, .22, .06)], [(1.15, -.95, .06), (.85, -.7, .06), (1.15, -.45, .06), (.8, -.3, .06)], [(2.75, .2, .06), (2.3, -.1, .06), (2.75, -.25, .06), (2.45, -.4, .06)], [(-.5, 1.55, .06), (0, 1.75, .06), (.2, 1.4, .06), (.6, 1.6, .06)]):
        tube(p, .06, 'ink')
        d = Vector(p[-1]) - Vector(p[-2])
        cyl(p[-1], .12, .3, 'ink', .04, rot=(0, 90, math.degrees(math.atan2(d.y, d.x))))
    camera((0, -11.6, 8.2), (0, .05, .5), 64)
    return (1200, 1200), 'rose'


def story3():
    """Everything is there. One span is missing."""
    stage('peri', back=10)
    lights(.95, (0, 0, 1))
    for s, c in ((-1, 'peach'), (1, 'mint')):
        cyl((s * 2.5, .3, 0), 1.5, 1.3, 'white', .16)
        cyl((s * 2.5, .3, 1.3), 1.24, .12, c, .05)
    for x in (-1.02, 1.02):
        rbox((x, .3, 1.28), (.86, 1.0, .24), .08, 'green')
    rbox((.12, -1.75, .24), (1.06, 1.0, .24), .08, 'green', (0, 19, 26))
    person((-2.75, -.05, 1.42), 'lilac', 'skin2', .92)
    person((-2.15, .5, 1.42), 'sky', 'skin4', 1.02)
    person((-3.2, .62, 1.42), 'rose', 'skin1', .84)
    browser((2.6, .55, 1.42), 1.55, 1.2, bar='green', kind='chart')
    stack_db((1.95, -.28, 1.42), .28, 2, ('sky', 'lilac'), h=.22, gap=.05)
    rbox((3.2, -.25, 1.64), (.42, .42, .42), .1, 'peach')
    person((1.1, -2.35, 0), 'lime', 'skin3', .86)
    camera((0, -13.4, 6.6), (0, 0, .98), 54)
    return (1200, 1200), 'peri'


def story4():
    """One connected place."""
    stage('mint', back=10)
    lights(.95, (0, 0, 1))
    cyl((0, 0, 0), 3.55, .26, 'white', .12)
    cyl((0, 0, .26), 2.95, .12, 'cream', .05)
    magnet((0, 1.15, .38), R=1.15, r=.64, leg=.6, depth=.6, tiplen=.38, gap=.07, bev=.08)
    browser((-1.95, .75, .38), 1.25, 1.05, bar='peach', kind='site', rot=(0, 0, 24))
    browser((1.95, .75, .38), 1.25, 1.05, bar='sky', kind='chart', rot=(0, 0, -24))
    for i, (c, n) in enumerate((('lilac', 1), ('rose', 2), ('lime', 3))):
        for k in range(n):
            rbox((-2.2 + i * .52, -.72 - i * .12, .38 + .2 + k * .42), (.42, .42, .38), .09, c)
    stack_db((1.95, -.75, .38), .4, 3, ('aqua', 'peri', 'lilac'), h=.24, gap=.05)
    tube([(-1.85, .25, .42), (-1.2, -.1, .42), (-.55, -.2, .42), (0, -.1, .42), (.55, -.2, .42), (1.2, -.1, .42), (1.85, .25, .42)], .07, 'green')
    tube([(0, -.1, .42), (-.35, -.65, .42), (-1.0, -1.35, .42)], .07, 'green')
    tube([(0, -.1, .42), (.45, -.6, .42), (1.5, -.8, .42)], .07, 'green')
    ball((0, -.1, .44), .17, 'green')
    person((0, .75, .38), 'white', 'skin2', .98)
    person((-.72, -1.75, .38), 'sky', 'skin1', .9)
    person((.1, -2.05, .38), 'peach', 'skin3', .96)
    person((.9, -1.7, .38), 'lilac', 'skin4', .88)
    tree((-3.0, -.1, .26), .62, 'mint')
    tree((3.05, 0, .26), .62, 'lime')
    camera((0, -11.8, 6.4), (0, 0, .95), 56)
    return (1200, 1200), 'mint'


def story5():
    """2026: the needle points at the magnet, and people are already walking that way."""
    stage('mint', back=10)
    lights(1.0, (0, 0, 1))
    cyl((0, .1, 0), 3.9, .26, 'white', .12)
    cyl((0, .1, .26), 3.3, .1, 'cream', .05)
    # compass, leaning back on its stand, dial towards the camera
    c = empty((-1.15, .25, 2.16), (72, 0, 8), 1.0)
    cyl((0, 0, -.22), 1.72, .46, 'green', .16, parent=c)
    cyl((0, 0, .2), 1.44, .06, 'white', .02, parent=c)
    cyl((0, 0, .25), 1.12, .03, 'mint', .01, parent=c)
    for i in range(24):
        a = math.radians(i * 15)
        big = i % 6 == 0
        rbox((math.cos(a) * 1.28, math.sin(a) * 1.28, .27), (.2 if big else .12, .075 if big else .035, .05), .015, 'ink' if big else 'dgreen', (0, 0, i * 15), c)
    n = empty((0, 0, .36), (0, 0, 10), 1.0, c)
    extr([(1.16, 0), (0, .25), (0, -.25)], .12, 'dgreen', bev=.03, parent=n)
    extr([(-1.02, 0), (0, -.25), (0, .25)], .12, 'peach', bev=.03, parent=n)
    ball((0, 0, .46), .15, 'ink', c)
    torus((0, 1.98, 0), .3, .09, 'dgreen', (0, 0, 0), c)
    rbox((-1.15, .55, .66), (1.7, .6, .72), .2, 'white', (0, 0, 8))
    # the magnet it points to
    cyl((2.1, .95, .36), 1.3, .16, 'white', .07)
    magnet((2.1, .95, .52), R=1.2, r=.68, leg=.86, depth=.6, tiplen=.44, gap=.07, bev=.08)
    # the way there
    tube([(-2.3, -1.45, .4), (-1.2, -1.72, .4), (.1, -1.5, .4), (1.0, -.9, .4), (1.62, -.5, .4), (1.86, -.28, .4)], .085, 'green')
    for x, y, col, sk, h, r in ((-2.0, -2.0, 'lilac', 'skin4', .86, 0), (-.85, -2.2, 'sky', 'skin2', .98, 0), (.42, -1.98, 'peach', 'skin1', .9, 0), (1.42, -1.25, 'rose', 'skin3', .94, 0)):
        person((x, y, .36), col, sk, h, rot=r)
    person((2.1, .75, .52), 'white', 'skin2', 1.0)
    tree((-3.15, .2, .28), .74, 'mint')
    tree((3.3, -.2, .28), .62, 'lime')
    camera((0, -12.4, 5.4), (.02, 0, 1.32), 50)
    return (1600, 1280), 'mint'


def plane():
    """Paper dart folded from a printed page. Needs sheet.png (the typeset spread) next to the output."""
    w = stage('peach', floor=False)
    lights(.8, (0, 0, 0))
    V = dict(N=(2.3, 0, .02), KL=(-1.35, .1, .3), KR=(-1.35, -.1, .3), WL=(-1.35, 1.5, .52), WR=(-1.35, -1.5, .52), KB=(-1.2, 0, -.62))
    img = bpy.data.images.load(os.environ['SHEET'])
    m = bpy.data.materials.new('paper')
    m.use_nodes = True
    nt = m.node_tree
    bs = next(n for n in nt.nodes if n.type == 'BSDF_PRINCIPLED')
    bs.inputs['Roughness'].default_value = .9
    tx = nt.nodes.new('ShaderNodeTexImage')
    tx.image = img
    tx.extension = 'REPEAT'
    mx = nt.nodes.new('ShaderNodeMix')
    mx.data_type = 'RGBA'
    nt.links.new(tx.outputs['Color'], next(i for i in mx.inputs if i.name == 'Factor' and i.type == 'VALUE'))
    ca = [i for i in mx.inputs if i.type == 'RGBA']
    ca[0].default_value = lin('4A4640')
    ca[1].default_value = lin('FBF1E4')
    nt.links.new([o for o in mx.outputs if o.type == 'RGBA'][0], bs.inputs['Base Color'])
    for k, f in enumerate((('N', 'KL', 'WL'), ('N', 'WR', 'KR'), ('N', 'KB', 'KL'), ('N', 'KR', 'KB'))):
        me = bpy.data.meshes.new('pl')
        me.from_pydata([V[i] for i in f], [], [(0, 1, 2)])
        uv = me.uv_layers.new(name='uv')
        for li, i in enumerate(f):
            x, y, z = V[i]
            yy = y if k < 2 else z * 1.1 + 3
            uv.data[li].uv = (.03 + (x + 1.35) * .052, .12 + (yy + 1.5) * .156)
        me.materials.append(m)
        o = bpy.data.objects.new('pl', me)
        bpy.context.scene.collection.objects.link(o)
        sm = o.modifiers.new('s', 'SOLIDIFY')
        sm.thickness = .022
    camera((.9, -7.2, 3.1), (.36, 0, .04), 58)
    return (720, 420), 'peach'


# ------------------------------------------------------------------ 04 every business has its own shape
def fit():
    stage('lilac', back=12, curve=6)
    lights(1.0, (0, 0, .5))
    tri = roundpoly([(-.82, -.66), (.82, -.66), (0, .86)], .2)
    hexa = roundpoly([(.86 * math.cos(math.radians(60 * i + 30)), .86 * math.sin(math.radians(60 * i + 30))) for i in range(6)], .14)
    shapes = [circ(.78), roundpoly([(-.72, -.72), (.72, -.72), (.72, .72), (-.72, .72)], .2), tri, hexa]
    xs = [-3.3, -1.1, 1.1, 3.3]
    holes = [[(x + px, py) for px, py in sh] for x, sh in zip(xs, shapes)]
    extr(rrect(9.3, 2.9, .5), .62, 'white', (0, 0, .31), bev=.1, holes=holes)
    rbox((0, 0, .06), (8.9, 2.5, .1), .05, 'lilac')
    cols = ['peach', 'sky', 'green', 'rose']
    zs = [.42, .42, 2.0, .42]
    rots = [(0, 0, 0), (0, 0, 0), (14, -10, 10), (0, 0, 0)]
    for x, sh, c, z, r in zip(xs, shapes, cols, zs, rots):
        k = .93
        extr([(px * k, py * k) for px, py in sh], .74, c, (x, 0 if z < 1 else -.1, z), r, bev=.1)
    # who each shape belongs to
    person((-3.3, 0, .79), 'white', 'skin2', .86)
    person((-1.1, 0, .79), 'white', 'skin1', .86)
    person((3.3, 0, .79), 'white', 'skin3', .86)
    person((1.75, -2.35, 0), 'mint', 'skin4', .9)
    person((.55, -2.5, 0), 'peri', 'skin2', .98)
    camera((0, -14.5, 8.6), (0, -.2, .74), 56)
    return (1800, 1000), 'lilac'


# ------------------------------------------------------------------ 05 capabilities
def cap_systems():
    stage('sky', back=9)
    lights(.9, (0, 0, 1))
    rbox((0, 0, .12), (4.9, 3.7, .24), .12, 'white')
    s = 1.06
    blocks = [(-1.5, .7, 0, 'peach'), (-.3, .7, 0, 'lilac'), (.9, .7, 0, 'mint'), (-1.5, .7, 1, 'sky'), (-.3, .7, 1, 'rose'), (-.3, .7, 2, 'lime'), (.9, .7, 1, 'peri')]
    for x, y, z, c in blocks:
        rbox((x, y, .24 + s / 2 + z * (s + .04)), (s, s, s), .16, c)
        rbox((x, y - s / 2, .24 + s * .62 + z * (s + .04)), (.5, .05, .12), .025, 'white')
        rbox((x - .1, y - s / 2, .24 + s * .36 + z * (s + .04)), (.3, .05, .1), .025, 'white')
    z = .3
    tube([(-1.5, .1, z), (-1.5, -.55, z), (-.6, -.75, z), (.9, -.75, z), (1.7, -.75, z)], .075, 'green')
    tube([(-.3, .1, z), (-.3, -.4, z), (-.1, -.75, z)], .075, 'green')
    tube([(.9, .1, z), (.9, -.4, z), (.9, -.75, z)], .075, 'green')
    for x, y in ((-.1, -.75), (.9, -.75)):
        ball((x, y, z + .02), .13, 'green')
    browser((1.95, -.55, .24), 1.0, .86, bar='green', kind='chart', rot=(0, 0, -18))
    person((-1.55, -1.15, .24), 'white', 'skin2', .8)
    person((-.55, -1.3, .24), 'peach', 'skin1', .74)
    person((.5, -1.3, .24), 'lilac', 'skin3', .78)
    camera((4.6, -10.6, 6.4), (0, 0, 1.22), 62)
    return (1400, 1100), 'sky'


def cap_commerce():
    stage('peach', back=9)
    lights(.9, (0, 0, 1))
    rbox((0, 0, .12), (4.9, 3.7, .24), .12, 'white')
    rbox((-.35, .55, 1.3), (2.9, 1.5, 2.1), .12, 'cream')
    rbox((-.35, .55, 2.42), (3.1, 1.7, .22), .1, 'green')
    rbox((.55, -.2, .92), (.74, .08, 1.3), .03, 'sky')
    ball((.82, -.27, .9), .05, 'white')
    rbox((-.85, -.2, 1.06), (1.5, .08, .95), .03, 'aqua')
    for i, c in enumerate(('rose', 'lilac', 'lime')):
        rbox((-1.3 + i * .45, -.28, .78 + .13), (.26, .12, .34 + i * .07), .05, c)
    n = 8
    w = 3.2 / n
    for i in range(n):                                        # awning
        c = 'green' if i % 2 == 0 else 'white'
        rbox((-.35 - 1.6 + w * (i + .5), -.46, 1.97), (w - .01, .95, .1), .04, c, (24, 0, 0))
        cyl((-.35 - 1.6 + w * (i + .5), -.94, 1.69), w / 2 - .004, .14, c, .05, rot=(0, 0, 0))
    rbox((-.35, .05, 2.75), (1.5, .16, .46), .1, 'white')
    for i, c in enumerate(('peach', 'sky', 'green')):
        ball((-.75 + i * .4, -.06, 2.75), .11, c)
    # bag, parcels, card
    e = empty((1.9, -.75, .24), (0, 0, -14))
    rbox((0, 0, .5), (.92, .5, 1.0), .09, 'rose', parent=e)
    tube([(-.22, -.0, .98), (-.2, -.0, 1.28), (0, 0, 1.38), (.2, -.0, 1.28), (.22, -.0, .98)], .045, 'white', e)
    sparkle((0, -.26, .52), .2, 'white', depth=.06, parent=e)
    rbox((-1.75, -1.15, .5), (.72, .62, .52), .07, 'peach', (0, 0, 14))
    rbox((-1.68, -1.12, .98), (.5, .46, .4), .06, 'lime', (0, 0, -8))
    person((.0, -1.3, .24), 'lilac', 'skin2', .84)
    person((.75, -1.35, .24), 'sky', 'skin4', .76)
    tree((1.95, .75, .24), 1.15, 'mint')
    camera((-4.2, -10.8, 5.6), (0, 0, 1.3), 62)
    return (1400, 1100), 'peach'


def cap_ai():
    stage('lilac', back=9)
    lights(.9, (0, 0, 1))
    rbox((0, 0, .12), (4.9, 3.7, .24), .12, 'white')
    # conveyor
    rbox((0, -.85, .46), (4.4, .95, .36), .17, 'ink')
    for i in range(6):
        cyl((-1.9 + i * .76, -1.36, .46), .12, .06, 'white', .02, rot=(90, 0, 0))
    for i, (c, r) in enumerate((('soft', 28), ('soft', -22), ('peach', 8))):
        rbox((-1.8 + i * .66, -.85, .64 + .21), (.42, .42, .42), .09, c, (0, 0, r))
    for i, c in enumerate(('mint', 'sky', 'rose')):
        x = .55 + i * .64
        rbox((x, -.85, .64 + .22), (.44, .44, .44), .1, c)
        tube([(x - .09, -1.09, .85), (x - .02, -1.09, .78), (x + .1, -1.09, .95)], .028, 'green', smooth=False)
    # robot
    e = empty((-.05, .55, .24), (0, 0, 0))
    rbox((0, 0, .62), (1.3, .9, 1.1), .3, 'white', parent=e)
    rbox((0, -.43, .62), (.62, .08, .46), .04, 'mint', parent=e)
    sparkle((0, -.48, .62), .17, 'green', depth=.06, parent=e)
    cyl((0, 0, 1.15), .2, .2, 'soft', .04, parent=e)
    rbox((0, 0, 1.85), (1.75, 1.15, 1.15), .38, 'white', parent=e)
    rbox((0, -.5, 1.85), (1.36, .22, .74), .11, 'ink', parent=e)
    for s in (-1, 1):
        rbox((s * .32, -.63, 1.9), (.17, .06, .26), .03, 'green', parent=e)
        cyl((s * .86, 0, 1.85), .22, .14, 'green', .05, rot=(0, s * 90, 0), parent=e)
        tube([(s * .64, -.1, .95), (s * .98, -.5, .9), (s * .85, -1.0, .95)], .11, 'soft', e)
        ball((s * .85, -1.02, .95), .17, 'green', e)
    tube([(0, 0, 2.4), (0, 0, 2.72)], .04, 'soft', e)
    ball((0, 0, 2.82), .13, 'green', e)
    gear((-1.95, .75, .92), .62, 10, 'peri', rot=(90, 0, 18))
    gear((-1.45, .95, 1.75), .42, 9, 'peach', rot=(90, 0, 10))
    sparkle((1.75, .5, 2.4), .42, 'green', rot=(90, 0, -10))
    sparkle((2.2, .5, 1.75), .22, 'rose', rot=(90, 0, -10))
    person((1.75, .45, .24), 'lime', 'skin2', .92)
    camera((3.6, -10.8, 5.4), (0, -.1, 1.28), 62)
    return (1400, 1100), 'lilac'


def cap_growth():
    stage('lime', back=9)
    lights(.9, (0, 0, 1))
    rbox((0, 0, .12), (4.9, 3.7, .24), .12, 'white')
    hs = [.6, 1.0, 1.35, 1.95, 2.6]
    for i, (h, c) in enumerate(zip(hs, ('peach', 'sky', 'lilac', 'mint', 'green'))):
        rbox((-1.5 + i * .74, .75, .24 + h / 2), (.56, .56, h), .12, c)
    pts = [(-1.75, .2, 1.2), (-.8, .2, 1.65), (0, .2, 1.85), (.75, .2, 2.6), (1.5, .2, 3.35)]
    tube(pts, .075, 'ink')
    lathe([(0, 0), (.24, 0), (0, .42)], 'ink', (1.5, .2, 3.32), (0, 42, 0))
    # magnet drawing the audience in
    e = empty((-.95, -.9, .46), (0, 0, 80))
    magnet((0, 0, 0), R=.62, r=.34, leg=.3, depth=.36, tiplen=.22, gap=.05, rot=(0, 0, 0), bev=.05, parent=e)
    for i, (x, y, r, c) in enumerate(((-.2, -.72, .19, 'peach'), (.35, -1.15, .17, 'rose'), (.72, -.75, .2, 'sky'), (1.2, -1.15, .17, 'lilac'), (1.5, -.65, .19, 'lime'), (2.0, -1.05, .15, 'aqua'))):
        ball((x, y, .24 + r), r, c)
    # megaphone
    e = empty((1.95, .35, .95), (0, 0, 24))
    lathe([(0, 0), (.16, 0), (.2, .1), (.62, 1.0), (.66, 1.04), (.6, 1.06), (.16, .2), (0, .2)], 'white', (0, 0, 0), (74, 0, 0), parent=e)
    cyl((0, .16, -.04), .22, .3, 'green', .08, rot=(74, 0, 0), parent=e)
    rbox((0, .12, -.42), (.16, .22, .62), .07, 'ink', parent=e)
    person((2.1, -1.35, .24), 'peach', 'skin3', .74)
    camera((-3.8, -10.8, 5.6), (0, 0, 1.3), 62)
    return (1400, 1100), 'lime'


# ------------------------------------------------------------------ 09 vision
def vision():
    stage('aqua', back=26, curve=14, half=80, front=40)
    lights(1.9, (0, 4, 1))
    cyl((1.4, 14, 4.1), 2.9, .35, mat('FAD3AC', emit=.32), .12, rot=(90, 0, 0), steps=192)
    for x, y, rx, ry, rz, c in ((-9, 9, 8, 4, 3.0, 'lilac'), (9.5, 9.5, 8.5, 4, 3.4, 'sky'), (0, 10, 7, 3.4, 1.9, 'mint'), (-13, 4, 6, 4, 1.9, 'rose'), (13.5, 4.2, 6, 4, 2.1, 'lime')):
        ball((x, y, -.15), 1, c, scale=(rx, ry, rz))
    # the road everything shares
    road = [(-13, -4.6, .03), (-8, -3.0, .03), (-3.6, -4.0, .03), (.4, -2.2, .03), (2.4, .8, .03), (.6, 3.2, .03), (1.6, 6.2, .03)]
    o = tube(road, .42, 'green')
    o.scale = (1, 1, .22)
    for c in bpy.context.scene.collection.objects:
        if c.name.startswith('ball') and c.data.materials[0].name == 'm_green':
            c.scale = (1, 1, .22)
    o2 = tube([(9.8, -4.2, .03), (6.4, -3.4, .03), (3.2, -2.9, .03), (.7, -2.0, .03)], .3, 'green')
    o2.scale = (1, 1, .22)
    # businesses, each its own shape
    def house(x, y, c, roof, s=1.0, r=0):
        e = empty((x, y, 0), (0, 0, r), s)
        rbox((0, 0, .62), (1.3, 1.1, 1.24), .1, c, parent=e)
        extr(roundpoly([(-.86, 0), (.86, 0), (0, .72)], .1), 1.24, roof, (0, 0, 1.22), (90, 0, 0), bev=.06, parent=e)
        rbox((0, -.56, .38), (.34, .06, .62), .025, 'white', parent=e)
    def tower(x, y, c, n=4, s=1.0, r=0):
        e = empty((x, y, 0), (0, 0, r), s)
        rbox((0, 0, n * .36 + .06), (1.15, 1.0, n * .72 + .12), .12, c, parent=e)
        for k in range(n):
            for i in (-1, 0, 1):
                rbox((i * .32, -.5, .42 + k * .72), (.2, .05, .3), .022, 'white', parent=e)
    house(-6.2, -1.1, 'cream', 'peach', 1.15, 10)
    tower(-3.4, .4, 'lilac', 4, 1.1, 6)
    cyl((-1.1, 1.9, 0), .85, 1.5, 'aqua', .14)
    lathe([(.0, 0), (.87, 0)] + [(.87 * math.cos(math.radians(a)), .87 * math.sin(math.radians(a))) for a in range(6, 91, 6)], 'white', (-1.1, 1.9, 1.5))
    tower(5.2, -.4, 'sky', 3, 1.2, -10)
    house(7.8, -1.8, 'white', 'rose', 1.1, -14)
    e = empty((4.0, 2.8, 0), (0, 0, -6), 1.1)                 # factory
    rbox((0, 0, .6), (2.2, 1.2, 1.2), .08, 'peri', parent=e)
    for i in range(3):
        extr(roundpoly([(-.36, 0), (.36, 0), (.36, .46)], .05), 1.2, 'white', (-.72 + i * .72, 0, 1.18), (90, 0, 0), bev=.04, parent=e)
    cyl((.8, .4, 1.1), .16, 1.2, 'rose', .05, parent=e)
    house(-9.6, -1.2, 'white', 'green', .95, 16)
    for x, y, s, c in ((-7.9, -.6, 1.0, 'mint'), (-4.9, 1.2, 1.1, 'lime'), (2.6, 3.8, .9, 'mint'), (6.9, .8, 1.2, 'mint'), (9.6, -1.0, 1.1, 'lime'), (-11.3, -2.0, 1.2, 'mint'), (3.6, -1.4, .7, 'lime'), (-1.7, -.9, .7, 'mint')):
        tree((x, y, 0), s, c)
    for x, y, c, s, h in ((-5.4, -3.9, 'peach', 0, 1.05), (-4.6, -4.35, 'sky', 1, .95), (-.4, -3.4, 'lilac', 2, 1.0), (.35, -3.75, 'lime', 3, .9), (1.9, -1.6, 'rose', 1, 1.0), (6.1, -4.4, 'white', 0, .95), (6.9, -4.2, 'peach', 2, 1.02)):
        person((x, y, .06), c, SK[s], h)
    for x, y, z, s in ((-8.6, 8, 6.3, 1.0), (9.4, 9, 6.7, 1.15), (-3.4, 12, 7.6, .7)):
        for dx, r in ((-.9, .5), (0, .78), (.95, .56)):
            ball((x + dx * s, y, z), r * s, 'white', scale=(1.25, .7, .8))
    camera((0, -24, 7.2), (0, 2, 2.75), 46)
    return (2400, 1272), 'aqua'


# ------------------------------------------------------------------ 06 culture: six objects on transparent
def _icon(fn, look=(0, 0, .95), cam=(2.6, -8.2, 3.9), lens=92):
    stage('mint')
    lights(.75, (0, 0, 1))
    fn()
    camera(cam, look, lens)
    return (900, 900), 'mint'


def cul_curiosity():
    def f():
        e = empty((-.1, 0, 1.28), (0, -28, 8))
        torus((0, 0, 0), .74, .14, 'green', (90, 0, 0), e)
        cyl((0, .04, 0), .66, .06, mat('EAF4FF', rough=.1), .02, (90, 0, 0), e)
        cyl((0, 0, -.8), .12, .22, 'white', .04, (180, 0, 0), e)
        rbox((0, 0, -1.35), (.28, .28, .8), .13, 'ink', parent=e)
        sparkle((.0, -.14, .0), .34, 'peach', depth=.1, parent=e)
        rbox((-1.1, .5, .24), (.48, .48, .48), .12, 'sky', (0, 0, 20))
        ball((1.15, -.2, .2), .2, 'peach')
        rbox((.95, .7, .4), (.5, .5, .8), .12, 'lilac', (0, 0, -16))
    return _icon(f)


def cul_beyond():
    """Standing on the task to see the whole picture."""
    def f():
        for i, k in enumerate(('peach', 'sky', 'lilac')):
            h = .42 * (i + 1)
            rbox((-1.0 + i * .84, 0, h / 2), (.8, 1.1, h), .12, k)
        person((.68, 0, 1.26), 'green', 'skin2', 1.08)
        cyl((1.16, .9, 2.46), .4, .08, 'FBC98F', .03, (90, 0, 0))
        tree((-1.05, -.05, .42), .62, 'mint')
    return _icon(f, look=(0.1, 0, 1.22), lens=84)


def cul_ownership():
    """The key, and the lock it belongs to."""
    def f():
        rbox((-.62, .25, .56), (1.16, .6, 1.12), .2, 'peach', (0, 0, 10))
        tube([(-1.0, .18, 1.0), (-.98, .18, 1.5), (-.62, .25, 1.78), (-.26, .32, 1.5), (-.24, .32, 1.0)], .1, 'white')
        cyl((-.58, -.04, .62), .13, .08, 'ink', .03, (90, 0, 10))
        rbox((-.58, -.07, .44), (.1, .06, .26), .028, 'ink', (0, 0, 10))
        e = empty((.82, -.3, .9), (0, 32, -4))
        torus((0, 0, .5), .34, .12, 'green', (90, 0, 0), e)
        rbox((0, 0, -.36), (.2, .2, 1.1), .085, 'green', parent=e)
        rbox((.2, 0, -.78), (.3, .17, .15), .06, 'green', parent=e)
        rbox((.17, 0, -.5), (.24, .17, .15), .06, 'green', parent=e)
    return _icon(f, look=(.05, 0, .92))


def cul_collab():
    """Everyone round one table."""
    def f():
        cyl((0, 0, 0), 1.45, .16, 'white', .07)
        cyl((0, 0, .16), .46, .5, 'green', .12)
        cyl((0, 0, .62), .78, .12, 'cream', .05)
        sparkle((0, 0, 1.28), .34, 'green', rot=(86, 0, 10))
        for i, (k, s, h) in enumerate((('sky', 1, 1.0), ('rose', 0, .92), ('lilac', 3, 1.04), ('peach', 2, .96), ('lime', 1, .9))):
            a = math.radians(-60 + 72 * i)
            person((1.12 * math.cos(a), 1.12 * math.sin(a), .16), k, SK[s], h)
    return _icon(f, look=(0, 0, .66), cam=(1.6, -8.0, 5.0), lens=88)


def cul_experiment():
    """A flask mid-experiment."""
    def f():
        side = lambda z: .88 + (.3 - .88) * (z - .12) / (1.2 - .12)
        lathe([(0, 0), (.78, 0)] + arc(.78, .12, .12, -90, 0, 8) + [(side(.3), .3), (side(.5), .5), (side(.7), .7), (0, .7)], 'green')
        lathe([(0, .7), (side(.7), .7), (side(.95), .95), (.3, 1.2), (.275, 1.3), (.27, 1.5), (.27, 1.74)] + arc(.3, 1.8, .06, -90, 90, 8) + [(.22, 1.86), (.2, 1.8), (0, 1.8)], 'F4FAF7')
        for x, y, z, r, k in ((.0, 0, 1.98, .15, 'green'), (.2, 0, 2.32, .19, 'peach'), (-.24, 0, 2.44, .11, 'sky'), (.02, 0, 2.66, .09, 'lilac')):
            ball((x, y, z), r, k)
        for x, z, r in ((-.2, .32, .09), (.12, .5, .07), (.26, .26, .06)):
            ball((x, -side(z) + .02, z), r, 'mint')
        rbox((-1.15, .25, .3), (.5, .5, .6), .12, 'rose', (0, 0, 18))
        rbox((1.15, .25, .2), (.56, .56, .4), .12, 'sky', (0, 0, -14))
        ball((1.15, .25, .58), .18, 'lilac')
    return _icon(f, look=(0, 0, 1.3), lens=80)


def cul_purpose():
    """A target, hit."""
    def f():
        e = empty((0, .15, 1.15), (0, 0, 14))
        for i, (r, c) in enumerate(((1.02, 'rose'), (.74, 'white'), (.46, 'rose'), (.2, 'green'))):
            cyl((0, -.02 - i * .07, 0), r, .16, c, .05, (90, 0, 0), e)
        tube([(.04, -.35, .04), (.95, -1.5, .62)], .05, 'ink', e, smooth=False)
        for a in (0, 90):
            rbox((.93, -1.47, .61), (.04, .34, .26), .018, 'green', (32, a, -38), e)
        rbox((-.55, .42, .24), (.2, .2, .55), .08, 'ink', (14, -10, 0))
        rbox((.55, .42, .24), (.2, .2, .55), .08, 'ink', (14, 10, 0))
        rbox((-1.15, -.45, .22), (.44, .44, .44), .11, 'lime', (0, 0, 22))
    return _icon(f, look=(0.0, 0, 1.06), lens=88)


if __name__ == '__main__':
    name, outdir = sys.argv[1], sys.argv[2]
    scale = float(sys.argv[3]) if len(sys.argv) > 3 else 1
    smp = int(sys.argv[4]) if len(sys.argv) > 4 else 96
    for nm in name.split(','):
        reset()
        (w, h), tint = globals()[nm]()
        out = os.path.abspath(os.path.join(outdir, nm + '.png'))
        render(out, int(w * scale), int(h * scale), smp)
        finish(out, tint)
        print('done', nm)
