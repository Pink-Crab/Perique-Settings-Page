<?php

declare(strict_types=1);

/**
 * Repeater Field renderer
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

namespace PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element;

use stdClass;
use PinkCrab\Collection\Collection;
use PinkCrab\Collection\Traits\Indexed;
use PinkCrab\Form_Fields\Fields\Select;
use PinkCrab\Form_Fields\Abstract_Field;
use PinkCrab\Form_Fields\Fields\Raw_HTML;
use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Setting\Field\Repeater;
use PinkCrab\Perique_Settings_Page\Setting\Setting_Collection;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element_Default;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element_Factory;
use PinkCrab\Perique_Settings_Page\Setting\Field\Post_Selector as Field_Post_Selector;

class Repeater_Builder {

	/**
	 * The repeater
	 *
	 * @var Repeater
	 */
	protected $repeater;

	/**
	 * Repeater fields
	 *
	 * @var Setting_Collection
	 */
	protected $fields;

	/**
	 * Creates the single fields.
	 *
	 * @var Element_Factory
	 */
	protected $element_factory;

	public function __construct( Repeater $repeater ) {
		$this->repeater        = $repeater;
		$this->fields          = $repeater->get_fields();
		$this->element_factory = new Element_Factory();
	}

	public function create_sub_fields( $collection ) {
		return array_map(
			array( Element_Factory::class, 'from_field' ),
			$collection->to_array()
		);
	}

	/**
	 * Map all settings with dynamically generated ids.
	 *
	 * @return \PinkCrab\Perique_Settings_Page\Setting\Setting_Collection
	 */
	protected function map_with_key_placeholders(): Setting_Collection {
		return $this->fields->copy()->map(
			function( Field $field ) {
				$key = \sprintf( '%s[%s][%%i%%]', $this->repeater->get_key(), $field->get_key() );
				return $field->clone_as( $key );
			}
		);
	}

	/**
	 * Render the repeater content.
	 *
	 * @return string
	 */
	public function render_repeater(): string {
		$collection = $this->map_with_key_placeholders();
		$collection = $this->map_field_groups( $collection );
		return $this->repeater->get_layout() === 'row'
			? $this->render_as_rows( $collection )
			: $this->render_as_columns( $collection );
	}

	protected function render_as_rows( Setting_Collection $collection ): string {
		return 'todo';
	}

	protected function render_as_columns( Collection $columns ): string {
		$wrapper_template = sprintf(
			'<div id="%1$s" class="repeater__columns">%%s</div>
			<button id="%1$s__add" class="add_column_filed" data-field-id=%1$s>Add</button>
			<div id="%1$s__template" class="template" style="display:none">%%s</div>
			<input type="hidden" id="%1$s_sortorder" name="%1$s[sortorder]" value="%2$s">',
			$this->repeater->get_key(),
			join( ',', \range( 0, ( count( $this->repeater->get_value()->as_indexed() ) - 1 ) ) )
		);

		// Render all field groups into label and field groups.
		$fields_html = $this->render_field_groups( $columns );

		// Wrap each group into indexed div with controls
		$fields_html = array_map( array( $this, 'column_group_wrapper' ), $fields_html, array_keys( $fields_html ) );

		return \sprintf( $wrapper_template, join( PHP_EOL, $fields_html ), htmlentities( $this->get_column_template(), ENT_QUOTES ) );
	}

	/**
	 * Wraps the group of fields into a wrapper with controls.
	 *
	 * @param string $html
	 * @param int|null $index
	 * @return string
	 */
	public function column_group_wrapper( string $html, ?int $index = null ): string {
		$index_a = $index ?? '%i%';
		return sprintf(
			'<div id="%s" class="repeater_group__column" data-row="%s">%s%s</div>',
			"repeater__{$this->repeater->get_key()}__{$index_a }",
			$index_a,
			$this->render_group_header( $index ),
			$html
		);
	}

	public function get_column_template(): string {

		// Extract all of the template fields into a basic Indexed Collection.
		$collection = new class($this->fields->copy()->to_array()) extends Collection{
			use Indexed;
		};

		return $this->column_group_wrapper( $collection->map( array( $this, 'render_group_field' ) )->join() );
	}

	/**
	 * Undocumented function
	 *
	 * @param int $index
	 * @return string
	 */
	public function render_group_header( ?int $index = null ): string {
		$index     = $index ?? '%i%';
		$group_key = "repeater__{$this->repeater->get_key()}__{$index}";

		return <<<HTML
<div class="repeater_group__controls">
	<div class="group_handle"><button>M</button></div>
	<div class="remove_group"><button data-remove-group="$group_key">X</button></div>
</div>
HTML;
	}

	/**
	 * Renders a collection of field grounds into an array of HTML strings (for each group.)
	 *
	 * @param \PinkCrab\Collection\Collection $collection
	 * @return string[]
	 */
	protected function render_field_groups( Collection $collection ): array {
		return $collection->map(
			function( $fields ): array {
				return array_map( array( $this, 'render_group_field' ), $fields );
			}
		)->map( 'join' )->to_array();
	}

	/**
	 * Renders a single field as a group with label and input.
	 *
	 * @param \PinkCrab\Form_Fields\Abstract_Field $field
	 * @return string
	 */
	public function render_group_field( Field $field ): string {
		$r = \sprintf(
			'<div class="repeater_group__field %s">
	<div class="repeater_group__label"><label for="%s">%s</label></div>
	<div class="repeater_group__input">%s</div>
</div>',
			$field->get_type(),
			$field->get_key(),
			$field->get_label(),
			$this->render_field( $field )
		);

		return $r;
	}

	/**
	 * Renders a fields html element.
	 *
	 * @param \PinkCrab\Form_Fields\Abstract_Field $field
	 * @return string
	 */
	protected function render_field( Field $field ): string {
		return $this->element_factory->shared_attributes(
			$field,
			$this->element_factory->create_element( $field ),
		)->as_string();
	}

	/**
	 * Maps the values to the fields as an indexed group.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Setting_Collection $fields
	 * @return \PinkCrab\Collection\Collection
	 */
	protected function map_field_groups( Setting_Collection $fields ): Collection {

		// Collection of field groups per entry.
		$groups = new Collection();

		// Loop through all current value groups.
		foreach ( $this->repeater->get_value()->as_indexed() as $index => $values ) {
			$group = $this->set_group_values(
				$index,
				$this->field_group_template(),
				$values
			);
			$groups->push( $group );
		}

		return $groups;
	}

	/**
	 * Takes an array of values and matches them with values in a stdClass object.
	 *
	 * @param array<string, Field> $group
	 * @param stdClass $values
	 * @return array<string, Field>
	 */
	protected function set_group_values( int $index, array $group, stdClass $values ): array {
		$return = array();
		foreach ( $group as $field_key => $field ) {
			$mapped_key = \str_replace( '%i%', $index, $field->get_key() );
			$return[]   = $field->clone_as( $mapped_key )->set_value( \property_exists( $values, $field_key ) ? $values->{$field_key} : null );
		}
		return $return;
	}

	/**
	 * Gets a clone of the field group template.
	 *
	 * @return array<string, Field>
	 */
	protected function field_group_template(): array {
		return $this->fields->map(
			function( $e ) {
				return clone $e;
			}
		)->to_array();
	}

}
