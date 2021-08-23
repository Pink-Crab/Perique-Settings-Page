<?php

declare(strict_types=1);

/**
 * Post Selector element rendering service/helper
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

use PinkCrab\Form_Fields\Fields\Select;
use PinkCrab\Form_Fields\Abstract_Field;
use PinkCrab\Perique_Settings_Page\Setting\Field\Post_Selector as Field_Post_Selector;

class Query_Selector {

	/**
	 * The field
	 *
	 * @var Field_Post_Selector
	 */
	protected $field;

	public function __construct( Field_Post_Selector $field ) {
		$this->field = $field;
	}

	/**
	 * Maps the select options.
	 *
	 * @param object[] $objects
	 * @return array
	 */
	protected function map_select_options( $objects ): array {
		$label_mapper = $this->field->get_option_label();
		$value_mapper = $this->field->get_option_value();
		return \array_reduce(
			$objects,
			function( array $options, $object ) use ( $label_mapper, $value_mapper ): array {
				$options[ $value_mapper( $object ) ] = $label_mapper( $object );
				return $options;
			},
			array()
		);
	}


	/**
	 * Renders the form field content for the media library element.
	 *
	 * @return Abstract_Field
	 */
	public function post_selector_element(): Abstract_Field {
		// Set all posts as options.
		$posts   = $this->get_posts( $this->field->get_query_args() );
		$options = $this->map_select_options( $posts );

		return Select::create( $this->field->get_key() )
			->options( $options );
	}

	/**
	 * Gets all the posts based on the passed query.
	 *
	 * @return array
	 */
	private function get_posts(): array {
		return get_posts( $this->field->get_query_args() );
	}
}
