<?php

// gongenlin
// https://cloud.tencent.com/developer/article/1674384?areaSource=102001.4&traceId=j7tLWXXvYsLHeVuFcp1aA
// 自定义头像处理单元

class Simple_Local_Avatars {
    private $user_id_being_edited;
        
    public function __construct() {
        
        add_action( 'personal_options_update', array( $this, 'edit_user_profile_update' ) ); // 编辑个人资料触发
        add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) ); // 编辑他人资料触发
        
        add_filter( 'avatar_defaults', array( $this, 'avatar_defaults' ) );
    }
        
    public function edit_user_profile_update( $user_id ) {
        if ( ! isset( $_POST['_simple_local_avatar_nonce'] ) || ! gc_verify_nonce( $_POST['_simple_local_avatar_nonce'], 'simple_local_avatar_nonce' ) )            //security
            return;
        
        if ( ! empty( $_FILES['simple-local-avatar']['name'] ) ) {
            $mimes = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
                'bmp' => 'image/bmp',
                'tif|tiff' => 'image/tiff'
            );
        
            // front end (theme my profile etc) support
            if ( ! function_exists( 'gc_handle_upload' ) )
                require_once( ABSPATH . 'gc-admin/includes/file.php' );
        
            $this->avatar_delete( $user_id );    // delete old images if successful
        
            // need to be more secure since low privelege users can upload
            if ( strstr( $_FILES['simple-local-avatar']['name'], '.php' ) )
                gc_die('出于安全原因，扩展名“.php”不能在文件名中。');
        
            $this->user_id_being_edited = $user_id; // make user_id known to unique_filename_callback function
            $avatar = gc_handle_upload( $_FILES['simple-local-avatar'], array( 'mimes' => $mimes, 'test_form' => false, 'unique_filename_callback' => array( $this, 'unique_filename_callback' ) ) );
        
            if ( empty($avatar['file']) ) {     // handle failures
                switch ( $avatar['error'] ) {
                    case 'File type does not meet security guidelines. Try another.' :
                        add_action( 'user_profile_update_errors', create_function('$a','$a->add("avatar_error",__("请上传有效的图片文件。","simple-local-avatars"));') );              
                        break;
                    default :
                        add_action( 'user_profile_update_errors', create_function('$a','$a->add("avatar_error","<strong>".__("上传头像过程中出现以下错误：","simple-local-avatars")."</strong> ' . esc_attr( $avatar['error'] ) . '");') );
                }
        
                return;
            }
        
            update_user_meta( $user_id, 'simple_local_avatar', array( 'full' => $avatar['url'] ) );      // save user information (overwriting old)
        } elseif ( ! empty( $_POST['simple-local-avatar-erase'] ) ) {
            $this->avatar_delete( $user_id );
        }
    }
        
    /**
     * remove the custom get_avatar hook for the default avatar list output on options-discussion.php
     */
    public function avatar_defaults( $avatar_defaults ) {
        remove_action( 'get_avatar', array( $this, 'get_avatar' ) );
        return $avatar_defaults;
    }
        
    /**
     * delete avatars based on user_id
     */
    public function avatar_delete( $user_id ) {
        $old_avatars = get_user_meta( $user_id, 'simple_local_avatar', true );
        $upload_path = gc_upload_dir();
        
        if ( is_array($old_avatars) ) {
            foreach ($old_avatars as $old_avatar ) {
                $old_avatar_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $old_avatar );
                @unlink( $old_avatar_path );   
            }
        }
        
        delete_user_meta( $user_id, 'simple_local_avatar' );
    }
        
    public function unique_filename_callback( $dir, $name, $ext ) {
        $user = get_user_by( 'id', (int) $this->user_id_being_edited );
        $name = $base_name = sanitize_file_name( substr(md5($user->user_login),0,12) .gc_generate_password(8,false). '_avatar' );
        $number = 1;
        
        while ( file_exists( $dir . "/$name$ext" ) ) {
            $name = $base_name . '_' . $number;
            $number++;
        }
        
        return $name . $ext;
    }
}
        
$simple_local_avatars = new Simple_Local_Avatars;
