<?php

declare(strict_types=1);

/**
 * Value object for a repeater field
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

namespace PinkCrab\Perique_Settings_Page\Setting\Field;

use stdClass;
use JsonSerializable;

class Repeater_Value implements JsonSerializable {

	/**
	 * Holds the raw representation of the values.
	 *
	 * @var array<string, string[]>
	 */
	protected $raw_data;

	public function __construct( array $raw_data ) {
		$this->raw_data = $raw_data;
	}

	/**
	 * Get all values for a key from field name.
	 *
	 * @param string $key
	 * @return mixed[]
	 */
	public function get( string $key ) {
		if ( ! array_key_exists( $key, $this->raw_data ) ) {
			return null;
		}

		return $this->raw_data[ $key ];
	}

	/**
	 * Magic method, calls get()
	 *
	 * @param string $key
	 * @return void
	 */
	public function __get( string $key ) {
		return $this->get( $key );
	}

	/**
	 * Checks if a key exists.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has_field( string $key ): bool {
		return array_key_exists( $key, $this->raw_data );
	}

	/**
	 * Magic method, calls has_field()
	 *
	 * @param string $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return $this->has_field( $key );
	}

	/**
	 * Return the number of values per key.
	 * Takes the max of all fields.
	 *
	 * @return int
	 */
	public function group_count(): int {
		return (int) \max(
			array_map(
				function( $e ) {
					return is_array( $e )
					? count( $e )
					: 0;
				},
				$this->raw_data
			)
		);
	}

	/**
	 * Attempts to pluck a single index value.
	 *
	 * @param int $index
	 * @return stdClass|null
	 */
	public function get_index( int $index ): ?stdClass {

		if ( ( $index + 1 ) > $this->group_count() ) {
			return null;
		}

		$group = new stdClass();
		foreach ( $this->field_keys() as $key ) {
			$group->{$key} = is_array( $this->raw_data[ $key ] ) && array_key_exists( $index, $this->raw_data[ $key ] )
				? $this->raw_data[ $key ][ $index ]
				: null;
		}

		return $group;
	}

	/**
	 * All data grouped by index
	 * [
	 *    {"key1":"value1", "key2":1},
	 *    {"key1":"value2", "key2":2},
	 * ]
	 *
	 * @return array
	 */
	public function as_indexed(): array {
		$indexed = array();
		for ( $i = 0; $i < $this->group_count(); $i++ ) {
			$indexed[ $i ] = $this->get_index( $i );
		}
		return $indexed;
	}

	/**
	 * All data grouped by key
	 * {
	 *   "key1": ["value1", "value2"],
	 *   "key2": [1,2],
	 * }
	 *
	 * @return \stdClass|null
	 */
	public function as_fields(): ?stdClass {
		return ! empty( $this->raw_data )
			? (object) $this->raw_data
			: null;
	}

	/**
	 * Returns an array of all keys.
	 *
	 * @return array
	 */
	public function field_keys(): array {
		return array_keys( $this->raw_data );
	}

	/**
	 * Implementation of JsonSerializable()
	 *
	 * @return array<string, string[]>
	 */
	public function jsonSerialize() {
		return $this->raw_data;
	}

	/**
	 * Create an instance from a json encoded array (as per export)
	 *
	 * @param string $json
	 * @return self
	 */
	public static function from_json( string $json ): self {
		return new self( \json_decode( $json, true ) );
	}
}
