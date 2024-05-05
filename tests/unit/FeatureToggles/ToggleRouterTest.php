<?php

namespace Tests;

use FeatureToggles\FeatureToggles\ToggleRouter;

class ToggleRouterTest extends TestCase {
	/**
	 * デフォルトのフィーチャー名
	 *
	 * @var string
	 */
	private $default_feature_name = 'my_feature';

	/**
	 * デフォルトのフィーチャー名の環境変数名
	 *
	 * @var string
	 */
	private $default_feature_env_name = 'FEATURE_MY_FEATURE';

	/**
	 * `ToggleRouter` クラスの `is_feature_enabled` メソッドのテストケース
	 * フィーチャーオーバーライドとキャッシュがない場合、`null` を返すことを確認する。
	 */
	public function test_is_feature_enabled_with_no_overrides_and_no_cache_should_return_null() {
		$toggle_router = new ToggleRouter();

		$result = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertNull( $result );
	}

	/**
	 * `is_feature_enabled` メソッドのテストケース
	 * オーバーライド値が設定されている場合、オーバーライド値を返すことを確認する。
	 */
	public function test_is_feature_enabled_with_override_should_return_override_value() {
		$toggle_router = new ToggleRouter();
		$toggle_router->set_feature_override( $this->default_feature_name, true );

		$result = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertTrue( $result );
	}

	/**
	 * `is_feature_enabled` メソッドのテストケース
	 * リクエストパラメーターを介してフィーチャーが有効になっている場合、`true` を返すことを確認する。
	 */
	public function test_is_feature_enabled_with_request_parameter_enabled_should_return_true() {
		$toggle_router        = new ToggleRouter();
		$_REQUEST['features'] = array( 'enable_' . $this->default_feature_name );

		$result = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertTrue( $result );
	}

	/**
	 * `is_feature_enabled` メソッドのテストケース
	 * リクエストパラメーターを介してフィーチャーが無効になっている場合、`false` を返すことを確認する。
	 */
	public function test_is_feature_enabled_with_request_parameter_disabled_should_return_false() {
		$toggle_router        = new ToggleRouter();
		$_REQUEST['features'] = array( 'disable_' . $this->default_feature_name );

		$result = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertFalse( $result );
	}

	/**
	 * `is_feature_enabled` メソッドのテストケース
	 * フィーチャーの状態がキャッシュされている場合、キャッシュされた値を返すことを確認する。
	 */
	public function test_is_feature_enabled_with_cached_value_should_return_cached_value() {
		$toggle_router        = new ToggleRouter();
		$_REQUEST['features'] = array( 'disable_' . $this->default_feature_name );

		// フィーチャーの状態をキャッシュする.
		$result1 = $toggle_router->is_feature_enabled( $this->default_feature_name );
		$this->assertFalse( $result1 );

		// キャッシュされたままであることを確認する.
		$_REQUEST['features'] = array( 'enable_' . $this->default_feature_name );
		$result2              = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertFalse( $result2 );

		// キャッシュをクリアして、正しく動作することを確認する.
		$toggle_router->clear_feature_cache();
		$result3 = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertTrue( $result3 );
	}

	/**
	 * `is_feature_enabled` メソッドのテストケース
	 * 環境変数を介してフィーチャーが有効になっている場合、`true` を返すことを確認する。
	 */
	public function test_is_feature_enabled_with_environment_variable_enabled_should_return_true() {
		$toggle_router                           = new ToggleRouter();
		$_ENV[ $this->default_feature_env_name ] = 'true';

		$result = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertTrue( $result );
	}

	/**
	 * `is_feature_enabled` メソッドのテストケース
	 * 環境変数を介してフィーチャーが無効になっている場合、`false` を返すことを確認する。
	 */
	public function test_is_feature_enabled_with_environment_variable_disabled_should_return_false() {
		$toggle_router                           = new ToggleRouter();
		$_ENV[ $this->default_feature_env_name ] = 'false';

		$result = $toggle_router->is_feature_enabled( $this->default_feature_name );

		$this->assertFalse( $result );
	}

	/**
	 * `ToggleRouter` クラスの `is_local` メソッドのテストケース
	 * デフォルトでは、テスト環境はローカルと見なされる。
	 */
	public function test_is_local_should_return_true() {
		$toggle_router = new ToggleRouter();

		$result = $toggle_router->is_local();

		$this->assertTrue( $result );
	}

	/**
	 * `ToggleRouter` クラスの `is_production` メソッドのテストケース
	 */
	public function test_is_production_should_return_false() {
		$toggle_router = new ToggleRouter();

		$result = $toggle_router->is_production();

		$this->assertFalse( $result );
	}

	/**
	 * `ToggleRouter` クラスの `is_production` メソッドのテストケース
	 * オーバーライドが設定されている場合、`true` を返すことを確認する。
	 */
	public function test_is_production_should_be_true_when_override_is_set() {
		$toggle_router = new ToggleRouter();
		$toggle_router->set_feature_override( 'production', true );

		$result = $toggle_router->is_production();

		$this->assertTrue( $result );
	}
}
