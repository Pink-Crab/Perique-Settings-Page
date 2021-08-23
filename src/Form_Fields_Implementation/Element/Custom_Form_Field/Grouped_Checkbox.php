<?php

declare(strict_types=1);

/**
 * A customer settings field that renders a checkbox group.
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

namespace PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element\Custom_Form_Field;

use PinkCrab\Form_Fields\Abstract_Field;
use PinkCrab\Form_Fields\Traits\Options;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Form_Fields\Traits\Multiple;
use PinkCrab\Form_Fields\Fields\Input_Checkbox;

class Grouped_Checkbox extends Abstract_Field {

	use Options;

	/**
	 * The field type.
	 *
	 * @var string
	 */
	protected $type = 'checkebox_group';

	/**
	 * The checked value for each checkbox.
	 *
	 * @var string
	 */
	protected $checked_value = 'on';

	/**
	 * Set the current value(s)
	 *
	 * @param string|int|float|array<mixed> $current  The current value(s)
	 * @return static
	 */
	public function current( $current = null ) {
		if ( ! empty( $current ) ) {
			$this->current = is_array( $current ) ? $current : array( $current );
		} else {
			$this->current = array();
		}

		return $this;
	}


	/**
	 * Set the checked value for each checkbox.
	 *
	 * @param string $checked_value  The checked value for each checkbox.
	 * @return self
	 */
	public function checked_value( string $checked_value ): self {
		$this->checked_value = $checked_value;
		return $this;
	}

	/**
	 * Returns the select HTML
	 *
	 * @return string
	 */
	public function generate_field_html(): string {

		// Compose assets.
		$inputs     = $this->generate_checkboxes();
		$name       = $this->get_key();
		$attributes = $this->render_attributes();
		$classes    = $this->render_class();
		$disabled   = $this->render_disabled();

		return <<<HTML
		<fieldset name="$name" $classes $attributes $disabled>
			$inputs
		</fieldset>
HTML;
	}

	/**
	 * Renders all checkboxes as a string representation.
	 *
	 * @return string
	 */
	protected function generate_checkboxes(): string {
		return View::print_buffer(
			function() {

				// Checks if the checkbox is selected.
				$is_checked = function( $key ): bool {
					return array_key_exists( $key, $this->current );
				};

				// Loops thorugh each item and creates the checkboxes.
				foreach ( $this->get_options() as $key => $option ) {
					// Create the input.
					$input = new Input_Checkbox( "{$this->get_key()}[{$key}]" );

					$input->checked( $is_checked( $key ) )
						->attribute( 'value', $this->checked_value )
						->label( $option )
						->show_label();

					// Configure the label to show after input.
					$input->label_config()->position_before( false );

					// Render the input.
					$input->render();

				}
			}
		);
	}

}
