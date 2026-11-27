<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function admin_theme_store_css(array $data, bool $storefront): string
{
    $css = site_theme_css('light', $data['light'])
        . "@media (prefers-color-scheme: dark) {\n" . site_theme_css('dark', $data['dark']) . "}\n";
    $css .= <<<'CSS'
.site-header { border-block-start: .3rem solid var(--accent); }
.site-main { padding-block: clamp(1.6rem, 4vw, 3.5rem); }
.site-main:has(.theme-store-hero) > h1 { position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); }
.theme-store-hero { display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; color: var(--ink); background: var(--accent-wash); }
.theme-store-hero > div { align-self: center; padding: clamp(1.35rem, 5vw, 4rem); }
.theme-store-kicker { color: var(--accent); font-weight: 700; }
.theme-store-hero h2 { margin-block: .3rem .9rem; font-size: clamp(2rem, 5vw, 4.65rem); line-height: 1.03; letter-spacing: -.045em; }
.theme-store-hero img { display: block; width: 100%; height: 100%; min-height: 20rem; max-height: 43rem; object-fit: cover; }
.theme-store-hero .button { margin-block-start: .65rem; }
.theme-store-demo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 15rem), 1fr)); gap: 1rem; }
.theme-store-demo-grid article { display: flex; flex-direction: column; padding: 1rem; background: var(--surface); border: 1px solid var(--line); }
.theme-store-demo-grid img { display: block; width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-radius: inherit; margin-block-end: .7rem; }
.theme-store-demo-grid h2 { margin-block: .15rem .42rem; font-size: 1.2rem; line-height: 1.2; }
.theme-store-demo-grid p { flex: 1; color: var(--muted); font-size: .92rem; }
.theme-store-demo-grid strong { color: var(--accent); font-size: 1.14rem; }
.theme-store-details { max-width: 44rem; padding: clamp(1rem, 4vw, 2.5rem); background: var(--accent-wash); }
@media (max-width: 42rem) {
    .theme-store-hero { grid-template-columns: 1fr; }
    .theme-store-hero img { min-height: 0; max-height: 22rem; aspect-ratio: 4 / 3; }
}
CSS;
    $styles = [
        'trattoria' => '
.theme-store-hero, .theme-store-demo-grid article, .theme-store-details { border-radius: .85rem; }
.theme-store-hero h2 { font-family: Georgia, serif; font-weight: 800; }
.theme-store-kicker { text-transform: uppercase; letter-spacing: .12em; font-size: .82rem; }',
        'atelier' => '
.theme-store-hero { grid-template-columns: 1fr; text-align: center; border-radius: 6rem 6rem .7rem .7rem; }
.theme-store-hero > div { margin-inline: auto; max-width: 45rem; }
.theme-store-hero img { aspect-ratio: 2 / 1; min-height: 0; }
.theme-store-hero h2, .theme-store-demo-grid h2 { font-family: Georgia, serif; font-weight: 500; }
.theme-store-demo-grid article, .theme-store-details { border-radius: 1.5rem 1.5rem .55rem .55rem; }',
        'expedition' => '
.theme-store-hero { grid-template-columns: 1fr; position: relative; isolation: isolate; min-height: clamp(26rem, 58vw, 39rem); background: #17231d; color: #fff; }
.theme-store-hero > div { position: relative; align-self: end; max-width: 44rem; z-index: 1; }
.theme-store-kicker { color: #dce987; letter-spacing: .09em; }
.theme-store-hero img { position: absolute; inset: 0; }
.theme-store-hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(20deg, #0d1a16 8%, transparent 90%); }
.theme-store-hero h2 { text-transform: uppercase; font-weight: 850; }
.theme-store-demo-grid article { border-block-end: .25rem solid var(--accent); }',
    ];
    $css .= $styles[$data['style']];
    if (!$storefront) return $css;
    $css .= '
.shop-catalog .shop-item { border-color: var(--line); background: var(--surface); }
.shop-catalog .shop-item:hover { border-color: var(--accent); }
.shop-catalog .shop-item .shop-offer button { background: var(--accent); color: var(--on-accent); }
.shop-catalog .shop-item .shop-offer button:hover { filter: brightness(.88); }
.shop-catalog .shop-item .shop-offer > strong { color: var(--accent); }';
    $css .= $data['style'] === 'trattoria' ? '
.shop-catalog .shop-item img { border-radius: .6rem; }
.shop-catalog .shop-item h3 { font-family: Georgia, serif; }' : '
.shop-catalog .shop-item, .shop-catalog .shop-item img, .shop-catalog .shop-item .shop-offer button { border-radius: .2rem; }
.shop-catalog .shop-item h3 { font-weight: 800; }';
    return $css;
}
