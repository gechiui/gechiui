<?php
/**
 * Title: CRM主页
 * Slug: gcoa/index-crm
 * Categories: dashboard
 * Keywords: CRM主页
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="m-b-0">收入</p>
                        <h2 class="m-b-0">
                            <span>￥14,966</span>
                        </h2>
                    </div>
                    <div class="avatar avatar-icon avatar-lg avatar-blue">
                        <i class="anticon anticon-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="m-b-0">涨幅比</p>
                        <h2 class="m-b-0">
                            <span>26.80%</span>
                        </h2>
                    </div>
                    <div class="avatar avatar-icon avatar-lg avatar-cyan">
                        <i class="anticon anticon-bar-chart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="m-b-0">订单</p>
                        <h2 class="m-b-0">
                            <span>3057</span>
                        </h2>
                    </div>
                    <div class="avatar avatar-icon avatar-lg avatar-red">
                        <i class="anticon anticon-profile"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="m-b-0">总费用</p>
                        <h2 class="m-b-0">
                            <span>￥6,138</span>
                        </h2>
                    </div>
                    <div class="avatar avatar-icon avatar-lg avatar-gold">
                        <i class="anticon anticon-bar-chart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>总体评价</h5>
                </div>
                <div class="d-flex align-items-center m-t-20">
                    <h1 class="m-b-0 m-r-10 font-weight-normal">4.5</h1>
                    <div class="star-rating m-t-15">
                        <input type="radio" id="star1-5" name="rating-1" value="5" checked disabled/><label for="star1-5" title="5 star"></label>
                        <input type="radio" id="star1-4" name="rating-1" value="4" disabled/><label for="star1-4" title="4 star"></label>
                        <input type="radio" id="star1-3" name="rating-1" value="3" disabled/><label for="star1-3" title="3 star"></label>
                        <input type="radio" id="star1-2" name="rating-1" value="2" disabled/><label for="star1-2" title="2 star"></label>
                        <input type="radio" id="star1-1" name="rating-1" value="1" disabled/><label for="star1-1" title="1 star"></label>
                    </div>
                </div>
                <p><span class="text-success font-weight-bold">+1.5</span> 上个月的评分</p>
                <div class="m-t-30" style="height: 150px">
                    <canvas class="chart" id="rating-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center">
                    <h5>总收入</h5>
                    <div class="d-flex">
                        <div class="m-r-10">
                            <span class="badge badge-secondary badge-dot m-r-10"></span>
                            <span class="text-gray font-weight-semibold font-size-13">收入</span>
                        </div>
                        <div class="m-r-10">
                            <span class="badge badge-purple badge-dot m-r-10"></span>
                            <span class="text-gray font-weight-semibold font-size-13">订金</span>
                        </div>
                    </div>
                </div>
                <div class="m-t-50" style="height: 225px">
                    <canvas class="chart" id="sales-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>月度目标</h5>
                </div>  
                <div class="d-flex align-items-center position-relative m-v-50" style="height:150px;">
                    <div class="w-100 position-absolute" style="height:150px; top:0;">
                        <canvas class="chart m-h-auto" id="porgress-chart"></canvas>
                    </div>
                    <h2 class="w-100 text-center text-large m-b-0 text-success font-weight-normal">￥3,531</h2>
                 </div>
                <div class="d-flex justify-content-center align-items-center">
                    <span class="badge badge-success badge-dot m-r-10"></span>
                    <span>目标的<span class="font-weight-semibold">70%</span></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>最新交易</h5>
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
                                    <td>2020-11-12</td>
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
                                    <td>2020-11-11</td>
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
                                    <td>2020-10-12</td>
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
                                    <td>2020-10-03</td>
                                    <td>￥241.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success badge-dot m-r-10"></span>
                                            <span>赞成</span>
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
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/dashboard-crm.js"></script>
<!-- /gc:html -->