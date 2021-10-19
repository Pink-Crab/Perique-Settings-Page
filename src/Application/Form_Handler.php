<?php

declare(strict_types=1);

/**
 * Handles the settings form.
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

use PinkCrab\Perique_Settings_Page\Util\Form_Helper;
use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Setting\Field\Repeater;
use PinkCrab\Perique_Settings_Page\Setting\Abstract_Settings;
use PinkCrab\Perique_Settings_Page\Setting\Field\Repeater_Value;

class Form_Handler {

	/**
	 * The forms settings data
	 *
	 * @var Abstract_Settings
	 */
	protected $settings;

	/**
	 * The pages slug
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The nonce handle used for settings form
	 *
	 * @var string
	 */
	protected $nonce_handle;

	/**
	 * Have the settings been updated.
	 *
	 * @var bool
	 */
	protected $updated_settings = false;

	public function __construct( Abstract_Settings $settings, string $slug ) {
		$this->settings     = $settings;
		$this->slug         = $slug;
		$this->nonce_handle = Form_Helper::nonce_handle( $slug );
	}

	/**
	 * Process the global post
	 *
	 * @return void
	 */
	public function process(): void {
		if ( $this->valid_request() ) {
			foreach ( $this->settings->get_keys() as $key ) {
				$field = $this->settings->find( $key );

				// Process
				if ( is_object( $field ) && is_a( $field, Repeater::class ) ) {
					$this->update_repeater( $field );
					continue;
				}

				$this->update_setting( $key );
			}
		}

		// If we have processed any values, trigger notice.
		if ( $this->updated_settings === true ) {
			Form_Helper::success_notice( 'Page settings updated.' );
		}
	}

	/**
	 * Verifies if this is a correct request (has page slug and nonce in global post.)
	 *
	 * @return bool
	 */
	private function valid_request(): bool {
		return array_key_exists( 'page', $_POST )
		&& \sanitize_text_field( $_POST['page'] ) === $this->slug
		&& array_key_exists( 'pc_settings_nonce', $_POST )
		&& \wp_verify_nonce( \sanitize_text_field( $_POST['pc_settings_nonce'] ), $this->nonce_handle );
	}

	/**
	 * Updates a single value from the global post.
	 *
	 * @param string $key
	 * @return void
	 */
	private function update_setting( string $key ): void {

		$raw_value = array_key_exists( $key, $_POST ?: array() ) ? $_POST[ $key ] : '';
		$field     = $this->settings->find( $key );
		if ( is_a( $field, Field::class ) && $field->validate( $raw_value ) ) {

			$result = $this->settings->set( $key, $field->sanitize( $raw_value ) );

			// Update settings, if not already set.
			$this->updated_settings = $this->updated_settings === false
				? $result
				: $this->updated_settings;
		}

	}

	/**
	 * Updates the values in the data for a repeater.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Repeater $repeater
	 * @return void
	 */
	protected function update_repeater( Repeater $repeater ): void {

		$result = $this->settings->set(
			$repeater->get_key(),
			( new Repeater_Form_Value_Helper( $repeater ) )->process()
		);

		// Update settings, if not already set.
		$this->updated_settings = $this->updated_settings === false
			? $result
			: $this->updated_settings;

	}

}
