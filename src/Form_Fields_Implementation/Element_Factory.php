<?php

declare(strict_types=1);

/**
 * Renders form fields.
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

use PinkCrab\Form_Fields\Label_Config;
use PinkCrab\Form_Fields\Abstract_Field;
use PinkCrab\Form_Fields\Fields\Raw_HTML;
use PinkCrab\Form_Fields\Fields\Input_Text;
use PinkCrab\Form_Fields\Fields\Input_Radio;
use PinkCrab\Form_Fields\Fields\Input_Number;
use PinkCrab\Perique_Settings_Page\Util\Hooks;
use PinkCrab\Form_Fields\Fields\Input_Checkbox;
use PinkCrab\Perique_Settings_Page\Setting\Field\Text;
use PinkCrab\Form_Fields\Fields\Select as FieldsSelect;
use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Setting\Field\Radio;
use PinkCrab\Perique_Settings_Page\Setting\Field\Number;
use PinkCrab\Perique_Settings_Page\Setting\Field\Select;
use PinkCrab\Perique_Settings_Page\Setting\Field\Checkbox;
use PinkCrab\Perique_Settings_Page\Setting\Field\WP_Editor;
use PinkCrab\Perique_Settings_Page\Setting\Field\Media_Library;
use PinkCrab\Perique_Settings_Page\Setting\Field\Post_Selector;
use PinkCrab\Perique_Settings_Page\Setting\Field\Checkbox_Group;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element_Default;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element\Query_Selector;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element\WP_Editor as Element_WP_Editor;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element\Custom_Form_Field\Grouped_Checkbox;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element\Media_Library as Element_Media_Library;

class Element_Factory {

	/**
	 * Creates a form element from a settings field.
	 *
	 * @param \PinkCrab\Form_Fields\Abstract_Field $field
	 * @return string
	 */
	public static function from_field( Field $field ): string {
		$factory = new self();

		$form_element = $factory->create_element( $field );
		$form_element = $factory->shared_attributes( $field, $form_element );
		return $form_element->as_string();

	}

	/**
	 * Renders the
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	public function create_element( Field $field ): Abstract_Field {
		switch ( \get_class( $field ) ) {

			case Number::class:
				$element = $this->maybe_input_attributes( $field, Input_Number::create( $field->get_key() ) );
				return $this->maybe_number_attributes( $field, $element );

			case Text::class:
				return $this->maybe_input_attributes( $field, Input_Text::create( $field->get_key() ) );

			case Checkbox::class:
				$element = $this->maybe_input_attributes( $field, Input_Checkbox::create( $field->get_key() ) );
				$element->current( $field->get_checked_value() );
				$element->checked( '' !== $field->get_value() );
				return $element;

			case Select::class:
				$element = FieldsSelect::create( $field->get_key() )
					->options( $field->get_options() );
				return $this->maybe_multiple( $field, $element );

			case Media_Library::class:
				$element = Raw_HTML::create( $field->get_key() )
					->current( $field->get_value() );
				$element->content( array( new Element_Media_Library(), 'render_form_field_content' ) );
				return $element;

			case WP_Editor::class:
				$element = Raw_HTML::create( $field->get_key() )
					->current( $field->get_value() );
				$element->content( array( new Element_WP_Editor( $field ), 'render_form_field_content' ) );
				return $element;

			case Post_Selector::class:
				$element = ( new Query_Selector( $field ) )
					->post_selector_element( $field );
				return $this->maybe_multiple( $field, $element );

			case Checkbox_Group::class:
				return Grouped_Checkbox::create( $field->get_key() )
					->options( $field->get_options() )
					->checked_value( $field->get_checked_value() );

			case Radio::class:
				dump($field);
				$r = Input_Radio::create( $field->get_key() )
					->options( $field->get_options() )
					->show_label()
					->label_position( Label_Config::AFTER_INPUT )
					->current($field->get_value());
				dump($r);
				return $r;

			default:
				$element = Raw_HTML::create( $field->get_key() );
				$element->content(
					function( $element ) {
						return print_r( $element, true );
					}
				);
				return $element;
		}
	}

	/**
	 * SHARED FIELD HELPERS
	 */
	/**
	 * Sets all shared properties
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	public function shared_attributes( Field $field, Abstract_Field $element ): Abstract_Field {

		// Maybe add the description.
		$element = $this->maybe_description( $field, $element );

		// Maybe add all data attributes.
		$element = $this->add_data_attributes( $field, $element );

		if ( ! is_a( $field, Checkbox::class ) ) {
			$element = $element->current( $field->get_value() );
		}

		return $element
			->class( $this->element_classes( $field ) )
			->disabled( $field->is_disabled() )
			->read_only( $field->is_read_only() );
	}

	/**
	 * Adds the description if defined.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	private function maybe_description( Field $field, Abstract_Field $element ): Abstract_Field {
		if ( '' !== $field->get_description() ) {
			$element->description( $field->get_description() );
		}
		return $element;
	}

	/**
	 * Generates the
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @return string
	 */
	private function element_classes( Field $field ): string {
		$classes = \array_merge( Element_Default::INPUT_CLASSES, array( $field->get_type() ) );

		/**
		 * Filters the element wrapper classes.
		 *
		 * @param string[] Current wrapper classes.
		 * @param Field The current field being processed.
		 * @return string[] Wrapper classes.
		 */
		$classes = apply_filters( Hooks::ELEMENT_INPUT_CLASS, $classes, $field );

		return join( ' ', $classes );
	}

	/**
	 * Sets the attributes for all <input> fields.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	private function maybe_input_attributes( Field $field, Abstract_Field $element ): Abstract_Field {

		// Only add valid attributes for inputs.
		$allowed_input_attributes = array( 'placeholder', 'pattern', 'title' );
		$valid_attributes         = array_filter(
			$field->get_attributes(),
			function( string $attribute ) use ( $allowed_input_attributes ): bool {
				return in_array( $attribute, $allowed_input_attributes, true );
			},
			\ARRAY_FILTER_USE_KEY
		);

		// Loop through each allowed and add to element.i
		foreach ( $valid_attributes as $key => $value ) {
			$element->attribute( $key, $value );
		}

		return $element;
	}

	/**
	 * Adds all number attributes.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	private function maybe_number_attributes( Field $field, Abstract_Field $element ): Abstract_Field {
		// Only add valid attributes for inputs.
		$allowed_input_attributes = array( 'min', 'max', 'step' );
		$valid_attributes         = array_filter(
			$field->get_attributes(),
			function( string $attribute ) use ( $allowed_input_attributes ): bool {
				return in_array( $attribute, $allowed_input_attributes, true );
			},
			\ARRAY_FILTER_USE_KEY
		);

		// Loop through each allowed and add to element.i
		foreach ( $valid_attributes as $key => $value ) {
			$element->attribute( $key, $value );
		}

		return $element;
	}

	/**
	 * Adds all number attributes.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	public function maybe_multiple( Field $field, Abstract_Field $element ): Abstract_Field {
		if ( \method_exists( $field, 'is_multiple' ) ) {
			$element->multiple( $field->is_multiple() );
		}

		return $element;
	}

	/**
	 * Sets all data attributes to the element.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Field $field
	 * @param \PinkCrab\Form_Fields\Abstract_Field $element
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	private function add_data_attributes( Field $field, Abstract_Field $element ): Abstract_Field {
		$data_attributes = array_filter(
			$field->get_attributes(),
			function( string $attribute ): bool {
				return strpos( $attribute, 'data-' ) === 0;
			},
			\ARRAY_FILTER_USE_KEY
		);

		// Loop through each allowed and add to element.i
		foreach ( $data_attributes as $key => $value ) {
			$element->attribute( $key, $value );
		}

		return $element;
	}

}
