<?php
/**
 * WP-CLI commands for wp-starter.
 *
 * @package WPStarter
 */

declare(strict_types=1);

namespace WPStarter\CLI;

/**
 * Registers CLI commands when WP-CLI is available.
 */
final class WpStarter_CLI {

	/**
	 * Registers commands with WP-CLI.
	 */
	public static function register(): void {
		if ( ! class_exists( '\WP_CLI' ) ) {
			return;
		}

		\WP_CLI::add_command(
			'wpstarter patterns sync',
			array( self::class, 'patterns_sync' ),
			array(
				'shortdesc' => 'Create or update theme-managed synced patterns (wp_block).',
				'synopsis'  => array(
					array(
						'type'        => 'flag',
						'name'        => 'force',
						'description' => 'Rewrite pattern content from theme files even if version option matches.',
						'optional'    => true,
					),
				),
			)
		);

		\WP_CLI::add_command(
			'wpstarter blocks migrate',
			array( self::class, 'blocks_migrate' ),
			array(
				'shortdesc' => 'Scan post content for ACF blocks and apply registered migrations (scaffold).',
				'synopsis'  => array(
					array(
						'type'        => 'flag',
						'name'        => 'dry-run',
						'description' => 'Report changes without writing to the database.',
						'optional'    => true,
					),
					array(
						'type'        => 'assoc',
						'name'        => 'post-type',
						'description' => 'Comma-separated post types to scan (default: post,page).',
						'optional'    => true,
					),
					array(
						'type'        => 'assoc',
						'name'        => 'migration',
						'description' => 'Migration id to run (default: list available).',
						'optional'    => true,
					),
				),
			)
		);
	}

	/**
	 * Sync theme-managed synced patterns.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Rewrite pattern content from the theme even if the stored version matches.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpstarter patterns sync
	 *     wp wpstarter patterns sync --force
	 *
	 * @param array<int,string> $args Positional args.
	 * @param array<string,mixed> $assoc_args Associative args.
	 */
	public static function patterns_sync( array $args, array $assoc_args ): void {
		$force = isset( $assoc_args['force'] );

		if ( ! function_exists( 'wpstarter_bootstrap_cta_inner_synced_pattern' ) ) {
			require_once get_stylesheet_directory() . '/inc/synced-patterns.php';
		}

		$post_id = wpstarter_bootstrap_cta_inner_synced_pattern( $force );

		if ( ! $post_id ) {
			\WP_CLI::error( 'Could not create or update the CTA inner synced pattern.' );
		}

		\WP_CLI::success(
			sprintf(
				'CTA inner synced pattern is ready (post ID %d). Insert it from the editor via synced patterns / Library.',
				$post_id
			)
		);
	}

	/**
	 * Run block content migrations.
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Only report how many posts would be touched.
	 *
	 * [--post-type=<types>]
	 * : Comma-separated list of post types (default: post,page).
	 *
	 * [--migration=<id>]
	 * : Migration to run. If omitted, available migrations are listed.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpstarter blocks migrate --dry-run
	 *     wp wpstarter blocks migrate --migration=example_placeholder --post-type=page,post
	 *
	 * @param array<int,string> $args Positional args.
	 * @param array<string,mixed> $assoc_args Associative args.
	 */
	public static function blocks_migrate( array $args, array $assoc_args ): void {
		$dry_run = isset( $assoc_args['dry-run'] );

		$post_types = isset( $assoc_args['post-type'] )
			? array_map( 'trim', explode( ',', (string) $assoc_args['post-type'] ) )
			: array( 'post', 'page' );

		$migration = isset( $assoc_args['migration'] ) ? (string) $assoc_args['migration'] : '';

		$registry = self::get_migration_registry();

		if ( '' === $migration ) {
			\WP_CLI::log( 'Available migrations:' );
			foreach ( array_keys( $registry ) as $id ) {
				\WP_CLI::log( ' - ' . $id );
			}
			\WP_CLI::log( 'Pass --migration=<id> to execute one.' );

			return;
		}

		if ( ! isset( $registry[ $migration ] ) ) {
			\WP_CLI::error( sprintf( 'Unknown migration "%s".', $migration ) );
		}

		$query = new \WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		$changed = 0;
		$scanned = 0;

		foreach ( $query->posts as $post_id ) {
			$post_id = (int) $post_id;
			$post    = get_post( $post_id );
			if ( ! $post || '' === $post->post_content ) {
				continue;
			}

			$scanned++;

			$new_content = call_user_func( $registry[ $migration ], $post->post_content, $dry_run );

			if ( $new_content === $post->post_content ) {
				continue;
			}

			++$changed;

			if ( $dry_run ) {
				continue;
			}

			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => $new_content,
				)
			);
		}

		if ( $dry_run ) {
			\WP_CLI::success(
				sprintf(
					'Dry run complete. Scanned %d posts; migration "%s" would update %d.',
					$scanned,
					$migration,
					$changed
				)
			);

			return;
		}

		\WP_CLI::success(
			sprintf(
				'Migration "%s" finished. Scanned %d posts; updated %d.',
				$migration,
				$scanned,
				$changed
			)
		);
	}

	/**
	 * Registered migrations: callable receives ( string $post_content, bool $dry_run ) and returns new content.
	 *
	 * @return array<string, callable(string,bool):string>
	 */
	private static function get_migration_registry(): array {
		return array(
			'example_placeholder' => array( self::class, 'migration_example_placeholder' ),
		);
	}

	/**
	 * Example migration: no-op transform. Replace with real parse_blocks / serialize_blocks logic when you change template.json.
	 *
	 * @param string $post_content Raw post content.
	 * @param bool   $dry_run      Whether this is a dry run (unused).
	 */
	private static function migration_example_placeholder( string $post_content, bool $dry_run ): string {
		unset( $dry_run );

		if ( false === strpos( $post_content, 'acf/' ) ) {
			return $post_content;
		}

		// Placeholder: add real tree transforms with parse_blocks() / serialize_blocks().
		return $post_content;
	}
}
