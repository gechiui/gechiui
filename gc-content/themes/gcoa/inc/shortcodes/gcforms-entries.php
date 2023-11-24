<?php
/**
* Title: 表单条目列表
* Slug: gcoa_gcforms_entries
* 简码引用 [gcoa_gcforms_entries]
* 参数说明
* form_id: 表单ID
* user_id: 用户ID
* current_user: 表示是否当前用户
* type: 待审批('approval_pending'), 备注('note')
* status:
*/

function gcoa_gcforms_entries($atts, $content=null){
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
        // 加载详情页
        require get_template_directory() .  '/inc/shortcodes/gcforms-entry.php';
        $gcoa_entries_single = new GCOA_Entries_Single();
        if( empty( $_GET['gcoa-action'] ) ) {
            return $gcoa_entries_single->gcoa_gcforms_entry();
        }
    }
    else{
        // 删除动作判断
        $alert_html = gcoa_process_entry_delete();
        // 加载列表页
        return $alert_html.gcoa_gcforms_entries_table($atts, $content=null);
    }
}

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

// 绘制table表格
function gcoa_gcforms_entries_table($atts, $content = null){

    if( !isset($atts) || empty($atts) ){
        $atts = array();
    }

    $current_user = gc_get_current_user();
    $entries = array();

    // 待我的审批
    if($atts['type'] == 'approval_pending'){
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

        // 判断当前用户
        if( isset($atts['current_user']) && $atts['current_user'] == true ){
            $atts['user_id'] = $current_user->ID;
        }

        if( isset($atts['form_id']) ){
            // 循环表单ID
            // 将逗号分割的字符串，转成数组
            $atts['form_id'] = gc_parse_list($atts['form_id']);
            foreach( $atts['form_id'] as $key ){
                $new_atts = $atts;
                $new_atts['form_id'] = $key;
                $entries = (object) array_merge( (array) $entries, (array) gcforms()->entry->get_entries( $new_atts )); 
            }

        }else{
            $entries = gcforms()->entry->get_entries( $atts );
        }
    }

    
    // 绘制dataTables
    $table_html = '';
    foreach ($entries as $entry) {
        $entry->fields = json_decode($entry->fields);
        $title = get_the_title($entry->form_id);

        $status = empty( $entry->status ) ? esc_html__( '已提交', 'gcforms' ) : esc_html( ucwords( sanitize_text_field( $entry->status ) ) ) ;

        if( gcforms_current_user_can( 'delete_entry_single', $entry->entry_id ) ){
            $entry_delete_link = sprintf('<a href="%1$s"><button class="btn btn-icon btn-hover btn-sm btn-rounded"><i class="anticon anticon-delete"></i></button></a>',
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

        // 当未设置自定义的表单字段时，默认显示表单前两个
        if(!isset($atts['fields'])){
            $fields = array();
            foreach( $entry->fields as $field ){
                $fields[] = $field->id;
                // if(count($fields)>=2){
                //     break;
                // }
            }
        }else{
            // 将逗号分割的字符串，转成数组
            $fields = gc_parse_list($atts['fields']);
        }
        $content = '';//'<a href="?view=details&entry_id='. $entry->entry_id .'">';
        foreach( $fields as $key ){
            $field =  $entry->fields->{ $key } ;
            $content .= $field->name.'：'.$field->value .'<br>';
        }

        $table_head = '';
        $table_html .= '
        <tr style="cursor: pointer;" onClick="window.location.href=\'?view=details&entry_id='. $entry->entry_id .'\'">
            <td>'. $entry->entry_id .'</td>
            <td>'. $title .'</td>
            <td>'. $content .'</td>
            <td>'. $entry->date .'</td>
            <td>'. $status .'</td>
            <td class="text-right">
                <a href="?view=details&entry_id='. $entry->entry_id .'">
                    <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                        <i class="anticon anticon-eye"></i>
                    </button>
                </a>
                '. $entry_delete_link .'
            </td>
        </tr>
        ';
    }

    return '
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data-table" class="table table-hover e-commerce-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>表单</th>
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
add_shortcode('gcoa_gcforms_entries', 'gcoa_gcforms_entries');

