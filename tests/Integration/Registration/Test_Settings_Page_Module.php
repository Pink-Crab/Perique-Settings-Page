<?php

declare( strict_types=1 );

/**
 * Integration tests for Settings_Page_Module.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique_Settings_Page
 *
 * @group Integration
 * @group Registration
 */

namespace PinkCrab\Perique_Settings_Page\Tests\Integration\Registration;

use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\App_Config;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Perique_Settings_Page\Registration\Settings_Page_Module;
use PinkCrab\Perique_Settings_Page\Registration\Settings_Page_Middleware;

class Test_Settings_Page_Module extends WP_UnitTestCase {

	/** @testdox get_middleware returns the Settings_Page_Middleware FQCN. */
	public function test_get_middleware(): void {
		$this->assertSame( Settings_Page_Middleware::class, ( new Settings_Page_Module() )->get_middleware() );
	}

	/** @testdox pre_boot is a no-op. */
	public function test_pre_boot(): void {
		$module    = new Settings_Page_Module();
		$config    = new App_Config();
		$loader    = $this->createMock( Hook_Loader::class );
		$container = $this->createMock( DI_Container::class );

		// Should not throw.
		$module->pre_boot( $config, $loader, $container );
		$this->assertTrue( true );
	}

	/** @testdox pre_register registers the Picker_Rest_Controller on rest_api_init. */
	public function test_pre_register_hooks_rest_api_init(): void {
		$module    = new Settings_Page_Module();
		$config    = new App_Config();
		$loader    = $this->createMock( Hook_Loader::class );
		$container = $this->createMock( DI_Container::class );

		$loader->expects( $this->once() )
			->method( 'action' )
			->with( 'rest_api_init', $this->isType( 'array' ) );

		$module->pre_register( $config, $loader, $container );
	}

	/** @testdox post_register is a no-op. */
	public function test_post_register(): void {
		$module    = new Settings_Page_Module();
		$config    = new App_Config();
		$loader    = $this->createMock( Hook_Loader::class );
		$container = $this->createMock( DI_Container::class );

		// Should not throw.
		$module->post_register( $config, $loader, $container );
		$this->assertTrue( true );
	}
}
