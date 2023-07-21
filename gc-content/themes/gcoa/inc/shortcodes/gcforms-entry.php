<?php
class GCOA_Entries_Single {

    public $process;

    public function __construct() {
        $this->process = new \GCFormsApprovals\Process();

        require_once GCFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-single.php';
        $single = new \GCForms_Entries_Single();
        add_action( 'gcforms_entries_init', [ $single, 'process_note_add' ], 8, 1 );
        add_action( 'gcforms_entries_init', [ $single, 'process_note_delete' ], 8, 1 );

        do_action( 'gcforms_entries_init' );

    }
    public function gcoa_gcforms_entry(){
        
        $entry = gcforms()->entry->get( $_GET['entry_id'], [ 'cap' => false ] );
        $entry->entry_notes = gcforms()->entry_meta->get_meta(
            [
                'entry_id' => $entry->entry_id,
                'type'     => 'note',
            ]
        );
        $form = gcforms()->get( 'form' )->get( $entry->form_id );
        $form_data = gcforms_decode( $form->post_content );
        $the_user = get_user_by( 'id', $entry->user_id );

        $entry_delete_url  = gc_nonce_url(
            add_query_arg(
                [
                    'view'     => 'list',
                    'action'   => 'delete',
                    'form_id'  => $entry->form_id,
                    'entry_id' => $entry->entry_id,
                ]
            ),
            'bulk-entries'
        );

        if( gcforms_current_user_can( 'delete_entry_single', $entry->entry_id ) ){
            $entry_delete_link = sprintf('
                <div class="m-t-30">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-gold avatar-icon" >
                            <i class="anticon anticon-delete"></i>
                        </div>
                        <div class="m-l-10">
                            <h5 class="m-b-0">
                                <a href="%1$s" class="text-dark">删除此申请单</a>
                            </h5>
                        </div>
                    </div>
                </div>',
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
        return '
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-md-flex align-items-center">
                            <div class="text-center text-sm-left ">
                                <div class="avatar avatar-image" style="width: 100px; height:100px">
                                    <img src="'. get_avatar_url($the_user->ID) .'" alt="">
                                </div>
                            </div>
                            <div class="text-center text-sm-left m-v-15 p-l-30">
                                <h2 class="m-b-5">'.$the_user->display_name .' （提交人）</h2>
                                <p class="text-opacity font-size-13">提交时间：'. gcforms_datetime_format( $entry->date, '', true ) .'</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="d-md-block d-none border-left col-1"></div>
                            <div class="col">
                                <ul class="list-unstyled m-t-10">
                                    <li class="row">
                                        <p class="col-sm-4 col-4 font-weight-semibold text-dark m-b-5">
                                            <i class="m-r-10 text-primary anticon anticon-mail"></i>
                                            <span>邮件: </span> 
                                        </p>
                                        <p class="col font-weight-semibold"> '. $the_user->user_email .'</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                '. $this->details_fields( $entry, $form_data ) .'
                '. $this->details_approvals( $entry, $form_data ) .'
                '. $this->details_notes( $entry, $form_data ) .'
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>操作</h5>
                        '.$entry_delete_link.'
                    </div>
                </div>
            </div>
        </div>
    </div>
    ';
    }

    /**
     * Entry fields metabox.
     *
     * @since 1.1.5
     *
     * @param object $entry     Submitted entry values.
     * @param array  $form_data Form data and settings.
     */
    public function details_fields( $entry, $form_data ) {

        $hide_empty = isset( $_COOKIE['gcforms_entry_hide_empty'] ) && $_COOKIE['gcforms_entry_hide_empty'] === 'true';
        $form_title = isset( $form_data['settings']['form_title'] ) ? $form_data['settings']['form_title'] : '';

        if ( empty( $form_title ) ) {
            $form = gcforms()->get( 'form' )->get( $entry->form_id );

            $form_title = ! empty( $form )
                ? $form->post_title
                : sprintf( /* translators: %d - form id. */
                    esc_html__( '表单:%d', 'gcforms' ),
                    $entry->form_id
                );
        }
        
        $str = '<div class="card"><div class="card-body"><h5>'. esc_html( $form_title ) .'</h5>';


        $fields = apply_filters( 'gcforms_entry_single_data', gcforms_decode( $entry->fields ), $entry, $form_data );

        if ( empty( $fields ) ) {

            // Whoops, no fields! This shouldn't happen under normal use cases.
            $str .= '<p>' . esc_html__( '此条目没有任何字段', 'gcforms' ) . '</p>';

        } else {

            // Display the fields and their values.
            foreach ( $fields as $key => $field ) {

                if ( empty( $field['type'] ) ) {
                    continue;
                }

                $field_type = $field['type'];

                /** This filter is documented in /src/Pro/Admin/Entries/Edit.php */
                if ( ! (bool) apply_filters( "gcforms_pro_admin_entries_edit_is_field_displayable_{$field_type}", true, $field, $form_data ) ) {
                    continue;
                }

                $field_name = ! empty( $field['name'] ) ? esc_html( gc_strip_all_tags( $field['name'] ) ) : sprintf( esc_html__( '字段ID＃%d', 'gcforms' ), absint( $field['id'] ));

                $field_value  = isset( $field['value'] ) ? $field['value'] : '';
                $field_value  = apply_filters( 'gcforms_html_field_value', gc_strip_all_tags( $field_value ), $field, $form_data, 'entry-single' );
                $field_value  = ! gcforms_is_empty_string( $field_value ) ? gc_kses_post( nl2br( make_clickable( $field_value ) ) ) : esc_html__( '空白', 'gcforms' );

                $str .= '<div class="media m-b-30"><div class="media-body"><h6 class="m-b-0">'. $field_name .'</h6><span class="font-size-13 text-gray">'. $field_value .'</span></div></div>';

            }

        }

        return $str.'</div></div>';
    }

    /**
     * Entry notes metabox.
     *
     * @since 1.1.6
     *
     * @param object $entry     Submitted entry values.
     * @param array  $form_data Form data and settings.
     */
    public function details_notes( $entry, $form_data ) {

        $action_url = add_query_arg(
            [
                'view' => 'details',
                'entry_id' => absint( $entry->entry_id ),
            ],
            ''
        );
        $form_id    = ! empty( $form_data['id'] ) ? $form_data['id'] : $entry->form_id;

        $str = '<div class="card"><div class="card-body"><h5>备注</h5>';
        
        if ( empty( $entry->entry_notes ) ) {
            $str .= '<p>' . esc_html__( '没有备注。', 'gcforms' ) . '</p>';
        } else {
            $str .= '<div class="m-t-20"><ul class="list-group list-group-flush">';
            foreach ( $entry->entry_notes as $note ) {
                $user        = get_userdata( $note->user_id );
                $user_name   = ! empty( $user->display_name ) ? $user->display_name : $user->user_login;
                $user_url    = add_query_arg(
                    array(
                        'user_id' => absint( $user->ID ),
                    ),
                    admin_url( 'user-edit.php' )
                );

                $date  = gcforms_datetime_format( $note->date, '', true );

                if ( \gcforms_current_user_can( 'edit_entries_form_single', $form_data['id'] ) ) {

                    $delete_note_url = gc_nonce_url(
                        add_query_arg(
                            array(
                                'view' => 'details',
                                'entry_id' => absint( $entry->entry_id ),
                                'note_id'  => absint( $note->id ),
                                'action'   => 'delete_note',
                            ),
                            ''
                        ),
                        'gcforms_entry_details_deletenote'
                    );
                }
                $str .= '
                <li class="list-group-item p-h-0">
                    <div class="media m-b-15">
                        <div class="avatar avatar-image">
                            <img src="'. get_avatar_url($user->ID) .'" alt="">
                        </div>
                        <div class="media-body m-l-20">
                            <h6 class="m-b-0">
                                <a href="' . esc_url( $user_url ) . '" class="text-dark">'. esc_html( $user_name ) .'</a>
                            </h6>
                            <span class="font-size-13 text-gray">'. esc_html( $date ) .'</span>
                        </div>
                    </div>
                    <span>'. gc_kses_post( gc_unslash( $note->data ) ) .'</span>';
                if ( ! empty( $delete_note_url ) ){
                    $str .= '<div class="float-right"><a href="'. esc_url( $delete_note_url ) .'"><span class="badge badge-pill badge-blue font-size-12 p-h-10">删除</span></a></div>';
                }
                $str .= '</li>';
            }
            $str .= '</ul></div>';
        }
        $str .= '<h5 class="m-b-20">发表备注</h5>';
        $str .= '<form action="'.$action_url.'" method="post">';
        $str .= '<textarea id="entry_note" name="entry_note" class="form-control" cols="45" rows="8" maxlength="65525" required="" placeholder="填写备注内容"></textarea>';
        $str .= '<input type="hidden" name="_gcnonce" value="'.esc_attr( gc_create_nonce( 'gcforms_entry_details_addnote' ) ).'">';
        $str .= '<input type="hidden" name="entry_id" value="'.absint( $entry->entry_id ).'">';
        $str .= '<input type="hidden" name="form_id" value="'.absint( $form_id ).'">';
        $str .= '<div class="text-right m-t-25"><input type="submit" name="gcforms_add_note" class="btn btn-primary" value="提交"></input></div>';
        $str .= '</form>';
        return $str.'</div></div>';

    }

    // 构造审批流区块的HTML
    public function details_approvals( $entry, $form_data ) {
        if (
            empty( $form_data['settings']['approvals'] ) ||
            ! isset( $form_data['settings']['approvals_enable'] ) ||
            ! gc_validate_boolean( $form_data['settings']['approvals_enable'] )
        ) {
            return;
        }
        $form_id    = ! empty( $form_data['id'] ) ? $form_data['id'] : $entry->form_id;

        $entry->entry_approvals = $this->process->process_approvals_and_metas($entry->entry_id, $form_id, $form_data);
        
        $str = '<div class="card"><div class="card-body"><h5>审批流</h5>';
        // 没有定义审批流
        if ( empty( $entry->entry_approvals ) ) {
            $str .= '<p>' . esc_html__( '没有审批流。', 'gcforms' ) . '</p>';
            return $str.'</div></div>';
        }

        $str .= '<div class="m-t-20"><ul class="list-group list-group-flush">';
        $orders = gcforms_approvals()->get_available_orders();
        foreach ( $entry->entry_approvals as $approval ) {
            $user        = get_user_by('ID', $approval['user_id']);
            $user_name   = ! empty( $user->display_name ) ? $user->display_name : $user->user_login;
            $user_url    = add_query_arg(
                array(
                    'user_id' => absint( $user->ID ),
                ),
                admin_url( 'user-edit.php' )
            );
            $date  = isset($approval['meta']->date) ? gcforms_datetime_format( $approval['meta']->date, '', true ) : '';

            $str .= '
            <li class="list-group-item p-h-0">
                <div class="media m-b-15">
                    <div class="avatar avatar-image">
                        <img src="'. get_avatar_url($user->ID) .'" alt="">
                    </div>
                    <div class="media-body m-l-20">
                        <h6 class="m-b-0">
                            '. esc_html( $user_name ) .' （'. $approval['name'] .'）
                        </h6>
                        <span class="font-size-13 text-gray">'. $orders[$approval['order']] .'</span>
                    </div>
                </div>';
            // 对审批流进行逻辑判断
            if( $approval['type'] == 'approval_pending' && $approval['user_id'] == get_currentuserinfo()->ID && gcforms_current_user_can( 'edit_entries_form_single', $form_id && $approval_meta_data['id'] = $approval['id'] ) ){
                // 待审批人呈现HTML
                $str .= '
                    <form method="post">
                        <div class="form-row align-items-center">
                            <div class="col-auto">审批意见：</div>
                            <div class="col-auto"><select  name="entry_approval" class="custom-select" style="min-width: 180px;">';

                $status = gcforms_approvals()->get_available_custom_status($approval['status']);
                foreach( $status as $key => $value ){
                    $str .= '<option value ="'.$key.'">'.$value.'</option>';
                }

                $str .= '</select></div>';
                 $str .= '<div class="col-auto">
                            <input type="submit" name="gcforms_add_approval" class="btn btn-primary" value="确认审批">
                            <input type="hidden" id="_gcnonce" name="_gcnonce" value="'.esc_attr( gc_create_nonce( 'gcforms_entry_details_addapproval' ) ).'">
                            <input type="hidden" name="entry_id" value="'.absint( $entry->entry_id ).'">
                            <input type="hidden" name="form_id" value="'.absint( $form_id ).'">
                        </div>
                    </form>';
            }else{
                $str .= '
                <p>审批意见：'. gc_kses_post( gc_unslash( $approval['meta']->status ) ) .'</p>
                <p>审批时间：'. $date .'</p>';
            }
            if ( ! empty( $delete_url ) ){
                $str .= '<div class="float-right"><a href="'. esc_url( $delete_url ) .'"><span class="badge badge-pill badge-blue font-size-12 p-h-10">删除</span></a></div>';
            }
            $str .= '</li>';
        }
        $str .= '</ul></div>';
        
        return $str.'</div></div>';

    }

}
