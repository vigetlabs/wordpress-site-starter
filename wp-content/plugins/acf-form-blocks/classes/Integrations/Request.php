<?php
/**
 * Request Integration
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Integrations;

use ACFFormBlocks\Submission;
use WP_Error;

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
	 * The request body
	 *
	 * @var array
	 */
	protected array $body = [];

	/**
	 * The request mapping
	 *
	 * @var array
	 */
	protected array $mapping = [];

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init(): void {
		parent::init();

		$settings = get_field( 'request', $this->id );
		$headers  = get_field( 'headers', $this->id );
		$mapping  = get_field( 'mapping', $this->id );

		$this->config = apply_filters(
			'acffb_integration_request_config',
			[
				'url'     => $settings['url'] ?: '',
				'method'  => $settings['method'] ?: 'POST',
				'format'  => $settings['format'] ?: 'json',
				'headers' => $headers ?: [],
				'mapping' => $mapping ?: [],
			]
		);

		$this->url    = $this->config['url'];
		$this->method = $this->config['method'];
		$this->format = $this->config['format'];

		$this->headers = $this->config['headers'];
		$this->mapping = $this->config['mapping'];

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

		if ( empty( $this->mapping ) ) {
			foreach ( $this->submission->get_data()['content'] as $key => $value ) {
				$this->body[ $key ] = $value;
			}
		} else {
			foreach ( $this->mapping as $map ) {
				$value = $this->submission->get_field_data( $map['field'] );
				$this->body[ $map['key'] ] = $value;
			}
		}

		$response = $this->send();

		$submission->add_meta(
			'request_response',
			[
				'body'    => wp_remote_retrieve_body( $response ),
				'headers' => wp_remote_retrieve_headers( $response ),
				'code'    => wp_remote_retrieve_response_code( $response ),
			]
		);
	}

	/**
	 * Send the request
	 *
	 * @return array|WP_Error
	 */
	private function send(): array|WP_Error {
		$request_args = apply_filters(
			'acffb_integration_request_args',
			[
				'method'  => $this->method,
				'headers' => $this->headers,
				'body'    => $this->format_request_body(),
			],
			$this
		);

		return wp_remote_request( $this->url, $request_args );
	}

	/**
	 * Perform Test
	 *
	 * @return array|WP_Error
	 */
	public function test(): array|WP_Error {
		foreach ( $this->form->get_form_object()->get_fields() as $field ) {
			$this->body[ $field->get_name() ] = $field->get_dummy_value();
		}

		return $this->send();
	}

	/**
	 * Format the request body
	 *
	 * @return string
	 */
	private function format_request_body(): string {
		if ( 'json' === $this->format ) {
			return json_encode( $this->body );
		}

		return http_build_query( $this->body );
	}
}
