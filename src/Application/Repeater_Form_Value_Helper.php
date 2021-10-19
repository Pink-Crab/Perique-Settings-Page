<?php

declare(strict_types=1);

/**
 * Helper class for getting and parsing Repeater values when handling form submission
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique_Settings_Page
 */

namespace PinkCrab\Perique_Settings_Page\Application;

use PinkCrab\Perique_Settings_Page\Setting\Field\Repeater;
use PinkCrab\Perique_Settings_Page\Setting\Field\Repeater_Value;

class Repeater_Form_Value_Helper {

	/**
	 * The repeater field.
	 *
	 * @var Repeater
	 */
	protected $repeater;

	/**
	 * The values as found in the current $_POST
	 *
	 * @var array<string, string[]>
	 */
	protected $raw_post;

    // phpcs:ignore WordPress.Security.NonceVerification.Missing
	public function __construct( Repeater $repeater ) {
		$this->repeater = $repeater;

		// Set from Post.
		$this->raw_post = array_key_exists( $repeater->get_key(), $_POST ?: array() ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? $_POST[ $repeater->get_key() ] // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: array();
	}

	/**
	 * Processes the current global POST and generates
	 * a populated Repeater_Value object.
	 *
	 * @return Repeater_Value
	 */
	public function process(): Repeater_Value {
		return new Repeater_Value( $this->reorder_values() );
	}

	/**
	 * Gets the sort order either from the post
	 * or based on the highest index from values (from 0)
	 *
	 * @return int[]
	 */
	protected function get_sort_order(): array {
		if ( array_key_exists( 'sortorder', $this->raw_post )
		&& is_string( $this->raw_post['sortorder'] )
		&& \mb_strlen( $this->raw_post['sortorder'] ) !== 0 ) {
			$sort_order = \explode( ',', \sanitize_text_field( $this->raw_post['sortorder'] ) );
		} else {

			$from_values = $this->repeater->get_value();

			// If we have no values, return just [0] as sort order, else the count (-1)
			$sort_order = is_a( $from_values, Repeater_Value::class )
			&& $from_values->group_count() > 0
				? \range( 0, ( count( $from_values->as_indexed() ) - 1 ) )
				: array( 0 );
		}

		return array_map( 'intval', $sort_order );
	}

	/**
	 * Gets an array of all values from the global post
	 * after being run through the corresponding fields sanitize callback.
	 *
	 * @return array<string, mixed[]>
	 */
	protected function get_sanitized_post_values(): array {
		$sanitized = array();
		foreach ( $this->repeater->get_fields()->to_array() as $key => $field ) {
			$sanitized[ $key ] = array_key_exists( $key, $this->raw_post ) && is_array( $this->raw_post[ $key ] )
				? array_map( array( $field, 'sanitize' ), $this->raw_post[ $key ] )
				: null;
		}

		return $sanitized;
	}

	/**
	 * Reorders the values from the global post
	 * based on the defined sort order.
	 *
	 * @return array<string, mixed[]>
	 */
	protected function reorder_values(): array {
		$sanitized  = $this->get_sanitized_post_values();
		$sort_order = $this->get_sort_order();

		// Create an array with the original keys and null as the values.
		$new_data = array_fill_keys( array_keys( $sanitized ), null );

		// For each index in the sort order
		foreach ( $sort_order as $sort_index ) {
			// Map each sanitized value with the same index.
			foreach ( $sanitized as $key => $value ) {
				$new_data[ $key ][ (int) $sort_index ] =
					is_array( $value ) && array_key_exists( (int) $sort_index, $value )
						? $value[ (int) $sort_index ] : null;
			}
		}

		// Reset all keys.
		return array_map( 'array_values', $new_data );
	}
}
