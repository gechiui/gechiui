<?php
/**
 * Title: 项目主页
 * Slug: gcoa/index-projects
 * Categories: dashboard
 * Keywords: 项目主页
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">
<div class="page-header no-gutters">
    <div class="d-md-flex align-items-md-center justify-content-between">
        <div class="media m-v-10 align-items-center">
            <div class="avatar avatar-image avatar-lg">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
            </div>
            <div class="media-body m-l-15">
                <h4 class="m-b-0">欢迎回来, 阿七!</h4>
                <span class="text-gray">项目经理</span>
            </div>
        </div>
        <div class="d-md-flex align-items-center d-none">
            <div class="media align-items-center m-r-40 m-v-5">
                <div class="font-size-27">
                    <i class="text-primary anticon anticon-profile"></i>
                </div>
                <div class="d-flex align-items-center m-l-10">
                    <h2 class="m-b-0 m-r-5">78</h2>
                    <span class="text-gray">任务</span>
                </div>
            </div>
            <div class="media align-items-center m-r-40 m-v-5">
                <div class="font-size-27">
                    <i class="text-success  anticon anticon-appstore"></i>
                </div>
                <div class="d-flex align-items-center m-l-10">
                    <h2 class="m-b-0 m-r-5">21</h2>
                    <span class="text-gray">项目</span>
                </div>
            </div>
            <div class="media align-items-center m-v-5">
                <div class="font-size-27">
                    <i class="text-danger anticon anticon-team"></i>
                </div>
                <div class="d-flex align-items-center m-l-10">
                    <h2 class="m-b-0 m-r-5">39</h2>
                    <span class="text-gray">成员</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">项目完成 </h5>
                    <div class="dropdown dropdown-animated scale-left">
                        <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                            <i class="anticon anticon-ellipsis"></i>
                        </a>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button">
                                <i class="anticon anticon-printer"></i>
                                <span class="m-l-10">打印</span>                                                </button>
                            <button class="dropdown-item" type="button">
                                <i class="anticon anticon-download"></i>
                                <span class="m-l-10">下载</span>
                            </button>
                            <button class="dropdown-item" type="button">
                                <i class="anticon anticon-file-excel"></i>
                                <span class="m-l-10">导出</span>
                            </button>
                            <button class="dropdown-item" type="button">
                                <i class="anticon anticon-reload"></i>
                                <span class="m-l-10">Refresh</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-md-flex justify-content-space m-t-50">
                    <div class="completion-chart p-r-10">
                        <canvas class="chart" id="completion-chart"></canvas>
                    </div>
                    <div class="calendar-card border-0">
                        <div data-provide="datepicker-inline"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">团队成员</h5>
                    <div>
                        <a href="" class="btn btn-default btn-sm">查看全部</a> 
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="avatar-string m-l-5">
                        <a href="javascript:void(0);" data-toggle="tooltip" title="安子轩">
                            <div class="avatar avatar-image team-member">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                            </div>
                        </a>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                            <div class="avatar avatar-image team-member">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                            </div>
                        </a>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="阿七">
                            <div class="avatar avatar-image team-member">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                            </div>
                        </a>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="卖女孩的小火柴">
                            <div class="avatar avatar-image team-member">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                            </div>
                        </a>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="励志">
                            <div class="avatar avatar-image team-member">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-5.jpg" alt="">
                            </div>
                        </a>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="风一样的男人">
                            <div class="avatar avatar-image team-member">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                            </div>
                        </a>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="德祐">
                            <div class="avatar avatar-image team-member">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                            </div>
                        </a>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="Add Member">
                            <div class="avatar avatar-icon avatar-blue team-member">
                                <i class="anticon anticon-plus font-size-22"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">即将召开的会议</h5>
                    <div>
                        <a href="" class="btn btn-default btn-sm">查看全部</a> 
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="d-flex m-b-20">
                        <div class="text-center">
                            <div class="avatar avatar-text avatar-blue avatar-lg rounded">
                                <span class="font-size-22">17</span>
                            </div>
                        </div>
                        <div class="m-l-20">
                            <h5 class="m-b-0">
                                <a class="text-dark">用户界面讨论</a>
                            </h5>
                            <p class="m-b-0">执行核心任务.</p>
                        </div>
                    </div>
                    <div class="d-flex m-b-20">
                        <div class="text-center">
                            <div class="avatar avatar-text avatar-cyan avatar-lg rounded">
                                <span class="font-size-22">21</span>
                            </div>
                        </div>
                        <div class="m-l-20">
                            <h5 class="m-b-0">
                                <a class="text-dark">项目排期</a>
                            </h5>
                            <p class="m-b-0">预估项目交付周期.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="text-center">
                            <div class="avatar avatar-text avatar-gold avatar-lg rounded">
                                <span class="font-size-22">25</span>
                            </div>
                        </div>
                        <div class="m-l-20">
                            <h5 class="m-b-0">
                                <a class="text-dark">设计讨论</a>
                            </h5>
                            <p class="m-b-0">选择优质前段框架.</p>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">项目</h5>
                    <div>
                        <a href="" class="btn btn-default btn-sm">查看全部</a> 
                    </div>
                </div>
                <div class="table-responsive m-t-30">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>项目</th>
                                <th>任务</th>
                                <th>成员</th>
                                <th>进度</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image rounded" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-1.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <span>嘚瑟音 App</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>31</span>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-cyan font-size-12">完成</span>
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
                            </tr>
                            <tr>
                                <td>
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image rounded" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-2.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <span>摩卡房产</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>56</span>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-blue font-size-12">进行中</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress progress-sm w-100 m-b-0">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 76%"></div>
                                        </div>
                                        <div class="m-l-10">
                                            76%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image rounded" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-3.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <span>东方美意</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>21</span>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-blue font-size-12">进行中</span>
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
                            </tr>
                            <tr>
                                <td>
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image rounded" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-5.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <span>负荷工作室</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>68</span>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-blue font-size-12">进行中</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress progress-sm w-100 m-b-0">
                                            <div class="progress-bar" role="progressbar" style="width: 68%"></div>
                                        </div>
                                        <div class="m-l-10">
                                            68%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image rounded" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-6.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <span>硬度科技</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>165</span>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-red font-size-12">落后</span>
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
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">工单处理系统</h5>
                    <div>
                        <a href="" class="btn btn-default btn-sm">查看全部</a> 
                    </div>
                </div>
                <div class="table-responsive m-t-30">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>成员</th>
                                <th>状态</th>
                                <th>日期</th>
                                <th>任务</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-image" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                        </div>
                                        <span class="m-l-10">安子轩</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-gold font-size-12">中级</span>
                                </td>
                                <td>2020-11-11</td>
                                <td>
                                    <h5 class="m-b-0">定义用户和工作流</h5>
                                    <p class="m-b-0 font-size-13">按不同的用户权限执行不同流程</p>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-animated scale-left">
                                            <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                                <i class="anticon anticon-ellipsis"></i>
                                            </a>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-edit"></i>
                                                <span class="m-l-10">编辑</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-delete"></i>
                                                <span class="m-l-10">删除</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-check-square"></i>
                                                <span class="m-l-10">标记完成</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-image" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                        </div>
                                        <span class="m-l-10">卖女孩的小火柴</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-gold font-size-12">中级</span>
                                </td>
                                <td>2020-10-27</td>
                                <td>
                                    <h5 class="m-b-0">更改接口</h5>
                                    <p class="m-b-0 font-size-13">高效的媒体投放</p>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-animated scale-left">
                                            <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                                <i class="anticon anticon-ellipsis"></i>
                                            </a>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-edit"></i>
                                                <span class="m-l-10">编辑</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-delete"></i>
                                                <span class="m-l-10">删除</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-check-square"></i>
                                                <span class="m-l-10">标记完成</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-image" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-5.jpg" alt="">
                                        </div>
                                        <span class="m-l-10">励志</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-cyan font-size-12">Low</span>
                                </td>
                                <td>2020-10-29</td>
                                <td>
                                    <h5 class="m-b-0">创建数据表</h5>
                                    <p class="m-b-0 font-size-13">表结构原型设计初稿</p>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-animated scale-left">
                                            <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                                <i class="anticon anticon-ellipsis"></i>
                                            </a>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-edit"></i>
                                                <span class="m-l-10">编辑</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-delete"></i>
                                                <span class="m-l-10">删除</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-check-square"></i>
                                                <span class="m-l-10">标记完成</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-image" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                        </div>
                                        <span class="m-l-10">达里尔</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-red font-size-12">High</span>
                                </td>
                                <td>2020-10-02</td>
                                <td>
                                    <h5 class="m-b-0">验证链接</h5>
                                    <p class="m-b-0 font-size-13">Bugger bag egg's old boy willy jolly</p>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-animated scale-left">
                                            <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                                <i class="anticon anticon-ellipsis"></i>
                                            </a>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-edit"></i>
                                                <span class="m-l-10">编辑</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-delete"></i>
                                                <span class="m-l-10">删除</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-check-square"></i>
                                                <span class="m-l-10">标记完成</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-image" style="min-width: 40px">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                                        </div>
                                        <span class="m-l-10">风一样的男人</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-gold font-size-12">中级</span>
                                </td>
                                <td>2020-10-07</td>
                                <td>
                                    <h5 class="m-b-0">准备实施</h5>
                                    <p class="m-b-0 font-size-13">导轨系统</p>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-animated scale-left">
                                            <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                                <i class="anticon anticon-ellipsis"></i>
                                            </a>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-edit"></i>
                                                <span class="m-l-10">编辑</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-delete"></i>
                                                <span class="m-l-10">删除</span>
                                            </button>
                                            <button class="dropdown-item" type="button">
                                                <i class="anticon anticon-check-square"></i>
                                                <span class="m-l-10">标记完成</span>
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">活动</h5>
                    <div>
                        <a href="" class="btn btn-default btn-sm">查看全部</a> 
                    </div>
                </div>
                <div class="m-t-40">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-check font-size-22 text-success"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">卖女孩的小火柴</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">完成任务 </span> 
                                        <span class="m-l-5"> 原型设计</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">10:44 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-check font-size-22 text-success"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">阿七</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">完成任务 </span> 
                                        <span class="m-l-5"> 文档</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">10:44 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-message font-size-22 text-primary"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">阿七</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">评论  </span> 
                                        <span class="m-l-5"> 'That's not our work'</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">8:34 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-close-circle font-size-22 text-danger"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">德祐</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">删除</span> 
                                        <span class="m-l-5"> 一个文件</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">8:34 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-paper-clip font-size-22 text-primary"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">德克萨斯</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">附件   </span> 
                                        <span class="m-l-5"> Mockup Zip</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">8:34 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-check font-size-22 text-success"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">阿七</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">完成任务 </span> 
                                        <span class="m-l-5"> UI Revamp</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">10:44 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-message font-size-22 text-primary"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">风一样的男人</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">评论 </span> 
                                        <span class="m-l-5"> 'Hi，请在明天之前做这个'</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">8:34 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-check font-size-22 text-success"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">安子轩</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">完成任务 </span> 
                                        <span class="m-l-5"> UI Revamp</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">10:44 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-check font-size-22 text-success"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">德祐</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">完成任务 </span> 
                                        <span class="m-l-5"> Clean Up Workspace</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">11:25 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon bg-white">
                                    <i class="anticon anticon-check font-size-22 text-success"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <h5 class="m-b-5">励志</h5>
                                    <p class="m-b-0">
                                        <span class="font-weight-semibold">完成任务 </span> 
                                        <span class="m-l-5"> 创建 Workspace</span>
                                    </p>
                                    <span class="text-muted font-size-13">
                                        <i class="anticon anticon-clock-circle"></i>
                                        <span class="m-l-5">8:25 PM</span>
                                    </span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/chartjs/Chart.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/dashboard-project.js"></script>
<!-- /gc:html -->