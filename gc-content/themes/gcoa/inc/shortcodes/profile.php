<?php
/**
 * 个人资料
 * 引用方法 <!-- gc:pattern {"slug":"gcoa/profile"} /-->
 * 简码引用 [gcoa_profile]
 */
function  gcoa_profile(){
    $current_user = gc_get_current_user();
    $user_id = $current_user->ID;
    $action = ( isset( $_REQUEST['gcoa-action'] ) ) ? $_REQUEST['gcoa-action'] : '';
    switch ( $action ) {
        case 'update':
            $user = new stdClass;
            $user->ID = $user_id;
            if ( isset( $_POST['first_name'] ) ) {
                $user->first_name = sanitize_text_field( $_POST['first_name'] );
            }
            if ( isset( $_POST['nickname'] ) ) {
                $user->nickname = sanitize_text_field( $_POST['nickname'] );
            }
            if ( isset( $_POST['user_email'] ) ) {
                $user->user_email = sanitize_text_field( $_POST['user_email'] );
            }
            if ( isset( $_POST['description'] ) ) {
                $user->description = trim( $_POST['description'] );
            }
            $user_id = gc_update_user( $user );
            $alert = '
            <div class="alert alert-success">
                <div class="d-flex align-items-center justify-content-start">
                    <span class="alert-icon">
                        <i class="anticon anticon-check-o"></i>
                    </span>
                    <span>个人资料修改成功！</span>
                </div>
            </div>';
            break;
        default:
            break;
    }
    $profile_user = get_userdata( $user_id );
    return $alert .'
        <div class="card">
            <div class="card-body">
                <h4>个人资料</h4>
                <p>修改个人资料。</p>
                <div class="m-t-25" style="max-width: 700px">
                    <form action="/profile/" method="post" novalidate="novalidate">
                        <div class="form-group row">
                            <label for="user_login" class="col-sm-2 col-form-label">用户名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="user_login" name="user_login" disabled="disabled" value="'. esc_attr( $profile_user->user_login ) .'">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name" class="col-sm-2 col-form-label">姓名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="first_name" name="first_name" value="'. esc_attr( $profile_user->first_name ) .'">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nickname" class="col-sm-2 col-form-label">花名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nickname" name="nickname" value="'. esc_attr( $profile_user->nickname ) .'">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="user_email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="user_email" name="user_email" value="'. esc_attr( $profile_user->user_email ) .'">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">个人说明</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="description" name="description" rows="5" cols="30">'. esc_attr(  get_the_author_meta( 'user_description', $profile_user->ID ) ) .'</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <input type="hidden" name="gcoa-action" value="update" />
                                <button type="submit" class="btn btn-primary">更新个人资料</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';

}
add_shortcode('gcoa_profile', 'gcoa_profile');

// return array(
// 	'title'      => __( '个人资料', 'gcoa' ),
// 	'inserter' => false,
// 	'content'    => gcoa_profile()
// );
