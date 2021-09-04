<?php

declare(strict_types=1);

/**
 * Interface for a settings repository
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

namespace PinkCrab\Perique_Settings_Page\Form_Fields_Implementation;

use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Admin_Menu\Page\Page;
use PinkCrab\Perique_Settings_Page\Util\Hooks;
use PinkCrab\Perique_Settings_Page\Util\File_Helper;
use PinkCrab\Perique_Settings_Page\Util\Form_Helper;
use PinkCrab\Perique_Settings_Page\Page\Setting_Page;
use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Setting\Setting_View;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element_Default;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element_Factory;

class Form_Fields_Renderer implements Setting_View {

	/**
	 * Renders the passed view as a callable.
	 *
	 * @return callable
	 */
	public function generate_view_callback( Page $page ): callable {
		return function() use ( $page ) {
			print $this->parse_view( $page );
		};
	}

	/**
	 * Fires the pages header action and catches the result.
	 *
	 * @param PinkCrab\Perique_Admin_Menu\Page\Setting_Page $page
	 * @return string
	 */
	protected function generate_header( Setting_Page $page ): string {
		return View::print_buffer(
			function() use ( $page ) {
				\do_action( Hooks::settings_page_header_action( $page->slug() ), $page );
			}
		);
	}

	/**
	 * Fires the pages footer action and catches the result.
	 *
	 * @param PinkCrab\Perique_Admin_Menu\Page\Setting_Page $page
	 * @return string
	 */
	protected function generate_footer( Setting_Page $page ): string {
		return View::print_buffer(
			function() use ( $page ) {
				\do_action( Hooks::settings_page_footer_action( $page->slug() ), $page );
			}
		);
	}

	/**
	 * Parses the settings page view.
	 *
	 * @return string
	 */
	protected function parse_view( Setting_Page $page ): string {
		return View::print_buffer(
			function() use ( $page ) {

				/**
				 * Filters all of the view data used to generate the settings page
				 *
				 * @param array{title:string,header:string,page:string,nonce:string,fields:string,footer:string} $data
				 * @param Setting_Page $page
				 * @param Abstract_Settings $settings
				 * @return array{title:string,header:string,page:string,nonce:string,fields:string,footer:string} $data
				 */
				$data = \apply_filters(
					'foo',
					$this->compile_view_data( $page ),
					$page,
					$page->settings()
				);

				/**
				 * Filters the view path.
				 *
				 * @param string $view_path
				 * @param Setting_Page $page
				 * @return string view_path
				 */
				$view = \apply_filters(
					Hooks::settings_page_view_path( $page->slug() ),
					File_Helper::assets_path() . '/form_view.php',
					$page
				);

				include $view;
			}
		);
	}

	/**
	 * Compiles all the page view data.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Setting_Page $page
	 * @return array{title:string,header:string,page:string,nonce:string,fields:string,footer:string} $data
	 */
	protected function compile_view_data( Setting_Page $page ): array {
		$data['title']  = esc_html( $page->page_title() );
		$data['header'] = $this->generate_header( $page );
		$data['page']   = esc_html( $page->slug() );
		$data['nonce']  = \wp_create_nonce( Form_Helper::nonce_handle( $page->slug() ) );
		$data['fields'] = join( PHP_EOL, $this->parse_fields( $page ) );
		$data['footer'] = $this->generate_footer( $page );

		return $data;
	}

	/**
	 * Parses each field to a string representation of the field.
	 *
	 * @param \PinkCrab\Perique_Admin_Menu\Page\Setting_Page $page
	 * @return string[]
	 */
	protected function parse_fields( Setting_Page $page ): array {
		return array_map(
			function( Field $field ): string {

				// Set all parameters
				$input           = Element_Factory::from_field( $field );
				$wrapper_classes = $this->render_wrapper_classes( $field );
				$icon            = $this->render_icon( $field );
				$label           = $field->get_label();
				$description     = $this->render_description( $field );
				$inline_js       = $this->render_inline_js( $field );

				// Generate the element.
				return <<<HTML
                <div class="$wrapper_classes">
                    <div class="settings-page-field__title">
                        $icon $label
                    </div>
                    <div class="settings-page-field__input">
                        $input
                        $description
                        $inline_js
                    </div>
                </div>

    HTML;
			},
			$page->settings()->export()
		);
	}

	/**
	 * Renders the wrapper class for the field.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @return void
	 */
	protected function render_wrapper_classes( Field $field ) {
		$classes = apply_filters( Hooks::ELEMENT_WRAPPER_CLASS, Element_Default::WRAPPER_CLASSES, $field );
		// if ( \method_exists( $field, 'is_select2' ) ) {
		// 	$classes = array_merge( $classes, array( $field->select2_class() ) );
		// }
		return join( ' ', array_merge( $classes, array( $field->get_type() ) ) );
	}

	/**
	 * Renders the fields icon if defined.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @return string
	 */
	protected function render_icon( Field $field ): string {
		if ( ! is_null( $field->get_icon() ) ) {
			return \sprintf(
				'<span class="settings-page-field__icon"><img src="%s" alt="%s"></span>',
				\esc_url( $field->get_icon() ),
				\esc_html( $field->get_label() )
			);
		}
		return '';
	}

	/**
	 * Renders the field description.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @return string
	 */
	protected function render_description( Field $field ): string {
		if ( $field->get_description() !== '' ) {
			return \sprintf( "<p class='description'>%s</p>", $field->get_description() );
		}
		return '';
	}

	/**
	 * Renders the field description.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @return string
	 */
	public function render_inline_js( Field $field ): string {
		$return = '';
		// Maybe render selec2
		if ( \method_exists( $field, 'is_select2' ) ) {
			$return .= \sprintf( '<script>%s</script>', $field->get_select2_script() );
		}
		return $return;
	}
}
