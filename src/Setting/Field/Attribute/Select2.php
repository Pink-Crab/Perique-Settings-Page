<?php

declare(strict_types=1);

/**
 * The Select2 Attribute
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

namespace PinkCrab\Perique_Settings_Page\Setting\Field\Attribute;

trait Select2 {

	/**
	 * The script used to register select2
	 *
	 * @var string|null $select2_script
	 */
	protected $select2_script = null;

	/**
	 * Sets the select2 for this input/select.
	 *
	 * @param string $select2
	 * @return self
	 */
	public function use_select2( bool $select2 = true ):self {

		// Remove if set to false.
		if ( false === $select2 && $this->is_select2() ) {
			$key = array_search( 'select2', $this->flags, true );
			unset( $this->flags[ $key ] );
			return $this;
		}

		$this->flags[] = 'select2';
		return $this;
	}

	/**
	 * Checks if a select2 exists.
	 *
	 * @return bool
	 */
	public function is_select2(): bool {
		return \in_array( 'select2', $this->get_flags(), true );
	}

	/**
	 * Sets the script used to register the select2 initialisation script
	 *
	 * @param string|null $script
	 * @return void
	 */
	public function set_select2_script( ?string $script = null ) {
		$this->select2_script = $script;
	}

	/**
	 * Get string|null
	 *
	 * @return $string
	 */
	public function get_select2_script(): string {
		return $this->select2_script ?? \sprintf( 'jQuery(document).ready(function($) {$(\'.%s\').select2({width: \'100%%\',});});', \esc_attr( $this->select2_class() ) );
	}

	public function select2_class(): string {
		return $this->is_select2() ? apply_filters( 'ccc', 'pc_select2', $this ) : '';
	}
}
