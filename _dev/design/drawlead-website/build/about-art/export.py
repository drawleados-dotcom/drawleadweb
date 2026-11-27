"""Convert rendered PNGs (final if present, else draft) to the WebP files the page uses."""
import os
import numpy as np
from PIL import Image
SRC = os.path.dirname(os.path.abspath(__file__))
OUT = os.path.join(SRC, '..', '..', '..', 'assets', 'about')
M = {'hero': ('hero', (2400, 1128)), 'ai_before': ('ai-before', (1500, 1200)), 'ai_after': ('ai-after', (1500, 1200)),
     'story1': ('story-1', (1200, 1200)), 'story2': ('story-2', (1200, 1200)), 'story3': ('story-3', (1200, 1200)), 'story4': ('story-4', (1200, 1200)),
     'fit': ('fit', (1800, 1000)), 'cap_systems': ('cap-systems', (1400, 1100)), 'cap_commerce': ('cap-commerce', (1400, 1100)),
     'cap_ai': ('cap-ai', (1400, 1100)), 'cap_growth': ('cap-growth', (1400, 1100)), 'vision': ('vision', (2400, 1272)),
     'cul_curiosity': ('cul-curiosity', (720, 720)), 'cul_beyond': ('cul-beyond', (720, 720)), 'cul_ownership': ('cul-ownership', (720, 720)),
     'cul_collab': ('cul-collab', (720, 720)), 'cul_experiment': ('cul-experiment', (720, 720)), 'cul_purpose': ('cul-purpose', (720, 720))}


def fade(im):
    """Shadows die away before the frame does; solid objects are left alone."""
    a = np.asarray(im).astype('float32')
    h, w = a.shape[:2]
    m = .11 * min(w, h)
    x = np.minimum(np.arange(w), np.arange(w)[::-1])[None, :] / m
    y = np.minimum(np.arange(h), np.arange(h)[::-1])[:, None] / m
    r = np.clip(x, 0, 1) * np.clip(y, 0, 1)
    r = r * r * (3 - 2 * r)
    al = a[..., 3]
    a[..., 3] = np.where(al < 250, al * r, al)
    return Image.fromarray(a.clip(0, 255).astype('uint8'), 'RGBA')


tot = 0
for k, (n, size) in M.items():
    f = os.path.join(SRC, 'final', k + '.png')
    if not os.path.isfile(f):
        continue
    im = Image.open(f).convert('RGBA')
    if k != 'vision':
        im = fade(im)
    if im.size != size:
        im = im.resize(size, Image.LANCZOS)
    o = os.path.join(OUT, n + '.webp')
    im.save(o, quality=86, alpha_quality=88, method=6)
    kb = os.path.getsize(o) // 1024
    tot += kb
    print(f'{n:16s}{kb:5d} KB')
print('total', tot, 'KB')
