<?php
if ( !defined( 'ABSPATH' ) )exit; // Exit if accessed directly

class GCSignup_Mobile {
    
    private $Errors; 
    
    public function __construct() {
        if ( is_multisite() ) {
			if ( ! is_main_network() || ! is_main_site() ) {
                gc_redirect( apply_filters( 'gc_signup_location', network_site_url( 'gc-login.php?action=register-sms' ) ) );
                exit;
            }
		}
        
        $http_post     = ( 'POST' === $_SERVER['REQUEST_METHOD'] );
        $this->Errors = new GC_Error();
        
        if ( $http_post ) {
            //提交注册表单
            $this->validate_submit();
        }
        //页面绘制
        $this->pageinfo();
        
        gc_enqueue_script( 'pages-login-register-sms' );
        
    }
    
     //提交
    function validate_submit() {
        $user_mobile = '';
        $sms_code = '';
        $user_pass ='';
        
        //手机号码
        if ( isset( $_POST['user_mobile'] ) && is_string( $_POST['user_mobile'] ) ) {
            $user_mobile = gc_unslash( $_POST['user_mobile'] );
        }
        //验证码
        if ( isset( $_POST['sms_code'] ) && is_string( $_POST['sms_code'] ) ) {
            $sms_code = gc_unslash( $_POST['sms_code'] );
        }
        //密码
        if ( isset( $_POST['user_pass'] ) && is_string( $_POST['user_pass'] ) ) {
            $user_pass = gc_unslash( $_POST['user_pass'] );
        }

        //提交
        $user_id = register_new_mobile( $user_mobile, $sms_code, $user_pass );
        
         if ( is_gc_error( $user_id ) ) {
             $this->Errors =  $user_id;
         } else {
             //走登录
             $user = get_user_by( 'mobile', $user_mobile ); 
            if ( ! $user ) {
               $this->Errors =  new GC_Error(
                    'invalid_usermobile',
                    __('此手机号注册失败，请检查或更换手机号重试。')
                );
                return;
            }
            
            //写入cookie，让用户处于登录状态
             $credentials = array();
             $credentials['user_mobile'] = $user_mobile;
             $credentials['sms_code'] = $sms_code;
             $credentials['remember'] = false;
             $secure_cookie = '';
             $secure_cookie = apply_filters( 'secure_signon_cookie', $secure_cookie, $credentials );
            gc_set_auth_cookie( $user_id, false, $secure_cookie );
            do_action( 'gc_login', $user->user_login, $user );
            
            if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
                if ( headers_sent() ) {
                    $this->Errors  = new GC_Error(
                        'generic',
                        __('<strong>错误</strong>: Cookies因预料之外的输出被阻止。要获取帮助，请参见<a href=\"%1$s\">此文档</a>或访问<a href=\"%2$s\">支持论坛</a>。')
                    );
                    return;
                } elseif ( isset( $_POST[ 'testcookie' ] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
                    // If cookies are disabled, we can't log in even with a valid user and password.
                    $this->Errors = new GC_Error(
                        'generic',
                        __('<strong>错误</strong>: Cookies被阻止或者您的浏览器不支持。要使用GeChiUI，您必须<a href=\"%s\">启用cookies</a>。')
                    );
                    return;
                }
            }
             $redirect_to = ! empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '/gc-admin/profile.php';
            gc_safe_redirect( $redirect_to );
            exit;
         }

        //注册成功提示页面
        //confirm_user_signup( $user_name, $user_email );
        return true;
    }
    
    protected function pageinfo() {
        global $active_signup;
        
        $user_mobile = isset( $_POST[ 'user_mobile' ] ) ? $_POST[ 'user_mobile' ] : '';

        if ( !is_gc_error( $this->Errors ) ) {
            $this->Errors = new GC_Error();
        }

        $signup_for = isset( $_POST[ 'signup_for' ] ) ? esc_html( $_POST[ 'signup_for' ] ) : 'blog';

        $signup_user_defaults = array(
            'user_name' => $user_mobile,
            'user_mobile' => $user_mobile,
            'errors' => $this->Errors,
        );

        /**
         * Filters the default user variables used on the user sign-up form.
         *
       
         *
         * @param array $signup_user_defaults {
         *     An array of default user variables.
         *
         *     @type string   $user_name  The user username.
         *     @type string   $user_mobile The user email address.
         *     @type GC_Error $this->Errors     A GC_Error object with possible errors relevant to the sign-up user.
         * }
         */
        $filtered_results = apply_filters( 'signup_user_init', $signup_user_defaults );
        $user_name = $filtered_results[ 'user_name' ];
        $user_mobile = $filtered_results[ 'user_mobile' ];
        $this->Errors = $filtered_results[ 'errors' ];

        if ( !is_gc_error( $this->Errors ) ) {
            $this->Errors = new GC_Error();
        }
        
        $registration_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
        $redirect_to = apply_filters( 'registration_redirect', $registration_redirect );
        
        login_header( __( '注册表单' ), '', $this->Errors );
        gc_enqueue_script( 'user-profile' );
        ?>
<script>admin_ajax_url = '<?php echo html_entity_decode(admin_url(gc_nonce_url('admin-ajax.php?action=smscode', 'smscode' ))) ; ?>'</script>
<div class="card shadow-lg">
    <form name="mobileform" id="mobileform" method="post" novalidate="novalidate">
        <div class="card-body">
            <div class="align-items-center justify-content-between m-b-15 text-center">
                <h2 class="m-b-0">
                    <?php _e( '短信验证码注册' ); ?>
                </h2>
            </div>
            <div class="form-group">
                <p><?php echo sprintf( __('通过你的手机号注册,  或使用 <a href="%s">电子邮箱</a>'), esc_url( '/gc-login.php?action=register')); ?></p>
                <?php echo $generic_errmsg; ?>
            </div>
                <?php
                /** This action is documented in gc-signup.php */
                do_action( 'signup_hidden_fields', 'validate-user' );
                ?>
                <div class="form-group">
                    <label class="font-weight-semibold" for="user_mobile"><?php _e( '手机号' ); ?></label>
                    <div class="input-affix"> <i class="prefix-icon anticon anticon-mobile"></i>
                        <input type="text" class="form-control" name="user_mobile" id="user_mobile" placeholder="<?php _e( '请输入您的手机号' ); ?>" autocapitalize="none" autocorrect="off" value="<?php echo esc_attr( $user_mobile ); ?>" maxlength="200">
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-semibold" for="sms_code"><?php _e( '短信验证码' ); ?></label>
                    <div class="input-affix m-b-10">
                        <input type="text" id="sms_code" name="sms_code" class="form-control" placeholder="<?php _e( '请输入短信验证码' ); ?>">
                        <button  id="verify" class="btn btn-primary btn-sm suffix-icon" style="right: 3px; height: 34px" disabled><?php _e( '获取短信验证码' ); ?></button>
                    </div>
                </div>
                <div class="form-group user-pass-wrap">
                    <label class="font-weight-semibold" for="user_pass"><?php _e( '密码' ); ?></label>
                    <div class="input-affix m-b-10">
                        <input type="password" class="form-control" name="user_pass" id="user_pass" size="20" <?php echo $aria_describedby_error; ?> placeholder="<?php _e( '请输入您的密码' ); ?>" value="<?php echo esc_attr( $user_pass ); ?>" aria-label="<?php esc_attr_e( '显示密码' ); ?>"/>
                        <i class="suffix-icon anticon anticon-eye gc-hide-pw pointer"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between"> 
                        <div>
                            <p id="nav">
                                <a href="<?php echo esc_url( gc_login_url() ); ?>"><?php _e( '登录' ); ?></a>
                                    | 
                                <a href="<?php echo esc_url( gc_lostpassword_url() ); ?>"><?php _e( '忘记密码？' ); ?></a>
                            </p>
                        </div>
                        <input type="submit" name="gc-submit" id="gc-submit" class="btn btn-primary" value="<?php esc_attr_e( '注册' ); ?>" />
                    </div>
                </div>
        </div>
    </form>
</div>
<script type="text/javascript">
   function bindToggleButton() {
		$toggleButton = $pass1Row.find('.gc-hide-pw');
		$toggleButton.show().on( 'click', function () {
			if ( 'password' === $pass1.attr( 'type' ) ) {
				$pass1.attr( 'type', 'text' );
				resetToggle( false );
			} else {
				$pass1.attr( 'type', 'password' );
				resetToggle( true );
			}
		});
	}
</script>
<?php
}
}
new GCSignup_Mobile();
