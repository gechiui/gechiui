<?php
/**
* Title: 成员列表
* Slug: gcoa_users
* 简码引用 [gcoa_users]
*/

function gcoa_page_users_styles() {
    $theme_version = gc_get_theme()->get( 'Version' );

    $version_string = is_string( $theme_version ) ? $theme_version : false;

    gc_register_style(
        'gcoa-css-vendors-datatables-bootstrap',
        get_template_directory_uri() . '/assets/vendors/datatables/dataTables.bootstrap.min.css',
        array(),
        $version_string
    );
    gc_enqueue_style( 'gcoa-css-vendors-datatables-bootstrap' );

    gc_register_script(
        'gcoa-js-vendors-datatables',
        get_template_directory_uri() . '/assets/vendors/datatables/jquery.dataTables.min.js',
        array(),
        $version_string,
        true
    );
    gc_enqueue_script( 'gcoa-js-vendors-datatables' );

    gc_register_script(
        'gcoa-js-vendors-datatables-bootstrap',
        get_template_directory_uri() . '/assets/vendors/datatables/dataTables.bootstrap.min.js',
        array(),
        $version_string,
        true
    );
    gc_enqueue_script( 'gcoa-js-vendors-datatables-bootstrap' );

    gc_add_inline_script(
        'gcoa-js-vendors',
        '
class TablesDataTable {
    static init() {
        $("#data-table").DataTable();
     }
}
$(() => { TablesDataTable.init(); });
'
    );

}

function gcoa_users(){
    add_action( 'gc_enqueue_scripts', 'gcoa_page_users_styles' );

    $users = get_users();
    $table_html = '';

    foreach ($users as $user) {
        $table_html .= '
        <tr>
            <td>#'. $user->ID .'</td>
            <td>
                <div class="d-flex align-items-center">
                    <img class="img-fluid rounded" src="'. get_avatar_url($user->ID) .'" style="max-width: 60px" alt="">
                    <h6 class="m-b-0 m-l-10">'. $user->data->display_name .'</h6>
                </div>
            </td>
            <td>'. $user->data->user_email .'</td>
            <td><span class="list-text text-gray">'. get_the_author_meta( 'user_description', $user->ID ) .'</span></td>
        </tr>
        ';
    }

    return $alert.'
        <div class="page-header">
            <h2 class="header-title">成员列表</h2>
            <div class="header-sub-title">
                <nav class="breadcrumb breadcrumb-dash">
                    <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
                    <a class="breadcrumb-item" href="#">成员</a>
                    <span class="breadcrumb-item active">成员列表</span>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-hover e-commerce-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>成员名</th>
                                <th>邮箱</th>
                                <th>个人说明</th>
                            </tr>
                        </thead>
                        <tbody>
                            '. $table_html .'
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        ';
}
add_shortcode('gcoa_users', 'gcoa_users');
