<!doctype html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="description" content="Cuci karpet gulung & portable di Kuala Lumpur & Selangor. Kami ambil, cuci & hantar balik. Dari RM2 sekaki persegi. Tempah di WhatsApp.">
<link rel="icon" href="{{ asset('assets/cucikarpet-logo.png') }}">
<style>:root{color-scheme:light;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}body{margin:0}img{max-width:100%}[hidden]{display:none!important}
/* booking form */
.book{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:14px}
.book .full{grid-column:1/-1}
@media (max-width:640px){.book{grid-template-columns:1fr}}
input[type=text],input[type=email],input[type=tel],input[type=date],textarea{font:inherit;font-size:.95rem;color:var(--fg);background:var(--bg);border:1px solid var(--line);border-radius:10px;padding:9px 10px;width:100%;min-width:0}
textarea{min-height:72px;resize:vertical}
.okbox{background:var(--sky-soft);border:1.5px solid var(--accent);border-radius:var(--r);padding:16px 18px;display:flex;flex-direction:column;gap:4px}
.okbox b{font-family:var(--f-display);font-size:1.2rem}
.errbox{background:var(--ph-bg);color:var(--ph-fg);border-radius:10px;padding:12px 14px;font-size:.9rem}
.errbox ul{margin:6px 0 0;padding-left:18px}
.hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}
.bookitems{font-size:.92rem;color:var(--muted)}
</style>
<title>cucikarpet.co</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&family=Figtree:wght@400;500;600;700&display=swap">
<style>
/* Layout: brand-blue hero band with a measured rug, care-tag price cards, a working estimate calculator; a second #plan view holds the build plan. */
:root{
  --bg:#F4F7FC; --surface:#FFFFFF; --fg:#0B1A3A; --muted:#55648A; --line:#D8E1F0;
  --brand:#0B3BA8; --brand-deep:#082C80; --on-brand:#FFFFFF; --sky:#BFE4FF; --sky-soft:#E6F3FF;
  --accent:#0B3BA8; --accent-ink:#FFFFFF; --wa:#128C4A; --ph-bg:#FFF3C4; --ph-fg:#6B4E00;
  --rug:#E8F3FF; --rug-line:#0B3BA8;
  --f-display:"Lexend",ui-rounded,"Segoe UI",system-ui,sans-serif;
  --f-body:"Figtree",system-ui,-apple-system,"Segoe UI",sans-serif;
  --r:14px;
}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){
  --bg:#060F24; --surface:#0C1A3A; --fg:#E7EDFA; --muted:#9AA9CC; --line:#1E2E57;
  --brand:#0A2F88; --brand-deep:#061E5C; --on-brand:#FFFFFF; --sky:#9FD4FF; --sky-soft:#12264F;
  --accent:#7DB2FF; --accent-ink:#06132E; --wa:#3CCB7F; --ph-bg:#4A3A08; --ph-fg:#FFE48A;
  --rug:#DCEBFF; --rug-line:#0A2F88; color-scheme:dark}}
:root[data-theme="dark"]{
  --bg:#060F24; --surface:#0C1A3A; --fg:#E7EDFA; --muted:#9AA9CC; --line:#1E2E57;
  --brand:#0A2F88; --brand-deep:#061E5C; --on-brand:#FFFFFF; --sky:#9FD4FF; --sky-soft:#12264F;
  --accent:#7DB2FF; --accent-ink:#06132E; --wa:#3CCB7F; --ph-bg:#4A3A08; --ph-fg:#FFE48A;
  --rug:#DCEBFF; --rug-line:#0A2F88; color-scheme:dark}

*{box-sizing:border-box}
body{background:var(--bg);color:var(--fg);font-family:var(--f-body);font-size:16px;line-height:1.6}
body::before{content:"";position:fixed;inset:0 0 auto;height:env(safe-area-inset-top,0px);z-index:10;background:var(--brand-deep)}
html[lang="en"] .ms, html:not([lang="en"]) .en{display:none!important}
h1,h2,h3{font-family:var(--f-display);line-height:1.15;text-wrap:balance;margin:0;letter-spacing:-.01em}
h2{font-size:clamp(1.6rem,3.2vw,2.3rem);font-weight:600}
h3{font-size:1.15rem;font-weight:600}
p{margin:0}
a{color:var(--accent)}
:focus-visible{outline:3px solid var(--sky);outline-offset:2px;border-radius:6px}
.wrap{max-width:1120px;margin-inline:auto;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
.eyebrow{font-family:var(--f-display);font-size:.78rem;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)}
.lede{color:var(--muted);max-width:60ch}
.tnum{font-variant-numeric:tabular-nums}
mark.ph{background:var(--ph-bg);color:var(--ph-fg);padding:0 .3em;border-radius:4px;font-weight:600}

/* draft strip + header */
.draft{background:var(--ph-bg);color:var(--ph-fg);font-size:.85rem}
.draft .wrap{padding-block:8px}
header.top{position:sticky;top:env(safe-area-inset-top,0px);z-index:20;background:var(--brand-deep);color:var(--on-brand)}
header.top .wrap{display:flex;align-items:center;gap:16px;padding-block:10px;flex-wrap:wrap}
.logo{height:26px;width:auto;display:block}
nav.links{display:flex;gap:4px;margin-left:auto;flex-wrap:wrap}
nav.links a{color:var(--on-brand);text-decoration:none;font-size:.92rem;padding:6px 10px;border-radius:8px;opacity:.88}
nav.links a:hover{opacity:1;background:rgba(255,255,255,.1)}
.seg{display:inline-flex;border:1px solid rgba(255,255,255,.35);border-radius:999px;padding:2px}
.seg button{all:unset;cursor:pointer;font-size:.8rem;font-weight:600;padding:4px 10px;border-radius:999px;color:var(--on-brand)}
.seg button[aria-pressed="true"]{background:var(--on-brand);color:var(--brand-deep)}
.seg button:focus-visible{outline:2px solid var(--sky)}
.viewtabs{display:flex;gap:6px}
.viewtabs a{color:var(--on-brand);font-size:.8rem;text-decoration:none;padding:4px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.3)}
.viewtabs a[aria-current="page"]{background:var(--sky);color:var(--brand-deep);border-color:var(--sky);font-weight:600}
@media (max-width:820px){nav.links{display:none}.viewtabs{margin-left:auto}}

/* buttons */
.btn{display:inline-flex;align-items:center;gap:8px;font-family:var(--f-display);font-weight:500;font-size:1rem;padding:12px 20px;border-radius:999px;text-decoration:none;border:0;cursor:pointer;line-height:1.2}
.btn-wa{background:var(--wa);color:#fff}
.btn-wa:hover{filter:brightness(1.08)}
.btn-light{background:var(--on-brand);color:var(--brand-deep)}
.btn-ghost{background:transparent;color:var(--accent);border:1.5px solid var(--line)}
.btn-ghost:hover{border-color:var(--accent)}
.btn svg{width:18px;height:18px;flex:none}

/* hero */
.hero{background:var(--brand);color:var(--on-brand);overflow:hidden}
.hero .wrap{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center;padding-block:56px 64px}
.hero .eyebrow{color:var(--sky)}
.hero h1{font-size:clamp(2.2rem,5.2vw,3.7rem);font-weight:600;margin-top:14px}
.hero h1 em{font-style:normal;color:var(--sky)}
.hero p.sub{margin-top:18px;font-size:1.12rem;max-width:46ch;opacity:.92}
.hero .ctas{display:flex;gap:12px;flex-wrap:wrap;margin-top:28px}
.hero .facts{display:flex;gap:24px;flex-wrap:wrap;margin-top:32px;font-size:.9rem}
.hero .facts div{display:flex;flex-direction:column}
.hero .facts b{font-family:var(--f-display);font-size:1.4rem;font-weight:600}
.hero .facts span{opacity:.8}
.rugfig{max-width:100%;width:100%;height:auto;display:block}
@media (max-width:860px){.hero .wrap{grid-template-columns:1fr;padding-block:40px 48px}.rugfig{max-width:440px}}

section.blk{padding-block:72px}
section.blk .head{display:flex;flex-direction:column;gap:10px;margin-bottom:32px}

/* price tags */
.tags{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
@media (max-width:980px){.tags{grid-template-columns:repeat(2,1fr)}}
@media (max-width:520px){.tags{grid-template-columns:1fr}}
.tag{position:relative;background:var(--surface);border:1.5px solid var(--line);border-radius:10px 10px 22px 22px;padding:44px 22px 22px;display:flex;flex-direction:column;gap:6px}
.tag::before{content:"";position:absolute;top:14px;left:50%;width:14px;height:14px;margin-left:-7px;border-radius:50%;background:var(--bg);border:1.5px solid var(--line)}
.tag::after{content:"";position:absolute;top:36px;left:16px;right:16px;border-top:1.5px dashed var(--line)}
.tag .kind{font-family:var(--f-display);font-weight:600;font-size:1.05rem;margin-top:6px}
.tag .rate{font-family:var(--f-display);font-weight:600;font-size:2.4rem;color:var(--accent);line-height:1.1;margin-top:8px}
.tag .rate small{font-size:.95rem;font-weight:500;color:var(--muted)}
.tag .eg{font-size:.88rem;color:var(--muted);margin-top:auto;padding-top:12px;border-top:1px solid var(--line)}
.tag .desc{font-size:.92rem;color:var(--muted)}
.tag.hl{border-color:var(--accent)}
.tag.hl .badge{position:absolute;top:-11px;right:16px;background:var(--accent);color:var(--accent-ink);font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:3px 10px;border-radius:999px}
.note{font-size:.9rem;color:var(--muted);margin-top:20px}

/* calculator */
.calcband{background:var(--sky-soft)}
.calc{display:grid;grid-template-columns:1.6fr 1fr;gap:24px;align-items:start}
@media (max-width:900px){.calc{grid-template-columns:1fr}}
.panel{background:var(--surface);border:1px solid var(--line);border-radius:var(--r);padding:22px;min-width:0}
.row{display:grid;grid-template-columns:minmax(0,1.6fr) 74px 14px 74px 64px minmax(0,1fr) 36px;gap:8px;align-items:end;padding-block:14px;border-bottom:1px solid var(--line)}
.row:first-child{padding-top:0}
.row .x{text-align:center;padding-bottom:10px;color:var(--muted)}
.row .out{text-align:right;padding-bottom:8px;font-variant-numeric:tabular-nums}
.row .out b{display:block;font-family:var(--f-display);font-weight:600}
.row .out span{font-size:.8rem;color:var(--muted)}
label.f{display:flex;flex-direction:column;gap:4px;font-size:.75rem;color:var(--muted);font-weight:600;letter-spacing:.03em;min-width:0}
select,input[type=number]{font:inherit;font-size:.95rem;color:var(--fg);background:var(--bg);border:1px solid var(--line);border-radius:10px;padding:9px 10px;width:100%;min-width:0}
input[type=number]{font-variant-numeric:tabular-nums}
.rm{all:unset;cursor:pointer;width:36px;height:38px;display:grid;place-items:center;border-radius:10px;color:var(--muted)}
.rm:hover{background:var(--bg);color:var(--fg)}
.rm:focus-visible{outline:2px solid var(--accent)}
@media (max-width:560px){
  .row{grid-template-columns:1fr 1fr 1fr;row-gap:10px}
  .row .ftype{grid-column:1 / -1}
  .row .x{display:none}
  .row .out{grid-column:1 / 3;text-align:left;padding:0}
  .row .rm{justify-self:end}
}
.presets{display:flex;gap:6px;flex-wrap:wrap;align-items:center;margin-top:16px;font-size:.85rem;color:var(--muted)}
.chip{all:unset;cursor:pointer;font-size:.82rem;font-weight:600;padding:5px 11px;border-radius:999px;border:1px solid var(--line);color:var(--fg);background:var(--bg);font-variant-numeric:tabular-nums}
.chip:hover{border-color:var(--accent)}
.chip:focus-visible{outline:2px solid var(--accent)}
.addrow{margin-top:14px}
.sum{display:flex;flex-direction:column;gap:14px;position:sticky;top:calc(env(safe-area-inset-top,0px) + 72px)}
.sum .line{display:flex;justify-content:space-between;gap:12px;font-variant-numeric:tabular-nums}
.sum .line span:first-child{color:var(--muted)}
.sum .total{font-family:var(--f-display);font-size:2.4rem;font-weight:600;color:var(--accent);line-height:1}
.sum .small{font-size:.85rem;color:var(--muted)}
.copyline{display:flex;gap:8px;flex-wrap:wrap}
.status{font-size:.85rem;color:var(--wa);min-height:1.3em}

/* steps */
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:28px;list-style:none;padding:0;margin:0;counter-reset:s}
@media (max-width:900px){.steps{grid-template-columns:repeat(2,1fr)}}
@media (max-width:520px){.steps{grid-template-columns:1fr}}
.steps li{display:flex;flex-direction:column;gap:8px;counter-increment:s;border-top:2px solid var(--accent);padding-top:16px}
.steps li::before{content:counter(s);font-family:var(--f-display);font-weight:600;font-size:.85rem;color:var(--accent)}
.steps p{color:var(--muted);font-size:.95rem}

/* info strip */
.info{display:grid;grid-template-columns:repeat(3,1fr);gap:0;border:1px solid var(--line);border-radius:var(--r);background:var(--surface)}
.info > div{padding:22px;display:flex;flex-direction:column;gap:6px;min-width:0}
.info > div + div{border-left:1px solid var(--line)}
.info .v{font-family:var(--f-display);font-weight:500;font-size:1.1rem}
@media (max-width:760px){.info{grid-template-columns:1fr}.info > div + div{border-left:0;border-top:1px solid var(--line)}}

/* faq */
.faq{display:flex;flex-direction:column;max-width:780px}
.faq details{border-bottom:1px solid var(--line);padding-block:16px}
.faq summary{cursor:pointer;font-family:var(--f-display);font-weight:500;font-size:1.05rem;list-style:none;display:flex;justify-content:space-between;gap:16px}
.faq summary::-webkit-details-marker{display:none}
.faq summary::after{content:"+";color:var(--accent);font-size:1.3rem;line-height:1}
.faq details[open] summary::after{content:"−"}
.faq details p{margin-top:10px;color:var(--muted);max-width:65ch}

/* cta + footer */
.cta{background:var(--brand);color:var(--on-brand)}
.cta .wrap{display:flex;justify-content:space-between;align-items:center;gap:24px;flex-wrap:wrap;padding-block:48px}
.cta h2{max-width:22ch}
footer{background:var(--brand-deep);color:var(--on-brand);font-size:.9rem}
footer .wrap{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;padding-block:28px}
footer .num{font-family:var(--f-display);font-weight:500;user-select:all}
footer button{all:unset;cursor:pointer;font-size:.8rem;text-decoration:underline;margin-left:8px;opacity:.85}

/* ---------- plan view ---------- */
.planhero{background:var(--brand-deep);color:var(--on-brand)}
.planhero .wrap{padding-block:48px;display:flex;flex-direction:column;gap:12px}
.planhero .eyebrow{color:var(--sky)}
.planhero h1{font-size:clamp(1.9rem,4.4vw,3rem);font-weight:600}
.planhero p{max-width:62ch;opacity:.9}
.toc{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
.toc a{color:var(--on-brand);font-size:.85rem;text-decoration:none;border:1px solid rgba(255,255,255,.3);padding:5px 12px;border-radius:999px}
.toc a:hover{background:rgba(255,255,255,.1)}
.apps{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
@media (max-width:900px){.apps{grid-template-columns:1fr}}
.app{background:var(--surface);border:1px solid var(--line);border-radius:var(--r);padding:24px;display:flex;flex-direction:column;gap:10px;min-width:0}
.app .who{font-size:.8rem;font-weight:600;color:var(--accent);letter-spacing:.04em;text-transform:uppercase}
.app ul{margin:6px 0 0;padding-left:18px;color:var(--muted);font-size:.94rem;display:flex;flex-direction:column;gap:4px}
.pipe{display:flex;flex-wrap:wrap;gap:8px 6px;align-items:center;list-style:none;padding:0;margin:0}
.pipe li{display:flex;align-items:center;gap:6px}
.pipe .st{font-size:.85rem;font-weight:600;padding:6px 12px;border-radius:999px;background:var(--surface);border:1px solid var(--line);white-space:nowrap}
.pipe .st.t{border-color:var(--accent);color:var(--accent)}
.pipe .ar{color:var(--muted)}
.legend{display:flex;gap:16px;flex-wrap:wrap;font-size:.85rem;color:var(--muted);margin-top:14px}
.legend i{display:inline-block;width:12px;height:12px;border-radius:50%;border:1.5px solid var(--line);vertical-align:-1px;margin-right:6px}
.legend i.t{border-color:var(--accent)}
.tbl{overflow-x:auto;border:1px solid var(--line);border-radius:var(--r);background:var(--surface)}
table{border-collapse:collapse;width:100%;min-width:640px;font-size:.92rem}
th,td{text-align:left;padding:12px 16px;border-bottom:1px solid var(--line);vertical-align:top}
th{font-family:var(--f-display);font-weight:500;font-size:.8rem;letter-spacing:.06em;text-transform:uppercase;color:var(--muted)}
tr:last-child td{border-bottom:0}
td code{font-family:ui-monospace,"SF Mono",Menlo,monospace;font-size:.84rem;color:var(--accent)}
.phases{display:grid;gap:0;border-left:2px solid var(--line);margin-left:8px}
.phase{position:relative;padding:0 0 28px 28px;display:grid;grid-template-columns:200px 1fr;gap:20px}
.phase::before{content:"";position:absolute;left:-8px;top:4px;width:14px;height:14px;border-radius:50%;background:var(--bg);border:2px solid var(--accent)}
.phase:first-child::before{background:var(--accent)}
.phase .when{display:flex;flex-direction:column;gap:2px}
.phase .when b{font-family:var(--f-display);font-weight:600}
.phase .when span{font-size:.85rem;color:var(--muted)}
.phase ul{margin:0;padding-left:18px;color:var(--muted);display:flex;flex-direction:column;gap:3px}
@media (max-width:640px){.phase{grid-template-columns:1fr;gap:8px}}
.stack{display:grid;grid-template-columns:repeat(2,1fr);gap:0;border:1px solid var(--line);border-radius:var(--r);background:var(--surface)}
.stack > div{padding:18px 20px;border-bottom:1px solid var(--line);display:flex;flex-direction:column;gap:4px;min-width:0}
.stack > div:nth-child(odd){border-right:1px solid var(--line)}
.stack > div:nth-last-child(-n+2){border-bottom:0}
.stack .k{font-size:.8rem;color:var(--muted);font-weight:600;letter-spacing:.04em;text-transform:uppercase}
.stack .v{font-family:var(--f-display);font-weight:500}
.stack p{font-size:.9rem;color:var(--muted)}
@media (max-width:640px){.stack{grid-template-columns:1fr}.stack > div:nth-child(odd){border-right:0}.stack > div:nth-last-child(2){border-bottom:1px solid var(--line)}}
.todo{list-style:none;padding:0;margin:0;display:grid;grid-template-columns:repeat(2,1fr);gap:10px 28px}
.todo li{display:flex;gap:10px;align-items:baseline}
.todo li::before{content:"";flex:none;width:14px;height:14px;border:1.5px solid var(--accent);border-radius:4px;transform:translateY(2px)}
@media (max-width:640px){.todo{grid-template-columns:1fr}}
.alt{background:var(--sky-soft)}
@media (prefers-reduced-motion:no-preference){.btn,.chip,.tag{transition:filter .15s,border-color .15s,background .15s}}
</style>

</head>
<body>
<header class="top"><div class="wrap">
  <a href="#site" aria-label="cucikarpet.co"><img class="logo" src="{{ asset('assets/cucikarpet-logo.png') }}" alt="cucikarpet.co"></a>
  <nav class="links" id="sitenav">
    <a href="#harga"><span class="ms">Harga</span><span class="en">Prices</span></a>
    <a href="#kira"><span class="ms">Kira harga</span><span class="en">Estimate</span></a>
    <a href="#cara"><span class="ms">Cara</span><span class="en">How it works</span></a>
    <a href="#soalan"><span class="ms">Soalan</span><span class="en">FAQ</span></a>
  </nav>
  <div class="seg" role="group" aria-label="Bahasa / Language">
    <button type="button" id="lang-ms" aria-pressed="true">BM</button>
    <button type="button" id="lang-en" aria-pressed="false">EN</button>
  </div>
</div></header>

<main id="view-site">
  <section class="hero"><div class="wrap">
    <div>
      <div class="eyebrow"><span class="ms">Cuci karpet gulung &amp; portable</span><span class="en">Rug &amp; portable carpet cleaning</span></div>
      <h1><span class="ms">Karpet kotor? Kami <em>ambil, cuci</em> &amp; hantar balik.</span><span class="en">Dirty rug? We <em>pick it up, clean it</em> and bring it back.</span></h1>
      <p class="sub"><span class="ms">Harga ikut keluasan, dari RM2 sekaki persegi. Kira anggaran dalam beberapa saat, kemudian tempah terus di WhatsApp.</span><span class="en">Priced by area, from RM2 per square foot. Get an estimate in seconds, then book straight on WhatsApp.</span></p>
      <div class="ctas">
        <a class="btn btn-wa wa-link" href="#" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.5 0-3-.4-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.7.3-.2.3-.9.9-.9 2.2s1 2.5 1.1 2.7c.1.2 1.9 2.9 4.6 4 1.7.7 2.4.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
          <span class="ms">Tempah di WhatsApp</span><span class="en">Book on WhatsApp</span>
        </a>
        <a class="btn btn-light" href="#kira"><span class="ms">Kira harga</span><span class="en">Get an estimate</span></a>
      </div>
      <div class="facts">
        <div><b class="tnum">RM2–5</b><span><span class="ms">sekaki persegi</span><span class="en">per sq ft</span></span></div>
        <div><b>4</b><span><span class="ms">jenis karpet</span><span class="en">carpet types</span></span></div>
        <div><b class="tnum">5–7</b><span><span class="ms">hari bekerja</span><span class="en">working days</span></span></div>
      </div>
    </div>
    <svg class="rugfig" viewBox="0 0 440 320" role="img" aria-label="Karpet 6 × 9 kaki = 54 sqft">
      <!-- dimension lines -->
      <g stroke="var(--sky)" stroke-width="1.5" fill="none">
        <path d="M44 30 H330"/><path d="M44 24 V36"/><path d="M330 24 V36"/>
        <path d="M18 54 V262"/><path d="M12 54 H24"/><path d="M12 262 H24"/>
      </g>
      <g fill="var(--sky)" font-family="Lexend, sans-serif" font-size="13" font-weight="500">
        <text x="187" y="22" text-anchor="middle">9 kaki</text>
        <text x="10" y="162" text-anchor="middle" transform="rotate(-90 10 162)">6 kaki</text>
      </g>
      <!-- fringe -->
      <g stroke="var(--rug)" stroke-width="2" stroke-linecap="round">
        <path d="M44 62 h-10 M44 78 h-10 M44 94 h-10 M44 110 h-10 M44 126 h-10 M44 142 h-10 M44 158 h-10 M44 174 h-10 M44 190 h-10 M44 206 h-10 M44 222 h-10 M44 238 h-10 M44 254 h-10"/>
      </g>
      <!-- rug body -->
      <rect x="44" y="54" width="290" height="208" rx="4" fill="var(--rug)"/>
      <rect x="58" y="68" width="262" height="180" rx="2" fill="none" stroke="var(--rug-line)" stroke-width="2"/>
      <rect x="68" y="78" width="242" height="160" rx="2" fill="none" stroke="var(--rug-line)" stroke-width="1" stroke-dasharray="4 5"/>
      <path d="M189 104 L245 158 L189 212 L133 158 Z" fill="var(--rug-line)"/>
      <path d="M189 126 L223 158 L189 190 L155 158 Z" fill="var(--rug)"/>
      <circle cx="189" cy="158" r="8" fill="var(--rug-line)"/>
      <!-- roll -->
      <rect x="326" y="46" width="44" height="224" rx="22" fill="var(--sky)"/>
      <rect x="326" y="46" width="44" height="224" rx="22" fill="none" stroke="var(--rug-line)" stroke-width="1.5" opacity=".35"/>
      <ellipse cx="348" cy="258" rx="22" ry="10" fill="var(--rug)" stroke="var(--rug-line)" stroke-width="1.5"/>
      <path d="M348 258 m-14 0 a14 6 0 1 0 28 0 a10 4.5 0 1 0 -20 0 a6 3 0 1 0 12 0" fill="none" stroke="var(--rug-line)" stroke-width="1.2"/>
      <!-- care tag -->
      <path d="M370 120 C 392 120, 392 150, 384 168" stroke="var(--sky)" stroke-width="1.5" fill="none"/>
      <g transform="translate(356 168) rotate(6)">
        <rect width="78" height="96" rx="8" fill="var(--on-brand)"/>
        <circle cx="39" cy="12" r="4" fill="var(--brand)"/>
        <text x="39" y="40" text-anchor="middle" font-family="Lexend, sans-serif" font-size="20" font-weight="600" fill="var(--brand)">54</text>
        <text x="39" y="55" text-anchor="middle" font-family="Figtree, sans-serif" font-size="11" fill="var(--brand)">sqft</text>
        <line x1="12" y1="64" x2="66" y2="64" stroke="var(--brand)" stroke-dasharray="3 3" opacity=".5"/>
        <text x="39" y="82" text-anchor="middle" font-family="Lexend, sans-serif" font-size="12" font-weight="600" fill="var(--brand)">RM172.80</text>
      </g>
    </svg>
  </div></section>

  <section class="blk" id="harga"><div class="wrap">
    <div class="head">
      <div class="eyebrow"><span class="ms">Senarai harga</span><span class="en">Price list</span></div>
      <h2><span class="ms">Bayar ikut keluasan karpet</span><span class="en">Pay by the size of your rug</span></h2>
      <p class="lede"><span class="ms">Lebar × panjang (kaki) × kadar jenis karpet. Contoh di bawah untuk karpet saiz biasa 5 × 7 kaki (35 sqft).</span><span class="en">Width × length (feet) × the rate for your carpet type. Examples below use a common 5 × 7 ft rug (35 sq ft).</span></p>
    </div>
    <div class="tags" id="tags"></div>
    <p class="note"><span class="ms">Ukuran akhir diambil oleh team kami semasa pickup. Tak pasti jenis karpet anda? Hantar gambar di WhatsApp.</span><span class="en">Our team takes the final measurement at pickup. Not sure which type you have? Send us a photo on WhatsApp.</span></p>
  </div></section>

  <section class="blk calcband" id="kira"><div class="wrap">
    <div class="head">
      <div class="eyebrow"><span class="ms">Kalkulator</span><span class="en">Calculator</span></div>
      <h2><span class="ms">Kira anggaran harga</span><span class="en">Work out your estimate</span></h2>
      <p class="lede"><span class="ms">Tambah setiap karpet, kemudian hantar senarai ini terus ke WhatsApp kami.</span><span class="en">Add each rug, then send the list straight to our WhatsApp.</span></p>
    </div>
    <div class="calc">
      <div class="panel">
        <div id="rows"></div>
        <div class="presets"><span class="ms">Saiz biasa:</span><span class="en">Common sizes:</span>
          <button type="button" class="chip" data-w="4" data-l="6">4 × 6</button>
          <button type="button" class="chip" data-w="5" data-l="7">5 × 7</button>
          <button type="button" class="chip" data-w="6" data-l="9">6 × 9</button>
          <button type="button" class="chip" data-w="8" data-l="10">8 × 10</button>
          <span class="ms">(isi karpet terakhir)</span><span class="en">(fills the last rug)</span>
        </div>
        <button type="button" class="btn btn-ghost addrow" id="addrow">+ <span class="ms">Tambah karpet</span><span class="en">Add another rug</span></button>
      </div>
      <aside class="panel sum" aria-live="polite">
        <div class="eyebrow"><span class="ms">Anggaran</span><span class="en">Estimate</span></div>
        <div class="total tnum" id="total">RM0.00</div>
        <div class="line"><span><span class="ms">Jumlah karpet</span><span class="en">Rugs</span></span><span id="tcount">0</span></div>
        <div class="line"><span><span class="ms">Jumlah keluasan</span><span class="en">Total area</span></span><span id="tarea">0 sqft</span></div>
        <div class="line"><span><span class="ms">Kos cucian</span><span class="en">Cleaning</span></span><span id="tsub">RM0.00</span></div>
        <div class="line"><span>Pickup &amp; <span class="ms">hantar</span><span class="en">delivery</span></span><span id="tfee">RM0.00</span></div>
        <p class="small"><span class="ms">Pickup &amp; hantar percuma untuk tempahan RM150 ke atas. Bawah RM150, caj RM15. Harga muktamad selepas karpet diukur semasa pickup.</span><span class="en">Free pickup &amp; delivery for orders of RM150 and above. Below RM150, a RM15 charge applies. Final price is confirmed after we measure at pickup.</span></p>
        <a class="btn btn-wa wa-link" href="#" target="_blank" rel="noopener" id="wa-send">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.7.3-.2.3-.9.9-.9 2.2s1 2.5 1.1 2.7c.1.2 1.9 2.9 4.6 4 1.7.7 2.4.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
          <span class="ms">Hantar ke WhatsApp</span><span class="en">Send to WhatsApp</span>
        </a>
        <div class="copyline">
          <button type="button" class="btn btn-ghost" id="copymsg"><span class="ms">Salin mesej</span><span class="en">Copy message</span></button>
        </div>
        <div class="status" id="copystatus"></div>
      </aside>
    </div>
  </div></section>

  <section class="blk" id="tempah"><div class="wrap">
    <div class="head">
      <div class="eyebrow"><span class="ms">Tempah pickup</span><span class="en">Book a pickup</span></div>
      <h2><span class="ms">Tempah terus di sini</span><span class="en">Book right here</span></h2>
      <p class="lede"><span class="ms">Karpet dari kalkulator di atas akan dihantar bersama tempahan. Kami sahkan tarikh pickup melalui WhatsApp.</span><span class="en">The rugs from the calculator above are sent with your booking. We confirm the pickup date on WhatsApp.</span></p>
    </div>
    @if (session('booking'))
      <div class="okbox" role="status">
        <b><span class="ms">Tempahan diterima: </span><span class="en">Booking received: </span>{{ session('booking.number') }}</b>
        <span><span class="ms">{{ session('booking.carpets') }} karpet · anggaran {{ session('booking.total') }}. Kami akan WhatsApp anda untuk sahkan pickup.</span><span class="en">{{ session('booking.carpets') }} rugs · estimate {{ session('booking.total') }}. We will WhatsApp you to confirm pickup.</span></span>
        @if (session('booking.email'))<span class="small"><span class="ms">Salinan dihantar ke emel anda.</span><span class="en">A copy was sent to your email.</span></span>@endif
      </div>
    @else
    @if ($errors->any())
      <div class="errbox" role="alert"><b><span class="ms">Semak borang:</span><span class="en">Please check the form:</span></b>
        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form class="panel" method="post" action="{{ route('booking.store') }}" id="bookform" novalidate>
      @csrf
      <input type="hidden" name="items_json" id="items_json">
      <div class="hp" aria-hidden="true"><label for="website">Website</label><input type="text" id="website" name="website" tabindex="-1" autocomplete="off"></div>
      <p class="bookitems" id="bookitems"></p>
      <div class="book">
        <label class="f" for="b-name"><span><span class="ms">Nama</span><span class="en">Name</span></span><input type="text" id="b-name" name="name" required maxlength="120" value="{{ old('name') }}" autocomplete="name"></label>
        <label class="f" for="b-phone"><span><span class="ms">Telefon (WhatsApp)</span><span class="en">Phone (WhatsApp)</span></span><input type="tel" id="b-phone" name="phone" required maxlength="30" value="{{ old('phone') }}" autocomplete="tel" placeholder="012-345 6789"></label>
        <label class="f" for="b-email"><span><span class="ms">Emel (pilihan)</span><span class="en">Email (optional)</span></span><input type="email" id="b-email" name="email" maxlength="150" value="{{ old('email') }}" autocomplete="email"></label>
        <label class="f" for="b-area"><span><span class="ms">Kawasan</span><span class="en">Area</span></span><select id="b-area" name="area">@foreach (config('cucikarpet.areas') as $a)<option @selected(old('area') === $a)>{{ $a }}</option>@endforeach</select></label>
        <label class="f full" for="b-address"><span><span class="ms">Alamat pickup</span><span class="en">Pickup address</span></span><textarea id="b-address" name="address" required maxlength="500" autocomplete="street-address">{{ old('address') }}</textarea></label>
        <label class="f" for="b-date"><span><span class="ms">Tarikh pickup pilihan</span><span class="en">Preferred pickup date</span></span><input type="date" id="b-date" name="pickup_date" value="{{ old('pickup_date') }}" min="{{ now()->toDateString() }}"></label>
        <label class="f" for="b-slot"><span>Slot</span><select id="b-slot" name="pickup_slot">@foreach (config('cucikarpet.slots') as $sl)<option @selected(old('pickup_slot') === $sl)>{{ $sl }}</option>@endforeach</select></label>
        <label class="f full" for="b-notes"><span><span class="ms">Nota (pilihan)</span><span class="en">Notes (optional)</span></span><textarea id="b-notes" name="notes" maxlength="500" placeholder="">{{ old('notes') }}</textarea></label>
      </div>
      <button type="submit" class="btn btn-wa" style="justify-self:start"><span class="ms">Hantar tempahan</span><span class="en">Send booking</span></button>
      <p class="small" style="color:var(--muted);font-size:.85rem"><span class="ms">Harga muktamad selepas karpet diukur semasa pickup. Bayar penuh semasa delivery.</span><span class="en">Final price is confirmed after measuring at pickup. Pay in full on delivery.</span></p>
    </form>
    @endif
  </div></section>

  <section class="blk" id="cara"><div class="wrap">
    <div class="head">
      <div class="eyebrow"><span class="ms">Cara ia berfungsi</span><span class="en">How it works</span></div>
      <h2><span class="ms">Dari rumah anda dan kembali, bersih</span><span class="en">From your door and back, clean</span></h2>
    </div>
    <ol class="steps">
      <li><h3><span class="ms">Tempah di WhatsApp</span><span class="en">Book on WhatsApp</span></h3><p><span class="ms">Hantar anggaran dari kalkulator atau gambar karpet. Kami sahkan tarikh pickup.</span><span class="en">Send the calculator estimate or a photo of your rug. We confirm a pickup date.</span></p></li>
      <li><h3><span class="ms">Kami ambil</span><span class="en">We pick it up</span></h3><p><span class="ms">Team kami datang, ukur setiap karpet dan pasang tag supaya tidak tertukar.</span><span class="en">Our team comes over, measures each rug and tags it so nothing gets mixed up.</span></p></li>
      <li><h3><span class="ms">Cuci &amp; keringkan</span><span class="en">Wash &amp; dry</span></h3><p><span class="ms">Dicuci ikut jenis fabrik, kemudian dikeringkan sepenuhnya sebelum diperiksa.</span><span class="en">Washed to suit the fabric, then dried completely before a final check.</span></p></li>
      <li><h3><span class="ms">Hantar balik</span><span class="en">Delivered back</span></h3><p><span class="ms">Karpet digulung kemas dan dihantar ke pintu anda. Bayar penuh semasa penghantaran, tiada deposit.</span><span class="en">Rolled up neatly and delivered to your door. Pay in full on delivery, no deposit.</span></p></li>
    </ol>
  </div></section>

  <section class="blk" style="padding-top:0"><div class="wrap">
    <div class="info">
      <div><div class="eyebrow"><span class="ms">Kawasan servis</span><span class="en">Service area</span></div><div class="v"><span class="ms">Lembah Klang: Kuala Lumpur &amp; Selangor</span><span class="en">Klang Valley: Kuala Lumpur &amp; Selangor</span></div><p class="small" style="color:var(--muted);font-size:.9rem"><span class="ms">Luar kawasan? Tanya kami di WhatsApp.</span><span class="en">Outside the area? Ask us on WhatsApp.</span></p></div>
      <div><div class="eyebrow"><span class="ms">Tempoh siap</span><span class="en">Turnaround</span></div><div class="v"><span class="ms">5–7 hari bekerja</span><span class="en">5–7 working days</span></div><p style="color:var(--muted);font-size:.9rem"><span class="ms">Bergantung pada cuaca dan jenis karpet.</span><span class="en">Depends on weather and carpet type.</span></p></div>
      <div><div class="eyebrow">Pickup &amp; <span class="ms">hantar</span><span class="en">delivery</span></div><div class="v"><span class="ms">Percuma dari RM150</span><span class="en">Free from RM150</span></div><p style="color:var(--muted);font-size:.9rem"><span class="ms">Bawah RM150, caj RM15 untuk ambil &amp; hantar.</span><span class="en">Below RM150, RM15 covers pickup and delivery.</span></p></div>
    </div>
  </div></section>

  <section class="blk alt" id="soalan"><div class="wrap">
    <div class="head">
      <div class="eyebrow"><span class="ms">Soalan lazim</span><span class="en">FAQ</span></div>
      <h2><span class="ms">Soalan yang selalu ditanya</span><span class="en">Questions people ask</span></h2>
    </div>
    <div class="faq">
      <details open><summary><span class="ms">Bagaimana harga dikira?</span><span class="en">How is the price worked out?</span></summary><p><span class="ms">Lebar × panjang karpet dalam kaki, didarab dengan kadar jenis karpet. Contoh: karpet Standard 6 × 9 kaki = 54 sqft × RM3.20 = RM172.80. Pickup &amp; hantar percuma untuk tempahan RM150 ke atas; bawah RM150, caj RM15. Team kami ukur semula semasa pickup dan sahkan harga akhir.</span><span class="en">Rug width × length in feet, multiplied by the rate for its type. Example: a 6 × 9 ft Standard rug = 54 sq ft × RM3.20 = RM172.80. Pickup and delivery are free for orders of RM150 and above; below RM150 there is a RM15 charge. Our team re-measures at pickup and confirms the final price.</span></p></details>
      <details><summary><span class="ms">Saya tak pasti jenis karpet saya.</span><span class="en">I'm not sure what type my rug is.</span></summary><p><span class="ms">Hantar gambar depan dan belakang karpet di WhatsApp. Kami akan beritahu kategori dan anggaran harga.</span><span class="en">Send a photo of the front and back on WhatsApp. We'll tell you the category and an estimate.</span></p></details>
      <details><summary><span class="ms">Berapa lama karpet saya akan siap?</span><span class="en">How long until my rug is back?</span></summary><p><span class="ms">Biasanya 5–7 hari bekerja dari pickup. Karpet tebal seperti shaggy mungkin ambil masa lebih lama untuk kering.</span><span class="en">Usually 5–7 working days from pickup. Thick rugs such as shaggy can take longer to dry.</span></p></details>
      <details><summary><span class="ms">Cuci karpet dinding-ke-dinding?</span><span class="en">Do you clean wall-to-wall carpet?</span></summary><p><span class="ms">Servis kami untuk karpet gulung dan portable yang boleh dibawa ke tempat cucian kami.</span><span class="en">Our service is for rugs and portable carpets that can be taken to our cleaning facility.</span></p></details>
      <details><summary><span class="ms">Bagaimana cara bayar?</span><span class="en">How do I pay?</span></summary><p><span class="ms">Tiada deposit. Bayar penuh semasa karpet dihantar balik, secara tunai, DuitNow QR atau pindahan bank.</span><span class="en">No deposit. Pay in full when your rug is delivered back, by cash, DuitNow QR or bank transfer.</span></p></details>
    </div>
  </div></section>

  <section class="cta"><div class="wrap">
    <h2><span class="ms">Karpet anda patut bersih minggu ini.</span><span class="en">Your rug could be clean this week.</span></h2>
    <a class="btn btn-light wa-link" href="#" target="_blank" rel="noopener"><span class="ms">Tempah pickup</span><span class="en">Book a pickup</span></a>
  </div></section>

  <footer><div class="wrap">
    <img class="logo" src="{{ asset('assets/cucikarpet-logo.png') }}" alt="cucikarpet.co">
    <div>WhatsApp: <span class="num" id="wanum"></span><button type="button" id="copynum"><span class="ms">Salin</span><span class="en">Copy</span></button></div>
    <div style="opacity:.75">© 2026 cucikarpet.co</div>
  </div></footer>
</main>


<script>window.CK_CFG = @json(["rates" => $rates, "freeFrom" => $freeFrom, "pickupFee" => $pickupFee]);</script>
<script>
(function(){
  var WA = "601174493244";
  var WA_DISPLAY = "+60 11-7449 3244";
  var CFG = window.CK_CFG || {}; var FREE_FROM = CFG.freeFrom || 150, PICKUP_FEE = CFG.pickupFee || 15;
  function feeFor(sub){ return sub>0 && sub<FREE_FROM ? PICKUP_FEE : 0; }
  var TYPES = [
    {id:"wool",   rate:5.00, ms:"Handmade / Wool / Silk", en:"Handmade / Wool / Silk", dms:"Karpet tenunan tangan, bulu dan sutera. Dicuci dengan lebih teliti.", den:"Hand-woven, wool and silk rugs. Cleaned with extra care."},
    {id:"shaggy", rate:3.50, ms:"Shaggy", en:"Shaggy", dms:"Bulu panjang dan tebal yang perlukan masa kering lebih lama.", den:"Long, thick pile that needs more drying time."},
    {id:"std",    rate:3.20, ms:"Standard / Turki", en:"Standard / Turkish", dms:"Karpet mesin biasa dan karpet Turki untuk ruang tamu.", den:"Everyday machine-made and Turkish rugs for the living room.", hl:true},
    {id:"velvet", rate:2.00, ms:"Velvet / Tatami", en:"Velvet / Tatami", dms:"Karpet nipis, velvet dan tikar tatami.", den:"Thin rugs, velvet and tatami mats."}
  ];
  TYPES.forEach(function(t){ t.code = t.id === "std" ? "standard" : t.id; if (CFG.rates && CFG.rates[t.code]) t.rate = CFG.rates[t.code]; });
  var byId = {}; TYPES.forEach(function(t){ byId[t.id]=t; });
  var rows = [{type:"std", w:5, l:7, q:1}];
  var lang = "ms";
  try { var s = localStorage.getItem("ck-lang"); if (s==="en"||s==="ms") lang = s; } catch(e){}

  function rm(n){ return "RM" + n.toFixed(2); }
  function num(v){ var n = parseFloat(v); return isFinite(n) && n>0 ? n : 0; }
  function fmt(n){ return (Math.round(n*100)/100).toString(); }
  function t(ms,en){ return lang==="en"?en:ms; }

  // price tags
  var tagsEl = document.getElementById("tags");
  tagsEl.innerHTML = TYPES.map(function(tp){
    return '<div class="tag'+(tp.hl?' hl':'')+'">'+
      (tp.hl?'<span class="badge"><span class="ms">Paling biasa</span><span class="en">Most common</span></span>':'')+
      '<div class="kind"><span class="ms">'+tp.ms+'</span><span class="en">'+tp.en+'</span></div>'+
      '<div class="desc"><span class="ms">'+tp.dms+'</span><span class="en">'+tp.den+'</span></div>'+
      '<div class="rate tnum">RM'+tp.rate.toFixed(2)+' <small>/ sqft</small></div>'+
      '<div class="eg tnum"><span class="ms">5 × 7 kaki = </span><span class="en">5 × 7 ft = </span><b>'+rm(tp.rate*35)+'</b></div>'+
    '</div>';
  }).join("");

  // calculator rows
  var rowsEl = document.getElementById("rows");
  function renderRows(){
    rowsEl.innerHTML = rows.map(function(r,i){
      var opts = TYPES.map(function(tp){ return '<option value="'+tp.id+'"'+(tp.id===r.type?' selected':'')+'>'+t(tp.ms,tp.en)+' · RM'+tp.rate.toFixed(2)+'</option>'; }).join("");
      return '<div class="row" data-i="'+i+'">'+
        '<label class="f ftype" for="r'+i+'-type">'+t("Jenis karpet","Carpet type")+'<select id="r'+i+'-type" data-k="type">'+opts+'</select></label>'+
        '<label class="f" for="r'+i+'-w">'+t("Lebar (kaki)","Width (ft)")+'<input id="r'+i+'-w" data-k="w" type="number" inputmode="decimal" min="0" step="0.5" value="'+r.w+'"></label>'+
        '<span class="x">×</span>'+
        '<label class="f" for="r'+i+'-l">'+t("Panjang (kaki)","Length (ft)")+'<input id="r'+i+'-l" data-k="l" type="number" inputmode="decimal" min="0" step="0.5" value="'+r.l+'"></label>'+
        '<label class="f" for="r'+i+'-q">'+t("Kuantiti","Qty")+'<input id="r'+i+'-q" data-k="q" type="number" inputmode="numeric" min="1" step="1" value="'+r.q+'"></label>'+
        '<div class="out"><b id="r'+i+'-amt">RM0.00</b><span id="r'+i+'-sq">0 sqft</span></div>'+
        '<button type="button" class="rm" data-rm="'+i+'" aria-label="'+t("Buang karpet ini","Remove this rug")+'"'+(rows.length<2?' hidden':'')+'>✕</button>'+
      '</div>';
    }).join("");
    update();
  }
  function lineSq(r){ return num(r.w)*num(r.l)*Math.max(1,Math.floor(num(r.q))||1); }
  function update(){
    var total=0, area=0, count=0;
    rows.forEach(function(r,i){
      var sq=lineSq(r), amt=sq*byId[r.type].rate;
      total+=amt; area+=sq; count+=Math.max(1,Math.floor(num(r.q))||1);
      var a=document.getElementById("r"+i+"-amt"), b=document.getElementById("r"+i+"-sq");
      if(a) a.textContent=rm(amt); if(b) b.textContent=fmt(sq)+" sqft";
    });
    var fee=feeFor(total);
    document.getElementById("tsub").textContent=rm(total);
    document.getElementById("tfee").textContent= total>0 && !fee ? t("Percuma","Free") : rm(fee);
    document.getElementById("total").textContent=rm(total+fee);
    document.getElementById("tarea").textContent=fmt(area)+" sqft";
    document.getElementById("tcount").textContent=count;
    var href="https://wa.me/"+WA+"?text="+encodeURIComponent(message(total));
    var links=document.querySelectorAll(".wa-link");
    for(var k=0;k<links.length;k++) links[k].href = links[k].id==="wa-send" ? href : "https://wa.me/"+WA+"?text="+encodeURIComponent(t("Salam cucikarpet.co, saya nak tempah cuci karpet.","Hi cucikarpet.co, I'd like to book a carpet cleaning."));
  }
  function message(total){
    var lines=[t("Salam cucikarpet.co, saya nak tempah cuci karpet:","Hi cucikarpet.co, I'd like to book a carpet cleaning:")];
    rows.forEach(function(r,i){
      var tp=byId[r.type], q=Math.max(1,Math.floor(num(r.q))||1), sq=lineSq(r);
      lines.push((i+1)+". "+t(tp.ms,tp.en)+" "+fmt(num(r.w))+"×"+fmt(num(r.l))+" "+t("kaki","ft")+(q>1?" ×"+q:"")+" = "+fmt(sq)+" sqft ("+rm(sq*tp.rate)+")");
    });
    var fee=feeFor(total);
    lines.push(t("Kos cucian: ","Cleaning: ")+rm(total));
    lines.push(t("Pickup & hantar: ","Pickup & delivery: ")+(fee?rm(fee):t("Percuma","Free")));
    lines.push(t("Anggaran jumlah: ","Estimated total: ")+rm(total+fee));
    lines.push("");
    lines.push(t("Nama:","Name:"));
    lines.push(t("Alamat pickup:","Pickup address:"));
    lines.push(t("Tarikh pilihan:","Preferred date:"));
    return lines.join("\n");
  }
  rowsEl.addEventListener("input",function(e){
    var row=e.target.closest(".row"); if(!row) return;
    var i=+row.getAttribute("data-i"), k=e.target.getAttribute("data-k"); if(!k) return;
    rows[i][k]=e.target.value; update();
  });
  rowsEl.addEventListener("click",function(e){
    var b=e.target.closest("[data-rm]"); if(!b) return;
    rows.splice(+b.getAttribute("data-rm"),1); renderRows();
  });
  document.getElementById("addrow").addEventListener("click",function(){
    rows.push({type:"std",w:5,l:7,q:1}); renderRows();
    var last=document.getElementById("r"+(rows.length-1)+"-type"); if(last) last.focus();
  });
  document.querySelectorAll(".chip").forEach(function(c){
    c.addEventListener("click",function(){
      var r=rows[rows.length-1]; r.w=+c.getAttribute("data-w"); r.l=+c.getAttribute("data-l");
      var i=rows.length-1; document.getElementById("r"+i+"-w").value=r.w; document.getElementById("r"+i+"-l").value=r.l; update();
    });
  });

  function copyText(txt, statusEl, okMsg){
    function fallback(){
      var ta=document.createElement("textarea"); ta.value=txt; ta.setAttribute("readonly",""); ta.style.position="fixed"; ta.style.opacity="0";
      document.body.appendChild(ta); ta.select();
      var ok=false; try{ ok=document.execCommand("copy"); }catch(e){}
      document.body.removeChild(ta);
      if(statusEl) statusEl.textContent = ok ? okMsg : t("Tak dapat salin. Sila salin secara manual.","Couldn't copy. Please copy it manually.");
    }
    try{
      navigator.clipboard.writeText(txt).then(function(){ if(statusEl) statusEl.textContent=okMsg; }, fallback);
    }catch(e){ fallback(); }
  }
  document.getElementById("copymsg").addEventListener("click",function(){
    var total=rows.reduce(function(s,r){return s+lineSq(r)*byId[r.type].rate;},0);
    copyText(message(total), document.getElementById("copystatus"), t("Mesej disalin. Tampal di WhatsApp kami.","Message copied. Paste it into our WhatsApp."));
  });
  document.getElementById("wanum").textContent=WA_DISPLAY;
  document.getElementById("copynum").addEventListener("click",function(){ copyText(WA_DISPLAY.replace(/[^+\d]/g,""), null, ""); this.querySelector(lang==="en"?".en":".ms").textContent=t("Disalin","Copied"); });

  // booking form: send the calculator rows with the booking
  var bookform = document.getElementById("bookform");
  function bookSummary(){
    var el=document.getElementById("bookitems"); if(!el) return;
    var n=rows.reduce(function(s,r){return s+(lineSq(r)>0?Math.max(1,Math.floor(num(r.q))||1):0);},0);
    var sub=rows.reduce(function(s,r){return s+lineSq(r)*byId[r.type].rate;},0);
    el.textContent = n ? t("Karpet dari kalkulator: "+n+" · anggaran "+rm(sub+feeFor(sub)),"Rugs from the calculator: "+n+" · estimate "+rm(sub+feeFor(sub))) : t("Tambah karpet dalam kalkulator di atas dahulu.","Add your rugs in the calculator above first.");
  }
  if (bookform) {
    bookform.addEventListener("submit", function(e){
      var items = rows.filter(function(r){return lineSq(r)>0;}).map(function(r){return {type: byId[r.type].code, w: num(r.w), l: num(r.l), q: Math.max(1,Math.floor(num(r.q))||1)};});
      if (!items.length) { e.preventDefault(); document.getElementById("kira").scrollIntoView(); return; }
      document.getElementById("items_json").value = JSON.stringify(items);
    });
    document.getElementById("rows").addEventListener("input", bookSummary);
    document.getElementById("rows").addEventListener("click", function(){ setTimeout(bookSummary, 0); });
    document.getElementById("addrow").addEventListener("click", function(){ setTimeout(bookSummary, 0); });
    document.querySelectorAll(".chip").forEach(function(c){ c.addEventListener("click", function(){ setTimeout(bookSummary, 0); }); });
  }

  // language
  function setLang(l){
    lang=l; document.documentElement.lang = l==="en"?"en":"ms";
    document.getElementById("lang-ms").setAttribute("aria-pressed", l!=="en");
    document.getElementById("lang-en").setAttribute("aria-pressed", l==="en");
    try{ localStorage.setItem("ck-lang",l); }catch(e){}
    renderRows(); bookSummary();
  }
  document.getElementById("lang-ms").addEventListener("click",function(){ setLang("ms"); });
  document.getElementById("lang-en").addEventListener("click",function(){ setLang("en"); });

  // views: #plan shows the build plan; anything else shows the site
  var PLAN_IDS = ["plan","p-apps","p-flow","p-admin","p-team","p-data","p-stack","p-phase","p-todo"];
  function route(){ return;
    var h=(location.hash||"").slice(1);
    var plan = PLAN_IDS.indexOf(h)>-1;
    document.getElementById("view-site").hidden = plan;
    document.getElementById("view-plan").hidden = !plan;
    document.getElementById("sitenav").style.visibility = plan ? "hidden" : "";
    document.getElementById("tab-site").setAttribute("aria-current", plan?"false":"page");
    document.getElementById("tab-plan").setAttribute("aria-current", plan?"page":"false");
    if(h==="plan"||h==="site") window.scrollTo(0,0);
  }
  window.addEventListener("hashchange",route);
  setLang(lang); route();
})();
</script>

</body>
</html>
