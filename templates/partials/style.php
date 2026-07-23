*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
:root{
 --bg:#f0f0ee;
 --bg2:#e8e8e6;
 --white:#ffffff;
 --black:#111112;
 --g200:#d8d8d4;
 --g300:#c0c0bc;
 --g400:#949490;
 --g500:#70706c;
 --g600:#4a4a48;
 --border:#dcdcda;
 --blue:#32b46f;
 --violet:#14855a;
 --grad:linear-gradient(110deg,#32b46f 0%,#14855a 100%);
 --teal:#32b46f;
 --rose:#e11d48;
 --amber:#23a065;
 --green:#32b46f;
 --orange:#14855a;
 --font:'Montserrat',sans-serif;
}
body{font-family:var(--font);background:var(--bg);color:var(--black);overflow-x:hidden;-webkit-font-smoothing:antialiased}

/* ── GRID TEXTURE ── */
.grid-bg{
 position:absolute;inset:0;pointer-events:none;
 background-image:linear-gradient(rgba(0,0,0,0.055) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,0.055) 1px,transparent 1px);
 background-size:68px 68px;
}

/* ── NAV ── */
nav{
 position:fixed;top:0;left:0;right:0;z-index:200;
 display:flex;align-items:center;justify-content:space-between;
 padding:1rem 3.5rem;
 background:rgba(240,240,238,0.9);
 backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);
 border-bottom:1px solid var(--border);
}
.logo{display:flex;align-items:center;line-height:1;text-decoration:none;flex-shrink:0}
.logo img{height:44px;width:auto;display:block}
.logo:hover{opacity:.82;transition:opacity .2s}
.nav-links{display:flex;gap:1.6rem;list-style:none;align-items:center}
@media(max-width:1240px){.nav-links{gap:1.1rem}.nav-links a{font-size:11.5px}}
.nav-links a{font-size:12.5px;color:var(--g500);text-decoration:none;font-weight:600;letter-spacing:.01em;transition:color .15s}
.nav-links a:hover{color:var(--black)}
.nav-btn{border-radius:6px;
 background:var(--black);color:#fff;border:none;
 padding:9px 22px;
 font-family:var(--font);font-size:11.5px;font-weight:700;
 letter-spacing:.07em;text-transform:uppercase;cursor:pointer;
 text-decoration:none;transition:opacity .2s;
}
.nav-btn:hover{opacity:.82}

/* ── BUTTONS ── */
.btn{
 display:inline-flex;align-items:center;gap:8px;
 font-family:var(--font);font-weight:700;font-size:12px;
 letter-spacing:.07em;text-transform:uppercase;
 text-decoration:none;cursor:pointer;border:none;
 transition:all .2s;border-radius:8px;
}
.btn-black{background:var(--black);color:#fff;padding:15px 30px;border-radius:6px}
.btn-black:hover{opacity:.84;transform:translateY(-1px);box-shadow:0 8px 24px rgba(0,0,0,.2)}
.btn-ghost{background:var(--white);color:var(--black);padding:14px 29px;border:1.5px solid var(--border);border-radius:6px}
.btn-ghost:hover{border-color:var(--g400);transform:translateY(-1px)}
.btn-sm{font-size:10.5px;padding:10px 18px}
.btn-outline2{background:transparent;color:var(--black);padding:14px 29px;border:1.5px solid var(--border);border-radius:6px}
.btn-outline2:hover{border-color:var(--black);transform:translateY(-1px)}

/* ── SECTION BASE ── */
section{padding:7rem 3.5rem;border-bottom:1px solid var(--border);position:relative}
.eyebrow{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:1.1rem}
.eyebrow-line{width:32px;height:1.5px;background:var(--blue)}
.eyebrow-text{font-size:10.5px;text-transform:uppercase;letter-spacing:.15em;color:var(--blue);font-weight:700}
.sec-h{
 font-size:clamp(38px,5.5vw,62px);font-weight:800;
 letter-spacing:-.025em;line-height:1.04;
 text-align:center;margin-bottom:.85rem;
}
.sec-h .g{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.sec-h .fade{color:var(--g300)}
.sec-sub{
 font-size:15px;color:var(--g500);text-align:center;
 max-width:490px;margin:0 auto 3.5rem;line-height:1.65;font-weight:400;
}
.sec-cta{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-top:3rem}

/* ── HERO ── */
#hero{
 min-height:100vh;display:flex;flex-direction:column;
 align-items:center;justify-content:center;text-align:center;
 padding:7rem 3.5rem 4rem;overflow:hidden;border-bottom:1px solid var(--border);
}
/* Industry tabs */
.ind-tabs{
 display:flex;gap:0;justify-content:center;margin-bottom:0;
 border:1.5px solid var(--border);background:var(--white);
 overflow:hidden;width:fit-content;margin-left:auto;margin-right:auto;
}
.ind-tab{
 font-family:var(--font);font-size:12px;font-weight:700;
 letter-spacing:.05em;text-transform:uppercase;
 padding:13px 28px;border:none;border-right:1.5px solid var(--border);
 background:transparent;color:var(--g400);cursor:pointer;
 transition:all .22s ease;position:relative;
 display:flex;flex-direction:column;align-items:center;gap:5px;
 min-width:130px;
}
.ind-tab:last-child{border-right:none}
.ind-tab .tab-label{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;transition:color .22s}
.ind-tab .tab-sub{font-size:9px;font-weight:500;color:var(--g300);letter-spacing:.04em;transition:color .22s;white-space:nowrap}
.ind-tab .tab-bar{
 position:absolute;bottom:0;left:0;right:0;height:3px;
 background:transparent;transition:background .22s;
}
.ind-tab:hover:not(.active){background:var(--bg);color:var(--black)}
.ind-tab:hover:not(.active) .tab-label{color:var(--black)}
.ind-tab.active{background:var(--black);color:#fff}
.ind-tab.active .tab-label{color:#fff}
.ind-tab.active .tab-sub{color:rgba(255,255,255,.5)}
/* screens fade transition */
.screens-row{transition:opacity .25s ease}
.screens-row.switching{opacity:0}
.screens-row{transition:opacity .3s ease;border:none;outline:none;background:transparent}

/* Product frame */
.hero-product-wrap{width:100%;max-width:1100px;margin-top:10rem;position:relative;animation:fu .7s ease .5s both}
.hero-product-frame{background:var(--white);border:1.5px solid var(--border);border-radius:14px;overflow:hidden;box-shadow:0 32px 100px rgba(0,0,0,.13)}
.hpf-bar{display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--bg2);border-bottom:1px solid var(--border)}
.hpf-url{display:flex;align-items:center;gap:5px;flex:1;background:var(--white);border:1px solid var(--border);border-radius:4px;padding:4px 10px;font-size:10px;color:var(--g400);font-weight:500;max-width:260px}
.hpf-body{display:flex;height:340px}
.hpf-sidebar{width:140px;flex-shrink:0;background:var(--black);padding:16px 12px;display:flex;flex-direction:column;gap:0}
.hpf-logo-sm{font-size:14px;font-weight:900;letter-spacing:.04em;margin-bottom:20px;background:linear-gradient(115deg,#32b46f,#14855a);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hpf-nav-items{display:flex;flex-direction:column;gap:3px}
.hpf-nav{display:flex;align-items:center;gap:7px;font-size:10px;color:rgba(255,255,255,.45);padding:7px 8px;border-radius:8px;font-weight:600;cursor:pointer;transition:all .15s;letter-spacing:.02em}
.hpf-nav.active{background:rgba(50,180,111,.25);color:#fff;border-left:2px solid var(--blue)}
.hpf-nav:hover:not(.active){background:rgba(255,255,255,.06);color:rgba(255,255,255,.7)}
.hpf-main{flex:1;padding:16px;background:var(--bg);overflow:hidden;display:flex;flex-direction:column;gap:12px}
.hpf-kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
.hpf-kpi{background:var(--white);border:1px solid var(--border);border-radius:6px;padding:10px 12px}
.hpf-kv{font-size:18px;font-weight:800;letter-spacing:-.02em;line-height:1;margin-bottom:3px}
.hpf-kl{font-size:8.5px;color:var(--g400);text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-bottom:5px}
.hpf-ktag{font-size:8px;font-weight:700;padding:2px 7px;display:inline-block}
.hpf-charts-row{display:flex;gap:10px;flex:1;min-height:0}
.hpf-chart-box{background:var(--white);border:1px solid var(--border);border-radius:6px;padding:11px;display:flex;flex-direction:column;gap:8px;overflow:hidden}
.hpf-chart-head{display:flex;justify-content:space-between;align-items:center}
.hpf-chart-title{font-size:10px;font-weight:800;letter-spacing:-.01em}
.hpf-chart-sub{font-size:8.5px;color:var(--g400);font-weight:500}
/* Floating badges */
.hero-float{position:absolute;background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:12px 16px;box-shadow:0 8px 28px rgba(0,0,0,.08);z-index:4}
.hero-float-l{bottom:60px;left:-16px}
.hero-float-r{top:60px;right:-16px}
.hero-glow-r{position:absolute;top:-80px;right:-80px;width:540px;height:540px;background:radial-gradient(circle,rgba(50,180,111,.12) 0%,transparent 65%);pointer-events:none}
.hero-glow-l{position:absolute;bottom:-60px;left:-60px;width:420px;height:420px;background:radial-gradient(circle,rgba(50,180,111,.08) 0%,transparent 65%);pointer-events:none}
.hero-eyebrow{display:flex;align-items:center;gap:14px;margin-bottom:2.25rem;animation:fu .7s ease both}
.hero-eline{width:36px;height:1.5px;background:var(--blue)}
.hero-etxt{font-size:10.5px;text-transform:uppercase;letter-spacing:.17em;color:#32b46f;font-weight:700}
/* BIG DISPLAY TYPE */
.hero-h{
 font-size:clamp(52px,8vw,110px);
 font-weight:900;line-height:.92;letter-spacing:-.04em;
 margin-bottom:1.75rem;animation:fu .7s ease .1s both;
}
.hero-h .grad{background:linear-gradient(115deg,#32b46f 0%,#14855a 55%,#0f7a52 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-h .ghost{color:var(--g300);display:block}
.grad-os{background:linear-gradient(115deg,#32b46f,#3cbd7a);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-style:normal;display:inline-block;padding-right:4px}
.grad-ai{background:linear-gradient(115deg,#14855a,#14855a);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-style:normal;display:inline-block;padding-right:4px}
.hero-h .solid{color:var(--black);display:block}
.hero-p{font-size:16.5px;color:var(--g500);max-width:500px;line-height:1.65;font-weight:400;margin-bottom:2.5rem;animation:fu .7s ease .2s both}
.hero-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;animation:fu .7s ease .3s both;margin-bottom:0}

.hero-stats{
 display:flex;width:100%;max-width:700px;
 border-top:1px solid var(--border);padding-top:2.5rem;margin-top:4rem;
 animation:fu .7s ease .5s both;
}
.hstat{flex:1;text-align:center;padding:0 1.5rem;border-right:1px solid var(--border)}
.hstat:last-child{border-right:none}
.hstat-n{font-size:56px;font-weight:800;letter-spacing:-.03em;line-height:1}
.hstat-n.gr{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hstat-l{font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:var(--g400);margin-top:8px;font-weight:600}

/* ── HERO SCREENS ── */
.hero-screens{
 width:100%;max-width:1060px;margin-top:3.5rem;
 animation:fu .7s ease .55s both;position:relative;
}
.screens-row{display:flex;align-items:center;justify-content:center;gap:0}
.screen{background:var(--white);border:1.5px solid var(--border);overflow:hidden;box-shadow:0 16px 56px rgba(0,0,0,.09);border-radius:10px;}
.screen-c{width:420px;z-index:3;box-shadow:0 24px 80px rgba(0,0,0,.13)}
.screen-s{width:276px}
.screen-l{transform:perspective(960px) rotateY(15deg) translateX(24px) scale(.87);z-index:2;opacity:.85}
.screen-r{transform:perspective(960px) rotateY(-15deg) translateX(-24px) scale(.87);z-index:2;opacity:.85}
/* screen internals */
.s-bar{display:flex;align-items:center;gap:5px;padding:8px 11px;background:var(--bg);border-bottom:1px solid var(--border)}
.s-dot{width:9px;height:9px;border-radius:50%}
.s-title{margin-left:auto;font-size:9px;font-weight:700;letter-spacing:.06em;color:var(--g400);text-transform:uppercase}
.s-body{padding:13px}
.s-mod{font-size:9.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:9px}
.s-krow{display:flex;gap:5px;margin-bottom:11px}
.s-k{flex:1;background:var(--bg);border-radius:5px;padding:7px 6px;border:1px solid var(--border)}
.s-kv{font-size:12px;font-weight:800;line-height:1;margin-bottom:2px}
.s-kl{font-size:7.5px;color:var(--g400);text-transform:uppercase;letter-spacing:.05em;font-weight:600}
.s-bars{display:flex;align-items:flex-end;gap:3px;height:44px;margin-bottom:9px}
.s-bar{flex:1;background:var(--border);border-radius:8px; 2px 0 0}
.s-divider{height:1px;background:var(--border);margin:9px 0}
.s-list{display:flex;flex-direction:column;gap:5px}
.s-li{display:flex;align-items:center;gap:6px;font-size:9.5px;color:var(--g500)}
.s-d{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.s-badge{margin-left:auto;font-size:7.5px;padding:2px 5px;font-weight:700}
.s-hbars{display:flex;flex-direction:column;gap:7px}
.s-hrow{display:flex;align-items:center;gap:6px;font-size:8.5px;color:var(--g500)}
.s-hrow span:first-child{width:50px;font-weight:600}
.s-track{flex:1;height:3.5px;background:var(--border);border-radius:8px;overflow:hidden}
.s-fill{height:100%;border-radius:8px;}

/* ── MARQUEE ── */
.mq-wrap{overflow:hidden;border-bottom:1px solid var(--border);background:var(--white);padding:0}
.mq-track{display:flex;width:max-content;animation:marquee 26s linear infinite}
.mq-item{display:flex;align-items:center;gap:1rem;padding:.95rem 2.5rem;border-right:1px solid var(--border);white-space:nowrap}
.mq-icon{width:14px;height:14px;color:var(--blue);flex-shrink:0}
.mq-item span{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--g500)}

/* ── FUNCTIONS ── */
#functions{background:var(--bg)}
.fn-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);border:1.5px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:0}
.fn-card{background:var(--white);padding:1.85rem;display:flex;flex-direction:column;position:relative;overflow:hidden;cursor:default;transition:background .2sborder-radius:8px;}
.fn-card:hover{background:#fafafa}
.fn-card:hover .fn-arrow{opacity:1;transform:translateX(0)}
.fn-top-bar{height:3px;margin:-1.85rem -1.85rem 1.4rem;border-radius:8px; 0 0}
.fn-icon{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:.9rem;flex-shrink:0;box-shadow:0 6px 20px rgba(0,0,0,.15)}
.fn-icon svg{width:26px;height:26px}
.fn-num{font-size:9.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.3rem}
.fn-name{font-size:15px;font-weight:800;letter-spacing:-.01em;margin-bottom:.45rem}
.fn-desc{font-size:11.5px;color:var(--g500);line-height:1.55;flex:1;font-weight:400;margin-bottom:1.1rem}
.fn-tags{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:1.1rem}
.fn-tag{font-size:8.5px;border:1px solid var(--border);border-radius:8px;padding:2.5px 6px;color:var(--g400);background:var(--bg);text-transform:uppercase;letter-spacing:.06em;font-weight:600}
.fn-arrow{opacity:0;transform:translateX(-4px);transition:all .2s;font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;text-decoration:none;display:flex;align-items:center;gap:5px;font-family:var(--font);border:none;background:none;cursor:pointer;padding:0}

/* ── SOLUTIONS (2-COL) ── */
.sol-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--border);border:1.5px solid var(--border);border-radius:12px;overflow:hidden}
.sol-card{background:var(--white);padding:2.3rem 1.9rem;display:flex;flex-direction:column;position:relative;overflow:hidden;transition:background .2s}
.sol-card:hover{background:#fafafa}
.sol-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:1.15rem;flex-shrink:0;background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 20px rgba(50,180,111,.3)}
.sol-label{font-size:9.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--blue);margin-bottom:.45rem}
.sol-name{font-size:22px;font-weight:800;letter-spacing:-.02em;line-height:1.1;margin-bottom:.5rem}
.sol-tag{font-size:13.5px;font-weight:700;color:var(--g600);line-height:1.4;margin-bottom:.8rem}
.sol-desc{font-size:12.5px;color:var(--g500);line-height:1.65;font-weight:400;margin-bottom:1.5rem}
.sol-list{list-style:none;display:flex;flex-direction:column;gap:.65rem;margin-bottom:1.6rem;flex:1}
.sol-list li{display:flex;align-items:flex-start;gap:9px;font-size:12px;color:var(--g600);font-weight:500;line-height:1.5}
.sol-check{width:16px;height:16px;border-radius:50%;background:rgba(50,180,111,.14);flex-shrink:0;display:flex;align-items:center;justify-content:center;margin-top:1px}
.sol-metrics{display:flex;gap:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);margin-bottom:1.5rem}
.sol-metric-v{font-size:23px;font-weight:800;letter-spacing:-.02em;color:var(--blue);line-height:1}
.sol-metric-l{font-size:9px;text-transform:uppercase;letter-spacing:.08em;color:var(--g400);font-weight:600;margin-top:5px}
.sol-arrow{font-size:10.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;text-decoration:none;color:var(--blue);display:inline-flex;align-items:center;gap:6px;transition:gap .2s}
.sol-card:hover .sol-arrow{gap:11px}

/* ── SOLUTION CARD TABS ── */
.sol-tabs{display:flex;gap:0;border:1.5px solid var(--border);border-radius:8px;overflow:hidden;background:var(--white);margin-bottom:1.4rem}
.sol-tab{
 flex:1;font-family:var(--font);font-size:10px;font-weight:800;
 letter-spacing:.06em;text-transform:uppercase;
 padding:11px 10px;border:none;border-right:1.5px solid var(--border);
 background:transparent;color:var(--g400);cursor:pointer;
 transition:background .22s,color .22s;line-height:1.35;text-align:center;
}
.sol-tab:last-child{border-right:none}
.sol-tab:hover:not(.active){background:rgba(50,180,111,.07);color:var(--black)}
.sol-tab.active{background:var(--grad);color:#fff}
.sol-panel{display:none;flex-direction:column;flex:1}
.sol-panel.active{display:flex;animation:solFade .3s ease}
@keyframes solFade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:560px){.sol-tab{font-size:9px;padding:10px 6px}}
@media(max-width:1024px){.sol-grid{grid-template-columns:1fr}}
@media(max-width:960px){.sol-card{padding:2.1rem 1.6rem}}
.fn-cta-card{background:var(--black) !important;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:1.25rem;padding:2.5rem 2rem}
.fn-cta-card:hover{background:#1a1a1c !important}


/* ── INDUSTRIES VISUAL GRID ── */
.ind-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.ind-card{background:var(--white);border-radius:10px;border:1.5px solid var(--border);overflow:hidden;display:flex;flex-direction:column;transition:all .2s;opacity:0;transform:translateY(50px)}
.ind-card.in{opacity:1;transform:translateY(0)}
.ind-card:hover{box-shadow:0 6px 28px rgba(0,0,0,.1);border-color:var(--g300)}
.ind-card-top{
  padding:2rem 1.75rem 1.75rem;
  position:relative;
  min-height:140px;
  display:flex;flex-direction:column;justify-content:flex-end;
  background:var(--bg) !important;
  background-image:
    linear-gradient(rgba(50,180,111,0.07) 1px, transparent 1px),
    linear-gradient(90deg, rgba(50,180,111,0.07) 1px, transparent 1px) !important;
  background-size:32px 32px !important;
  border-bottom:1px solid var(--border);
}
.ind-emoji{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:.75rem;box-shadow:0 4px 16px rgba(50,180,111,.3)}
.ind-card-title{font-size:16px;font-weight:800;color:var(--black);letter-spacing:-.01em;margin-bottom:.3rem}
.ind-card-tag{font-size:10px;color:var(--g400);font-weight:600;text-transform:uppercase;letter-spacing:.08em}
.ind-card-body{padding:1.5rem 1.75rem;flex:1;display:flex;flex-direction:column;gap:1rem}
.ind-problems,.ind-solutions{display:flex;flex-direction:column;gap:6px}
.ind-prob-label,.ind-sol-label{font-size:9px;text-transform:uppercase;letter-spacing:.1em;font-weight:700;margin-bottom:3px}
.ind-prob-label{color:#14855a}
.ind-sol-label{color:#32b46f}
.ind-prob,.ind-sol{display:flex;align-items:flex-start;gap:7px;font-size:11.5px;line-height:1.45;font-weight:400}
.ind-prob{color:var(--g600)}
.ind-sol{color:var(--g600)}
.ind-cta{margin-top:auto;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;text-decoration:none;color:var(--blue);display:flex;align-items:center;gap:5px;padding-top:.75rem;transition:gap .2s}
.ind-cta:hover{gap:9px}
@media(max-width:960px){.ind-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){.ind-grid{grid-template-columns:1fr}}


.ind-card.d2{transition-delay:.1s}
.ind-card.d3{transition-delay:.2s}
.ind-card{transition:opacity .65s ease, transform .65s ease, box-shadow .2s, transform .2s;}
/* ── TECH ── */
.tech-icon-new{
  width:56px;height:56px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;
  margin-bottom:.25rem;flex-shrink:0;
  box-shadow:0 8px 24px rgba(0,0,0,.3);
}
#tech{background:var(--white)}
.tech-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;background:transparent;border:none;border-radius:0;overflow:visible}
.tech-card{background:#12201a;padding:2.25rem 2rem;display:flex;flex-direction:column;gap:.85rem;transition:all .2s;border-radius:8px;border:1px solid rgba(255,255,255,.08);}
.tech-card:hover{background:#1a2e24;border-color:rgba(255,255,255,.18);transform:translateY(-2px)}
.tech-icon{width:50px;height:50px;border-radius:8px;display:flex;align-items:center;justify-content:center}
.tech-icon svg{width:24px;height:24px}
.tech-name{font-size:17px;font-weight:800;letter-spacing:-.015em;color:#fff}
.tech-desc{font-size:12.5px;color:rgba(255,255,255,.45);line-height:1.6;font-weight:400}
.tech-tags{display:flex;flex-wrap:wrap;gap:4px}
.t-tag{font-size:8.5px;border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:2.5px 6px;color:rgba(255,255,255,.4);background:rgba(255,255,255,.05);text-transform:uppercase;letter-spacing:.06em;font-weight:600}

/* ── CASES ── */
#cases{background:var(--bg)}
.cases-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.case-card{background:var(--white);border-radius:10px;border:1.5px solid var(--border);overflow:hidden;display:flex;flex-direction:column;transition:all .2s}
.case-screen{background:var(--bg2);border-bottom:1.5px solid var(--border);padding:0;overflow:hidden;position:relative;height:180px}
.cs-bar{display:flex;align-items:center;gap:4px;padding:7px 10px;background:var(--bg);border-bottom:1px solid var(--border)}
.cs-dot{width:7px;height:7px;border-radius:50%}
.cs-title{margin-left:auto;font-size:8px;font-weight:700;letter-spacing:.06em;color:var(--g400);text-transform:uppercase}
.cs-body{padding:10px 12px;display:flex;gap:8px;height:calc(180px - 30px)}
.cs-sidebar{width:60px;flex-shrink:0;display:flex;flex-direction:column;gap:4px}
.cs-nav-item{height:8px;background:var(--border);border-radius:2px;width:100%}
.cs-nav-item.active{background:#32b46f;width:80%}
.cs-main{flex:1;display:flex;flex-direction:column;gap:6px;overflow:hidden}
.cs-krow{display:flex;gap:4px}
.cs-k{flex:1;background:var(--white);border:1px solid var(--border);border-radius:4px;padding:5px;display:flex;flex-direction:column;gap:2px}
.cs-kv{font-size:9px;font-weight:800;color:#32b46f;line-height:1}
.cs-kl{font-size:6.5px;color:var(--g400);text-transform:uppercase;letter-spacing:.04em;font-weight:600}
.cs-chart{display:flex;flex-direction:column;gap:3px;flex:1}
.cs-lbl{font-size:6.5px;color:var(--g400);text-transform:uppercase;letter-spacing:.06em;font-weight:700}
.cs-bars-row{display:flex;align-items:flex-end;gap:2px;height:36px}
.cs-bar-i{flex:1;background:rgba(50,180,111,.15);border-radius:1px 1px 0 0}
.cs-bar-hi{background:#32b46f !important}
.cs-hbar-r{display:flex;align-items:center;gap:4px;font-size:7px;color:var(--g500)}
.cs-hbar-r span:first-child{width:38px;font-weight:600;font-size:6.5px}
.cs-track2{flex:1;height:3px;background:var(--border);border-radius:2px;overflow:hidden}
.cs-fill2{height:100%;background:#32b46f;border-radius:2px}
.cs-list-r{display:flex;flex-direction:column;gap:3px}
.cs-list-item{display:flex;align-items:center;gap:4px;font-size:7.5px;color:var(--g500)}
.cs-dot2{width:5px;height:5px;border-radius:50%;flex-shrink:0;background:#32b46f}
.cs-badge-s{margin-left:auto;font-size:6px;padding:1.5px 4px;border-radius:2px;font-weight:700;background:rgba(50,180,111,.1);color:#32b46f}
.cs-funnel{display:flex;flex-direction:column;gap:2px}
.cs-fbar2{border-radius:2px;height:14px;display:flex;align-items:center;padding:0 6px;font-size:7px;font-weight:600;color:#32b46f}
.case-card:hover{box-shadow:0 6px 28px rgba(0,0,0,.1);border-color:var(--g300)}
.case-top-bar{display:none}
.case-body{padding:1.85rem;flex:1;display:flex;flex-direction:column;gap:.9rem}
.case-tag{font-size:9.5px;text-transform:uppercase;letter-spacing:.1em;font-weight:700;padding:3.5px 9px;border-radius:8px;display:inline-flex;align-self:flex-start}
.case-title{font-size:19px;font-weight:800;letter-spacing:-.01em;line-height:1.2}
.case-list{list-style:none;flex:1;display:flex;flex-direction:column;gap:0}
.case-list li{font-size:12.5px;color:var(--g500);padding:7px 0;display:flex;align-items:center;gap:8px;font-weight:400}
.case-list li:first-child{border-top:none}
.case-list li::before{content:'→';font-weight:700;font-size:10px;flex-shrink:0}

/* ── INDUSTRIES ── */
#industries{background:var(--white)}


/* ── WHY ── */
#why{background:var(--bg)}
.why-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:14px}
.why-card{background:#12201a;border-radius:10px;border:1.5px solid rgba(255,255,255,.1);padding:1.85rem;display:flex;flex-direction:column;gap:.7rem;transition:all .2s;overflow:hidden;position:relative}
.why-card:hover{box-shadow:0 4px 20px rgba(0,0,0,.4);border-color:rgba(255,255,255,.25)}
.why-bar{height:3px;margin:-1.85rem -1.85rem 1rem;border-radius:8px; 0 0}
.why-icon{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(0,0,0,.12)}
.why-icon svg{width:24px;height:24px}
.why-name{font-size:13.5px;font-weight:800;letter-spacing:-.01em;color:#fff}
.why-desc{font-size:11.5px;color:rgba(255,255,255,.45);line-height:1.55;font-weight:400}

/* ── DASHBOARDS ── */
#dashboards{background:var(--white)}
/* ── DASHBOARD GRID ── */
.dash-grid{
 display:grid;
 grid-template-columns:repeat(3,1fr);
 gap:16px;
}
.dash-card{
 background:var(--white);
 border-radius:10px;
 border:1.5px solid var(--border);
 overflow:hidden;
 transition:opacity .65s ease, transform .65s ease, box-shadow .2s;
 opacity:0;
 transform:translateY(64px);
}
.dash-card:hover{
 box-shadow:0 12px 40px rgba(0,0,0,.09);
 transform:translateY(-2px) !important;
}
.dash-card.visible{
 opacity:1;
 transform:translateY(0);
}
.dash-card.visible.d2{transition-delay:.13s}
.dash-card.visible.d3{transition-delay:.26s}
.dash-card.visible.d1{transition-delay:0s}

.dash-top{display:none}
.dash-head{display:flex;align-items:center;gap:9px;padding:12px 15px;border-bottom:1px solid var(--border);background:var(--bg)}
.dash-ico{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,.2)}
.dash-ico svg{width:18px;height:18px}
.dash-mod-name{font-size:12px;font-weight:800;letter-spacing:-.01em}
.dash-body{padding:15px}
.d-krow{display:flex;gap:6px;margin-bottom:12px}
.d-k{flex:1;background:var(--bg);border-radius:8px;padding:8px 7px;border:1px solid var(--border)}
.d-kv{font-size:13px;font-weight:800;line-height:1;margin-bottom:2px}
.d-kl{font-size:7.5px;color:var(--g400);text-transform:uppercase;letter-spacing:.05em;font-weight:600}
.d-lbl{font-size:8.5px;color:var(--g400);text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:7px}
.d-bars{display:flex;align-items:flex-end;gap:4px;height:54px;margin-bottom:12px}
.d-bar{flex:1;background:var(--border);border-radius:8px; 2px 0 0;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;overflow:hidden}
.d-bar span{font-size:6.5px;color:var(--g400);padding-bottom:2px;font-weight:600}
.d-hr{height:1px;background:var(--border);margin:10px 0}
.d-rows{display:flex;flex-direction:column;gap:5px}
.d-row{display:flex;align-items:center;gap:6px;font-size:10.5px;color:var(--g500)}
.d-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.d-val{margin-left:auto;font-size:10.5px;font-weight:700}
.d-hbars{display:flex;flex-direction:column;gap:7px}
.d-hbar{display:flex;align-items:center;gap:6px;font-size:9px;color:var(--g500)}
.d-hbar span:first-child{width:62px;font-weight:600;font-size:8.5px}
.d-track{flex:1;height:4px;background:var(--border);border-radius:8px;overflow:hidden}
.d-fill{height:100%;border-radius:8px;}
.d-hbar span:last-child{width:20px;text-align:right;font-size:8.5px;font-weight:700}
.d-status{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;margin-bottom:12px}
.d-sbox{border-radius:6px;padding:8px 5px;text-align:center}
.d-sv{font-size:14px;font-weight:800;line-height:1}
.d-sl{font-size:7px;color:var(--g400);text-transform:uppercase;letter-spacing:.04em;font-weight:600;margin-top:2px}
.d-funnel{display:flex;flex-direction:column;gap:4px;margin-bottom:12px}
.d-fbar{border-radius:8px;height:20px;display:flex;align-items:center;padding:0 9px;font-size:9.5px;font-weight:600}
.d-channels{display:flex;flex-direction:column;gap:8px}
.d-ch{display:flex;align-items:center;gap:7px}
.d-ch-ico{width:20px;height:20px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.d-ch-ico svg{width:11px;height:11px}
.d-ch-name{font-size:9px;color:var(--g500);width:55px;font-weight:600}
.d-ch-pct{font-size:9px;font-weight:800;width:26px}
.d-ch-track{flex:1;height:3.5px;background:var(--border);border-radius:8px;overflow:hidden}
.d-ch-fill{height:100%;border-radius:8px;}

/* ── CTA ── */
#cta{
 padding:9rem 3.5rem;text-align:center;position:relative;
 overflow:hidden;background:var(--black);border-bottom:none;
}
.cta-grid-bg{position:absolute;inset:0;pointer-events:none;background-image:linear-gradient(rgba(255,255,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:68px 68px}
.cta-glow{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:800px;height:800px;background:radial-gradient(circle,rgba(50,180,111,.22) 0%,rgba(50,180,111,.12) 35%,transparent 65%);pointer-events:none}
.cta-h{font-size:clamp(50px,8vw,96px);font-weight:900;letter-spacing:-.035em;line-height:.94;color:#fff;margin-bottom:1.25rem;position:relative}
.cta-h .fade{color:rgba(255,255,255,.2)}
.cta-h .gr{background:linear-gradient(115deg,#4ecb87,#34a87c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:inline-block;padding-right:3px}
.cta-h .gr2{background:linear-gradient(115deg,#32b46f,#4ecb87);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:inline-block;padding-right:3px}
.cta-h .gr3{background:linear-gradient(115deg,#34a87c,#7fd9a8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:inline-block}
.cta-p{font-size:16px;color:rgba(255,255,255,.48);margin-bottom:2.5rem;font-weight:400;max-width:420px;margin-left:auto;margin-right:auto;line-height:1.65;position:relative}
.cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative}
.cta-btn-w{background:#fff;color:var(--black);padding:15px 32px;border-radius:8px;font-family:var(--font);font-weight:800;font-size:12px;letter-spacing:.07em;text-transform:uppercase;text-decoration:none;border:none;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
.cta-btn-w:hover{background:#e8e8e4;transform:translateY(-1px);box-shadow:0 10px 32px rgba(255,255,255,.14)}
.cta-btn-g{background:transparent;color:rgba(255,255,255,.7);padding:14px 31px;border-radius:8px;font-family:var(--font);font-weight:700;font-size:12px;letter-spacing:.07em;text-transform:uppercase;text-decoration:none;border:1.5px solid rgba(255,255,255,.2);cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
.cta-btn-g:hover{border-color:rgba(255,255,255,.55);color:#fff;transform:translateY(-1px)}
.cta-note{margin-top:2.5rem;font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:rgba(255,255,255,.22);font-weight:600;position:relative}

/* ── FOOTER ── */
footer{padding:2.75rem 3.5rem;display:grid;grid-template-columns:1.3fr 1fr 1fr;gap:2rem;background:var(--white);border-top:1.5px solid var(--border)}
.ft-logo{font-size:19px;font-weight:800;letter-spacing:.04em;margin-bottom:.5rem}
.ft-sub{font-size:9.5px;text-transform:uppercase;letter-spacing:.11em;color:var(--g400);font-weight:600;line-height:2}
.ft-links{display:flex;flex-direction:column;gap:9px}
.ft-links a{font-size:12.5px;color:var(--g500);text-decoration:none;font-weight:500;transition:color .15s}
.ft-links a:hover{color:var(--black)}
.ft-contact{font-size:11.5px;color:var(--g400);line-height:2.2;font-weight:500}
.ft-copy{margin-top:.85rem;font-size:10.5px;color:var(--g300);font-weight:500}

/* ── REVEAL ── */
.rv{opacity:0;transform:translateY(20px);transition:opacity .65s ease,transform .65s ease}
.rv.in{opacity:1;transform:translateY(0)}
.d1{transition-delay:.08s}.d2{transition-delay:.16s}.d3{transition-delay:.24s}.d4{transition-delay:.32s}

/* ── KEYFRAMES ── */
@keyframes fu{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
@keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}

/* ── RESPONSIVE ── */

/* ── TABLET ── */
@media(max-width:960px){
  nav{padding:1rem 1.5rem}
  .nav-links{display:none}
  .logo img{height:38px}
  section{padding:5rem 1.5rem}
  #hero{padding:6rem 1.5rem 3rem}
  .hero-h{font-size:clamp(48px,9vw,96px)}
  .fn-grid{grid-template-columns:repeat(2,1fr)}
  .tech-grid{grid-template-columns:repeat(2,1fr)}
  .cases-grid,.dash-grid{grid-template-columns:1fr}
  .why-grid{grid-template-columns:repeat(2,1fr)}
  .ind-grid{grid-template-columns:repeat(2,1fr)}
  footer{grid-template-columns:1fr;gap:2.5rem}
  #cta{padding:5rem 1.5rem}
  .cta-h{font-size:clamp(40px,8vw,72px)}
  .hero-product-wrap{margin-top:4rem}
  .hpf-body{height:auto;flex-direction:column}
  .hpf-sidebar{width:100%;flex-direction:row;padding:10px;gap:8px;overflow-x:auto}
  .hpf-main{padding:12px}
  .hpf-kpi-row{grid-template-columns:repeat(2,1fr)}
  .hpf-charts-row{flex-direction:column}
  .ind-tabs{flex-wrap:wrap}
  .ind-tab{min-width:auto;flex:1 1 45%}
  .hero-stats{max-width:100%;flex-wrap:wrap;gap:1.5rem}
  .hstat{flex:1 1 40%;border-right:none;padding:0}
  .sec-h{font-size:clamp(28px,6vw,48px)}
}

/* ── MOBILE ── */
@media(max-width:560px){
  nav{padding:.9rem 1.25rem}
  .logo img{height:32px}
  section{padding:4rem 1.25rem}
  #hero{padding:5.5rem 1.25rem 3rem}
  .hero-h{font-size:clamp(40px,11vw,72px);letter-spacing:-.03em;line-height:.92}
  .hero-p{font-size:15px;max-width:100%}
  .hero-btns{flex-direction:column;align-items:center;width:100%}
  .hero-btns .btn{width:100%;justify-content:center}
  .fn-grid,.why-grid,.ind-grid{grid-template-columns:1fr}
  .tech-grid{grid-template-columns:1fr}
  .cases-grid,.dash-grid{grid-template-columns:1fr}
  .screen-s{display:none}
  .screen-c{width:100% !important;max-width:100%}
  .screens-row{flex-direction:column}
  .hero-product-wrap{margin-top:3rem;overflow:hidden}
  .hero-product-frame{border-radius:8px}
  .hpf-body{height:auto;flex-direction:column}
  .hpf-sidebar{display:none}
  .hpf-main{padding:12px}
  .hpf-kpi-row{grid-template-columns:repeat(2,1fr);gap:6px}
  .hpf-charts-row{flex-direction:column;gap:8px}
  .hpf-chart-box{padding:10px}
  .ind-tabs{flex-wrap:wrap;width:100%;max-width:100%}
  .ind-tab{flex:1 1 48%;min-width:120px;padding:10px 12px}
  .ind-tab .tab-sub{display:none}
  #tabProgressWrap{min-width:100% !important;width:100% !important}
  .hero-stats{flex-wrap:wrap;gap:1.25rem;justify-content:center;max-width:100%}
  .hstat{flex:1 1 40%;border-right:none;border-bottom:1px solid var(--border);padding-bottom:1rem}
  .hstat:last-child,.hstat:nth-child(2n){border-bottom:none}
  .hstat-n{font-size:36px}
  .sec-h{font-size:clamp(26px,7vw,40px);letter-spacing:-.02em}
  .sec-sub{font-size:14px}
  .sec-cta{flex-direction:column;align-items:center}
  .sec-cta .btn{width:100%;max-width:360px;justify-content:center}
  footer{grid-template-columns:1fr;padding:2rem 1.25rem;gap:2rem}
  #cta{padding:4.5rem 1.25rem}
  .cta-h{font-size:clamp(36px,9vw,60px)}
  .cta-btns{flex-direction:column;align-items:center}
  .cta-btn-w,.cta-btn-g{width:100%;max-width:360px;justify-content:center}
  .why-grid{grid-template-columns:1fr}
  .ind-chip{font-size:12px;padding:8px 14px}
  .case-screen{height:160px}
  .cs-body{height:calc(160px - 28px)}
  .mq-wrap{display:none}
  .fn-cta-card{padding:2rem 1.5rem}
  .hero-float{display:none}
  .dash-card{margin:0}
}

/* ══════════════════ ABOUT PAGE ══════════════════ */
#about-hero{min-height:auto;padding-top:9.5rem;text-align:center}
#about-hero .sec-h{margin-left:auto;margin-right:auto}

.story-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:3.5rem;align-items:start}
.story-card{background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:2.1rem 2rem;box-shadow:0 12px 40px rgba(0,0,0,.05)}
.story-card-label{font-size:10.5px;text-transform:uppercase;letter-spacing:.14em;color:var(--blue);font-weight:700;margin-bottom:1.3rem}
.story-facts{list-style:none;display:flex;flex-direction:column}
.story-facts li{display:flex;justify-content:space-between;gap:1rem;padding:.85rem 0;border-bottom:1px solid var(--border);font-size:12.5px}
.story-facts li:last-child{border-bottom:none}
.story-facts li span{color:var(--g400);font-weight:600;text-transform:uppercase;letter-spacing:.04em;font-size:10.5px}
.story-facts li strong{color:var(--black);font-weight:700;text-align:right}
.story-tagline{margin-top:1.4rem;padding-top:1.3rem;border-top:1px solid var(--border);font-size:13px;font-weight:800;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.story-tagline span{background:none;-webkit-text-fill-color:var(--g400);color:var(--g400);font-weight:600}

.founder-card{display:flex;gap:2.75rem;background:var(--white);border:1.5px solid var(--border);border-radius:16px;padding:2.75rem;align-items:flex-start;box-shadow:0 12px 40px rgba(0,0,0,.05)}
.founder-avatar{width:118px;height:118px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;color:#fff;font-size:36px;font-weight:800;letter-spacing:-.02em;flex-shrink:0;box-shadow:0 12px 32px rgba(50,180,111,.35)}
.founder-name{font-size:23px;font-weight:800;letter-spacing:-.01em}
.founder-title{font-size:13px;color:var(--blue);font-weight:700;margin-top:.2rem;margin-bottom:.3rem}
.founder-loc{font-size:10.5px;color:var(--g400);text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:1.3rem}
.founder-bio{font-size:14px;color:var(--g500);line-height:1.75;margin-bottom:1.3rem;font-weight:400}
.founder-quote{border-left:3px solid var(--blue);padding:.15rem 0 .15rem 1.15rem;font-size:15.5px;font-weight:600;color:var(--black);font-style:italic;margin-bottom:1.4rem;line-height:1.5}
.founder-quote cite{display:block;margin-top:.5rem;font-size:10.5px;font-style:normal;color:var(--g400);font-weight:700;text-transform:uppercase;letter-spacing:.06em}
.founder-skills{display:flex;flex-wrap:wrap;gap:6px}

.why-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}

.industry-chips{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;max-width:840px;margin:0 auto}
.ind-chip2{font-size:13px;font-weight:700;color:var(--g600);background:var(--white);border:1.5px solid var(--border);border-radius:30px;padding:10px 22px;transition:all .2s;cursor:default}
.ind-chip2:hover{border-color:var(--blue);color:var(--blue);transform:translateY(-2px)}

@media(max-width:960px){
  .story-grid{grid-template-columns:1fr;gap:2.5rem}
  .founder-card{flex-direction:column;align-items:center;text-align:center;padding:2.25rem}
  .founder-skills{justify-content:center}
  .founder-quote{text-align:left}
  .why-grid-4{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:560px){
  #about-hero{padding-top:7.5rem}
  .why-grid-4{grid-template-columns:1fr}
  .founder-card{padding:1.75rem}
  .founder-avatar{width:96px;height:96px;font-size:30px}
  .industry-chips{gap:8px}
  .ind-chip2{padding:9px 16px;font-size:12px}
  .story-facts li strong{text-align:right;font-size:12px}
}

/* ══════════════════ BLOG ══════════════════ */
#blog-hero{min-height:auto;padding-top:9.5rem;text-align:center}
#blog-hero .sec-h{margin-left:auto;margin-right:auto}

.blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.blog-card{background:var(--white);border:1.5px solid var(--border);border-radius:12px;overflow:hidden;display:flex;flex-direction:column;text-decoration:none;color:inherit;transition:all .2s}
.blog-card:hover{box-shadow:0 12px 40px rgba(0,0,0,.09);border-color:var(--g300);transform:translateY(-2px)}
.blog-card-img{height:190px;background:var(--bg2) center/cover no-repeat;border-bottom:1.5px solid var(--border);position:relative}
.blog-card-img.placeholder{display:flex;align-items:center;justify-content:center}
.blog-card-img.placeholder::after{content:'Drawlead';font-size:15px;font-weight:800;letter-spacing:-.01em;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.blog-card-body{padding:1.5rem 1.6rem;display:flex;flex-direction:column;gap:.6rem;flex:1}
.blog-card-date{font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:var(--g400);font-weight:700}
.blog-card-title{font-size:17px;font-weight:800;letter-spacing:-.01em;line-height:1.3}
.blog-card-excerpt{font-size:12.5px;color:var(--g500);line-height:1.6;flex:1}
.blog-card-arrow{font-size:10.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--blue);margin-top:.4rem}

/* Single post */
#post-hero{padding-top:9.5rem;padding-bottom:2rem;text-align:center}
.post-meta{font-size:11.5px;color:var(--g400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:1rem}
.post-featured{max-width:920px;margin:0 auto 1rem;border-radius:14px;overflow:hidden;border:1.5px solid var(--border)}
.post-featured img{width:100%;display:block}
#post-body{padding-top:2rem}
.post-content{max-width:720px;margin:0 auto;font-size:15.5px;line-height:1.85;color:var(--g600)}
.post-content h2{font-size:26px;font-weight:800;letter-spacing:-.015em;margin:2rem 0 1rem;color:var(--black)}
.post-content h3{font-size:20px;font-weight:800;letter-spacing:-.01em;margin:1.75rem 0 .9rem;color:var(--black)}
.post-content p{margin-bottom:1.25rem}
.post-content ul,.post-content ol{margin:0 0 1.25rem 1.4rem}
.post-content li{margin-bottom:.5rem}
.post-content a{color:var(--blue);font-weight:600}
.post-content blockquote{border-left:3px solid var(--blue);padding:.2rem 0 .2rem 1.3rem;font-style:italic;color:var(--black);font-weight:600;margin:1.5rem 0}
.post-content img{max-width:100%;border-radius:10px;margin:1.5rem 0}
.post-back{max-width:720px;margin:2.5rem auto 0}
.post-back a{font-size:12.5px;font-weight:700;color:var(--g500);text-decoration:none}
.post-back a:hover{color:var(--blue)}

@media(max-width:960px){
  .blog-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:560px){
  .blog-grid{grid-template-columns:1fr}
  #blog-hero{padding-top:7.5rem}
  #post-hero{padding-top:7.5rem}
  .post-content{font-size:14.5px}
}
