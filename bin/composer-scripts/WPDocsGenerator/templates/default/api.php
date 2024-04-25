<?php
/**
 * API Template
 *
 * phpcs:disable
 *
 * @var Builder $this
 *
 * @package WPDocsGenerator
 */

use Viget\ComposerScripts\WPDocsGenerator\Builders\Builder;
use Viget\ComposerScripts\WPDocsGenerator\DocItem;

echo 'API:' . PHP_EOL . PHP_EOL;

/**
 * @param DocItem[] $items
 * @param string $current
 * @return void
 */
function generateApi( array $items, string $current = '' ): void {
	foreach ($items as $item) {
		if ('public' !== $item->access) {
			continue;
		}

		$name = $item->name;
		$separator = $item->isStatic ? '::' : '->';

		if (in_array($item->node, ['function', 'method'], true)) {
			$name .= '(';

			$params = '';
			foreach ($item->parameters as $param) {
				if ( $params ) {
					$params .= ', ';
				}
				$types = array_diff($param->returnTypes, ['null'] );
				$types = implode( '|', $types );
				if ( $types ) {
					if ( $param->isNullable ) {
						$types = '?' . $types;
					}
					$params .= $types . ' ';
				}
				$params .= '$' . $param->name;
				if ( $param->defaultValue ) {
					$params .= ' = ' . $param->defaultValue;
				}
			}
			if ( $params ) {
				$name .= ' ' . $params . ' ';
			}

			$name .= ')';

		} elseif ('property' === $item->node && $item->isStatic) {
			$name = '$' . $name;
		}

		$fullName = $current ? $current . $separator . $name : $name;

		if (!in_array($item->node, ['property', 'method'], true) || empty($item->api)) {
			// Display the API information
			echo $fullName . PHP_EOL;
			echo $item->description . PHP_EOL;

			if (!empty($item->parameters)) {
				echo '  Args:' . PHP_EOL;
				foreach ($item->parameters as $param) {
					echo '  - $' . $param->name;
					if ( ! empty( $param->returnTypes ) ) {
						echo ' (' . implode( '|', $param->returnTypes ) . ')';
					}
					if ( $param->description ) {
						echo ': ' . $param->description;
					}
					echo PHP_EOL;
				}
			}

			echo PHP_EOL;
		}

		// Recursively call the function for sub-APIs
		if (!empty($item->api)) {
			generateApi($item->api, $fullName);
		}
	}
}

generateApi( $this->api->tree );
