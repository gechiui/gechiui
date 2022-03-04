<?php
/**
 * Privacy Policy Guide Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_privacy_options' ) ) {
	gc_die( __( '抱歉，您不能管理此站点的隐私选项。' ) );
}

if ( ! class_exists( 'GC_Privacy_Policy_Content' ) ) {
	include_once ABSPATH . 'gc-admin/includes/class-gc-privacy-policy-content.php';
}

add_filter(
	'admin_body_class',
	static function( $body_class ) {
		$body_class .= ' privacy-settings ';

		return $body_class;
	}
);

gc_enqueue_script( 'privacy-tools' );

require_once ABSPATH . 'gc-admin/admin-header.php';

?>
<div class="privacy-settings-header">
	<div class="privacy-settings-title-section">
		<h1>
			<?php _e( '隐私' ); ?>
		</h1>
	</div>

	<nav class="privacy-settings-tabs-wrapper hide-if-no-js" aria-label="<?php esc_attr_e( '次要菜单' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'options-privacy.php' ) ); ?>" class="privacy-settings-tab">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( '设置', '隐私设置' );
			?>
		</a>

		<a href="<?php echo esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) ); ?>" class="privacy-settings-tab active" aria-current="true">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( '隐私指南', '隐私设置' );
			?>
		</a>
	</nav>
</div>

<hr class="gc-header-end">

<div class="notice notice-error hide-if-js">
	<p><?php _e( '隐私设置需要JavaScript支持。' ); ?></p>
</div>

<div class="privacy-settings-body hide-if-no-js">
	<h2><?php _e( '《隐私政策指南》' ); ?></h2>
	<h3 class="section-title"><?php _e( '介绍' ); ?></h3>
	<p><?php _e( '此文字模板能够帮助您创建网站的隐私政策。' ); ?></p>
	<p><?php _e( '我们向您推荐了您可能会用到的章节。在每个章节标题下您都可以找到简短说明，来向您介绍您应该提供何种信息。有些章节包括了推荐的政策内容，其他章节则需要您的主题和插件来提供相应内容。' ); ?></p>
	<p><?php _e( '请编辑您的隐私政策内容、删除范本提供的摘要内容并为您的主题和插件加入所需的内容。在您发布您的政策页面之后，请记得将其加入网站的导航菜单。' ); ?></p>
	<p><?php _e( '您有责任编写周全的隐私政策。您需要确保您的隐私政策符合所有国内法和国际隐私法规要求，及时更新您的政策，并力求其准确。' ); ?></p>
	<div class="privacy-settings-accordion">
		<h4 class="privacy-settings-accordion-heading">
			<button aria-expanded="false" class="privacy-settings-accordion-trigger" aria-controls="privacy-settings-accordion-block-privacy-policy-guide" type="button">
				<span class="title"><?php _e( '《隐私政策指南》' ); ?></span>
				<span class="icon"></span>
			</button>
		</h4>
		<div id="privacy-settings-accordion-block-privacy-policy-guide" class="privacy-settings-accordion-panel" hidden="hidden">
			<?php
			$content = GC_Privacy_Policy_Content::get_default_content( true, false );
			echo $content;
			?>
		</div>
	</div>
	<hr class="hr-separator">
	<h3 class="section-title"><?php _e( '政策' ); ?></h3>
	<div class="privacy-settings-accordion gc-privacy-policy-guide">
		<?php GC_Privacy_Policy_Content::privacy_policy_guide(); ?>
	</div>
</div>
<?php

require_once ABSPATH . 'gc-admin/admin-footer.php';
