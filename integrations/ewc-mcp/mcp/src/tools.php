<?php
declare(strict_types=1);

function ewcSchema(array $properties, array $required = []): array
{
    return ['type' => 'object', 'properties' => (object) $properties, 'required' => $required,
        'additionalProperties' => false];
}

function ewcString(int $max, string $description = ''): array
{
    return ['type' => 'string', 'minLength' => 1, 'maxLength' => $max, 'description' => $description];
}

function ewcList(array $item, int $max): array
{
    return ['type' => 'array', 'items' => $item, 'minItems' => 1, 'maxItems' => $max];
}

function ewcTools(): array
{
    $path = ewcString(180, 'Logical CMS path such as /news/ or /news/article/.');
    $themeId = ewcString(48, 'Lowercase Latin letters, digits, hyphens or underscores.');
    $block = ewcSchema([
        'style' => ['type' => 'string', 'enum' => ['hero', 'text', 'callout']],
        'heading' => ewcString(160),
        'text' => ewcString(4800, 'Plain text. Separate paragraphs with a blank line.'),
        'imageUrl' => ewcString(500, 'HTTPS URL or a site-root-relative path to an existing image.'),
        'imageAlt' => ewcString(160),
        'linkUrl' => ewcString(500, 'HTTPS URL or a site-root-relative path.'),
        'linkLabel' => ewcString(80),
    ], ['style', 'text']);
    $page = [
        'path' => $path, 'title' => ewcString(160), 'heading' => ewcString(160),
        'description' => ewcString(300), 'menuLabel' => ewcString(80),
        'menuVisible' => ['type' => 'boolean'], 'order' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 9999],
        'listChildren' => ['type' => 'boolean', 'description' => 'Include the native list of child pages at the end.'],
        'blocks' => ewcList($block, 12),
    ];
    $product = [
        'path' => $path, 'title' => ewcString(160), 'description' => ewcString(300),
        'sku' => ewcString(80), 'price' => ewcString(16, 'Positive decimal amount, e.g. 1290.50.'),
        'blocks' => ewcList($block, 12),
    ];
    $design = [
        'themeId' => $themeId, 'name' => ewcString(80),
        'accent' => ewcString(7, 'Six-digit hex color, for example #315647.'),
        'background' => ewcString(7), 'surface' => ewcString(7), 'textColor' => ewcString(7),
        'font' => ['type' => 'string', 'enum' => ['sans', 'serif']],
        'radius' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 32],
    ];
    $designRequired = ['themeId', 'name', 'accent', 'background', 'surface', 'textColor'];
    $draftId = ewcString(32);
    $definitions = [
        'list_pages' => ['List EWC-lite pages and sections in the active content set, with paths and titles. Use prefix and offset when needed.',
            ewcSchema(['prefix' => $path, 'offset' => ['type' => 'integer', 'minimum' => 0]]), true],
        'list_themes' => ['List installed native design themes and identify the active theme and its content scope.',
            ewcSchema([]), true],
        'list_shop_categories' => ['List categories of the native EWC-lite Shop module. Use their IDs when adding products.',
            ewcSchema(['offset' => ['type' => 'integer', 'minimum' => 0]]), true],
        'draft_shop_category' => ['Prepare a new category in the native shop, optionally under an existing category.',
            ewcSchema(['name' => ewcString(80), 'slug' => ewcString(64), 'parentId' => ewcString(64),
                'description' => ewcString(400)], ['name', 'slug']), false],
        'draft_shop_product' => ['Prepare a native Shop product and its purchasable offer. Check list_shop_categories first. Price is in the shop currency.',
            ewcSchema([
                'categoryId' => ewcString(64), 'slug' => ewcString(64), 'title' => ewcString(160),
                'summary' => ewcString(320), 'blocks' => ewcList($block, 12),
                'imageUrl' => ewcString(500, 'HTTPS URL or site-root-relative URL for the main photo.'),
                'sku' => ewcString(80), 'price' => ewcString(16),
                'stock' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 99999999],
                'type' => ['type' => 'string', 'enum' => ['physical', 'service', 'digital']],
                'active' => ['type' => 'boolean', 'description' => 'Set to true to make the product visible after publication.'],
            ], ['categoryId', 'slug', 'title', 'sku', 'price', 'stock', 'type', 'active']), false],
        'draft_page' => ['Prepare a new CMS page in an existing section of the active content set. Use a new logical path.',
            ewcSchema($page, ['path', 'title', 'blocks']), false],
        'draft_add_content' => ['Prepare additional blocks at the end of an existing EWC-lite page.',
            ewcSchema(['path' => $path, 'blocks' => ewcList($block, 12)], ['path', 'blocks']), false],
        'draft_product' => ['Prepare a new native EWC-lite product page with a price and SKU in an existing section.',
            ewcSchema($product, ['path', 'title', 'sku', 'price', 'blocks']), false],
        'draft_design' => ['Prepare a new design theme with a site palette, font and radius. The theme uses shared pages when activated.',
            ewcSchema($design, $designRequired), false],
        'draft_restyle' => ['Prepare new styling on the active theme without replacing its pages. Requires an active custom theme.',
            ewcSchema(array_diff_key($design, ['themeId' => true, 'name' => true]), ['accent', 'background', 'surface', 'textColor']), false],
        'draft_site' => ['Prepare a complete native EWC-lite theme with its own home page, sections and optional product pages. Provide parent sections for nested pages.',
            ewcSchema($design + [
                'siteDescription' => ewcString(300),
                'pages' => ewcList(ewcSchema($page, ['path', 'title', 'blocks']), 20),
                'products' => ewcList(ewcSchema($product, ['path', 'title', 'sku', 'price', 'blocks']), 30),
            ], array_merge($designRequired, ['pages'])), false],
        'draft_activate_theme' => ['Prepare to activate an installed theme. Its own content set, if present, will become the public site.',
            ewcSchema(['themeId' => $themeId], ['themeId']), false],
        'preview_draft' => ['Read the exact structured preview of a draft and its proposed target.',
            ewcSchema(['draftId' => $draftId], ['draftId']), true],
        'publish' => ['Apply the previously reviewed draft after explicit user approval. Changes affect the connected EWC-lite installation.',
            ewcSchema(['draftId' => $draftId], ['draftId']), false],
    ];
    $tools = [];
    foreach ($definitions as $name => $definition) {
        [$description, $schema, $readOnly] = $definition;
        $tools[$name] = [
            'name' => $name, 'description' => $description, 'inputSchema' => $schema,
            'annotations' => [
                'readOnlyHint' => $readOnly,
                'destructiveHint' => $name === 'publish',
                'idempotentHint' => $readOnly || $name === 'publish',
                'openWorldHint' => false,
            ],
        ];
    }
    return $tools;
}

function ewcValidate($value, array $schema, string $field): void
{
    switch ($schema['type']) {
        case 'object':
            if (!is_object($value)) {
                throw new EwcInput($field . ' must be an object.');
            }
            foreach ($schema['required'] as $name) {
                if (!property_exists($value, $name)) {
                    throw new EwcInput($field . '.' . $name . ' is required.');
                }
            }
            foreach (get_object_vars($value) as $name => $entry) {
                if (!property_exists($schema['properties'], $name)) {
                    throw new EwcInput($field . ' contains an unknown field.');
                }
                ewcValidate($entry, $schema['properties']->$name, $field . '.' . $name);
            }
            return;
        case 'array':
            if (!is_array($value) || count($value) < $schema['minItems'] || count($value) > $schema['maxItems']) {
                throw new EwcInput($field . ' has an invalid number of items.');
            }
            foreach ($value as $i => $entry) {
                ewcValidate($entry, $schema['items'], $field . '[' . $i . ']');
            }
            return;
        case 'string':
            if (!is_string($value) || trim($value) === '' || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value) ||
                (isset($schema['maxLength']) && preg_match_all('/./us', $value) > $schema['maxLength']) ||
                (isset($schema['enum']) && !in_array($value, $schema['enum'], true))) {
                throw new EwcInput($field . ' is invalid.');
            }
            return;
        case 'integer':
            if (!is_int($value) || $value < $schema['minimum'] || $value > ($schema['maximum'] ?? PHP_INT_MAX)) {
                throw new EwcInput($field . ' is out of range.');
            }
            return;
        case 'boolean':
            if (!is_bool($value)) {
                throw new EwcInput($field . ' must be a boolean.');
            }
    }
}

function callEwcTool(EwcSite $site, string $name, stdClass $args): array
{
    ewcValidate($args, ewcTools()[$name]['inputSchema'], 'arguments');
    switch ($name) {
        case 'list_pages':
            return $site->listPages($args->prefix ?? '/', $args->offset ?? 0);
        case 'list_themes':
            return $site->listThemes();
        case 'list_shop_categories':
            return $site->listShopCategories($args->offset ?? 0);
        case 'preview_draft':
            return $site->readDraft($args->draftId);
        case 'publish':
            return $site->publish($args->draftId);
        default:
            return $site->draft($name, $args);
    }
}
