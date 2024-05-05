<?php
/**
 * ToggleRouter
 *
 * @package Feature Toggles
 */

namespace FeatureToggles\FeatureToggles;

/**
 * Class ToggleRouter
 *
 * This class handles feature toggles.
 */
class ToggleRouter {

	/**
	 * フィーチャーフラグのオーバーライドを保持する配列
	 *
	 * @var bool[]
	 */
	private $feature_overrides = array();

	/**
	 * フィーチャーフラグのキャッシュを保持する配列
	 *
	 * @var ?bool[]
	 */
	private $feature_cache = array();

	/**
	 * 環境タイプのキャッシュ
	 *
	 * @var ?string
	 */
	private $environment_type_cache = null;

	/**
	 * Check if a feature is enabled.
	 *
	 * @param string $feature_name The name of the feature.
	 * @return ?bool Whether the feature is enabled or not.
	 */
	public function is_feature_enabled( string $feature_name ): ?bool {
		// オーバーライドが設定されている場合は、その値を返す.
		if ( array_key_exists( $feature_name, $this->feature_overrides ) ) {
			return $this->feature_overrides[ $feature_name ];
		}

		// キャッシュされた値がある場合はそれを使用する.
		if ( array_key_exists( $feature_name, $this->feature_cache ) ) {
			return $this->feature_cache[ $feature_name ];
		}

		$this->feature_cache[ $feature_name ] = $this->evaluate_feature_state( $feature_name );

		return $this->feature_cache[ $feature_name ];
	}

	/**
	 * 与えられたフィーチャーの状態を評価する
	 *
	 * リクエストパラメータ、環境変数の順で評価する
	 *
	 * @param string $feature_name The name of the feature.
	 * @return ?bool The evaluated feature state.
	 */
	private function evaluate_feature_state( string $feature_name ): ?bool {
		return $this->check_feature_state_from_request( $feature_name )
		?? $this->check_feature_state_from_env( $feature_name );
	}

	/**
	 * Check if a feature is enabled in request parameters.
	 *
	 * 'features' という特殊なリクエストパラメータを評価するため、nonce チェックを行わない。
	 *
	 * @param string $feature_name フィーチャー名.
	 * @return ?bool フィーチャーが有効かどうか、指定されていない場合は `null` を返す。
	 */
	private function check_feature_state_from_request( string $feature_name ): ?bool {
		$feature_name_lower = mb_strtolower( $feature_name );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['features'] ) && is_array( $_REQUEST['features'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$feature_params = $_REQUEST['features'];
			if (
				in_array( $feature_name_lower, $feature_params, true )
				|| in_array( 'enable_' . $feature_name_lower, $feature_params, true )
				) {
				return true;
			} elseif ( in_array( 'disable_' . $feature_name_lower, $feature_params, true ) ) {
				return false;
			}
		}
		return null;
	}

	/**
	 * 環境変数からフィーチャーフラグの状態を評価する
	 *
	 * 環境変数からフィーチャーフラグの状態を評価し、
	 * 指定されていればその状態を `bool` で返し、指定がなければ `null` を返す。
	 *
	 * @param string $feature_name フィーチャー名.
	 * @return ?bool フィーチャーが有効かどうか、指定されていない場合は `null` を返す。
	 */
	private function check_feature_state_from_env( string $feature_name ): ?bool {
		$feature_upper_key = 'FEATURE_' . mb_strtoupper( $feature_name );
		if ( ! isset( $_ENV[ $feature_upper_key ] ) ) {
			return null;
		}
		return in_array( $_ENV[ $feature_upper_key ], array( 'true', 'on', '1' ), true );
	}

	/**
	 * フィーチャーフラグの上書き
	 *
	 * @param string $feature_name フィーチャー名.
	 * @param bool   $enabled 有効かどうか.
	 * @return void
	 */
	public function set_feature_override( string $feature_name, bool $enabled ): void {
		$this->feature_overrides[ $feature_name ] = $enabled;
	}

	/**
	 * フィーチャーフラグの上書きをクリア
	 *
	 * @return void
	 */
	public function clear_feature_overrides(): void {
		$this->feature_overrides = array();
	}

	/**
	 * フィーチャーフラグのキャッシュをクリア
	 *
	 * @return void
	 */
	public function clear_feature_cache(): void {
		$this->feature_cache = array();
	}

	/**
	 * 環境タイプを取得し、キャッシュする
	 *
	 * @return string 環境タイプ
	 */
	private function get_environment_type(): string {
		if ( null === $this->environment_type_cache ) {
			$this->environment_type_cache = wp_get_environment_type();
		}
		return $this->environment_type_cache;
	}

	/**
	 * Check if the application is in production.
	 *
	 * @return bool Whether the application is in production or not.
	 */
	public function is_production(): bool {
		return $this->is_environment_type( 'production' );
	}

	/**
	 * Check if the application is in staging.
	 *
	 * @return bool Whether the application is in staging or not.
	 */
	public function is_staging(): bool {
		return $this->is_environment_type( 'staging' );
	}

	/**
	 * Check if the application is in development.
	 *
	 * @return bool Whether the application is in development or not.
	 */
	public function is_development(): bool {
		return $this->is_environment_type( 'development' );
	}

	/**
	 * Check if the application is in local.
	 *
	 * @return bool Whether the application is in local or not.
	 */
	public function is_local(): bool {
		return $this->is_environment_type( 'local' );
	}

	/**
	 * 指定された環境タイプかどうかをチェック
	 *
	 * @param string $type 環境タイプ.
	 * @return bool 指定された環境タイプかどうか
	 */
	public function is_environment_type( string $type ): bool {
		if ( ! isset( $this->feature_cache[ $type ] ) ) {
			$this->feature_cache[ $type ] = $type === $this->get_environment_type();
		}

		return $this->is_feature_enabled( $type );
	}
}
