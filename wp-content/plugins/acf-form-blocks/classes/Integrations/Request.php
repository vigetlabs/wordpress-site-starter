<?php
/**
 * Request Integration
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Integrations;

use ACFFormBlocks\Elements\Field;
use ACFFormBlocks\Submission;

/**
 * Request Integration Class
 */
class Request extends Integration {

	/**
	 * The request URL
	 *
	 * @var string
	 */
	protected string $url = '';

	/**
	 * The request method
	 *
	 * @var string
	 */
	protected string $method = 'POST';

	/**
	 * The request format
	 *
	 * @var string
	 */
	protected string $format = 'json';

	/**
	 * The request headers
	 *
	 * @var array
	 */
	protected array $headers = [];

	/**
	 * The request mapping
	 *
	 * @var array
	 */
	protected array $mapping = [];

	/**
	 * Request constructor.
	 *
	 * @param array $config
	 */
	public function __construct( array $config = [] ) {
		parent::__construct( $config );

		$this->url    = $config['url'] ?? '';
		$this->method = $config['method'] ?? 'POST';
		$this->format = $config['format'] ?? 'json';

		$this->headers = $config['headers'] ?? [];
		$this->mapping = $config['mapping'] ?? [];

		if ( 'json' === $this->format ) {
			$this->headers['Content-Type'] = 'application/json';
		}
	}

	/**
	 * Process the request
	 *
	 * @param Submission $submission
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
	public function process( Submission $submission ): void {
		parent::process( $submission );

		if ( ! $this->url ) {
			throw new \Exception( 'Request URL is required.' );
		}

		$request_args = [
			'method'  => $this->method,
			'headers' => $this->headers,
		];

		$request_args['body'] = $this->format_request_body();

		$request_args = apply_filters( 'acffb_integration_request_args', $request_args, $this );

		$response = wp_remote_request( $this->url, $request_args );

		// TODO: Something with the response
	}

	/**
	 * Format the request body
	 *
	 * @return string
	 */
	private function format_request_body(): string {
		// TODO: WIP - Implement mapping and field values
		$body = array_map(
			function ( Field $field ) {
				return $field->get_value();
			},
			$this->mapping
		);

		if ( 'json' === $this->format ) {
			return json_encode( $body );
		}

		return http_build_query( $body );
	}
}
