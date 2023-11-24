<?php
/**
 * Title: 文件管理
 * Slug: gcoa/app-file-manager
 * Categories: pages
 * Keywords: 文件管理
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="file-manager-wrapper">
    <div class="file-manager-nav">
        <div class="d-flex flex-column justify-content-between h-100">
            <div class="p-t-20">
                <ul class="menu nav flex-column">
                    <li class="nav-item">
                        <a href="" class="nav-link active">
                            <i class="anticon anticon-folder text-primary"></i>
                            <span>我的文件</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="anticon anticon-clock-circle text-primary"></i>
                            <span>最近</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="anticon anticon-share-alt text-primary"></i>
                            <span>分享</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="anticon anticon-star text-primary"></i>
                            <span>收藏</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="anticon anticon-delete text-primary"></i>
                            <span>回收</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="m-b-30 m-h-25">
                <div class="d-flex justify-content-between">
                    <span class="text-gray">可用空间</span>
                    <span class="text-gray">30%</span>
                </div>
                <div class="progress progress-sm m-t-10">
                    <div class="progress-bar" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="m-t-15">
                    <button class="btn btn-default w-100">
                        <i class="anticon anticon-upload"></i>
                        <span class="m-l-5">上传</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="file-manager-content">
        <div class="file-manager-content-header">
            <div class="file-manager-search">
                <a href="javascript:void(0);">
                    <i id="open-manager-menu" class="anticon anticon-menu-fold toggle-icon"></i>
                    <i id="close-manager-menu" class="anticon anticon-menu-unfold toggle-icon d-none"></i>
                </a>
                <input placeholder="搜索...">
            </div>
            <div class="file-manager-tools">
                <ul class="list-inline m-b-0">
                    <li class="list-inline-item">
                        <a class="text-dark" href="javascript:void(0);">
                            <i class="anticon anticon-file-add"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-dark" href="javascript:void(0);">
                            <i class="anticon anticon-bars"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-dark" href="javascript:void(0);">
                            <i class="anticon anticon-setting"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="file-manager-content-body">
            <div class="file-manager-content-files">
                <div class="unselect-bg"></div>
                <h5 class="relative">文件夹</h5>
                <div class="file-wrapper m-t-20">
                    <div class="file">
                        <div class="media align-items-center">
                            <div class="m-r-15 font-size-30">
                                <i class="anticon anticon-folder text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">壁纸</h6>
                                <span class="font-size-13 text-muted">57.3MB</span>
                            </div>
                        </div>
                    </div>
                    <div class="file">
                        <div class="media align-items-center">
                            <div class="m-r-15 font-size-30">
                                <i class="anticon anticon-folder text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">小电影</h6>
                                <span class="font-size-13 text-muted">43.9MB</span>
                            </div>
                        </div>
                    </div>
                    <div class="file">
                        <div class="media align-items-center">
                            <div class="m-r-15 font-size-30">
                                <i class="anticon anticon-folder text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">旅游计划</h6>
                                <span class="font-size-13 text-muted">19.8MB</span>
                            </div>
                        </div>
                    </div>
                </div>
                <h5 class="m-t-60 relative">文件</h5>
                <div class="file-wrapper m-t-20">
                    <div class="file vertical" >
                        <div class="font-size-40">
                            <i class="anticon anticon-file-pdf text-danger"></i>
                        </div>
                        <div class="m-t-10">
                            <h6 class="mb-0">App Flow.pdf</h6>
                            <span class="font-size-13 text-muted">19.8MB</span>
                        </div>
                    </div>
                    <div class="file vertical" >
                        <div class="font-size-40">
                            <i class="anticon anticon-file-word text-primary"></i>
                        </div>
                        <div class="m-t-10">
                            <h6 class="mb-0">文档.doc</h6>
                            <span class="font-size-13 text-muted">1.2MB</span>
                        </div>
                    </div>
                    <div class="file vertical" >
                        <div class="font-size-40">
                            <i class="anticon anticon-file-excel text-success"></i>
                        </div>
                        <div class="m-t-10">
                            <h6 class="mb-0">费用.xls</h6>
                            <span class="font-size-13 text-muted">518KB</span>
                        </div>
                    </div>
                    <div class="file vertical" >
                        <div class="font-size-40">
                            <i class="anticon anticon-file-ppt text-secondary"></i>
                        </div>
                        <div class="m-t-10">
                            <h6 class="mb-0">演示.ppt</h6>
                            <span class="font-size-13 text-muted">308KB</span>
                        </div>
                    </div>
                    <div class="file vertical" >
                        <div class="font-size-40">
                            <i class="anticon anticon-file-word text-primary"></i>
                        </div>
                        <div class="m-t-10">
                            <h6 class="mb-0">Guideline.doc</h6>
                            <span class="font-size-13 text-muted">1.2MB</span>
                        </div>
                    </div>
                    <div class="file vertical" >
                        <div class="font-size-40">
                            <i class="anticon anticon-file-excel text-success"></i>
                        </div>
                        <div class="m-t-10">
                            <h6 class="mb-0">Annual_Report.xls</h6>
                            <span class="font-size-13 text-muted">518KB</span>
                        </div>
                    </div>
                    <div class="file vertical" >
                        <div class="font-size-40">
                            <i class="anticon anticon-file-word text-primary"></i>
                        </div>
                        <div class="m-t-10">
                            <h6 class="mb-0">Design_brief.doc</h6>
                            <span class="font-size-13 text-muted">168KB</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="file-manager-content-details">
                <div class="content-details d-none">
                    <div class="p-h-25 p-v-15 d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="m-b-0">App Flow.pdf</h5>
                        <div class="content-details-close">
                            <a class="text-dark" href="javascript:void(0);">
                                <i class="anticon anticon-right-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="m-b-10">
                        <div class="d-flex justify-content-around display-3 align-items-center content-details-file">
                            <i class="anticon anticon-file-pdf text-danger"></i>
                        </div>
                    </div>
                    <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-details">简介</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-activity">活动</a>
                        </li>
                    </ul>
                    <div class="tab-content m-t-15" id="myTabContent">
                        <div class="tab-pane fade show active" id="tab-details">
                            <div class="p-h-25 p-v-15">
                                <dl class="row m-b-10">
                                    <dt class="col-5 text-dark">类型:</dt>
                                    <dd class="col-7">pdf</dd>
                                </dl>   
                                <dl class="row m-b-10">
                                    <dt class="col-5 text-dark">大小:</dt>
                                    <dd class="col-7">19.8MB</dd>
                                </dl>   
                                <dl class="row m-b-10">
                                    <dt class="col-5 text-dark">修改日期:</dt>
                                    <dd class="col-7">2020-11-11</dd>
                                </dl>   
                                <dl class="row m-b-10">
                                    <dt class="col-5 text-dark">创建日期:</dt>
                                    <dd class="col-7">2020-11-11</dd>
                                </dl> 
                                <dl class="row m-b-10">
                                    <dt class="col-5 text-dark">作者:</dt>
                                    <dd class="col-7">安子轩</dd>
                                </dl>    
                            </div>
                            <div class="border-top border-bottom p-h-25 p-v-10 d-flex align-items-center">
                                <span class="text-dark font-weight-semibold m-r-10">添加说明</span> 
                                <button class="m-r-5 btn btn-icon btn-hover btn-rounded btn-sm">
                                    <i class="anticon anticon-edit"></i>
                                </button>
                            </div> 
                        </div>
                        <div class="tab-pane fade" id="tab-activity" role="tabpanel">
                            <div class="p-h-20 p-v-15">
                                <div class="m-b-25">
                                    <div class="p-b-10 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-image m-r-10">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                            </div>
                                            <div class="text-gray">
                                                <span class="text-dark font-weight-semibold">安子轩 </span>
                                                <span>添加 </span>
                                                <span>2 个文件</span>
                                                <div class="text-muted font-size-13">
                                                    7:57PM
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-unstyled m-l-50 m-t-15">
                                            <li class="m-b-15">
                                                <div class="avatar avatar-icon avatar-red avatar-sm">
                                                    <i class="anticon anticon-file-pdf"></i>
                                                </div>
                                                <span class="text-gray">Guide Line.pdf</span>
                                            </li>
                                            <li class="m-b-15">
                                                <div class="avatar avatar-icon avatar-blue avatar-sm">
                                                    <i class="anticon anticon-file-word"></i>
                                                </div>
                                                <span class="text-gray">Business Plan.doc</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="m-b-25">
                                    <div class="p-b-10 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-image m-r-10">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                            </div>
                                            <div class="text-gray">
                                                <span class="text-dark font-weight-semibold">安子轩 </span>
                                                <span>添加 </span>
                                                <span>1 个文件</span>
                                                <div class="text-muted font-size-13">
                                                    7:57PM
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-unstyled m-l-50 m-t-15">
                                            <li class="m-b-15">
                                                <div class="avatar avatar-icon avatar-cyan avatar-sm">
                                                    <i class="anticon anticon-file-excel"></i>
                                                </div>
                                                <span class="text-gray">Expenses.xls</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-details-no-data">
                    <div class="text-center">
                        <img class="img-fluid opacity-04" src="<?php echo get_template_directory_uri(); ?>/assets/images/others/file-manager.png" alt="">
                        <p class="text-muted m-t-20">选择文件夹或文件，查看详细信息</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/file-manager.js"></script>
<!-- /gc:html -->