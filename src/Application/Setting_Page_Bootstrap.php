<?php

declare(strict_types=1);

/**
 * Series of helper functions for bootstrapping the extension
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

use Dice\Dice;
use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Settings_Page\Setting\Setting_View;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Form_Fields_Renderer;
use PinkCrab\Perique_Settings_Page\Setting\Setting_Repository;
use PinkCrab\Perique_Settings_Page\Application\Setting_Page_Controller;
use PinkCrab\Perique_Settings_Page\Application\WP_Options_Settings_Repository;

class Setting_Page_Bootstrap {

	/**
	 * Applies all DI Rules and Registerable classes to the App.
	 *
	 * @param \PinkCrab\Perique\Application\App $app
	 * @return \PinkCrab\Perique\Application\App
	 */
	public static function apply( App $app ): App {

		// Include the settings page controller in registration classes.
		$app->registration_classes( array( self::settings_page_controller() ) );

		// Include the default settings page view renderer.
		$app->container_config(
			function( DI_Container $container ) {
				$container->addRules( self::default_di_rules() );
			}
		);

		return $app;
	}

	/**
	 * Adds in the default DI Rules.
	 *
	 * @return array
	 */
	public static function default_di_rules(): array {
		return array(
			'*'                 => array(
				'substitutions' => array(
					Setting_View::class       => new Form_Fields_Renderer(),
					Setting_Repository::class => new WP_Options_Settings_Repository(),
				),
			),
			// Setting_Page::class => array(
			// 	'call' => array(
			// 		array( 'construct_settings', array( array( \Dice\Dice::INSTANCE => DI_Container::class ) ), Dice::CHAIN_CALL ),
			// 	),
			// ),
		);
	}

	/**
	 * Returns the settings page controller class name.
	 *
	 * @return string
	 */
	public static function settings_page_controller(): string {
		return Setting_Page_Controller::class;
	}
}
