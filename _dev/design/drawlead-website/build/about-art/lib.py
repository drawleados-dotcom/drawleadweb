"""Small clay-render kit on top of bpy: palette, rounded primitives, peg people, props, stage, render."""
import bpy, bmesh, math, random, os, sys
from mathutils import Vector

# The eight brand tints, used for stages (backdrops).
TINT = dict(mint='DAF1E5', sky='DFEAF9', lilac='E8E2F5', aqua='DCEFF0', peach='F8E6D6',
            peri='E3E5F6', rose='F5E1E9', lime='EDF0D7')
# Deeper siblings of the same hues, so objects read against a pale stage.
OBJ = dict(mint='9FDBBC', sky='A6C6F3', lilac='BFAEEE', aqua='9CD6DA', peach='F7BC8C', peri='ABB4F2',
           rose='F0A5C2', lime='CFDB7E', white='F7F8F6', cream='F6EFE4', green='32B46F', dgreen='1D7A49',
           ink='20262A', soft='E4E9E6', skin1='EBC09A', skin2='C98E66', skin3='9C6544', skin4='F2D3B6')


def lin(hexv):
    r, g, b = (int(hexv[i:i + 2], 16) / 255 for i in (0, 2, 4))
    f = lambda c: c / 12.92 if c <= .04045 else ((c + .055) / 1.055) ** 2.4
    return (f(r), f(g), f(b), 1)


_mats = {}
EXPO = -.42


def mat(name, rough=.5, emit=0, coat=.0, glass=False):
    key = (name, rough, emit, coat, glass)
    if key in _mats:
        return _mats[key]
    hexv = OBJ.get(name) or TINT.get(name) or name
    m = bpy.data.materials.new(f'm_{name}')
    m.use_nodes = True
    p = next(n for n in m.node_tree.nodes if n.type == 'BSDF_PRINCIPLED')
    p.inputs['Base Color'].default_value = lin(hexv)
    p.inputs['Roughness'].default_value = rough
    if 'Specular IOR Level' in p.inputs:
        p.inputs['Specular IOR Level'].default_value = .35
    if 'Coat Weight' in p.inputs:
        p.inputs['Coat Weight'].default_value = coat
    if emit:
        p.inputs['Emission Color'].default_value = lin(hexv)
        p.inputs['Emission Strength'].default_value = emit
    if glass:
        p.inputs['Alpha'].default_value = .26
        p.inputs['Roughness'].default_value = .12
    _mats[key] = m
    return m


def M(x):
    return x if isinstance(x, bpy.types.Material) else mat(x)


def reset():
    bpy.ops.wm.read_factory_settings(use_empty=True)
    _mats.clear()


def _add(o, parent=None):
    bpy.context.scene.collection.objects.link(o)
    if parent:
        o.parent = parent
    return o


def empty(loc=(0, 0, 0), rot=(0, 0, 0), scale=1, parent=None):
    o = bpy.data.objects.new('e', None)
    o.location, o.rotation_euler = loc, [math.radians(a) for a in rot]
    o.scale = (scale,) * 3
    return _add(o, parent)


def _finish(o, loc, rot, parent):
    o.location = loc
    o.rotation_euler = [math.radians(a) for a in rot]
    return _add(o, parent)


def _from_bm(bm, name, material, smooth=True):
    me = bpy.data.meshes.new(name)
    bm.to_mesh(me)
    bm.free()
    for p in me.polygons:
        p.use_smooth = smooth
    me.materials.append(M(material))
    return bpy.data.objects.new(name, me)


def rbox(loc, size, r=.08, m='white', rot=(0, 0, 0), parent=None, seg=6):
    """Rounded box, centred on loc."""
    bm = bmesh.new()
    bmesh.ops.create_cube(bm, size=1)
    for v in bm.verts:
        v.co = Vector((v.co.x * size[0], v.co.y * size[1], v.co.z * size[2]))
    r = min(r, min(size) * .499)
    bmesh.ops.bevel(bm, geom=bm.edges[:], offset=r, offset_type='OFFSET', segments=seg, profile=.5, affect='EDGES')
    o = _from_bm(bm, 'rbox', m)
    w = o.modifiers.new('wn', 'WEIGHTED_NORMAL')
    w.keep_sharp = False
    return _finish(o, loc, rot, parent)


def lathe(profile, m='white', loc=(0, 0, 0), rot=(0, 0, 0), steps=96, parent=None):
    """Solid of revolution from [(radius, z), ...]."""
    bm = bmesh.new()
    rings = []
    for r, z in profile:
        if r < 1e-5:
            rings.append([bm.verts.new((0, 0, z))])
        else:
            rings.append([bm.verts.new((r * math.cos(2 * math.pi * i / steps), r * math.sin(2 * math.pi * i / steps), z)) for i in range(steps)])
    for a, b in zip(rings, rings[1:]):
        if len(a) == 1 and len(b) == 1:
            continue
        for i in range(steps):
            j = (i + 1) % steps
            if len(a) == 1:
                bm.faces.new((a[0], b[j], b[i]))
            elif len(b) == 1:
                bm.faces.new((a[i], a[j], b[0]))
            else:
                bm.faces.new((a[i], a[j], b[j], b[i]))
    bmesh.ops.recalc_face_normals(bm, faces=bm.faces[:])
    o = _from_bm(bm, 'lathe', m)
    return _finish(o, loc, rot, parent)


def arc(cx, cz, r, a0, a1, n=8):
    return [(cx + r * math.cos(math.radians(a0 + (a1 - a0) * i / n)), cz + r * math.sin(math.radians(a0 + (a1 - a0) * i / n))) for i in range(n + 1)]


def cyl(loc, r, h, m='white', bev=.06, rot=(0, 0, 0), parent=None, steps=128):
    """Rounded cylinder standing on loc."""
    bev = min(bev, r * .99, h * .499)
    prof = [(0, 0)] + arc(r - bev, bev, bev, -90, 0) + arc(r - bev, h - bev, bev, 0, 90) + [(0, h)]
    # extra loops near the caps keep the flat tops flat under smooth shading
    prof = [(0, 0), (max(r - bev - .01, .001), 0)] + prof[1:-1] + [(max(r - bev - .01, .001), h), (0, h)]
    return lathe(prof, m, loc, rot, steps, parent)


def ball(loc, r, m='white', parent=None, scale=(1, 1, 1)):
    bm = bmesh.new()
    bmesh.ops.create_uvsphere(bm, u_segments=64, v_segments=40, radius=r)
    o = _from_bm(bm, 'ball', m)
    o.scale = scale
    return _finish(o, loc, (0, 0, 0), parent)


def torus(loc, R, r, m='green', rot=(0, 0, 0), parent=None):
    bm = bmesh.new()
    U, V = 112, 32
    vs = [[bm.verts.new(((R + r * math.cos(2 * math.pi * v / V)) * math.cos(2 * math.pi * u / U),
                         (R + r * math.cos(2 * math.pi * v / V)) * math.sin(2 * math.pi * u / U),
                         r * math.sin(2 * math.pi * v / V))) for v in range(V)] for u in range(U)]
    for u in range(U):
        for v in range(V):
            bm.faces.new((vs[u][v], vs[(u + 1) % U][v], vs[(u + 1) % U][(v + 1) % V], vs[u][(v + 1) % V]))
    o = _from_bm(bm, 'torus', m)
    return _finish(o, loc, rot, parent)


def roundpoly(pts, r, n=8):
    """Round the corners of a closed polygon."""
    out = []
    k = len(pts)
    rs = r if isinstance(r, (list, tuple)) else [r] * k
    for i in range(k):
        p0, p1, p2 = Vector(pts[i - 1]), Vector(pts[i]), Vector(pts[(i + 1) % k])
        d1, d2 = (p0 - p1), (p2 - p1)
        rr = min(rs[i], d1.length * .49, d2.length * .49)
        if rr < 1e-4:
            out.append(tuple(p1))
            continue
        a, b = p1 + d1.normalized() * rr, p1 + d2.normalized() * rr
        for j in range(n + 1):
            t = j / n
            q = a * (1 - t) ** 2 + p1 * 2 * t * (1 - t) + b * t * t
            out.append((q.x, q.y))
    return out


def rrect(w, h, r, cx=0, cy=0):
    return roundpoly([(cx - w / 2, cy - h / 2), (cx + w / 2, cy - h / 2), (cx + w / 2, cy + h / 2), (cx - w / 2, cy + h / 2)], r)


def circ(r, cx=0, cy=0, n=96):
    return [(cx + r * math.cos(2 * math.pi * i / n), cy + r * math.sin(2 * math.pi * i / n)) for i in range(n)]


def extr(outer, depth, m='white', loc=(0, 0, 0), rot=(0, 0, 0), bev=.04, holes=(), parent=None):
    """Extruded 2D shape with bevelled edges, centred on its depth. Lies in XY; rot=(90,0,0) stands it up facing -Y."""
    cu = bpy.data.curves.new('x', 'CURVE')
    cu.dimensions = '2D'
    cu.fill_mode = 'BOTH'
    bev = min(bev, depth * .49)
    cu.extrude = depth / 2 - bev
    cu.bevel_depth = bev
    cu.bevel_resolution = 6
    cu.offset = -bev
    for poly in [outer, *holes]:
        sp = cu.splines.new('POLY')
        sp.points.add(len(poly) - 1)
        for p, (x, y) in zip(sp.points, poly):
            p.co = (x, y, 0, 1)
        sp.use_cyclic_u = True
        sp.use_smooth = True
    cu.materials.append(M(m))
    return _finish(bpy.data.objects.new('x', cu), loc, rot, parent)


def tube(pts, r=.06, m='green', parent=None, closed=False, smooth=True):
    cu = bpy.data.curves.new('t', 'CURVE')
    cu.dimensions = '3D'
    cu.bevel_depth = r
    cu.bevel_resolution = 8
    cu.resolution_u = 24
    cu.use_fill_caps = True
    if smooth:
        sp = cu.splines.new('BEZIER')
        sp.bezier_points.add(len(pts) - 1)
        for bp, p in zip(sp.bezier_points, pts):
            bp.co = p
            bp.handle_left_type = bp.handle_right_type = 'AUTO'
    else:
        sp = cu.splines.new('POLY')
        sp.points.add(len(pts) - 1)
        for bp, p in zip(sp.points, pts):
            bp.co = (*p, 1)
    sp.use_cyclic_u = closed
    cu.materials.append(M(m))
    o = _finish(bpy.data.objects.new('t', cu), (0, 0, 0), (0, 0, 0), parent)
    if not closed:
        ball(pts[0], r, m, parent)
        ball(pts[-1], r, m, parent)
    return o


# ---------------------------------------------------------------- props
def person(loc, col='mint', skin='skin1', h=1.0, parent=None, rot=0):
    """Peg person, about 1.12 * h tall."""
    e = empty(loc, (0, 0, rot), h, parent)
    b = .05
    prof = [(0, 0), (.235, 0)] + arc(.25, b, b, -90, 0, 6) + [(.292, .16), (.272, .34), (.236, .5)] + arc(.11, .555, .118, 12, 88, 10) + [(0, .675)]
    lathe(prof, col, parent=e)
    ball((0, 0, .85), .205, skin, e)
    return e


def sparkle_pts(R=1, k=.2, n=18, tip=.075):
    """Four-point star with rounded tips."""
    w = R * tip
    pts = []
    for q in range(4):
        th = math.pi / 2 * q
        rot = lambda x, y: (x * math.cos(th) - y * math.sin(th), x * math.sin(th) + y * math.cos(th))
        # round cap at the tip of this arm
        for i in range(9):
            a = -math.pi / 2 + math.pi * i / 8
            pts.append(rot(R - w + w * math.cos(a), w * math.sin(a)))
        # concave sweep to the next arm
        p0, p2 = Vector((R - w, w)), Vector((w, R - w))
        c = Vector((k * R * .9, k * R * .9))
        for i in range(1, n):
            t = i / n
            v = p0 * (1 - t) ** 2 + c * 2 * t * (1 - t) + p2 * t * t
            pts.append(rot(v.x, v.y))
    return pts


def sparkle(loc, R=.3, m='green', rot=(90, 0, 0), depth=None, parent=None):
    return extr(sparkle_pts(R, .2), depth or R * .42, m, loc, rot, bev=R * .055, parent=parent)


def gear(loc, R=.6, teeth=10, m='peri', depth=.28, rot=(90, 0, 0), hole=.36, parent=None):
    pts = []
    for i in range(teeth):
        a = 2 * math.pi * i / teeth
        w = 2 * math.pi / teeth
        for da, rr in ((-.30, .82), (-.19, 1), (.19, 1), (.30, .82)):
            pts.append((R * rr * math.cos(a + da * w), R * rr * math.sin(a + da * w)))
    return extr(roundpoly(pts, R * .06, 4), depth, m, loc, rot, bev=.035, holes=[circ(R * hole)], parent=parent)


def bubble(loc, w=1.1, h=.78, m='sky', rot=(90, 0, 0), depth=.22, dots='white', parent=None):
    e = empty(loc, rot, 1, parent)
    extr(rrect(w, h, .24), depth, m, bev=.06, parent=e)
    extr(roundpoly([(-w * .3, -h * .3), (-w * .02, -h * .42), (-w * .34, -h * .78)], .05), depth * .9, m, bev=.05, parent=e)
    if dots:
        for i in (-1, 0, 1):
            ball((i * w * .22, 0, depth * .5), .075, dots, e)
    return e


def browser(loc, w=2.2, h=1.6, m='white', bar='green', rot=(0, 0, 0), parent=None, kind='site', d=.2):
    """Standing window facing -Y, base at loc."""
    e = empty(loc, rot, 1, parent)
    y = -d / 2
    rbox((0, 0, h / 2), (w, d, h), .1, m, parent=e)
    rbox((0, y, h - .17), (w - .12, .05, .2), .025, bar, parent=e)
    for i in range(3):
        ball((-w / 2 + .2 + i * .14, y - .03, h - .17), .04, 'white', e)
    top = h - .36
    if kind == 'site':
        rbox((0, y, top - .26), (w - .28, .05, .44), .025, 'mint', parent=e)
        rbox((-w * .17, y - .03, top - .2), (w * .42, .05, .09), .025, 'white', parent=e)
        rbox((-w * .24, y - .03, top - .36), (w * .24, .05, .09), .025, 'green', parent=e)
        n = 3
        cw = (w - .28 - .12 * (n - 1)) / n
        for i, c in enumerate(('sky', 'peach', 'lilac')):
            rbox((-w / 2 + .14 + cw / 2 + i * (cw + .12), y, (top - .56) / 2 + .08), (cw, .05, top - .72), .025, c, parent=e)
    elif kind == 'sheet':
        cols, rows = 4, 4
        cw, rh = (w - .28) / cols, (top - .2) / rows
        for r in range(rows):
            for c in range(cols):
                col = 'mint' if r == 0 else ('soft' if (r + c) % 3 else 'lime')
                rbox((-w / 2 + .14 + cw * (c + .5), y, .12 + rh * (rows - r - .5)), (cw - .06, .05, rh - .06), .022, col, parent=e)
    elif kind == 'chart':
        hs = (.32, .5, .42, .68, .9)
        cw = (w - .4) / len(hs)
        for i, k in enumerate(hs):
            bh = (top - .22) * k
            rbox((-w / 2 + .2 + cw * (i + .5), y, .12 + bh / 2), (cw * .62, .06, bh), .028, ('sky', 'lilac', 'peach', 'aqua', 'green')[i], parent=e)
    elif kind == 'list':
        rows = 4
        rh = (top - .16) / rows
        for r in range(rows):
            z = .1 + rh * (rows - r - .5)
            ball((-w / 2 + .3, y - .01, z), .09, ('rose', 'sky', 'lime', 'lilac')[r], e)
            rbox((.16, y, z), (w - .86, .05, .1), .024, 'soft', parent=e)
    return e


def phone(loc, rot=(0, 0, 0), parent=None, m='ink', screen='mint'):
    e = empty(loc, rot, 1, parent)
    rbox((0, 0, .75), (.78, .12, 1.5), .06, m, parent=e)
    rbox((0, -.055, .75), (.66, .03, 1.34), .014, screen, parent=e)
    rbox((-.08, -.08, 1.12), (.36, .03, .16), .014, 'white', parent=e)
    rbox((.08, -.08, .86), (.36, .03, .16), .014, 'green', parent=e)
    rbox((-.08, -.08, .6), (.36, .03, .16), .014, 'white', parent=e)
    return e


def stack_db(loc, r=.5, n=3, cols=('sky', 'lilac', 'peri'), parent=None, gap=.06, h=.3):
    for i in range(n):
        cyl((loc[0], loc[1], loc[2] + i * (h + gap)), r, h, cols[i % len(cols)], .09, parent=parent)


def magnet(loc, R=2.0, r=1.15, leg=1.0, depth=.9, rot=(90, 0, 0), m='green', tip='white', scale=1, tiplen=.62, gap=.1, parent=None, bev=.12):
    """Horseshoe as an arch, tips down, base of the tips at loc."""
    e = empty(loc, (0, 0, 0), scale, parent)
    g = empty((0, 0, 0), rot, 1, e)
    z0 = tiplen + gap
    zc = z0 + leg
    out = [(-R, z0), (-r, z0)] + [(r * math.cos(math.radians(180 - 180 * i / 48)), zc + r * math.sin(math.radians(180 - 180 * i / 48))) for i in range(49)] + \
          [(r, z0), (R, z0)] + [(R * math.cos(math.radians(180 * i / 64)), zc + R * math.sin(math.radians(180 * i / 64))) for i in range(65)]
    extr(out, depth, m, bev=bev, parent=g)
    for s in (-1, 1):
        extr(rrect(R - r, tiplen, .12, s * (R + r) / 2, tiplen / 2), depth, tip, bev=bev, parent=g)
    return e


def tree(loc, s=1.0, col='mint', parent=None):
    e = empty(loc, (0, 0, 0), s, parent)
    cyl((0, 0, 0), .06, .4, 'peach', .02, parent=e)
    lathe([(0, .25)] + arc(.18, .42, .17, -80, 0, 8) + [(.3, .7), (.16, 1.05)] + arc(.04, 1.08, .1, 0, 90, 6) + [(0, 1.18)], col, parent=e)
    return e


# ---------------------------------------------------------------- stage, camera, render
def stage(tint='mint', floor=True, **_):
    """Invisible floor that only catches shadows; the page supplies the colour."""
    sc = bpy.context.scene
    w = bpy.data.worlds.new('w')
    sc.world = w
    w.use_nodes = True
    bg = next(n for n in w.node_tree.nodes if n.type == 'BACKGROUND')
    c = lin(TINT.get(tint, 'FFFFFF'))
    bg.inputs[0].default_value = tuple(.55 + .45 * v for v in c[:3]) + (1,)
    bg.inputs[1].default_value = .72
    if floor:
        bm = bmesh.new()
        bmesh.ops.create_grid(bm, x_segments=1, y_segments=1, size=90)
        o = _from_bm(bm, 'floor', mat(TINT.get(tint, 'FFFFFF'), rough=1))
        o.is_shadow_catcher = True
        _finish(o, (0, 0, 0), (0, 0, 0), None)
    return w


def area(loc, target, size, energy, color=(1, 1, 1)):
    l = bpy.data.lights.new('a', 'AREA')
    l.shape = 'DISK'
    l.size, l.energy, l.color = size, energy, color
    o = bpy.data.objects.new('a', l)
    o.location = loc
    o.rotation_euler = (Vector(target) - Vector(loc)).to_track_quat('-Z', 'Y').to_euler()
    return _add(o)


def lights(scale=1.0, target=(0, 0, 1), key=1.0):
    k = scale
    area((-6 * k, -6 * k, 10 * k), target, 5 * k, 1500 * k * k * key, (1, .985, .96))
    area((8 * k, -5 * k, 6 * k), target, 8 * k, 420 * k * k, (.95, .97, 1))
    area((2 * k, 7 * k, 9 * k), target, 6 * k, 500 * k * k)


def camera(loc, look, lens=60, dof=None, fstop=2.8, shift=(0, 0)):
    c = bpy.data.cameras.new('c')
    c.lens = lens
    c.shift_x, c.shift_y = shift
    o = bpy.data.objects.new('c', c)
    o.location = loc
    o.rotation_euler = (Vector(look) - Vector(loc)).to_track_quat('-Z', 'Y').to_euler()
    if dof:
        c.dof.use_dof = True
        c.dof.focus_distance = dof
        c.dof.aperture_fstop = fstop
    _add(o)
    bpy.context.scene.camera = o
    return o


def render(out, w, h, samples=96, transparent=False, tint=None, probe=(.02, .98)):
    s = bpy.context.scene
    s.render.engine = 'CYCLES'
    s.cycles.device = 'CPU'
    s.cycles.use_denoising = True
    try:
        s.cycles.denoiser = 'OPENIMAGEDENOISE'
    except Exception:
        pass
    s.cycles.use_adaptive_sampling = True
    s.cycles.adaptive_threshold = .03
    s.cycles.max_bounces = 6
    s.cycles.diffuse_bounces = 4
    s.cycles.caustics_reflective = s.cycles.caustics_refractive = False
    s.render.film_transparent = transparent
    s.render.image_settings.file_format = 'PNG'
    s.render.image_settings.color_mode = 'RGBA' if transparent else 'RGB'
    try:
        s.view_settings.view_transform = 'Khronos PBR Neutral'
    except Exception:
        s.view_settings.view_transform = 'Standard'
    s.view_settings.look = 'None'
    s.render.resolution_percentage = 100

    def go(path, ww, hh, smp):
        s.render.resolution_x, s.render.resolution_y = ww, hh
        s.cycles.samples = smp
        s.render.filepath = path
        bpy.ops.render.render(write_still=True)

    s.view_settings.exposure = EXPO
    s.render.film_transparent = True
    s.render.image_settings.color_mode = 'RGBA'
    s.cycles.samples = samples
    go(out, w, h, samples)


def finish(png, tint):
    """Pure shadow pixels come back black; give them the stage hue so they never look dirty on a pastel."""
    from PIL import Image
    import numpy as np
    im = np.asarray(Image.open(png).convert('RGBA')).astype('float32')
    hexv = TINT.get(tint, 'DFE6E3')
    t = np.array([int(hexv[i:i + 2], 16) for i in (0, 2, 4)], 'float32')
    deep = t * .34 * np.array([.96, 1.0, 1.0], 'float32')
    a = im[..., 3:4] / 255
    partial = np.clip((1 - a) * 6, 0, 1)
    k = (1 - im[..., :3].max(-1, keepdims=True) / 255) ** 2 * partial
    im[..., :3] = im[..., :3] * (1 - k) + (im[..., :3] + deep) * k
    im[..., 3:4] = np.where(a < .985, a * .92, a) * 255
    im[..., 3] = np.where(im[..., 3] < 2.2, 0, im[..., 3])
    Image.fromarray(im.clip(0, 255).astype('uint8'), 'RGBA').save(png)
