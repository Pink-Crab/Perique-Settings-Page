<?php

declare(strict_types=1);

/**
 * Series of helper functions regarding files and directories.
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

use TypeError;
use PinkCrab\Enqueue\Enqueue;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\Hookable;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Settings_Page\Util\Hooks;
use PinkCrab\Perique_Settings_Page\Util\File_Helper;
use PinkCrab\Perique_Admin_Menu\Group\Abstract_Group;
use PinkCrab\Perique_Settings_Page\Page\Setting_Page;
use PinkCrab\Perique_Settings_Page\Setting\Setting_View;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;
use PinkCrab\Perique_Admin_Menu\Hooks as Admin_Page_Hooks;
use PinkCrab\Perique_Settings_Page\Application\Form_Handler;

class Setting_Page_Controller implements Hookable {

	/**
	 * Global scripts handle.
	 */
	public const PAGE_GLOBALS_SCRIPTS = 'pc_setting_page_scripts';

	/**
	 * Global styles handle.
	 */
	public const PAGE_GLOBALS_STYLES = 'pc_setting_page_styles';

	/**
	 * Script/style handle for select2.
	 */
	public const SELECT2_HANDLE = 'pc_select_2';

	/**
	 * View Generator
	 *
	 * @var Setting_View
	 */
	protected $setting_view_generator;

	/**
	 * DI Container
	 *
	 * @var DI_Container
	 */
	protected $di_container;

	public function __construct(
		Setting_View $setting_view_generator,
		DI_Container $di_container
	) {
		$this->setting_view_generator = $setting_view_generator;
		$this->di_container           = $di_container;
	}

	/**
	 * Registers the page hooks.
	 *
	 * @param \PinkCrab\Loader\Hook_Loader $loader
	 * @return void
	 */
	public function register( Hook_Loader $loader ): void {
		$loader->admin_action( Admin_Page_Hooks::PAGE_REGISTRAR_PRIMARY, array( $this, 'register_primary_page' ), 2 );
		$loader->admin_action( Admin_Page_Hooks::PAGE_REGISTRAR_SUB, array( $this, 'register_sub_page' ), 2 );
	}

	/**
	 * Registers the primary page.
	 *
	 * @param Setting_Page $page
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group|null $group
	 * @return void
	 * @throws Page_Exception (204) If page fails to be created.
	 * @throws TypeError If not group passed.
	 */
	public function register_primary_page( Setting_Page $page, ?Abstract_Group $group ): void {

		if ( $group === null ) {
			throw new TypeError( 'Valid group must be passed to create Setting_Page' );
		}

		$page->construct_settings( $this->di_container );

		$result = add_menu_page(
			$page->page_title() ?? '',
			$group->get_group_title(),
			$group->get_capability(),
			$page->slug(),
			$this->setting_view_generator->generate_view_callback( $page ),
			$group->get_icon(),
			(int) $group->get_position()
		);

		// Call failed action for logging etc.
		if ( ! is_string( $result ) ) {
			throw Page_Exception::failed_to_register_page( $page );
		}

		// Register callback for handling the settings page form.
		add_action( "load-{$result}", $this->load_page_callback_generator( $page ) );
		add_action( 'admin_enqueue_scripts', $this->enqueue_scripts( $page ) );
	}

	/**
	 * Registers the sub page.
	 *
	 * @param Setting_Page $page
	 * @param \PinkCrab\Perique_Admin_Menu\Group\Abstract_Group|null $group
	 * @return void
	 * @throws Page_Exception (204) If page fails to be created.
	 * @throws TypeError If not group passed.
	 */
	public function register_sub_page( Setting_Page $page, string $parent_slug ): void {

		$page->construct_settings( $this->di_container );

		$result = add_submenu_page(
			$parent_slug,
			$page->page_title() ?? '',
			$page->menu_title(),
			$page->capability(),
			$page->slug(),
			$this->setting_view_generator->generate_view_callback( $page ),
			$page->position()
		);

		// Call failed action for logging etc.
		if ( ! is_string( $result ) ) {
			throw Page_Exception::failed_to_register_page( $page );
		}

		// Register callback for handling the settings page form.
		add_action( "load-{$result}", $this->load_page_callback_generator( $page ) );
		add_action( 'admin_enqueue_scripts', $this->enqueue_scripts( $page ) );
	}

	/**
	 * Generate the callback for loading/saving the page.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Page\Setting_Page $page
	 * @return callable
	 */
	protected function load_page_callback_generator( Setting_Page $page ): callable {
		return function() use ( $page ): void {
			$form_handler = new Form_Handler( $page->settings(), $page->slug() );
			$form_handler->process();
		};
	}

	/**
	 * Generates the callback for rendering all scripts and styles.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Page\Setting_Page $page
	 * @return callable
	 */
	protected function enqueue_scripts( Setting_Page $page ): callable {
		return function( $hook ) use ( $page ): void {
			// Ensure the correct page is being loaded.
			if ( $hook !== \get_plugin_page_hook( $page->slug(), $page->parent_slug() ) ) {
				return;
			}

			// Render global scripts.
			if ( ! wp_script_is( self::PAGE_GLOBALS_SCRIPTS ) ) {
				$this->global_page_scripts( $page );
			}

			// Render global styles.
			if ( ! \wp_style_is( self::PAGE_GLOBALS_STYLES ) ) {
				$this->global_page_styles( $page );
			}

			// If the page uses select2 and the global scripts have not been enabled.
			if ( $page->use_select2() && ! \wp_script_is( self::SELECT2_HANDLE ) ) {
				$this->global_select2( $page );
			}

			// Render all page scripts
			if ( ! is_null( $page->enqueue_scripts() ) ) {
				$page->enqueue_scripts()->register();
			}

			// Render all page styles.
			if ( ! is_null( $page->enqueue_styles() ) ) {
				$page->enqueue_styles()->register();
			}

		};
	}

	/**
	 * Registers the global scripts for the page.
	 *
	 * @param Setting_Page $page
	 * @return void
	 */
	protected function global_page_scripts( $page ): void {
		// Include setting page JS.
		$script = Enqueue::script( self::PAGE_GLOBALS_SCRIPTS )
			->src( File_Helper::assets_url() . '/script.js' )
			->deps( 'jquery' )
			->localize(
				array(
					'mediaLibraryPreviewEndPoint'    => get_rest_url( null, 'wp/v2/media' ),
					'mediaLibraryNoImagePlaceholder' => File_Helper::assets_url() . '/no-image.png',
				)
			);

		// Filter for attributes.
		$script = \apply_filters( Hooks::PAGE_GLOBAL_SCRIPT, $script, $page );

		// If we still have a valid enqueue script.
		if ( \is_a( $script, Enqueue::class ) ) {
			$script->register();
		}
	}

	/**
	 * Registers the global styles for the page.
	 *
	 * @param Setting_Page $page
	 * @return void
	 */
	protected function global_page_styles( $page ) {
		$style = Enqueue::style( self::PAGE_GLOBALS_STYLES )->src( File_Helper::assets_url() . '/style.css' );

		// Filter for attributes.
		$style = \apply_filters( Hooks::PAGE_GLOBAL_STYLE, $style, $page );

		// If we still have a valid enqueue style.
		if ( \is_a( $style, Enqueue::class ) ) {
			$style->register();
		}
	}

	/**
	 * Registers the global select2 scripts and styles.
	 *
	 * @param Setting_Page $page
	 * @return void
	 */
	protected function global_select2( $page ): void {
		$script = Enqueue::script( self::SELECT2_HANDLE )
			->src( 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' )
			->deps( 'jquery' )
			->footer( false );
		$script = \apply_filters( Hooks::PAGE_SELECT2_SCRIPT, $script, $page );
		if ( \is_a( $script, Enqueue::class ) ) {
			$script->register();
		}

		$style = Enqueue::style( self::SELECT2_HANDLE )
			->src( 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
		$style = \apply_filters( Hooks::PAGE_SELECT2_STYLE, $style, $page );
		if ( \is_a( $style, Enqueue::class ) ) {
			$style->register();
		}
	}
}
