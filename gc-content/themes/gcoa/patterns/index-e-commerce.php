<?php
/**
 * Title: 电商主页
 * Slug: gcoa/index-e-commerce
 * Categories: dashboard
 * Keywords: 电商主页
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">
<div class="row">
    <div class="col-lg-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="m-b-0 text-muted">售价</p>
                                <h2 class="m-b-0">￥23,523</h2>
                            </div>
                            <span class="badge badge-pill badge-cyan font-size-12">
                                <i class="anticon anticon-arrow-up"></i>
                                <span class="font-weight-semibold m-l-5">6.71%</span>
                            </span>
                        </div>
                        <div class="m-t-40">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary badge-dot m-r-10"></span>
                                    <span class="text-gray font-weight-semibold font-size-13">月度目标</span>                                                    </div>
                                <span class="text-dark font-weight-semibold font-size-13">70% </span>
                            </div>
                            <div class="progress progress-sm w-100 m-b-0 m-t-10">
                                <div class="progress-bar bg-primary" style="width: 70%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="m-b-0 text-muted">订金</p>
                                <h2 class="m-b-0">￥8,753</h2>
                            </div>
                            <span class="badge badge-pill badge-red font-size-12">
                                <i class="anticon anticon-arrow-down"></i>
                                <span class="font-weight-semibold m-l-5">3.26%</span>
                            </span>
                        </div>
                        <div class="m-t-40">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-success badge-dot m-r-10"></span>
                                    <span class="text-gray font-weight-semibold font-size-13">月度目标</span>                                                    </div>
                                <span class="text-dark font-weight-semibold font-size-13">60% </span>
                            </div>
                            <div class="progress progress-sm w-100 m-b-0 m-t-10">
                                <div class="progress-bar bg-success" style="width: 60%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="m-b-0 text-muted">订单</p>
                                <h2 class="m-b-0">1,753</h2>
                            </div>
                            <span class="badge badge-pill badge-red font-size-12">
                                <i class="anticon anticon-arrow-down"></i>
                                <span class="font-weight-semibold m-l-5">2.71%</span>
                            </span>
                        </div>
                        <div class="m-t-40">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-warning badge-dot m-r-10"></span>
                                    <span class="text-gray font-weight-semibold font-size-13">月度目标</span>                                                    </div>
                                <span class="text-dark font-weight-semibold font-size-13">45% </span>
                            </div>
                            <div class="progress progress-sm w-100 m-b-0 m-t-10">
                                <div class="progress-bar bg-warning" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="m-b-0 text-muted">子公司</p>
                                <h2 class="m-b-0">236</h2>
                            </div>
                            <span class="badge badge-pill badge-gold font-size-12">
                                <i class="anticon anticon-arrow-up"></i>
                                <span class="font-weight-semibold m-l-5">N/A</span>
                            </span>
                        </div>
                        <div class="m-t-40">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-secondary badge-dot m-r-10"></span>
                                    <span class="text-gray font-weight-semibold font-size-13">月度目标</span>                                                    </div>
                                <span class="text-dark font-weight-semibold font-size-13">50% </span>
                            </div>
                            <div class="progress progress-sm w-100 m-b-0 m-t-10">
                                <div class="progress-bar bg-secondary" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>售价统计</h5>
                    <div class="dropdown dropdown-animated scale-left">
                        <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                            <i class="anticon anticon-ellipsis"></i>
                        </a>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" type="button">
                                <i class="anticon anticon-printer"></i>
                                <span class="m-l-10">打印</span>
                            </button>
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
                                <span class="m-l-10">刷新</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="d-inline-block m-r-30">
                        <p class="m-b-0 d-flex align-items-center">
                            <span class="badge badge-primary badge-dot m-r-10"></span>
                            <span>在线</span>
                        </p>
                    </div>
                    <div class="d-inline-block">
                        <p class="m-b-0 d-flex align-items-center">
                            <span class="badge badge-blue badge-dot m-r-10"></span>
                            <span>离线</span>
                        </p>
                    </div>
                </div>
                <div class="m-t-50">
                    <canvas class="chart" style="height: 205px" id="sales-chart"></canvas>
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
                    <h5>收入</h5>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-default">查看全部</a>
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="d-md-flex">
                        <div class="pr-4 m-v-10 border-right border-hide-md">
                            <p class="m-b-0">收入</p>
                            <h3 class="m-b-0">
                                <span>￥58,323</span>
                                <span class="text-success m-l-10 font-size-14">+6.71%</span>
                            </h3>
                        </div>
                        <div class="px-md-4 m-v-10 border-right border-hide-md">
                            <p class="m-b-0">销售</p>
                            <h3 class="m-b-0">
                                <span>￥17,523</span>
                                <span class="text-danger m-l-10 font-size-14">+1.82%</span>
                            </h3>
                        </div>
                        <div class="px-md-4 m-v-10">
                            <p class="m-b-0">成本</p>
                            <h3 class="m-b-0">
                                <span>￥8,217</span>
                                <span class="text-success m-l-10 font-size-14">+11.2%</span>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-t-50" style="height: 240px">
                    <canvas class="chart" id="revenue-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>热门产品</h5>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-default">查看全部</a>
                    </div>
                </div>
                <div class="m-t-30">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item p-h-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex">
                                    <div class="avatar avatar-image m-r-15">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-9.jpg" alt="">
                                    </div>
                                    <div>
                                        <h6 class="m-b-0">
                                            <a href="javascript:void(0);" class="text-dark"> 灰色沙发</a>
                                        </h6>
                                        <span class="text-muted font-size-13">家居装饰</span>
                                    </div>
                                </div>
                                <span class="badge badge-pill badge-cyan font-size-12">
                                    <span class="font-weight-semibold m-l-5">+18.3%</span>
                                </span>
                            </div>
                        </li>
                        <li class="list-group-item p-h-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex">
                                    <div class="avatar avatar-image m-r-15">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-10.jpg" alt="">
                                    </div>
                                    <div>
                                        <h6 class="m-b-0">
                                            <a href="javascript:void(0);" class="text-dark">魔音耳机</a>
                                        </h6>
                                        <span class="text-muted font-size-13">电子产品</span>
                                    </div>
                                </div>
                                <span class="badge badge-pill badge-cyan font-size-12">
                                    <span class="font-weight-semibold m-l-5">+12.7%</span>
                                </span>
                            </div>
                        </li>
                        <li class="list-group-item p-h-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex">
                                    <div class="avatar avatar-image m-r-15">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-11.jpg" alt="">
                                    </div>
                                    <div>
                                        <h6 class="m-b-0">
                                            <a href="javascript:void(0);" class="text-dark">木犀牛</a>
                                        </h6>
                                        <span class="text-muted font-size-13">家居装饰</span>
                                    </div>
                                </div>
                                <span class="badge badge-pill badge-cyan font-size-12">
                                    <span class="font-weight-semibold m-l-5">+9.2%</span>
                                </span>
                            </div>
                        </li>
                        <li class="list-group-item p-h-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex">
                                    <div class="avatar avatar-image m-r-15">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-12.jpg" alt="">
                                    </div>
                                    <div>
                                        <h6 class="m-b-0">
                                            <a href="javascript:void(0);" class="text-dark">红色椅子</a>
                                        </h6>
                                        <span class="text-muted font-size-13">家居装饰</span>
                                    </div>
                                </div>
                                <span class="badge badge-pill badge-cyan font-size-12">
                                    <span class="font-weight-semibold m-l-5">+7.7%</span>
                                </span>
                            </div>
                        </li>
                        <li class="list-group-item p-h-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex">
                                    <div class="avatar avatar-image m-r-15">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-13.jpg" alt="">
                                    </div>
                                    <div>
                                        <h6 class="m-b-0">
                                            <a href="javascript:void(0);" class="text-dark">手环</a>
                                        </h6>
                                        <span class="text-muted font-size-13">电子产品</span>
                                    </div>
                                </div>
                                <span class="badge badge-pill badge-cyan font-size-12">
                                    <span class="font-weight-semibold m-l-5">+5.8%</span>
                                </span>
                            </div>
                        </li>
                    </ul> 
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5>客户</h5>
                <div class="m-v-45 text-center" style="height: 220px">
                    <canvas class="chart" id="customer-chart"></canvas>
                </div>
                <div class="row p-t-25">
                    <div class="col-md-8 m-h-auto">
                        <div class="d-flex justify-content-between align-items-center m-b-20">
                            <p class="m-b-0 d-flex align-items-center">
                                <span class="badge badge-warning badge-dot m-r-10"></span>
                                <span>直接</span>
                            </p>
                            <h5 class="m-b-0">350</h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center m-b-20">
                            <p class="m-b-0 d-flex align-items-center">
                                <span class="badge badge-primary badge-dot m-r-10"></span>
                                <span>引荐</span>
                            </p>
                            <h5 class="m-b-0">450</h5>
                        </div>
                        <div class="d-flex justify-content-between align-items-center m-b-20">
                            <p class="m-b-0 d-flex align-items-center">
                                <span class="badge badge-danger badge-dot m-r-10"></span>
                                <span>微商</span>
                            </p>
                            <h5 class="m-b-0">100</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>新订单</h5>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-default">查看全部</a>
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>客户</th>
                                    <th>日期</th>
                                    <th>金额</th>
                                    <th>状态</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#5331</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-image" style="height: 30px; min-width: 30px; max-width:30px">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                                </div>
                                                <h6 class="m-l-10 m-b-0">安子轩</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2020-11-11</td>
                                    <td>￥137.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success badge-dot m-r-10"></span>
                                            <span>赞成</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#5375</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-image" style="height: 30px; min-width: 30px; max-width:30px">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                                </div>
                                                <h6 class="m-l-10 m-b-0">达里尔</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2020-11-06</td>
                                    <td>￥322.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success badge-dot m-r-10"></span>
                                            <span>赞成</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#5762</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-image" style="height: 30px; min-width: 30px; max-width:30px">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                                                </div>
                                                <h6 class="m-l-10 m-b-0">阿七</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2020-11-01</td>
                                    <td>￥543.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success badge-dot m-r-10"></span>
                                            <span>赞成</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#5865</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-image" style="height: 30px; min-width: 30px; max-width:30px">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                                </div>
                                                <h6 class="m-l-10 m-b-0">卖女孩的小火柴</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2020-10-28</td>
                                    <td>￥876.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary badge-dot m-r-10"></span>
                                            <span>待定</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#5213</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-image" style="height: 30px; min-width: 30px; max-width:30px">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-5.jpg" alt="">
                                                </div>
                                                <h6 class="m-l-10 m-b-0">励志</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2020-10-28</td>
                                    <td>￥241.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success badge-dot m-r-10"></span>
                                            <span>赞成</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#5211</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-image" style="height: 30px; min-width: 30px; max-width:30px">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                                                </div>
                                                <h6 class="m-l-10 m-b-0">风一样的男人</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2020-10-28</td>
                                    <td>￥872.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-danger badge-dot m-r-10"></span>
                                            <span>拒绝</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/chartjs/Chart.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/dashboard-e-commerce.js"></script>
<!-- /gc:html -->