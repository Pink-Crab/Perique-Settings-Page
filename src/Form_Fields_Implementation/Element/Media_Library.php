<?php

declare(strict_types=1);

/**
 * Media Library element rendering service/helper
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
use PinkCrab\Perique_Settings_Page\Util\File_Helper;

class Media_Library {

	/**
	 * Renders the form field content for the media library element.
	 *
	 * @param \PinkCrab\Form_Fields\Abstract_Field $field
	 * @return string
	 */
	public function render_form_field_content( Abstract_Field $field ): string {

		$key   = $field->get_key();
		$value = $field->get_current();

		// Attempt to get the media data.
		$media = ! empty( $field->get_current() )
			? wp_get_attachment_image_src( \absint( $field->get_current() ) )
			: null;
		$title = $media ? esc_html( get_the_title( \absint( $field->get_current() ) ) ) : '';
		$src   = $media ? esc_url( $media[0] ) : File_Helper::get_file_url( dirname( __DIR__, 3 ) . '/assets/no-image.png' );

		return <<<HTML
<div id="media_upload_$key" class="settings-page-field__input media-upload">
	<div class="media_upload__preview">
		<figure>
			<img src="$src" alt="$title" data-media-library-preview="$key">
			<figcaption id="{$key}_title">$title</figcaption>
		</figure>
	</div>
	<div class="media_upload__upload">
		<input type="hidden" name="$key" id="$key" data-media-library-file-name="$key" value="$value">
		<button class="button access_media_library" data-key="$key" >Select</button>
		<button class="button media_upload_clear" data-key="$key" >Clear</button>
	</div>
</div>

HTML;
	}
}
