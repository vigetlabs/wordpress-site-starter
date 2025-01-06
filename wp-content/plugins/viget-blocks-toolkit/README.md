# Viget Blocks Toolkit

This is a toolkit for creating custom blocks with ACF Pro. It provides a simple way to create blocks with ACF Pro.

This toolkit also includes a feature that adds icons to buttons.

## Creating Custom Blocks in your Theme

To create a block in your theme, simply create a `blocks` folder in the root of your theme directory. Each block should have its own folder and a `block.json` file. The `block.json` file should contain the block configuration. You can then use a `render.php` file or `render.twig` file to render the block.

## Hooks

### `vgtbt_block_locations` (Filter)

Filter the block locations. This allows you to add or change where custom blocks can be found.

```php
<?php
add_filter(
	'vgtbt_block_locations',
	function ( array $locations ): array {
		$locations[] = get_template_directory() . '/other-blocks';
		return $locations;
	}
);
```

### `vgtbt_button_icons` (Filter)

Filter the button icons.

```php
<?php
add_filter(
	'vgtbt_button_icons',
	function ( array $icons ): array {
		$icons['my-custom-icon'] = [ // The key is the unique icon slug.
			'label'       => __( 'My Custom Icon', 'my-text-domain' ),
			'icon'        => '<svg ... ></svg>',
			'defaultLeft' => false, // Optional, defaults icon to align left.
		];
		
		return $icons;
	}
);
```

### `vgtbt_supported_icon_blocks` (Filter)

Filter the supported icon blocks. Note: the frontend and editor CSS may need to be manually added for additional blocks.

```php
<?php
add_filter(
	'vgtbt_supported_icon_blocks',
	function ( array $blocks ): array {
		$blocks[] = 'core/heading';
		return $blocks;
	}
);
```

### `vgtbt_button_icons_editor_css` (Filter)

Filter the editor CSS for the button icons. This is useful when some icons do not use outline fill the fill property causes issues. Or can also be used to specify icon dimensions using `max-height`.

```php
add_filter(
	'vgtbt_button_icons_editor_css',
	function ( string $css ): string {
		return $css . '.components-button.button-icon-picker__icon-my-custom-icon svg { fill:none; }';
	}
);
```

### `vgtbt_unregister_block_styles` (Filter)

Unregister block styles from core blocks.

```php
add_filter(
	'vgtbt_unregister_block_styles',
	function ( array $styles ): array {
		$styles[] = [
			'core/separator',
			'dots',
		];

		return $styles;
	}
);

```
### `vgtbt_unregister_block_variations` (Filter)

Unregister block variations from core blocks.

```php
add_filter(
	'vgtbt_unregister_block_variations',
	function ( array $variations ): array {
		$variations[] = [
			'core/social-link',
			'bluesky',
		];
		return $variations;
	}
);
```
