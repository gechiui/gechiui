<?php
/**
 * usernew settings administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once  dirname( __DIR__ )  . '/admin.php';

gc_enqueue_script( 'pages-login-register-sms' );

if(! has_filter('gc_sms') ){
    gc_die( __( '短信服务未开启，无法使用相关功能，请安装并启用短信插件。' ) );
}

//获取当前用户信息
$current_user = gc_get_current_user();

$title = __( '更换手机号' );

$http_post     = ( 'POST' === $_SERVER['REQUEST_METHOD'] );
if ( $http_post ) {
    $user_mobile = '';
    $sms_code = '';
    //手机号码
    if ( isset( $_POST['user_mobile'] ) && is_string( $_POST['user_mobile'] ) ) {
        $user_mobile = gc_unslash( $_POST['user_mobile'] );
    }
    //验证码
    if ( isset( $_POST['sms_code'] ) && is_string( $_POST['sms_code'] ) ) {
        $sms_code = gc_unslash( $_POST['sms_code'] );
    }
    
    //提交
    $result = update_mobile($user_mobile, $sms_code, $current_user ->ID);
    if( is_gc_error( $result ) ){
        $message = $result->get_error_message();
    } else{
         $message =  __( '手机号修改成功。') ;
    }
}
require_once ABSPATH . 'gc-admin/admin-header.php';

?>
<script>admin_ajax_url = '<?php echo html_entity_decode(admin_url(gc_nonce_url('admin-ajax.php?action=smscode', 'smscode' ))) ; ?>'</script>
<div class="wrap">
    <h1><?php echo esc_html( $title ); ?></h1>
    <form method="post" name="mobileform" id="mobileform" >
        <?php
        if ( isset($message) ) {
            ?>
        <div id="message" class="updated notice is-dismissible"><p><strong><?php echo $message; ?></strong></p></div>
        <?php
        }
        ?>
        <table class="form-table" role="presentation">
            <tr class="user-rich-editing-wrap">
                <th scope="row"><?php _e( '新手机号' ); ?></th>
                <td>
                    <input type="text" class="regular-text ltr" name="user_mobile" id="user_mobile" placeholder="<?php _e( '请输入您的手机号' ); ?> " autocapitalize="none" autocorrect="off" value="<?php echo esc_attr( $user_mobile ); ?>" maxlength="200">
                </td>
            </tr>
            <tr class="user-rich-editing-wrap">
                <th scope="row"><?php _e( '短信验证码' ); ?></th>
                <td>
                    <div class="input-group">
                        <input type="text" id="sms_code" name="sms_code" class="regular-text ltr" placeholder="<?php _e( '请输入短信验证码' ); ?>" aria-describedby="verify">
                       <button  id="verify" class="btn btn-primary btn-sm suffix-icon m-l-10 input-group-append" disabled><?php _e( '获取短信验证码' ); ?></button>
                    </div>
                </td>
            </tr>
        </table>
        <?php
            do_settings_fields( 'up_mobile', 'default' );
            do_settings_fields( 'up_mobile', 'remote_publishing' ); // A deprecated section.
            do_settings_sections( 'up_mobile' );
            submit_button(  __( '更新手机号码' ) ); 
        ?>
    </form>
</div>
<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
