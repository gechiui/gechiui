<?php
/**
 * Title: 项目列表
 * Slug: gcoa/app-project-list
 * Categories: tables
 * Keywords: 项目列表
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header no-gutters">
    <div class="row align-items-md-center">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5">
                    <div class="input-affix m-v-10">
                        <i class="prefix-icon anticon anticon-search opacity-04"></i>
                        <input type="text" class="form-control" placeholder="搜索">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="text-md-right m-v-10">
                <div class="btn-group m-r-10">
                    <button id="list-view-btn" type="button" class="btn btn-default btn-icon" data-toggle="tooltip" data-placement="bottom" title="列表">
                        <i class="anticon anticon-ordered-list"></i>
                    </button>
                    <button id="card-view-btn" type="button" class="btn btn-default btn-icon active" data-toggle="tooltip" data-placement="bottom" title="卡片">
                        <i class="anticon anticon-appstore"></i>
                    </button>
                </div>
                <button class="btn btn-primary" data-toggle="modal" data-target="#create-new-project">
                    <i class="anticon anticon-plus"></i>
                    <span class="m-l-5">创建项目</span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div id="card-view">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-1.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">酷应用</h5>
                                    <span class="text-muted font-size-13">31 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">Android+IOS APP 整体开发.</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">进度</span>
                                <span class="font-weight-semibold">100%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-cyan">完成</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德祐">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-2.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">美赛网管理后台模型UE</h5>
                                    <span class="text-muted font-size-13">56 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">高效的媒体投放 跨媒体平台.</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">进度</span>
                                <span class="font-weight-semibold">100%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-cyan">完成</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="卖女孩的小火柴">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="安子轩">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="2 More">
                                        <div class="avatar avatar-text avatar-sm">
                                            <span class="text-dark">+2</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-3.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">精灵社区后台UE</h5>
                                    <span class="text-muted font-size-13">21 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">Jelly-o sesame snaps halvah croissant oat cake cookie.</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">进度</span>
                                <span class="font-weight-semibold">87%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar" role="progressbar" style="width: 87%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-blue">进行中</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="大米">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-11.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="3 More">
                                        <div class="avatar avatar-text avatar-sm">
                                            <span class="text-dark">+3</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-4.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">好孩子后台UE</h5>
                                    <span class="text-muted font-size-13">38 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">Irish skinny, grinder affogato, dark, sweet carajillo flavour seasonal.</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">进度</span>
                                <span class="font-weight-semibold">73%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar" role="progressbar" style="width: 73%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-blue">进行中</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="阿七">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德祐">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-5.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">Fortier 自动化脚本</h5>
                                    <span class="text-muted font-size-13">68 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">Let us wax poetic about the beauty of the cheeseburger.</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">进度</span>
                                <span class="font-weight-semibold">73%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar" role="progressbar" style="width: 73%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-blue">进行中</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="安子轩">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-6.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">Web页面前端设计</h5>
                                    <span class="text-muted font-size-13">68 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">表结构原型设计初稿 who was busy with three boys</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">进度</span>
                                <span class="font-weight-semibold">62%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar" role="progressbar" style="width: 62%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-blue">进行中</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="卖女孩的小火柴">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="2 More">
                                        <div class="avatar avatar-text avatar-sm">
                                            <span class="text-dark">+2</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-7.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">标签组开发</h5>
                                    <span class="text-muted font-size-13">90 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">Caerphilly swiss fromage frais. Brie cheese and wine fromage.</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">进度</span>
                                <span class="font-weight-semibold">62%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar" role="progressbar" style="width: 62%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-blue">进行中</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="大米">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-9.jpg" alt="">
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="media">
                                <div class="avatar avatar-image rounded">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-8.jpg" alt="">
                                </div>
                                <div class="m-l-10">
                                    <h5 class="m-b-0">梅伦网UE</h5>
                                    <span class="text-muted font-size-13">165 任务</span>
                                </div>
                            </div>
                            <div class="dropdown dropdown-animated scale-left">
                                <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                    <i class="anticon anticon-ellipsis"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-eye"></i>
                                        <span class="m-l-10">查看</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-edit"></i>
                                        <span class="m-l-10">编辑</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="m-t-25">你看到这里有天线宝宝吗? The path of the righteous.</p>
                        <div class="m-t-30">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-semibold">落后</span>
                                <span class="font-weight-semibold">28%</span>
                            </div>
                            <div class="progress progress-sm m-t-10">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 28%"></div>
                            </div>
                        </div>
                        <div class="m-t-20">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-pill badge-red">进行中</span>
                                </div>
                                <div>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                                        <div class="avatar avatar-image avatar-sm">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                        </div>
                                    </a>
                                    <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="3 More">
                                        <div class="avatar avatar-text avatar-sm">
                                            <span class="text-dark">+3</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card d-none" id="list-view">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>项目</th>
                            <th>任务</th>
                            <th>成员</th>
                            <th>进度</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-1.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">酷应用</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>31 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德祐">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        <i class="anticon anticon-check-o text-success"></i>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-2.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">美赛网管理后台模型UE</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>56 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="卖女孩的小火柴">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="安子轩">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="2 More">
                                            <div class="avatar avatar-text avatar-sm">
                                                <span class="text-dark">+2</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        <i class="anticon anticon-check-o text-success"></i>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-3.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">精灵社区后台UE</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>21 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="大米">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-11.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="3 More">
                                            <div class="avatar avatar-text avatar-sm">
                                                <span class="text-dark">+3</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar" role="progressbar" style="width: 87%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        87%
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-4.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">好孩子后台UE</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>38 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="阿七">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德祐">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar" role="progressbar" style="width: 73%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        73%
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-5.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">Fortier 自动化脚本</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>68 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="安子轩">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar" role="progressbar" style="width: 73%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        73%
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-6.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">Web页面前端设计</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>68 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="卖女孩的小火柴">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="2 More">
                                            <div class="avatar avatar-text avatar-sm">
                                                <span class="text-dark">+2</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar" role="progressbar" style="width: 62%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        62%
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-7.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">标签组开发</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>90 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="大米">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-9.jpg" alt="">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar" role="progressbar" style="width: 62%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        62%
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-image rounded">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-8.jpg" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">梅伦网UE</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>165 任务</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="德克萨斯">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                                            <div class="avatar avatar-image avatar-sm">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                            </div>
                                        </a>
                                        <a class="m-r-5" href="javascript:void(0);" data-toggle="tooltip" title="3 More">
                                            <div class="avatar avatar-text avatar-sm">
                                                <span class="text-dark">+3</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-sm w-100 m-b-0">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 28%"></div>
                                    </div>
                                    <div class="m-l-10">
                                        <i class="anticon anticon-close-o text-danger"></i>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-eye"></i>
                                            <span class="m-l-10">查看</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-edit"></i>
                                            <span class="m-l-10">编辑</span>
                                        </button>
                                        <button class="dropdown-item" type="button">
                                            <i class="anticon anticon-delete"></i>
                                            <span class="m-l-10">删除</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create-new-project">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">创建项目</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="anticon anticon-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="new-project-name">项目</label>
                        <input type="text" class="form-control" id="new-project-name" placeholder="请输入项目名称">
                    </div>
                    <div class="form-group">
                        <label for="new-project-desc">说明</label>
                        <textarea id="new-project-desc" class="form-control" placeholder=""></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">创建</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/project-list.js"></script>
<!-- /gc:html -->