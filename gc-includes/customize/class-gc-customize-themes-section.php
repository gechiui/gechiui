<?php
/**
 * Customize API: GC_Customize_Themes_Section class
 *
 * @package GeChiUI
 * @subpackage Customize
 */

/**
 * Customize Themes Section class.
 *
 * A UI container for theme controls, which are displayed within sections.
 *
 * @see GC_Customize_Section
 */
class GC_Customize_Themes_Section extends GC_Customize_Section {

	/**
	 * Section type.
	 *
	 * @since 4.2.0
	 * @var string
	 */
	public $type = 'themes';

	/**
	 * Theme section action.
	 *
	 * Defines the type of themes to load (installed, gcorg, etc.).
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $action = '';

	/**
	 * Theme section filter type.
	 *
	 * Determines whether filters are applied to loaded (local) themes or by initiating a new remote query (remote).
	 * When filtering is local, the initial themes query is not paginated by default.
	 *
	 * @since 4.9.0
	 * @var string
	 */
	public $filter_type = 'local';

	/**
	 * Get section parameters for JS.
	 *
	 * @since 4.9.0
	 * @return array Exported parameters.
	 */
	public function json() {
		$exported                = parent::json();
		$exported['action']      = $this->action;
		$exported['filter_type'] = $this->filter_type;

		return $exported;
	}

	/**
	 * Render a themes section as a JS template.
	 *
	 * The template is only rendered by PHP once, so all actions are prepared at once on the server side.
	 *
	 * @since 4.9.0
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="theme-section">
			<button type="button" class="customize-themes-section-title themes-section-{{ data.id }}">{{ data.title }}</button>
			<?php if ( current_user_can( 'install_themes' ) || is_multisite() ) : // @todo Upload support. ?>
			<?php endif; ?>
			<div class="customize-themes-section themes-section-{{ data.id }} control-section-content themes-php">
				<div class="theme-overlay" tabindex="0" role="dialog" aria-label="<?php esc_attr_e( '主题详情' ); ?>"></div>
				<div class="theme-browser rendered">
					<div class="customize-preview-header themes-filter-bar">
						<?php $this->filter_bar_content_template(); ?>
					</div>
					<?php $this->filter_drawer_content_template(); ?>
					<div class="error unexpected-error" style="display: none; ">
						<p>
							<?php
							printf(
								/* translators: %s: Support forums URL. */
								__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
								__( 'https://www.gechiui.com/support/forums/' )
							);
							?>
						</p>
					</div>
					<ul class="themes">
					</ul>
					<p class="no-themes"><?php _e( '未找到主题，请重新搜索。' ); ?></p>
					<p class="no-themes-local">
						<?php
						printf(
							/* translators: %s: "搜索www.GeChiUI.com主题" button text. */
							__( '未找到主题，请重新搜索，或%s。' ),
							sprintf( '<button type="button" class="button-link search-dotorg-themes">%s</button>', __( '搜索www.GeChiUI.com主题' ) )
						);
						?>
					</p>
					<p class="spinner"></p>
				</div>
			</div>
		</li>
		<?php
	}

	/**
	 * Render the filter bar portion of a themes section as a JS template.
	 *
	 * The template is only rendered by PHP once, so all actions are prepared at once on the server side.
	 * The filter bar container is rendered by @see `render_template()`.
	 *
	 * @since 4.9.0
	 */
	protected function filter_bar_content_template() {
		?>
		<button type="button" class="btn btn-primary customize-section-back customize-themes-mobile-back"><?php _e( '转到主题源代码' ); ?></button>
		<# if ( 'gcorg' === data.action ) { #>
			<div class="search-form">
				<label for="gc-filter-search-input-{{ data.id }}" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '搜索主题...'  );
					?>
				</label>
				<input type="search" id="gc-filter-search-input-{{ data.id }}" placeholder="<?php esc_attr_e( '搜索主题...'  ); ?>" aria-describedby="{{ data.id }}-live-search-desc" class="gc-filter-search">
				<div class="search-icon" aria-hidden="true"></div>
				<span id="{{ data.id }}-live-search-desc" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '搜索结果会随着您的输入而不断更新。' );
					?>
				</span>
			</div>
			<button type="button" class="button feature-filter-toggle">
				<span class="filter-count-0"><?php _e( '筛选主题' ); ?></span><span class="filter-count-filters">
					<?php
					/* translators: %s: Number of filters selected. */
					printf( __( '筛选主题（%s）' ), '<span class="theme-filter-count">0</span>' );
					?>
				</span>
			</button>
		<# } else { #>
			<div class="themes-filter-container">
				<label for="{{ data.id }}-themes-filter" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '搜索主题...'  );
					?>
				</label>
				<input type="search" id="{{ data.id }}-themes-filter" placeholder="<?php esc_attr_e( '搜索主题...'  ); ?>" aria-describedby="{{ data.id }}-live-search-desc" class="gc-filter-search gc-filter-search-themes" />
				<div class="search-icon" aria-hidden="true"></div>
				<span id="{{ data.id }}-live-search-desc" class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '搜索结果会随着您的输入而不断更新。' );
					?>
				</span>
			</div>
		<# } #>
		<div class="filter-themes-count">
			<span class="themes-displayed">
				<?php
				/* translators: %s: Number of themes displayed. */
				printf( __( '%s个主题' ), '<span class="theme-count">0</span>' );
				?>
			</span>
		</div>
		<?php
	}

	/**
	 * Render the filter drawer portion of a themes section as a JS template.
	 *
	 * The filter bar container is rendered by @see `render_template()`.
	 *
	 * @since 4.9.0
	 */
	protected function filter_drawer_content_template() {
		/*
		 * @todo Use the .org API instead of the local core feature list.
		 * The .org API is currently outdated and will be reconciled when the .org themes directory is next redesigned.
		 */
		$feature_list = get_theme_feature_list( false );
		?>
		<# if ( 'gcorg' === data.action ) { #>
			<div class="filter-drawer filter-details">
				<?php foreach ( $feature_list as $feature_name => $features ) : ?>
					<fieldset class="filter-group">
						<legend><?php echo esc_html( $feature_name ); ?></legend>
						<div class="filter-group-feature">
							<?php foreach ( $features as $feature => $feature_name ) : ?>
								<input type="checkbox" id="filter-id-<?php echo esc_attr( $feature ); ?>" value="<?php echo esc_attr( $feature ); ?>" />
								<label for="filter-id-<?php echo esc_attr( $feature ); ?>"><?php echo esc_html( $feature_name ); ?></label>
							<?php endforeach; ?>
						</div>
					</fieldset>
				<?php endforeach; ?>
			</div>
		<# } #>
		<?php
	}
}
