<?php
/**
 * 我提交的申请单
 * 引用方法 <!-- gc:pattern {"slug":"gcoa/gcforms-my-entries"} /-->
 * 简码 [gcoa_gcforms_entries]
 */

function gcoa_page_entries_styles() {
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

function gcoa_process_entry_delete() {

        if ( empty( $_GET['_gcnonce'] ) || ! gc_verify_nonce( sanitize_key( $_GET['_gcnonce'] ), 'bulk-entries' ) ) {
            return;
        }

        if ( empty( $_GET['entry_id'] ) ) {
            return;
        }

        if ( $_GET['action'] != 'delete' ) {
            return;
        }

        $entry_id   = absint( $_GET['entry_id'] );

        // Capability check.
        if ( ! gcforms_current_user_can( 'delete_entry_single', $entry_id ) ) {
            return;
        }

        $is_deleted = gcforms()->entry->delete( $entry_id );

        if ( ! $is_deleted ) {
            $this->alerts[] = [
                'type'    => 'error',
                'message' => esc_html__( '删除申请单时出现问题。请重试。', 'gcforms' ),
                'dismiss' => true,
            ];

            return;
        }

        return '
            <div class="alert alert-success">
                <div class="d-flex align-items-center justify-content-start">
                    <span class="alert-icon">
                        <i class="anticon anticon-check-o"></i>
                    </span>
                    <span>申请单删除成功！</span>
                </div>
            </div>';
    }

function gcoa_gcforms_entries(){
    add_action( 'gc_enqueue_scripts', 'gcoa_page_entries_styles' );
    // 先判断 GCforms Pro 的版本
    if (
        ! function_exists( 'gcforms' ) ||
        ! gcforms()->pro ||
        version_compare( gcforms()->version, '1.6.1.2', '<' )
    ) {
        $alert = '<div class="notice notice-error"><p>此功能需要GCForms Pro 1.6.1.2 以上，才能工作。</p></div>';
        return $alert;
    }

    if( $_GET['view'] =='details' && isset( $_GET['entry_id'] ) ){
        require get_template_directory() .  '/inc/shortcodes/gcforms-entry.php';
        $gcoa_entries_single = new GCOA_Entries_Single();
        if( empty( $_GET['gcoa-action'] ) ) {
            return $gcoa_entries_single->gcoa_gcforms_entry();
        }
    }
    else{
        // 删除动作判断
        $alert_html = gcoa_process_entry_delete();
        return $alert_html.gcoa_gcforms_entries_table();
    }
}

// 绘制table表格
function gcoa_gcforms_entries_table(){

    $current_user = gc_get_current_user();
    $entries = array();
    if($_GET['type'] == 'approval_pending'){
        $metas = gcforms()->entry_meta->get_meta(
            [
                'user_id'   => $current_user->ID,
                'type' => 'approval_pending',
            ]
        );
        foreach ($metas as $meta) {
            $entries[] = gcforms()->entry->get( $meta->entry_id, [ 'cap' => false ] );
        }

    }else{
        $entries = gcforms()->entry->get_entries( [ 'user_id' => $current_user->ID ] );
    }
    
    // 绘制dataTables
    $table_html = '';
    foreach ($entries as $entry) {
        $title = get_the_title($entry->form_id);
        $fields = '';
        foreach( json_decode($entry->fields) as $field){
            $fields .= $field->value.', ' ;
        }
        $status = empty( $entry->status ) ? esc_html__( '已提交', 'gcforms' ) : esc_html( ucwords( sanitize_text_field( $entry->status ) ) ) ;

        if( gcforms_current_user_can( 'delete_entry_single', $entry->entry_id ) ){
            $entry_delete_link = sprintf('<a class="btn btn-icon btn-hover btn-sm btn-rounded" href="%1$s"><i class="anticon anticon-delete"></i></a>',
                gc_nonce_url(
                    add_query_arg(
                        [
                            'view'     => 'list',
                            'action'   => 'delete',
                            'form_id'  => $entry->form_id,
                            'entry_id' => $entry->entry_id,
                        ]
                    ),
                    'bulk-entries'
                )
            );
        }

        $table_html .= '
        <tr>
            <td>'. $entry->entry_id .'</td>
            <td>'. $title .'</td>
            <td><a href="?view=details&entry_id='. $entry->entry_id .'">'. $fields .'</a></td>
            <td>'. $entry->date .'</td>
            <td>'. $status .'</td>
            <td class="text-right">
                <a class="btn btn-icon btn-hover btn-sm btn-rounded" href="?view=details&entry_id='. $entry->entry_id .'">
                    <i class="anticon anticon-eye"></i>
                </a>
                '. $entry_delete_link .'
            </td>
        </tr>
        ';
    }

    return '
        <div class="page-header">
                '.gcoa_get_header_html().'
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-hover e-commerce-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>审批表</th>
                                <th>内容</th>
                                <th>提交时间</th>
                                <th>状态</th>
                                <th>操作</th>
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
function gcoa_get_header_html(){
    if($_GET['type'] == "approval_pending"){
        return '
            <a class="btn btn-default" href="./">我提交的</a>
            <a class="btn btn-primary" href="./?type=approval_pending">待我审批</a>
        ';
    }else{
       return '
            <a class="btn btn-primary" href="./">我提交的</a>
            <a class="btn btn-default" href="./?type=approval_pending">待我审批</a>
        '; 
    }
}
add_shortcode('gcoa_gcforms_entries', 'gcoa_gcforms_entries');

