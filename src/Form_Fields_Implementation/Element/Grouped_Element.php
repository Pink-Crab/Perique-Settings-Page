<?php

declare(strict_types=1);

/**
 * Helper/Renderer for all grouped sets of fields.
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

namespace PinkCrab\Perique_Settings_Page\Form\Element;

use PinkCrab\Form_Fields\Abstract_Field;
use PinkCrab\Perique_Settings_Page\Setting\Field\Select;
use PinkCrab\Perique_Settings_Page\Setting\Field\Checkbox_Group;

class Grouped_Element {

	/**
	 * Renders a grouped checkbox as custom HTML.
	 *
	 * @param \PinkCrab\Perique_Settings_Page\Setting\Field\Checkbox_Group $field
	 * @return \PinkCrab\Form_Fields\Abstract_Field
	 */
	public function render_grouped_checkboxes( Checkbox_Group $field ):Abstract_Field {
		dump( $field );
		return new Select( $field->get_key() );
	}
}
