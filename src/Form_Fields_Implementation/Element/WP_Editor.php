<?php

declare(strict_types=1);

/**
 * WP_Editor element rendering service/helper
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

use PinkCrab\Form_Fields\Abstract_Field;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique_Settings_Page\Setting\Field\Field;
use PinkCrab\Perique_Settings_Page\Setting\Field\WP_Editor as WP_Editor_Field;

class WP_Editor {

	/**
	 * The setting
	 *
	 * @var WP_Editor_Field
	 */
	protected $setting;

	public function __construct( WP_Editor_Field $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Renders the form field content for the media library element.
	 *
	 * @param Abstract_Field $field
	 * @return string
	 */
	public function render_form_field_content( Abstract_Field $field ): string {
		return View::print_buffer(
			function() use ( $field ) {
				wp_editor( $field->get_current(), $field->get_key(), $this->setting->get_options() );
			}
		);
	}

}
