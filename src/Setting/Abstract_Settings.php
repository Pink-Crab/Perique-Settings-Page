<?php

declare(strict_types=1);

/**
 * Abstract settings object.
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

namespace PinkCrab\Perique_Settings_Page\Setting;

use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Setting\Setting_Collection;
use PinkCrab\Perique_Settings_Page\Setting\Setting_Repository;

abstract class Abstract_Settings {

	/**
	 * The settings
	 *
	 * @var Setting_Collection;
	 */
	protected $settings;

	/**
	 * The settings repository.
	 *
	 * @var Setting_Repository
	 */
	protected $settings_repository;

	public function __construct( Setting_Repository $settings_repository ) {
		$this->settings_repository = $settings_repository;
		$this->settings            = $this->fields( new Setting_Collection() );
		$this->refresh_settings();
	}

	/**
	 * Populates the settings group with all fields.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Setting_Collection $settings
	 * @return \PinkCrab\Perique_Settings_Page\Setting\Setting_Collection
	 */
	abstract protected function fields( Setting_Collection $settings): Setting_Collection;

	/**
	 * Denotes of the settings is grouped
	 *
	 * @return bool
	 */
	abstract protected function is_grouped(): bool;

	/**
	 * Denotes the group key (can be used a prefix for key, or the key all settings are saved under)
	 *
	 * @return string
	 */
	abstract public function group_key(): string;

	/**
	 * Prefixes the key for the
	 *
	 * @param string $key
	 * @return string
	 */
	public function prefix_key( string $key ): string {
		return "{$this->group_key()}_{$key}";
	}

	/**
	 * Checks if a setting exists
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has( string $key ): bool {
		return $this->settings->has( $key );
	}

	/**
	 * Finds a setting from the repository.
	 *
	 * @param string $key
	 * @return Field|null
	 */
	public function find( string $key ): ?Field {
		return $this->settings->has( $key )
			? $this->settings->get( $key )
			: null;
	}

	/**
	 * Gets a value from the settings, if not set will return default.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get( string $key, $default = null ) {
		$field = $this->find( $key );
		return $field ? $field->get_value() : $default;
	}

	/**
	 * Sets a setting based on its key and value.
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return bool
	 */
	public function set( string $key, $data ): bool {
		if ( $this->is_grouped() && $this->settings_repository->allow_grouped() ) {
			$this->set_grouped( $key, $data );
		} else {
			$this->set_single( $key, $data );
		}

		return $this->settings->has( $key );
	}

	/**
	 * Sets a value as a grouped data set.
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return void
	 */
	protected function set_grouped( string $key, $data ): void {
		$this->settings->set_value( $key, $data );
		$this->store_grouped();
		$this->refresh_settings();
	}

	/**
	 * Sets a value as a single data set.
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return void
	 */
	protected function set_single( string $key, $data ): void {
		$updated = $this->settings_repository->set( $this->prefix_key( $key ), $data );
		if ( $updated ) {
			$this->settings->set_value( $key, $data );
		}
	}

	/**
	 * Deletes a setting if it exists, returns null if it doesn't.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete( string $key ): ?bool {
		if ( ! $this->settings->has( $key ) ) {
			return null;
		}

		if ( $this->is_grouped() && $this->settings_repository->allow_grouped() ) {
			$this->delete_grouped( $key );
		} else {
			$this->delete_single( $key );
		}

		return ! $this->settings->has( $key );
	}

	/**
	 * Deletes a value as a single data set.
	 *
	 * @param string $key
	 * @return void
	 */
	protected function delete_single( string $key ): void {
		$removed = $this->settings_repository->delete( $this->prefix_key( $key ) );
		if ( $removed ) {
			$this->settings->set_value( $key, null );
		}
	}

	/**
	 * Deletes a value as a grouped data set.
	 *
	 * @param string $key
	 * @return void
	 */
	protected function delete_grouped( string $key ): void {
		$this->settings->set_value( $key, null );
			$this->store_grouped();
			$this->refresh_settings();
	}

	/**
	 * Sets the value of the settings.
	 *
	 * @return void
	 */
	protected function refresh_settings(): void {

		if ( $this->is_grouped() && $this->settings_repository->allow_grouped() ) {
			$settings = $this->get_grouped_values();
			foreach ( $this->settings->get_keys() as $key ) {
				$this->settings->set_value( $key, array_key_exists( $key, $settings ) ? $settings[ $key ] : null );
			}
		} else {
			foreach ( $this->settings->get_keys() as $key ) {
				$this->settings->set_value( $key, $this->settings_repository->get( $this->prefix_key( $key ) ) );
			}
		}

	}

	/**
	 * Gets the grouped values.
	 *
	 * @return array
	 */
	protected function get_grouped_values(): array {
		return $this->settings_repository->get( $this->group_key() );
	}

	/**
	 * Returns the settings collection as an array.
	 *
	 * @return Field[]
	 */
	public function export(): array {
		// Update values from repository.
		$this->refresh_settings();
		return $this->settings->to_array();
	}

	/**
	 * Returns all the settings keys from collection.
	 *
	 * @return array<string>
	 */
	public function get_keys(): array {
		return array_values( $this->settings->get_keys() );
	}

	/**
	 * Updates the options as a single, grouped value.
	 *
	 * @return void
	 */
	protected function store_grouped(): void {
		$settings = array_map(
			function( $e ) {
				return $e->get_value();
			},
			$this->settings->to_array()
		);
		$this->settings_repository->set( $this->group_key(), $settings );
	}
}
