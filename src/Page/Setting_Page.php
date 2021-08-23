<?php

declare(strict_types=1);

/**
 * The abstract class used to create Page Groups within WP-Admin
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

namespace PinkCrab\Perique_Settings_Page\Page;

use PinkCrab\Enqueue\Enqueue;
use Exception;
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Settings_Page\Setting\Abstract_Settings;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Admin_Menu\Exception\Page_Exception;
use PinkCrab\Perique_Settings_Page\Util\File_Helper;

abstract class Setting_Page implements Page {

	/**
	 * The pages menu slug.
	 *
	 * @var string|null
	 */
	protected $parent_slug;

	/**
	 * The pages menu slug.
	 *
	 * @var string
	 */
	protected $page_slug;

	/**
	 * The menu title
	 *
	 * @var string
	 */
	protected $menu_title;

	/**
	 * The pages title
	 *
	 * @var string
	 */
	protected $page_title;

	/**
	 * The pages position, in relation to other pages in group.
	 *
	 * @var int|null
	 */
	protected $position = null;

	/**
	 * The min capabilities required to access page.
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * The settings data.
	 *
	 * @var Abstract_Settings
	 */
	protected $settings;

	/**
	 * Returns the full name of the settings class.
	 *
	 * @return string
	 */
	abstract public function settings_class_name(): string;

	/**
	 * @return Abstract_Settings
	 */
	public function settings(): Abstract_Settings {
		if ( $this->settings === null ) {
			throw Page_Exception::undefined_property( 'settings', $this );
		}
		return $this->settings;
	}

	/**
	 * @return string|null
	 */
	public function parent_slug(): ?string {
		return $this->parent_slug;
	}

	/**
	 * @return string
	 */
	public function slug(): string {
		if ( $this->page_slug === null ) {
			throw Page_Exception::undefined_property( 'page_slug', $this );
		}
		return $this->page_slug;
	}

	/**
	 * @return string
	 */
	public function menu_title(): string {
		if ( $this->menu_title === null ) {
			throw Page_Exception::undefined_property( 'menu_title', $this );
		}
		return $this->menu_title;
	}

	/**
	 * @return string|null
	 */
	public function page_title(): ?string {
		return $this->page_title;
	}

	/**
	 * @return int|null
	 */
	public function position(): ?int {
		return $this->position;
	}

	/**
	 * @return string
	 */
	public function capability(): string {
		return $this->capability;
	}

	/**
	 * Renders the settings for this page using the DI Container.
	 *
	 * @param DI_Container $container
	 * @return Abstract_Settings|null
	 */
	public function construct_settings( DI_Container $container ): void {
		$settings = $container->create( $this->settings_class_name() );

		// Throw exception if not settings created.
		if ( ! is_a( $settings, Abstract_Settings::class ) ) {
			// @TODO
		}

		$this->settings = $settings;
	}

	/**
	 * Renders the page view.
	 *
	 * @return callable
	 */
	public function render_view(): callable {
		return function() {
			throw new Exception( 'SETTINGS PAGE render_view() CALLED INCORRECTLY.' );
		};
	}

	/**
	 * Returns any scripts that should be enqueued.
	 *
	 * @return Enqueue|null
	 */
	public function enqueue_scripts(): ?Enqueue {
		return null;
	}

	/**
	 * Returns any styles that should be enqueued.
	 *
	 * @return Enqueue|null
	 */
	public function enqueue_styles(): ?Enqueue {
		return Enqueue::style( $this->slug() )
			->src( File_Helper::get_file_url( dirname( __DIR__, 1 ) . '/Form/style.css' ) );
	}

}
