---
name: wp-block-generator
description: >
  Generates complete WordPress ACF block code for this WP Site Starter project.
  Use this skill whenever the user provides a block ticket, description, design,
  data model, or asks to build a new block — even casual phrasings like
  "build a block for X", "I need a block that does Y", "create a [name] block",
  or "scaffold a block". The skill knows the file structure, naming conventions,
  PHP/Timber render patterns, Twig templates, block.json schema, ACF field group
  JSON format, accessibility expectations, and Alpine.js patterns used in this
  codebase. Always use this skill instead of guessing conventions from scratch.
---

# WP Site Starter — Block Generator

The full instructions live in the repo's tool-agnostic agent guide so any AI assistant (Cursor, Aider, Continue, Copilot, etc.) can follow the same conventions.

**Read and follow [AGENTS.md](../../../AGENTS.md) at the repo root.** The "Block generation" section there is the source of truth: file layout, templates, conventions, naming, accessibility requirements, Alpine.js patterns, and the citation rule for anything not directly grounded in the repo.

## When this skill applies

Trigger on any request that asks for a new block — including:

- Pasting a ticket or design spec.
- "Build a block for X."
- "Create a [name] block."
- "Scaffold a block that does Y."
- "I need a block with these ACF fields…"

## Before generating

1. **Detect the active theme** — there is exactly one directory under `wp-content/themes/`. Use that slug. Read `Text Domain:` from its `style.css`. Derive the PHP namespace and `@package` from existing `render.php` / `block.php` files in that theme.
2. **Detect Twig on/off** — look for any `wp-content/themes/<theme-slug>/blocks/**/render.twig`. If none exist, Twig is disabled (the developer opted out at project setup) — generate `render.php` only, with inline HTML.
3. **Read at least one existing block** in the active theme as a ground-truth reference, especially when the request is unusual. Existing blocks live at `wp-content/themes/<theme-slug>/blocks/`.

## Output

Follow the "Output format" section of [AGENTS.md](../../../AGENTS.md):

- Each generated file in a clearly labeled code block preceded by its repo-relative path.
- A "Next steps" section covering file placement, ACF import, and any manual setup.
- Inline citations to official WordPress / ACF / Timber / Alpine / WCAG docs for anything that isn't directly grounded in existing code in this repo.

## Scope

Currently: **blocks only**. CPTs, taxonomies, patterns, template parts, and ACF options pages are tracked as TODO in AGENTS.md — if asked, look at existing examples and cite current WordPress documentation.
