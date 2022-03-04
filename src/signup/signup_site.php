<?php
if ( !defined( 'ABSPATH' ) )exit; // Exit if accessed directly

class GCSignup_Site {

    private $Errors;

    public function __construct() {

        $this->Errors = new GC_Error();

        if ( !empty( $_POST[ 'blogname' ] ) ) {
            //提交注册表单(含成功提示页面)
            if(! $this->validate_submit()){
                //页面绘制
                $this->pageinfo();
                gc_enqueue_script( 'pages-signup-site' );
            }
        }else{
            //页面绘制
            $this->pageinfo();
            gc_enqueue_script( 'pages-signup-site' );
        }
    }

    //提交
    function validate_submit() {
        global $blogname, $blog_title, $errors, $domain, $path;
        $current_user = gc_get_current_user();
        if ( !is_user_logged_in() ) {
            die();
        }

        $result = validate_blog_form();
        // Extracted values set/overwrite globals.
        $domain = $result[ 'domain' ];
        $path = $result[ 'path' ];
        $blogname = $result[ 'blogname' ];
        $blog_title = $result[ 'blog_title' ];
        $errors = $result[ 'errors' ];

        //支持搜索引擎收录，默认不开
        $blog_meta_defaults = array(
            'lang_id' => 1, //语言ID
            'public' => 0, //支持搜索引擎收录  1 for true, 0 for false.
        );

        $blog_meta_defaults[ 'GCLANG' ] = gc_unslash( sanitize_text_field( 'zh_CN' ) ); //语言，强制中文
        //        echo gc_unslash( sanitize_text_field( 'zh_CN' ) );
        //        exit;

        /**
         * Filters the new site meta variables.
         *
         * Use the {@see 'add_signup_meta'} filter instead.
         *
         * @since MU
         * @deprecated 3.0.0 Use the {@see 'add_signup_meta'} filter instead.
         *
         * @param array $blog_meta_defaults An array of default blog meta variables.
         */
        $meta_defaults = apply_filters_deprecated( 'signup_create_blog_meta', array( $blog_meta_defaults ), '3.0.0', 'add_signup_meta' );
        /**
         * Filters the new default site meta variables.
         *
       
         *
         * @param array $meta {
         *     An array of default site meta variables.
         *
         *     @type int $lang_id     The language ID.
         *     @type int $blog_public Whether search engines should be discouraged from indexing the site. 1 for true, 0 for false.
         * }
         */

        $meta = apply_filters( 'add_signup_meta', $meta_defaults );
        $blog_id = gcmu_create_blog( $domain, $path, $blog_title, $current_user->ID, $meta, get_current_network_id() );
        if ( is_gc_error( $blog_id ) ) {
            $errors = new GC_Error('blogname', $blog_id->get_error_message());
            return false;
        }
        //站点创建成功，给出成功提示
        //confirm_another_blog_signup( $domain, $path, $blog_title, $current_user->user_login, $current_user->user_email, $meta, $blog_id );
        $this->newsite_ok( $blog_id );
        return true;
    }

    protected function pageinfo($user_name = '', $user_email = '', $blogname = '', $blog_title = '', $errors = '') {
        //function signup_blog( $user_name = '', $user_email = '', $blogname = '', $blog_title = '', $errors = '' ) {
        global $blogname, $blog_title, $errors, $domain, $path;
        if ( !isset($errors) || !is_gc_error( $errors ) ) {
            $errors = new GC_Error();
        }

        $signup_blog_defaults = array(
            'blogname' => $blogname,
            'blog_title' => $blog_title,
            'errors' => $errors,
        );

        /**
         * Filters the default site creation variables for the site sign-up form.
         *
       
         *
         * @param array $signup_blog_defaults {
         *     An array of default site creation variables.
         *
         *     @type string   $user_name  The user username.
         *     @type string   $user_email The user email address.
         *     @type string   $blogname   The blogname.
         *     @type string   $blog_title The title of the site.
         *     @type GC_Error $errors     A GC_Error object with possible errors relevant to new site creation variables.
         * }
         */
        $filtered_results = apply_filters( 'signup_blog_init', $signup_blog_defaults );

        $blogname = $filtered_results[ 'blogname' ];
        $blog_title = $filtered_results[ 'blog_title' ];
        $errors = $filtered_results[ 'errors' ];

        if ( !is_gc_error( $errors ) ) {
            $errors = new GC_Error();
        }

        $current_network = get_network();
        //只考虑二级域名模式的部署方案
        $site_domain = preg_replace( '|^www\.|', '', $current_network->domain );
        $site = __( '您的域名' ) . '.' . $site_domain . $current_network->path;
        //站点域名的校验提示
        $errmsg_domain = $errors->get_error_message( 'blogname' );
        if ( $errmsg_domain ) {
            $errmsg_domain = '<p class="text-danger">' . $errmsg_domain . '</p>';
        } else {
            $errmsg_domain = '<p>至少4个字符。只能使用数字和字母。</p>';
        }
        //站点标题的校验提示
        $errmsg_title = $errors->get_error_message( 'blog_title' );
        if ( $errmsg_title ) {
            $errmsg_title = '<p class="text-danger">' . $errmsg_title . '</p>';
        } else {
            $errmsg_title = '很多位置都会使用这个标题';
        }

        ?>
<form id="newsiteform" method="post">
    <?php         
        /** This action is documented in gc-signup.php */
        do_action( 'signup_hidden_fields', 'validate-site' );
    ?>
    <div class="card-body">
        <div class="align-items-center justify-content-between m-b-30 text-center">
            <h2 class="m-b-0">创建新站点</h2>
        </div>
        <div class="form-group">
            <?php if ( is_subdomain_install() ) { ?>
            <label class="font-weight-semibold" for="blogname">站点域名:</label>
            <div class="input-affix d-flex align-items-center"> <i class="prefix-icon anticon anticon-link"></i>
                <input type="text" class="form-control" name="blogname"  id="blogname" value="<?php echo esc_attr( $blogname ); ?>" maxlength="60" placeholder="请输入域名">
                <span class="p-h-10 suffix-icon">.<?php echo esc_html( $site_domain ); ?></span> 
            </div>
            <?php }else{ ?>
            <label class="font-weight-semibold" for="blogname">站点目录:</label>
            <div class="input-group"> 
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><?php echo esc_html( $site_domain ); ?>/</span>
                </div>
                <input type="text" class="form-control" name="blogname"  id="blogname" value="<?php echo esc_attr( $blogname ); ?>" maxlength="60" placeholder="请输入目录名">
            </div>
            <?php } ?>
            
            <?php echo $errmsg_domain; ?> 
        </div>
        <div class="form-group">
            <label class="font-weight-semibold" for="blog_title">站点标题:</label>
            <div class="input-affix"> <i class="prefix-icon anticon anticon-compass"></i>
                <input type="text" class="form-control" name="blog_title"  id="blog_title" value="<?php echo esc_attr( $blog_title ); ?>" maxlength="32" placeholder="请输入站点标题">
            </div>
            <?php echo $errmsg_title; ?> 
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-block" name="submit" id="submit">立即开通</button>
        </div>
    </div>
</form>
<?php
do_action( 'signup_blogform', $errors );
}
//站点创建成功
function newsite_ok( $blog_id ) {
    switch_to_blog( $blog_id );
    $login_url = admin_url();
    restore_current_blog();

    ?>
<div class="card-body">
    <div class="align-items-center justify-content-between m-b-30 text-center">
        <h2 class="m-b-0">站点创建成功</h2>
    </div>
    <div class="form-group"> 您的站点已经创建成功，点击进入站点后台进行站点设置。 </div>
    <div class="form-group"> <a class="btn btn-primary btn-block" href="<?php echo $login_url ; ?>">进入管理后台</a> </div>
</div>
<?php
}
}
new GCSignup_Site();
