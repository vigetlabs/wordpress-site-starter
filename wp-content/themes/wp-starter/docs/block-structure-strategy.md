# Block structure strategy (wp-starter)

This document records how each theme block should handle **inner structure** versus **sitewide updates**. It complements the README section on `template.json`.

## How to read this table

| Strategy | When to use |
|----------|-------------|
| **template.json** | Per-instance composition in the editor; structure is copied into `post_content` at save. Theme file changes affect **new** blocks only unless you run a migration. |
| **Server render** | Layout, wrappers, and markup that should change for **every** instance when PHP/Twig/CSS ships—without rewriting the database. |
| **Synced pattern** | One canonical block tree (`wp_block`) referenced by instances; editing the pattern updates **all** references (see bundled “CTA inner” prototype). |
| **Migration / WP-CLI** | When you change `template.json` or inner markup and must reshape **existing** saved content. |

## Per-block guidance

| Block | Inner blocks / template | Recommended source-of-truth for structure |
|-------|---------------------------|-------------------------------------------|
| Accordion | `template.json` | **template.json** + **migration** when structure changes; accordion semantics are usually per-page. |
| Alert Banner | `template.json` | **template.json** + **server render** for outer shell; consider **migration** if inner columns change often. |
| Breadcrumbs | `template.json` (via `wpstarter_breadcrumbs`) | **Server render** + PHP logic; template seeds editor only. |
| CTA | `template.json` | **Synced pattern** prototype for shared inner copy/layout; otherwise **template.json** + **migration**. |
| Image w/Caption | `template.json`, `templateLock: all` | **template.json** + **theme.json** / CSS for presentation; **migration** if inner block order changes. |
| Logo Grid | `template.json` | **template.json** + **migration** if grid structure changes. |
| Navigation Container | `template.json` | **template.json** + **server render** for nav chrome; **migration** for large inner restructures. |
| Page Header | `template.json` | **template.json** + **migration** when columns or block types change; optional **synced pattern** if many pages must share one hero layout. |
| Text & Icon Cards | Parent + child `template.json` | **template.json** + **migration**; child cards are repeated instances. |
| Text & Image | `template.json` | **template.json** + **migration** if layout blocks change. |
| Video Embed | `template.json` | Mostly embed attributes; **server render** / oEmbed for output. |
| Video Player | `template.json` | **template.json** + **server render** for player shell. |

## Related docs

- [Patterns and block locking](https://developer.wordpress.org/themes/patterns/patterns-and-block-locking/)
- [Inner blocks / templates](https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/nested-blocks-inner-blocks/)
- WP-CLI: `wp wpstarter blocks migrate` (see theme `inc/cli/`)
