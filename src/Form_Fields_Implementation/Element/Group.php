<?php

declare(strict_types=1);

/**
 * Helper for groups of fields
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

use PinkCrab\Collection\Collection;
use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Form_Fields_Implementation\Element_Factory;

class Group {

	protected $element_factory;

	public function __construct() {
		$this->element_factory = new Element_Factory();
	}

	/**
	 * Renders a fields html element.
	 *
	 * @param \PinkCrab\Form_Fields\Abstract_Field $field
	 * @return string
	 */
	public function render_field( Field $field ): string {
		return $this->element_factory->shared_attributes(
			$field,
			$this->element_factory->create_element( $field )
		)->as_string();
	}

	/**
	 * Renders a collection of field grounds into an array of HTML strings (for each group.)
	 *
	 * @param \PinkCrab\Collection\Collection<Field[]> $collection
	 * @return string[]
	 */
	public function render_field_groups( Collection $collection ): array {
		return $collection->map(
			/**
			 * Renders each group of fields into an collection of HTML fields with labels.
			 *
			 * @param Field[] $fields
			 * @return string[]
			 */
			function( array $fields ): array {
				return array_map( array( $this, 'render_group_field' ), $fields );
			}
		)
		->map( 'join' )
		->to_array();
	}

	/**
	 * Renders a single field as a group with label and input.
	 *
	 * @param \PinkCrab\Form_Fields\Abstract_Field $field
	 * @return string
	 */
	public function render_group_field( Field $field ): string {
		return \sprintf(
			'<div class="group_field %s">
	<div class="group_field__label"><label for="%s">%s</label></div>
	<div class="group_field__input">%s</div>
</div>',
			$field->get_type(),
			$field->get_key(),
			$field->get_label(),
			$this->render_field( $field )
		);
	}

}
