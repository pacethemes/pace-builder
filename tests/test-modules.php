<?php

/**
 * Class ModulesTest
 */
class ModulesTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
		require_once( PTPB()->plugin_path() . '/includes/class-pace-builder-stage.php' );
	}

	public function test_modules_exist() {
		// check if all modules being queued have a processor
		$modules = PTPB()->stage()->get_modules();
		foreach ( $modules as $name => $props ) {
			$name = ucwords( $name );
			$cls  = "PTPB_{$name}_Module";
			$this->assertTrue( class_exists( $cls ) );
			$this->assertInstanceOf( 'PTPB_Module', new $cls( null ) );
			$this->assertTrue( method_exists( $cls, 'get_content' ) );
		}
	}

	public function test_extract_data_attr() {
		$string = '<div class="ptpb-cf7" id="ptpb_s2_r1_c1_m1" data-pb-process="true" data-type="contactform7" data-cf7-id="2482" data-cf7-title="CF1"><h3>SOME TITLE</h3></div>';
		$module = new PTPB_Module( null );
		$this->assertCount( 4, $module->extract_data_attr( $string ) );

		$string = '<div class="ptpb-cf7" id="ptpb_s2_r1_c1_m1" data-pb-process="true" data-type="contactform7" data-cf7-id="2482"><h3>SOME TITLE</h3></div>';
		$this->assertCount( 3, $module->extract_data_attr( $string ) );
	}

	public function test_modules_exist1() {

		$string = 'Unit tests are sweet';

		$this->assertEquals( 'Unit tests are sweet', $string );
		$this->assertNotEquals( 'Unit tests suck', $string );
	}


}

