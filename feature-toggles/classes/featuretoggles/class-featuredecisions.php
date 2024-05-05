<?php
/**
 * FeatureDecisions
 *
 * @package Feature Toggles
 */

namespace FeatureToggles\FeatureToggles;

/**
 * Class FeatureDecisions
 *
 * このクラスは、フィーチャートグルに基づいて決定を行う
 */
class FeatureDecisions {

	/**
	 * ToggleRouter instance.
	 *
	 * @var ToggleRouter
	 */
	private $toggle_router;

	/**
	 * Constructor.
	 *
	 * @param ToggleRouter $toggle_router The ToggleRouter instance.
	 */
	public function __construct( ToggleRouter $toggle_router ) {
		$this->toggle_router = $toggle_router;
	}

	/**
	 * Example の機能はデフォルトで本番環境で有効になっている
	 *
	 * @return bool example 機能が有効かどうか
	 */
	public function is_example_feature_enabled(): bool {
		return $this->toggle_router->is_feature_enabled( 'example_feature' )
			?? $this->toggle_router->is_production();
	}

	/**
	 * Beta feature が有効かどうかを返す
	 *
	 * @return bool Beta feature が有効かどうか
	 */
	public function is_beta_feature_enabled(): bool {
		return $this->toggle_router->is_feature_enabled( 'beta_feature' )
			?? false;
	}
}
