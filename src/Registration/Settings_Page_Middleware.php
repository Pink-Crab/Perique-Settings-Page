<?php

declare( strict_types=1 );

/**
 * Registration Middleware for configuring Settings Pages.
 *
 * Detects Settings_Page instances during Perique registration and
 * creates the associated settings class via the DI Container.
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

namespace PinkCrab\Perique_Settings_Page\Registration;

use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Perique_Settings_Page\Page\Settings_Page;
use PinkCrab\Perique_Settings_Page\Setting\Abstract_Settings;

class Settings_Page_Middleware implements Registration_Middleware {

	/**
	 * DI Container.
	 *
	 * @var DI_Container
	 */
	protected DI_Container $di_container;

	public function __construct( DI_Container $di_container ) {
		$this->di_container = $di_container;
	}

	/**
	 * Process a class instance during registration.
	 *
	 * If the instance is a Settings_Page, create its settings
	 * class via DI and inject it.
	 *
	 * @param object $class_instance
	 * @return object
	 */
	public function process( object $class_instance ): object {

		if ( ! $class_instance instanceof Settings_Page ) {
			return $class_instance;
		}

		/** @var class-string<Abstract_Settings> $settings_class */
		$settings_class = $class_instance->settings_class_name();

		// Register as shared so the same instance is returned for any DI resolution.
		$this->di_container->addRule( $settings_class, array( 'shared' => true ) );

		$settings = $this->di_container->create( $settings_class );

		if ( $settings instanceof Abstract_Settings ) {
			// Register the page as shared with a call rule so that when
			// Admin_Menu middleware creates this page, set_settings() is
			// called automatically with the shared settings instance.
			// Dice::INSTANCE tells expand() to resolve via create(), which
			// returns the shared instance since settings_class is shared.
			$this->di_container->addRule(
				\get_class( $class_instance ),
				array(
					'shared' => true,
					'call'   => array(
						array(
							'set_settings',
							array( array( \Dice\Dice::INSTANCE => $settings_class ) ),
						),
					),
				)
			);
		}

		return $class_instance;
	}

	/** @inheritDoc */
	public function setup(): void {}

	/** @inheritDoc */
	public function tear_down(): void {}
}
