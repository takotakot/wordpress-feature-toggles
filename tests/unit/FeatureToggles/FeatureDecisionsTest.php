<?php

namespace Tests;

use FeatureToggles\FeatureToggles\FeatureDecisions;
use FeatureToggles\FeatureToggles\ToggleRouter;

/**
 * FeatureDecisions のテストクラス
 */
class FeatureDecisionsTest extends TestCase {

	/**
	 * Example feature が有効であるかどうかをテスト
	 *
	 * @return void
	 */
	public function test_is_example_feature_enabled(): void {
		$toggle_router = $this->createMock( ToggleRouter::class );
		$toggle_router->expects( $this->once() )
			->method( 'is_feature_enabled' )
			->with( 'example_feature' )
			->willReturn( true );

		$feature_decisions = new FeatureDecisions( $toggle_router );

		// フィーチャーの有効状態が定義されていれば返す.
		$this->assertTrue( $feature_decisions->is_example_feature_enabled() );
	}

	/**
	 * Example feature が無効であるかどうかをテスト
	 *
	 * @return void
	 */
	public function test_is_example_feature_disabled(): void {
		$toggle_router = $this->createMock( ToggleRouter::class );
		$toggle_router->expects( $this->once() )
			->method( 'is_feature_enabled' )
			->with( 'example_feature' )
			->willReturn( false );

		$feature_decisions = new FeatureDecisions( $toggle_router );

		// フィーチャーの有効状態が定義されていれば返す.
		$this->assertFalse( $feature_decisions->is_example_feature_enabled() );
	}

	/**
	 * Example feature の設定がない場合は、production 環境であれば有効である
	 *
	 * @return void
	 */
	public function test_is_example_feature_enabled_with_production_environment(): void {
		$toggle_router = $this->createMock( ToggleRouter::class );
		$toggle_router->expects( $this->once() )
			->method( 'is_feature_enabled' )
			->with( 'example_feature' )
			->willReturn( null );
		$toggle_router->expects( $this->once() )
			->method( 'is_production' )
			->willReturn( true );

		$feature_decisions = new FeatureDecisions( $toggle_router );

		// 'example_feature'が明示的に設定されていない場合、production 環境では有効とみなされる.
		$this->assertTrue( $feature_decisions->is_example_feature_enabled() );
	}

	/**
	 * Example feature の設定がない場合は、production 環境でなければ無効である
	 *
	 * @return void
	 */
	public function test_is_example_feature_disabled_when_not_in_production_environment(): void {
		$toggle_router = $this->createMock( ToggleRouter::class );
		$toggle_router->expects( $this->once() )
			->method( 'is_feature_enabled' )
			->with( 'example_feature' )
			->willReturn( null );
		$toggle_router->expects( $this->once() )
			->method( 'is_production' )
			->willReturn( false );

		$feature_decisions = new FeatureDecisions( $toggle_router );

		// 'example_feature'が明示的に設定されていない場合、production 環境でなければ無効とみなされる.
		$this->assertFalse( $feature_decisions->is_example_feature_enabled() );
	}
}
