<?php
declare(strict_types=1);

function ewcLine(string $value): string
{
    return trim((string) preg_replace('/[\r\n\t\x{0085}\x{2028}\x{2029}]+/u', ' ', $value));
}

function ewcHtml(string $value): string
{
    return strtr(htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), ['[' => '&#91;', ']' => '&#93;']);
}

function ewcPublicLink(string $value): string
{
    if (preg_match('/[\x00-\x20\x7f\\\\]/', $value)) {
        throw new EwcInput('Link contains unsupported characters.');
    }
    $url = parse_url($value);
    if (!is_array($url) || isset($url['user']) || isset($url['pass'])) {
        throw new EwcInput('Use an HTTPS link or a path starting with one slash.');
    }
    if ((isset($url['scheme'], $url['host']) && strtolower($url['scheme']) === 'https') ||
        (!isset($url['scheme'], $url['host']) && preg_match('~^/(?!/)~', $value))) {
        return $value;
    }
    throw new EwcInput('Use an HTTPS link or a path starting with one slash.');
}

function ewcBlocks(array $blocks): string
{
    $output = [];
    foreach ($blocks as $block) {
        if (isset($block->imageUrl) !== isset($block->imageAlt) ||
            isset($block->linkUrl) !== isset($block->linkLabel)) {
            throw new EwcInput('Provide imageAlt with imageUrl and linkLabel with linkUrl.');
        }
        $copy = '';
        if (isset($block->heading)) {
            $copy .= '<h2>' . ewcHtml($block->heading) . '</h2>';
        }
        foreach (preg_split('/\R\s*\R/u', trim($block->text)) as $paragraph) {
            $copy .= '<p>' . ewcHtml($paragraph) . '</p>';
        }
        if (isset($block->linkUrl)) {
            $copy .= '<a class="button ewc-mcp-button" href="' . ewcHtml(ewcPublicLink($block->linkUrl)) . '">' .
                ewcHtml($block->linkLabel) . '</a>';
        }
        $image = '';
        if (isset($block->imageUrl)) {
            $loading = $block->style === 'hero' && !$output ? '' : ' loading="lazy"';
            $image = '<img src="' . ewcHtml(ewcPublicLink($block->imageUrl)) . '" alt="' .
                ewcHtml($block->imageAlt) . '"' . $loading . ' decoding="async">';
        }
        $output[] = '<section class="ewc-mcp-block ewc-mcp-' . $block->style . ($image === '' ? '' : ' ewc-mcp-with-image') .
            '"><div class="ewc-mcp-copy">' . $copy . '</div>' . $image . '</section>';
    }
    return implode("\n", $output) . "\n";
}

function ewcComponentCss(): string
{
    return <<<'CSS'
.ewc-mcp-block {
    box-sizing: border-box;
    margin: 1.5rem 0;
    padding: clamp(1rem, 3.75vw, 3rem);
    border-radius: var(--ewc-mcp-radius, 16px);
    background: var(--ewc-mcp-surface, var(--surface, transparent));
    overflow-wrap: anywhere;
}
.ewc-mcp-block h2 { margin: 0 0 .65em; line-height: 1.08; letter-spacing: -.025em; }
.ewc-mcp-block p { max-width: 65ch; line-height: 1.65; }
.ewc-mcp-hero h2 { font-size: clamp(1.85rem, 4.8vw, 3.65rem); }
.ewc-mcp-callout { border-inline-start: 4px solid var(--accent, currentColor); }
.ewc-mcp-with-image { display: grid; gap: clamp(1rem, 4vw, 3rem); align-items: center; }
.ewc-mcp-with-image img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-radius: inherit; }
.ewc-mcp-copy > :last-child { margin-bottom: 0; }
.ewc-mcp-button {
    display: inline-flex;
    align-items: center;
    min-height: 44px;
    padding: .55em 1.1em;
    border: 1px solid currentColor;
    border-radius: var(--ewc-mcp-radius, 12px);
    font-weight: 700;
}
.ewc-mcp-button:focus-visible { outline: 2px solid currentColor; outline-offset: 3px; }
.ewc-mcp-price { font-weight: 700; font-size: 1.4em; }
@media (min-width: 760px) {
    .ewc-mcp-with-image { grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); }
}
CSS;
}

function ewcDesignCss(stdClass $design): string
{
    foreach (['accent', 'background', 'surface', 'textColor'] as $name) {
        if (!preg_match('/\A#[a-f0-9]{6}\z/i', $design->$name)) {
            throw new EwcInput('Expected a six-digit hex color in ' . $name . '.');
        }
    }
    $font = ($design->font ?? 'sans') === 'serif' ? 'Georgia, serif' : 'system-ui, sans-serif';
    $radius = $design->radius ?? 16;
    $channels = array_map('hexdec', str_split(substr($design->background, 1), 2));
    $scheme = ($channels[0] * 299 + $channels[1] * 587 + $channels[2] * 114) < 128000 ? 'dark' : 'light';
    $palette = site_theme_css($scheme, [
        'paper' => $design->background,
        'surface' => $design->surface,
        'ink' => $design->textColor,
        'muted' => ewcMixColor($design->textColor, $design->surface, .78),
        'accent' => $design->accent,
        'accent-wash' => ewcMixColor($design->accent, $design->surface, .14),
        'line' => ewcMixColor($design->textColor, $design->surface, .20),
    ]);
    return $palette . <<<CSS
:root {
    --site-font: {$font};
    --site-font-heading: {$font};
    --ewc-mcp-radius: {$radius}px;
}
.ewc-mcp-block { color: var(--ink); }
.ewc-mcp-button { color: var(--on-accent); background: var(--accent); border-color: var(--accent); }
.ewc-mcp-button:hover { filter: brightness(.9); }
CSS
        . "\n\n" . ewcComponentCss() . "\n";
}

function ewcMixColor(string $front, string $back, float $weight): string
{
    $mixed = '#';
    foreach ([1, 3, 5] as $offset) {
        $a = hexdec(substr($front, $offset, 2));
        $b = hexdec(substr($back, $offset, 2));
        $mixed .= sprintf('%02x', (int) round($a * $weight + $b * (1 - $weight)));
    }
    return $mixed;
}

function ewcManagedCss(stdClass $design): string
{
    return "/* EWC MCP: BEGIN */\n" . ewcDesignCss($design) . "/* EWC MCP: END */\n";
}
