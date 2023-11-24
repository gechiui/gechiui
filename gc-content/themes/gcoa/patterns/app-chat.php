<?php
/**
 * Title: 聊天室
 * Slug: gcoa/app-chat
 * Categories: pages
 * Keywords: 聊天室
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="container-fluid p-h-0">
    <div class="chat chat-app row">
        <div class="chat-list">
            <div class="chat-user-tool">
                <i class="anticon anticon-search search-icon p-r-10 font-size-20"></i>
                <input placeholder="搜索...">
            </div>
            <div class="chat-user-list">
                <a class="chat-list-item p-h-25" href="javascript:void(0);">
                    <div class="media align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                        </div>
                        <div class="p-l-15">
                            <h5 class="m-b-0">安子轩</h5>
                            <p class="msg-overflow m-b-0 text-muted font-size-13">
                                Wow, 这个很酷!
                            </p>
                        </div>
                    </div>
                </a>
                <a class="chat-list-item p-h-25" href="javascript:void(0);">
                    <div class="media align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                        </div>
                        <div class="p-l-15">
                            <h5 class="m-b-0">达里尔</h5>
                            <p class="msg-overflow m-b-0 text-muted font-size-13">
                                Okay! 谢谢
                            </p>
                        </div>
                    </div>
                </a>
                <a class="chat-list-item p-h-25" href="javascript:void(0);">
                    <div class="media align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                        </div>
                        <div class="p-l-15">
                            <h5 class="m-b-0">阿七</h5>
                            <p class="msg-overflow m-b-0 text-muted font-size-13">
                                是我，能听见我吗.!!
                            </p>
                        </div>
                    </div>
                </a>
                <a class="chat-list-item p-h-25" href="javascript:void(0);">
                    <div class="media align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                        </div>
                        <div class="p-l-15">
                            <h5 class="m-b-0">卖女孩的小火柴</h5>
                            <p class="msg-overflow m-b-0 text-muted font-size-13">
                                ...但我想去参加聚会
                            </p>
                        </div>
                    </div>
                </a>
                <a class="chat-list-item p-h-25" href="javascript:void(0);">
                    <div class="media align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                        </div>
                        <div class="p-l-15">
                            <h5 class="m-b-0">德祐</h5>
                            <p class="msg-overflow m-b-0 text-muted font-size-13">
                                世界上最强者.
                            </p>
                        </div>
                    </div>
                </a>
            </div>   
        </div>
        <div class="chat-content">
            <div class="conversation">
                <div class="conversation-wrapper">
                    <div class="conversation-header justify-content-between">
                        <div class="media align-items-center">
                            <a href="javascript:void(0);" class="chat-close m-r-20 d-md-none d-block text-dark font-size-18 m-t-5" >
                                <i class="anticon anticon-left-circle"></i>
                            </a>
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                            </div>
                            <div class="p-l-15">
                                <h6 class="m-b-0">安子轩</h6>
                                <p class="m-b-0 text-muted font-size-13 m-b-0">
                                    <span class="badge badge-success badge-dot m-r-5"></span>
                                    <span>在线</span>
                                </p>
                            </div>
                        </div>
                        <div class="dropdown dropdown-animated scale-left">
                            <a class="text-dark font-size-20" href="javascript:void(0);" data-toggle="dropdown">
                                <i class="anticon anticon-setting"></i>
                            </a>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" type="button">动作</button>
                                <button class="dropdown-item" type="button">下个动作</button>
                                <button class="dropdown-item" type="button">更多</button>
                            </div>
                        </div>
                    </div>
                    <div class="conversation-body">
                        <div class="msg justify-content-center">
                            <div class="font-weight-semibold font-size-12"> 7:57PM </div>
                        </div>
                        <div class="msg msg-recipient">
                            <div class="m-r-10">
                                <div class="avatar avatar-image">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                </div>
                            </div>
                            <div class="bubble">
                                <div class="bubble-wrapper">
                                    <span>帅哥!给你看点好东西呀？</span>
                                </div>
                            </div>
                        </div>
                        <div class="msg msg-sent">
                            <div class="bubble">
                                <div class="bubble-wrapper">
                                    <span>哦？什么东西？</span>
                                </div>
                            </div>
                        </div>
                        <div class="msg msg-recipient">
                            <div class="m-r-10">
                                <div class="avatar avatar-image">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                </div>
                            </div>
                            <div class="bubble">
                                <div class="bubble-wrapper p-5">
                                    <img src="https://s3.envato.com/files/249796117/preview.__large_preview.png" alt="https://s3.envato.com/files/249796117/preview.__large_preview.png">
                                </div>
                            </div>
                        </div>
                        <div class="msg msg-recipient">
                            <div class="bubble m-l-50">
                                <div class="bubble-wrapper">
                                    <span>一套格尺Ai的后台框架模板</span>
                                </div>
                            </div>
                        </div>
                        <div class="msg msg-recipient">
                            <div class="bubble m-l-50">
                                <div class="bubble-wrapper">
                                    <span>一个可以快速创建后台模型的模板</span>
                                </div>
                            </div>
                        </div>
                        <div class="msg msg-sent">
                            <div class="bubble">
                                <div class="bubble-wrapper">
                                    <span>wow!这个很酷</span>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="conversation-footer">
                        <input class="chat-input" type="text" placeholder="Type a message...">
                        <ul class="list-inline d-flex align-items-center m-b-0">
                            <li class="list-inline-item m-r-15">
                                <a class="text-gray font-size-20" href="javascript:void(0);" data-toggle="tooltip" title="Emoji">
                                    <i class="anticon anticon-smile"></i>
                                </a>
                            </li> 
                            <li class="list-inline-item m-r-15">
                                <a class="text-gray font-size-20" href="javascript:void(0);" data-toggle="tooltip" title="附件">
                                    <i class="anticon anticon-paper-clip"></i>
                                </a>
                            </li>    
                            <li class="list-inline-item">
                                <button class="d-none d-md-block btn btn-primary">
                                    <span class="m-r-10">发送</span>
                                    <i class="far fa-paper-plane"></i>
                                </button>
                                <a href="javascript:void(0);" class="text-gray font-size-20 d-md-none d-block">
                                    <i class="far fa-paper-plane"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->
