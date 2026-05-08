# AGENTS.md

Instructions for AI coding assistants (Claude Code, Cursor, Aider, Continue, Copilot Chat, etc.) working in this WordPress Site Starter repo. The goal is to make this starter quick and reliable to extend, regardless of which assistant a developer uses.

This file is the source of truth. Tool-specific entry points (e.g. `.claude/skills/wp-block-generator/SKILL.md`) are thin wrappers that point here.

---

## Project orientation

- Single active theme under `wp-content/themes/<theme-slug>/`. There is exactly one theme directory; treat whatever is there as **the active theme**. The slug, text domain, function prefix, and namespace are renamed at project bootstrap (see [bin/composer-scripts/ProjectEvents/PostCreateProjectScript.php](bin/composer-scripts/ProjectEvents/PostCreateProjectScript.php)) — never hardcode `wp-starter` / `WPStarter`.
- Front-end stack: Vite, Tailwind v4, Alpine.js (`alpinejs`, `@alpinejs/focus`, `@alpinejs/persist`).
- Templating: Timber/Twig is the default render layer. **Twig is optional** — at project setup the developer may opt out, in which case all `*.twig` files are removed and blocks render via PHP only. Detect this by looking for `*.twig` in `wp-content/themes/<theme-slug>/blocks/` — if any exist, Twig is enabled.
- ACF Pro is included as a plugin and is the source of all custom fields and block registration.

### Detecting project conventions

Before generating any code, derive these values from the repo (do not assume):

| Variable | How to detect |
|---|---|
| `<theme-slug>` | The single directory under `wp-content/themes/` |
| `<text-domain>` | `Text Domain:` line in `wp-content/themes/<theme-slug>/style.css` (usually equals the slug) |
| `<function-prefix>` | `<text-domain>` with dashes → underscores, plus a trailing `_` (e.g. `wp-starter` → `wp_starter_`, but the project also uses a no-dash form for Twig fn names — see "Naming conventions" below) |
| `<Namespace>` | The PHP namespace root used in existing block files (`@package` and `namespace` declarations in any `blocks/*/render.php` or `blocks/*/block.php`). Typically PascalCase of the slug. |
| `<twig-enabled>` | True if any `wp-content/themes/<theme-slug>/blocks/**/render.twig` exists |

When in doubt, read an existing block (e.g. [`blocks/alert-banner/`](wp-content/themes/wp-starter/blocks/alert-banner/)) and mirror its conventions — those are the standard.

---

## Scope of this guide

**Currently covered:** custom ACF blocks (the dominant unit of work in this starter).

**TODO — not yet covered, contributions welcome:**
- Custom post types
- Custom taxonomies
- Block patterns
- Template parts
- ACF options pages

When asked to scaffold something in the TODO list, ask the developer for guidance, look at any existing examples in the repo, and cite current WordPress / ACF documentation (see "Sourcing" below).

---

## Block generation

When a developer provides a block ticket, design, data model, or even a casual "build a block for X" / "create a [name] block" — generate a complete block following this guide.

This section is adapted from a skill that has shipped many blocks in production. The inline templates below are the convention. **Note:** if conventions in the active theme drift from this guide, the live blocks under `wp-content/themes/<theme-slug>/blocks/` are the source of truth — read them before generating.

### Block architecture

Every block lives at `wp-content/themes/<theme-slug>/blocks/<block-slug>/` and contains:

| File | Always? | Purpose |
|---|---|---|
| `block.json` | yes | Block metadata, supports, attributes, ACF config |
| `style.css` | yes | Front-end + editor styles (Tailwind, scaffold empty) |
| `template.json` | most blocks | Default inner block structure |
| `render.php` | custom render blocks | PHP data prep → `Timber::render()` (Twig on) or inline HTML (Twig off) |
| `render.twig` | Twig-on custom render blocks | Twig output template |
| `block.php` | when needed | Helper functions, filters (Twig functions, block styles) |
| `<slug>/group_*.json` | when ACF fields needed | ACF field group JSON |

**Two rendering modes:**

1. **InnerBlocks-only** — `template.json` defines content structure. Use a minimal `render.php` (and `render.twig` when Twig is enabled) that just calls `inner_blocks()`. Use when no PHP data fetching is needed.
2. **Custom render** — `render.php` preps data and (Twig on) calls `Timber::render()` or (Twig off) outputs HTML inline. Use when the block needs ACF fields, WP queries, or custom logic.

Child blocks (nested inside a parent) always have `"parent": ["acf/<parent-slug>"]` in `block.json`.

### Step-by-step

1. **Detect conventions** — slug, text domain, namespace, Twig on/off (see "Detecting project conventions").
2. **Determine rendering mode** — does the block need PHP data (ACF fields, WP queries)? → custom render. Purely composed of core/child blocks? → InnerBlocks-only.
3. **Determine if ACF fields are needed** — if yes, generate `group_*.json`.
4. **Determine if child blocks are needed** — if yes, create a subdirectory for each child block with its own full set of files.
5. **Generate all files** per the templates below. Skip `render.twig` if Twig is disabled.
6. **Output instructions** telling the developer where each file goes and any manual steps (e.g. ACF field group import).

### File templates

#### `block.json`

```json
{
  "name": "<block-slug>",
  "title": "<Block Title>",
  "description": "Displays the <Block Title> Component.",
  "icon": "<dashicon-slug>",
  "category": "components",
  "textdomain": "<text-domain>",
  "keywords": ["keyword1", "keyword2"],
  "allowedBlocks": ["acf/<child-slug>"],
  "attributes": {
    "align": {
      "type": "string",
      "default": "full"
    }
  },
  "supports": {
    "jsx": true,
    "mode": false,
    "alignWide": true,
    "layout": {
      "default": {
        "type": "constrained"
      },
      "allowOrientation": false,
      "allowCustomContentAndWideSize": false
    }
  },
  "acf": {
    "mode": "preview"
  }
}
```

**Key notes:**

- `"name"` is just the slug (e.g. `"my-block"`), NOT prefixed with `acf/`.
- `"category": "components"` always.
- `"textdomain"` is the active theme's text domain.
- `"acf": { "mode": "preview" }` always.
- `"supports.jsx": true` for InnerBlocks; `false` for pure data blocks (e.g. breadcrumbs).
- `"supports.mode": false` always (disables ACF's block mode switcher).
- Remove `allowedBlocks` if the block has no children.
- For child blocks, add `"parent": ["acf/<parent-slug>"]`.
- Common `supports.layout` types: `"constrained"`, `"grid"`, `"flex"`.
- Add `"color": { "background": true }` under `supports` when background color switching is needed.
- Add `"spacing": { "padding": ["top","bottom"] }` under `supports` when padding control is needed.
- Add `"styles": [...]` for block style variations (like dismissible/default).
- Grid parent blocks (e.g. card containers): use `"type": "grid"` layout with `"columnCount"` and `"minimumColumnWidth": null`.
- For a real-world reference of supports/attributes, see [`blocks/alert-banner/block.json`](wp-content/themes/wp-starter/blocks/alert-banner/block.json) and [`blocks/cta/block.json`](wp-content/themes/wp-starter/blocks/cta/block.json).

#### `style.css`

```css
.acf-block-<block-slug> {
	/* Styles go here */
}
```

The selector is always `.acf-block-<block-slug>`. Leave the body empty — a developer or designer will style it. If there are child blocks, add nested selectors (each child uses its own `.acf-block-<child-slug>` selector). Tailwind v4 is available — apply via `@apply`.

#### `template.json`

Defines default inner block structure. The `"template"` key is an array of block tuples: `["block/name", {attrs}, [innerBlocks]]`.

```json
{
  "template": [
    ["core/heading", { "level": 2, "placeholder": "Heading..." }],
    ["core/paragraph", { "placeholder": "Text..." }]
  ]
}
```

Use `"templateLock": "all"` (or `"contentOnly"`) in the block's `attributes` when the inner block structure should be locked — see [`blocks/cta/block.json`](wp-content/themes/wp-starter/blocks/cta/block.json) for the `contentOnly` pattern.

> **Important:** `template.json` is applied when a block is first inserted (or while the inner area is empty). Editing it does **not** retroactively change blocks already saved in posts. For sitewide structural updates, use synced patterns or migrations — see [`docs/block-structure-strategy.md`](wp-content/themes/wp-starter/docs/block-structure-strategy.md) when generating blocks that may need future migrations.

#### `render.php` (Twig enabled — preferred)

```php
<?php
/**
 * Block: <Block Title>
 *
 * @global array $block
 *
 * @package <Namespace>
 */

$context = \Timber\Timber::context();
$context['block']  = $block;
$context['fields'] = get_fields(); // when ACF fields are used

\Timber\Timber::render(
    'blocks/<block-slug>/render.twig',
    $context
);
```

**Simpler pattern (no ACF fields, just inner blocks):**

```php
<?php
/**
 * Block: <Block Title>
 *
 * @global array $block
 *
 * @package <Namespace>
 */

$inner = [ // phpcs:ignore
    'template' => $block['template'] ?? [],
];
?>
<section <?php block_attrs( $block ); ?>>
    <div class="acf-block-inner__container">
        <?php inner_blocks( $inner ); ?>
    </div>
</section>
```

**With ACF fields + Timber:**

```php
<?php
/**
 * Block: <Block Title>
 *
 * @global array $block
 *
 * @package <Namespace>
 */

$my_field = get_field( 'my_field' );
$inner = [ // phpcs:ignore
    'template' => $block['template'] ?? [],
];

\Timber\Timber::render(
    'blocks/<block-slug>/render.twig',
    [
        'block'    => $block,
        'my_field' => $my_field,
        'inner'    => $inner,
    ]
);
```

**For data-only blocks (no inner blocks):**

```php
$terms = get_terms([ 'taxonomy' => 'my-taxonomy', 'hide_empty' => false ]);
$items = array_map( fn( $term ) => [
    'name' => $term->name,
    'icon' => get_field( 'icon', $term ),
], $terms );

\Timber\Timber::render( 'blocks/<block-slug>/render.twig', [
    'block' => $block,
    'items' => $items,
] );
```

#### `render.php` (Twig disabled)

When Twig is off, omit `render.twig` entirely and put HTML directly in `render.php`:

```php
<?php
/**
 * Block: <Block Title>
 *
 * @global array $block
 *
 * @package <Namespace>
 */

$inner = [ // phpcs:ignore
    'template' => $block['template'] ?? [],
];
?>
<section <?php block_attrs( $block ); ?>>
    <div class="acf-block-inner__container">
        <?php inner_blocks( $inner ); ?>
    </div>
</section>
```

For ACF data, fetch with `get_field()` at the top and echo with `esc_html()`/`esc_attr()`/`esc_url()` as appropriate.

#### `render.twig`

```twig
{#
  Block: <Block Title>
#}

{% set inner = {
    template: block.template ?? [],
} %}

<section {{ block_attrs( block ) }}>
    <div class="acf-block-inner__container">
        {{ inner_blocks( inner ) }}
    </div>
</section>
```

**With ACF fields:**

```twig
{#
  Block: <Block Title>
#}

{% set inner = {
    template: block.template ?? [],
} %}

<section {{ block_attrs( block ) }}>
    <div class="acf-block-inner__container">
        {% if fields.my_field %}
            <p>{{ fields.my_field }}</p>
        {% endif %}
        {{ inner_blocks( inner ) }}
    </div>
</section>
```

**Dev fallback pattern** (for blocks that pull dynamic data, show placeholder when empty):

```twig
{% if not items %}
    {% set items = [
        { name: 'Item One', icon: 'https://placehold.co/240x240' },
        { name: 'Item Two', icon: 'https://placehold.co/240x240' },
    ] %}
{% endif %}
```

#### `block.php` (only when needed)

Use when the block needs:

- Custom helper functions callable from Twig (registered via `timber/twig/functions` filter).
- `register_block_style()` calls for this block.
- Block-specific WordPress filters/actions.

```php
<?php
/**
 * Block: <Block Title>
 *
 * @package <Namespace>
 */

namespace <Namespace>\<PascalCaseBlockName>;

/**
 * My helper function.
 */
function my_helper( string $arg ): string {
    return esc_html( $arg );
}

// Register functions for Twig.
add_filter(
    'timber/twig/functions',
    function ( array $functions ) {
        $functions['<function-prefix><blockslugnodashes>_my_helper'] = [
            'callable' => '\\<Namespace>\\<PascalCaseBlockName>\\my_helper',
        ];
        return $functions;
    }
);
```

Twig function naming: `<function-prefix><blockslugnodashes>_<functionname>` (e.g. for text domain `wp-starter` and block `alert-banner`: `wp_starter_alertbanner_my_helper`). Match whatever the rest of the theme is using — read existing `block.php` files first.

For a complete real-world example with `$persist()` Alpine state, see [`blocks/alert-banner/block.php`](wp-content/themes/wp-starter/blocks/alert-banner/block.php).

#### ACF field group JSON (`group_*.json`)

Only needed when the block has ACF fields. Filename format is `group_<random_hex>.json`. Generate a plausible random hex key.

```json
{
    "key": "group_<random_hex>",
    "title": "Block: <Block Title>",
    "fields": [
        {
            "key": "field_<random_hex>",
            "label": "Field Label",
            "name": "field_name",
            "type": "text",
            "required": 0,
            "conditional_logic": 0,
            "wrapper": { "width": "", "class": "", "id": "" },
            "allow_in_bindings": 1
        }
    ],
    "location": [
        [
            {
                "param": "block",
                "operator": "==",
                "value": "acf\/<block-slug>"
            }
        ]
    ],
    "menu_order": 0,
    "position": "normal",
    "style": "default",
    "label_placement": "top",
    "instruction_placement": "label",
    "hide_on_screen": "",
    "active": true,
    "description": "",
    "show_in_rest": 0
}
```

**Common ACF field types and their extra properties:**

- `text` — no extra required.
- `textarea` — `"rows": 4, "new_lines": "wpautop"`.
- `image` — `"return_format": "array", "preview_size": "medium", "library": "all"`.
- `post_object` — `"post_type": ["..."], "post_status": ["publish"], "return_format": "object", "multiple": 0, "allow_null": 1, "ui": 1`.
- `relationship` — `"post_type": [...], "filters": ["search"], "return_format": "object", "elements": []`.
- `true_false` — `"message": "", "default_value": 0, "ui": 1`.
- `select` — `"choices": {"value": "Label"}, "default_value": "", "allow_null": 0, "multiple": 0, "ui": 1, "return_format": "value"`.
- `repeater` — `"sub_fields": [...], "min": 0, "max": 0, "layout": "table", "button_label": "Add Row"`.
- `link` — `"return_format": "array"`.
- `wysiwyg` — `"tabs": "all", "toolbar": "full", "media_upload": 1`.

For anything not listed, cite the [ACF field type reference](https://www.advancedcustomfields.com/resources/#field-types) inline in the output.

---

## Output format

When generating a block, output each file in a clearly labeled code block preceded by its path relative to the repo root:

```
wp-content/themes/<theme-slug>/blocks/<block-slug>/block.json
```
```json
{ ... }
```

```
wp-content/themes/<theme-slug>/blocks/<block-slug>/render.php
```
```php
<?php ... ?>
```

…and so on for every file.

After all files, output a short **"Next steps"** section listing:

1. Where to copy each file (relative to the repo root).
2. If ACF JSON was generated: import via WP Admin → Custom Fields → Tools → Import Field Groups, or place in `wp-content/themes/<theme-slug>/acf-json/` for auto-sync (create the directory if it doesn't exist).
3. Any other manual steps (registering a custom taxonomy, etc.).
4. A reminder to run `ddev npm run build` so editor/front-end CSS is generated.

---

## Helpers available in PHP

These global helpers are registered in the theme — use them freely:

- `block_attrs( $block, $class = '', $attrs = [] )` — outputs all block wrapper attributes.
- `inner_blocks( $args )` — outputs ACF inner blocks; accepts `template`, `allowedBlocks`, `templateLock`.
- `get_block_id( $block )` — returns a unique ID for the block instance.

Project-specific helpers (singletons, taxonomy accessors, icon registries, etc.) are not assumed by this guide — discover them by reading existing blocks in the active theme.

## Twig functions

- `{{ block_attrs( block ) }}` — wrapper attributes.
- `{{ inner_blocks( inner ) }}` — inner blocks output.
- `{{ function('is_admin') }}` — call any PHP function.
- `{{ function('get_field', 'field_name') }}` — call `get_field`.
- Standard Timber variables: `{{ post }}`, `{{ site }}`, etc.

---

## Naming conventions

| Thing | Convention | Example (theme = `wp-starter`) |
|---|---|---|
| Block slug | `kebab-case` | `my-new-block` |
| Block name in `block.json` | slug only | `"name": "my-new-block"` |
| Block name in ACF/WP refs | `acf/<slug>` | `"acf/my-new-block"` |
| PHP namespace | `<Namespace>\PascalCase` | `WPStarter\MyNewBlock` |
| `@package` tag | `<Namespace>` | `WPStarter` |
| CSS selector | `.acf-block-<slug>` | `.acf-block-my-new-block` |
| Twig function names | `<function-prefix><slugnodashes>_<fn>` | `wp_starter_mynewblock_get_id` |
| Twig template path | `blocks/<slug>/render.twig` | `blocks/my-new-block/render.twig` |
| ACF field group JSON key | `group_<8hex>` | `group_a1b2c3d4` |
| ACF field key | `field_<8hex>` | `field_e5f6a7b8` |

---

## Accessibility

Accessibility is a top priority for blocks shipped from this starter. When generating a block:

- **Use semantic HTML first** (`<section>`, `<article>`, `<nav>`, `<header>`, `<footer>`, `<button>`, `<a>`, headings in correct hierarchical order). Reach for ARIA only when semantics aren't enough.
- **Buttons vs. links**: actions are `<button>`, navigation is `<a>`. Never style a `<div>` or `<span>` to look interactive — assistive tech won't see it.
- **Heading hierarchy**: blocks should not assume an `h1` is available — default headings to `h2` or expose a `level` attribute (see [`blocks/cta/template.json`](wp-content/themes/wp-starter/blocks/cta/template.json)).
- **Keyboard support**: every interactive element must be keyboard reachable, with a visible focus state. If you add Alpine click handlers, also support keyboard equivalents (the dismiss button in `alert-banner` is a real `<button>`, which is correct).
- **Names and labels**: provide `aria-label` for icon-only controls. Use `__()` / `esc_attr__()` for translatable strings — see [`blocks/alert-banner/block.php`](wp-content/themes/wp-starter/blocks/alert-banner/block.php).
- **Images**: every `<img>` needs `alt`. ACF image fields return `alt` when `return_format` is `array` — use it. Decorative images get `alt=""`.
- **Color contrast**: pick text and background defaults that pass WCAG AA. Flag in "Next steps" if a generated block depends on the developer choosing accessible colors.
- **Motion**: respect `prefers-reduced-motion` for any animation.
- **Focus management**: dismissible/expandable widgets should manage focus and use `@alpinejs/focus` when needed.

Cite [WCAG 2.2](https://www.w3.org/TR/WCAG22/) or [WAI-ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/) when recommending non-obvious patterns.

---

## Interactivity: prefer Alpine.js

Alpine.js is the preferred interactivity layer for blocks in this starter. Available plugins: `@alpinejs/focus` (focus management) and `@alpinejs/persist` (`$persist()` for localStorage-backed state).

Patterns:

- Put Alpine attributes (`x-data`, `x-show`, `x-bind`, etc.) into the `$attrs` array passed to `block_attrs()` — see [`blocks/alert-banner/render.php`](wp-content/themes/wp-starter/blocks/alert-banner/render.php).
- Wrap Alpine attributes in `if ( ! is_admin() )` so they don't run inside the block editor preview.
- Reach for vanilla JS only when Alpine doesn't fit (e.g. heavy SDKs, third-party widgets).

For Alpine APIs not shown in existing blocks, cite the [Alpine.js docs](https://alpinejs.dev/start-here).

---

## Sourcing

When you recommend something that isn't directly grounded in this repo's existing blocks — a rare ACF field type, a `block.json` schema field, a WP API, an Alpine plugin, etc. — **cite the current official documentation inline** in the generated output. Authoritative sources:

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [`block.json` reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/) and [block schema](https://github.com/WordPress/gutenberg/blob/trunk/schemas/json/block.json)
- [WordPress code reference](https://developer.wordpress.org/reference/)
- [ACF documentation](https://www.advancedcustomfields.com/resources/) and [ACF field type reference](https://www.advancedcustomfields.com/resources/#field-types)
- [Timber documentation](https://timber.github.io/docs/)
- [Alpine.js documentation](https://alpinejs.dev/)
- [WCAG 2.2](https://www.w3.org/TR/WCAG22/)

Do not invent API surfaces. If you're not sure, fetch and cite, or ask the developer.

---

## Gotchas

- Never edit `theme.json` directly — it's generated from `src/theme-json/`.
- `"supports.mode": false` is always set — disables ACF's block mode switcher.
- The `acf-json/` folder (when present) auto-syncs ACF field groups; placing JSON there is preferred over manual import.
- `render.php` and `render.twig` are kept in sync when Twig is enabled.
- PHP files use `// phpcs:ignore` comments on lines that intentionally break PHPCS rules (like inline array assignments in render.php).
- For Alpine.js: `x-data`, `x-show`, etc. go in `$attrs` array in `render.php` and are only applied when `! is_admin()`.
- `$persist()` is available for Alpine.js persistent state (used in alert-banner).
- Child block `block.json` files must include `"parent": ["acf/<parent-slug>"]`.
- Always add a dev fallback in `render.twig` for blocks that pull dynamic data (taxonomies, CPT queries) so the block looks good before real data exists.
