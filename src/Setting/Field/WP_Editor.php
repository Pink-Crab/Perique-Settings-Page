<?php

declare(strict_types=1);

/**
 * WP_Editor (WYSIWYG Editor)
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
use PinkCrab\Perique_Settings_Page\Setting\Field\Attribute\Placeholder;

class WP_Editor extends Field {

	/**
	 * The type of field.
	 */
	public const TYPE = 'wp_editor';

	/**
	 * The WP_Editor options.
	 *
	 * @see https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
	 * @var array<string, string>
	 */
	protected $options = array();

	/**
	 * Static constructor for field.
	 *
	 * @param string $key
	 * @return Text
	 */
	public static function new( string $key ): WP_Editor {
		return new self( $key );
	}

	public function __construct( string $key ) {
		parent::__construct( $key, self::TYPE );

		// Sets the default sanitize callback to use the wp post sanitizer.
		$this->callbacks['sanitize'] = 'wp_kses_post';
	}

	/**
	 * Get the WP_Editor options.
	 *
	 * @return array
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Set the WP_Editor options.
	 *
	 * @param array $options  The WP_Editor options.
	 * @return self
	 */
	public function set_options( array $options ): self {
		$this->options = $options;
		return $this;
	}
}
