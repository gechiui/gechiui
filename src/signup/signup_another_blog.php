<?php
if ( !defined( 'ABSPATH' ) )exit; // Exit if accessed directly

class GCSignup_Another_Blog {
    public function __construct() {
        $this->pageinfo();
    }

    protected function pageinfo( $blogname = '', $blog_title = '', $errors = '' ) {
        //function signup_another_blog( $blogname = '', $blog_title = '', $errors = '' ) {
        $newblogname = isset( $_GET[ 'new' ] ) ? strtolower( preg_replace( '/^-|-$|[^-a-zA-Z0-9]/', '', $_GET[ 'new' ] ) ) : null;

        $current_user = gc_get_current_user();

        if ( !is_gc_error( $errors ) ) {
            $errors = new GC_Error();
        }

        $signup_defaults = array(
            'blogname' => $blogname,
            'blog_title' => $blog_title,
            'errors' => $errors,
        );

        /**
         * Filters the default site sign-up variables.
         * @param array $signup_defaults {
         *     An array of default site sign-up variables.
         *
         *     @type string   $blogname   The site blogname.
         *     @type string   $blog_title The site title.
         *     @type GC_Error $errors     A GC_Error object possibly containing 'blogname' or 'blog_title' errors.
         * }
         */
        $filtered_results = apply_filters( 'signup_another_blog_init', $signup_defaults );

        $blogname = $filtered_results[ 'blogname' ];
        $blog_title = $filtered_results[ 'blog_title' ];
        $errors = $filtered_results[ 'errors' ];
        ?>
<?php
$blogs = get_blogs_of_user( $current_user->ID );
if ( ( isset($_GET[ 'action' ]) && $_GET[ 'action' ] == 'newsite' ) || empty( $blogs )) {
    //我要新建，或者我没有站点的时候
    require_once 'src/signup/signup_site.php';
} else {
    ?>
<div class="card-body" id="my_site">
    <div class="align-items-center justify-content-between m-b-30 text-center">
        <h2 class="m-b-0">我的站点</h2>
    </div>
    <p><?php printf( '%s, 您已经拥有 (%s) 个站点:' ,$current_user->display_name, count($blogs) ); ?></p>
    <ul class="list-group list-group-flush">
        <?php
        foreach ( $blogs as $blog ) {
            $home_url = get_home_url( $blog->userblog_id );
            echo '<li class="list-group-item d-flex justify-content-between">
                                <p>' . $blog->blogname . '</p>
                                <a href="' . esc_url( $home_url ) . '">' . $blog->domain . '</a>
                            </li>';
        }
        ?>
    </ul>
    <div class="form-group p-t-20"> <a class="btn btn-primary btn-block" href="gc-signup.php?action=newsite" >我要创建新站点</a> </div>
</div>

<?php
}
if ( $newblogname ) {
    $newblog = get_blogaddress_by_name( $newblogname );

    if ( 'blog' === $active_signup || 'all' === $active_signup ) {
        printf(
            /* translators: %s: Site address. */
            '<p>您正在查找的站点%s不存在，但您可以现在创建它！</p>',
            '<strong>' . $newblog . '</strong>'
        );
    } else {
        printf(
            /* translators: %s: Site address. */
            '<p>您正在查找的站点%s不存在。</p>',
            '<strong>' . $newblog . '</strong>'
        );
    }
}
}
}
new GCSignup_Another_Blog();
