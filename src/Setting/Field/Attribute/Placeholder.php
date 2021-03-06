<?php

declare(strict_types=1);

/**
 * Placeholder attribute
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

trait Placeholder {

	/**
	 * Sets the placeholder for this input.
	 *
	 * @param string $placeholder
	 * @return self
	 */
	public function set_placeholder( string $placeholder ):self {
		$this->set_attribute( 'placeholder', $placeholder );
		return $this;
	}

	/**
	 * Checks if a placeholder exists.
	 *
	 * @return bool
	 */
	public function has_placeholder(): bool {
		return \array_key_exists( 'placeholder', $this->get_attributes() );
	}

	/**
	 * Gets the placeholder if set.
	 *
	 * @return string|null
	 */
	public function get_placeholder(): ?string {
		return $this->has_placeholder()
			? $this->get_attributes()['placeholder']
			: null;
	}
}
