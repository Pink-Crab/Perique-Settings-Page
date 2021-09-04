<?php

declare(strict_types=1);

/**
 * Unit tests for the parent functionality of the Settings Collection
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
 *
 * @group Unit
 * @group Settings
 */

namespace PinkCrab\Perique_Settings_Page\Tests\Unit\Setting;

use stdClass;
use WP_UnitTestCase;
use PinkCrab\Perique_Settings_Page\Setting\Field\Text;
use PinkCrab\Perique_Settings_Page\Setting\Setting_Collection;

class Test_Setting_Collection extends WP_UnitTestCase {

	public function test_is_typed_collection(): void {
		$field      = Text::new( 'foo' );
		$collection = new Setting_Collection( array( $field, 'invalid', new stdClass ) );
		$this->assertCount( 1, $collection );
		$this->assertTrue( $collection->contains( $field ) );

		// Check Push
		$collection->push( new stdClass );
		$this->assertCount( 1, $collection );

		// Check add
		$collection->set( 'invalid', 'not a field' );
		$this->assertCount( 1, $collection );
	}
}
