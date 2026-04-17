<?php

declare( strict_types=1 );

/**
 * Main module for the Settings Page.
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

namespace PinkCrab\Perique_Settings_Page\Registration;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\Module;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Settings_Page\Rest\Picker_Rest_Controller;

class Settings_Page_Module implements Module {

	/** @inheritDoc */
	public function get_middleware(): ?string {
		return Settings_Page_Middleware::class;
	}

	/** @inheritDoc */
	public function pre_boot( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {} // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed

	/** @inheritDoc */
	public function pre_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
		$loader->action( 'rest_api_init', array( new Picker_Rest_Controller(), 'register' ) );
	}

	/** @inheritDoc */
	public function post_register( App_Config $config, Hook_Loader $loader, DI_Container $di_container ): void {} // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
}
