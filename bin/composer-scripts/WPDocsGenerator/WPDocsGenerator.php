<?php
/**
 * Generate Documentation
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts\WPDocsGenerator;

use Viget\ComposerScripts\WPDocsGenerator\Builders\Builder;
use Viget\ComposerScripts\WPDocsGenerator\Builders\HtmlBuilder;
use Viget\ComposerScripts\WPDocsGenerator\Builders\MarkdownBuilder;
use Viget\ComposerScripts\WPDocsGeneratorScript;

/**
 * WPDocsGenerator Class
 */
class WPDocsGenerator {

	/**
	 * @var ?WPDocsGenerator
	 */
	public static ?WPDocsGenerator $instance = null;

	/**
	 * @var ?WPDocsGeneratorScript
	 */
	private ?WPDocsGeneratorScript $script = null;

	/**
	 * @var ?Parser
	 */
	private ?Parser $parser = null;

	/**
	 * @var ?ApiCollection
	 */
	private ?ApiCollection $api = null;

	/**
	 * @var ?ReferenceCollection
	 */
	private ?ReferenceCollection $references = null;

	/**
	 * @var ?HookCollection
	 */
	private ?HookCollection $hooks = null;

	/**
	 * @var array
	 */
	private array $config = [];

	/**
	 * Initialize WPDocsGenerator
	 *
	 * @return WPDocsGenerator
	 */
	public static function getInstance(): WPDocsGenerator
	{
		if ( null === self::$instance ) {
			self::$instance = new WPDocsGenerator();
		}

		return self::$instance;
	}

	/**
	 * Initialize this class
	 *
	 * @param WPDocsGeneratorScript $script
	 * @param array $config
	 * @return void
	 */
	public function init( WPDocsGeneratorScript $script, array $config ): void
	{
		$this->script = $script;
		$this->config = $config;
		$this->parser = new Parser();
	}

	/**
	 * Generate Docs.
	 * @return void
	 */
	public function generate(): void
	{
		$dir = $this->config['source'];

		// Parse the PHP files into objects.
		$this->parse( $dir );

		// Import references.
		$this->importReferences();

		// Group objects together, starting at the root of the tree.
		$this->collect( $this->api->tree );

		// Generate the docs.
		$this->build();

		// Report the results
		$this->finish();
	}

	/**
	 * @param string $path
	 * @return void
	 */
	private function parse( string $path ): void
	{
		$path    = str_replace( '//', '/', $path );
		$dirname = basename( $path );

		// Ignore directories based on config.
		if ( in_array( $dirname, $this->config['ignore'] ) ) {
			return;
		}

		// Make sure it exists.
		if ( ! is_dir( $path ) ) {
			return;
		}

		// Loop through all PHP files.
		$files = glob( $path . '*.php' );

		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$relative = str_replace( $this->config['source'], basename( $this->config['source'] ) . '/', $file );

				// Build API array.
				$api = $this->parser->parseApi( $file, $relative, $this->api );

				if ( is_null( $api ) ) {
					$this->script::writeError( 'Parser Collection Error: ' . $this->parser->error );
				} else {
					$this->api = $api;
				}

				// Build References array.
				$references = $this->parser->parseReferences( $file, $relative, $this->references );

				if ( is_null( $references ) ) {
					$this->script::writeError( 'Parser Collection Error: ' . $this->parser->error );
				} else {
					$this->references = $references;
				}

				// Build Hooks array.
				$hooks = $this->parser->parseHooks( $file, $relative, $this->hooks );

				if ( is_null( $hooks ) ) {
					$this->script::writeError( 'Parser Collection Error: ' . $this->parser->error );
				} else {
					$this->hooks = $hooks;
				}
			}
		}

		$subdirectories = glob( $path . '*/', GLOB_ONLYDIR );

		foreach ( $subdirectories as $subdirectory ) {
			$this->parse( $subdirectory );
		}
	}

	/**
	 * @param DocItem[] $objects
	 * @return void
	 */
	private function collect( array &$objects ): void
	{
		foreach ( $objects as &$object ) {
			$object = $this->collectReturnTypes( $object );

			// Traverse through this objects API.
			$this->collect( $object->api );
		}
	}

	/**
	 * @param DocItem $object
	 * @return DocItem
	 */
	private function collectReturnTypes( DocItem $object ): DocItem
	{
		foreach ( $object->returnTypes as $returnType ) {
			if ( array_key_exists( $returnType, $this->api->useStatements ) ) {
				$returnType = $this->api->useStatements[$returnType];
			}

			if ( array_key_exists( $returnType, $this->api->objects ) ) {
				$returnObject = $this->api->objects[$returnType];
			} else {
				continue;
			}

			// Exclude circular references.
			if ( 'class' !== $returnObject->node || $returnObject->getReference() === $object->getReference() ) {
				continue;
			}

			$object->api = $this->collectPropertiesAndMethods( $returnObject );
		}

		return $object;
	}

	/**
	 * @param DocItem $object
	 * @return array
	 */
	private function collectPropertiesAndMethods( DocItem $object ): array
	{
		$api = [];

		foreach ( $object->methods as $method ) {
			if ( 'public' !== $method->access || in_array( $method->name, [ '__construct', 'get_instance' ], true ) ) {
				continue;
			}

			$api[$method->getReference()] = $this->collectReturnTypes($method);
		}

		foreach ( $object->properties as $property ) {
			if ( 'public' !== $property->access || 'instance' === $property->name ) {
				continue;
			}

			$api[$property->getReference()] = $this->collectReturnTypes($property);
		}

		if (! empty($api) ) {
			$object->inApi = true;
			if ( array_key_exists( $object->getReference(), $this->api->objects ) ) {
				$this->api->objects[ $object->getReference() ]->inApi = true;
			}
		}

		return $api;
	}

	/**
	 * Get builder and build
	 * @return void
	 */
	private function build(): void
	{
		$builder = $this->getBuilder();
		$builder->build();
	}

	/**
	 * Get the Builder
	 * @return Builder|MarkdownBuilder|HtmlBuilder
	 */
	private function getBuilder(): Builder|MarkdownBuilder|HtmlBuilder
	{
		$builder = new Builder();

		if ( 'markdown' === $this->config['format'] ) {
			$builder = new MarkdownBuilder();
		}

		if ( 'html' === $this->config['format'] ) {
			$builder = new HtmlBuilder();
		}

		$builder->setApi( $this->api );
		$builder->setConfig( $this->config );
		$builder->setHooks( $this->hooks );

		return $builder;
	}

	/**
	 * Wrap up.
	 * @return void
	 */
	private function finish(): void
	{
		$this->script::writeInfo( 'Documentation generated successfully.' );
	}

	private function importReferences(): void
	{
		foreach ( $this->references->references as $reference ) {
			if ( array_key_exists( $reference->reference, $this->api->objects ) ) {
				$this->api->objects[ $reference->reference ]->references = $reference->uses;
			}
		}
	}
}
