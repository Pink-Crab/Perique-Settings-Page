<?php

declare(strict_types=1);

/**
 * Post Selector
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

namespace PinkCrab\Perique_Settings_Page\Setting\Field;

use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Setting\Field\Attribute\Data;
use PinkCrab\Perique_Settings_Page\Setting\Field\Attribute\Query;
use PinkCrab\Perique_Settings_Page\Setting\Field\Attribute\Options;
use PinkCrab\Perique_Settings_Page\Setting\Field\Attribute\Multiple;

class Post_Selector extends Field {

	/**
	 * The type of field.
	 */
	public const TYPE = 'post_selector';

	// Attributes.
	use Multiple, Data, Options, Query;

	/**
	 * Static constructor for field.
	 *
	 * @param string $key
	 * @return static
	 */
	public static function new( string $key ): Post_Selector {
		return new self( $key );
	}

	public function __construct( string $key ) {
		parent::__construct( $key, self::TYPE );
	}

	/**
	 * Returns a the defined label callback or fallback to map the options title.
	 *
	 * @return callable(\WP_Post):string
	 */
	public function get_option_label(): callable {
		return $this->callbacks['option_label'] ?? function( \WP_Post $post ): string {
			return $post->post_title;
		};
	}

	/**
	 * Returns a the defined label callback or fallback to map the options value.
	 *
	 * @return callable(mixed):string
	 */
	public function get_option_value(): callable {
		return $this->callbacks['option_label'] ?? function( \WP_Post $post ): string {
			return (string) $post->ID;
		};
	}
}
