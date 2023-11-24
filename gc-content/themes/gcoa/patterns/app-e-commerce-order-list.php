<?php
/**
 * Title: 订单列表
 * Slug: gcoa/app-e-commerce-order-list
 * Categories: pages
 * Keywords: 订单列表
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/datatables/dataTables.bootstrap.min.css" rel="stylesheet">
<div class="page-header">
    <h2 class="header-title">订单列表</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">应用</a>
            <a class="breadcrumb-item" href="#">电商</a>
            <span class="breadcrumb-item active">订单列表</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row m-b-30">
            <div class="col-lg-8">
                <div class="d-md-flex">
                    <div class="m-b-10">
                        <select class="custom-select" style="min-width: 180px;">
                            <option selected>状态</option>
                            <option value="all">All</option>
                            <option value="approved">赞成</option>
                            <option value="pending">待定</option>
                            <option value="rejected">拒绝</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-right">
                <button class="btn btn-primary">
                    <i class="anticon anticon-file-excel m-r-5"></i>
                    <span>导出</span>
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover e-commerce-table">
                <thead>
                    <tr>
                        <th>
                            <div class="checkbox">
                                <input id="checkAll" type="checkbox">
                                <label for="checkAll" class="m-b-0"></label>
                            </div>
                        </th>
                        <th>ID</th>
                        <th>客户</th>
                        <th>日期</th>
                        <th>金额</th>
                        <th>状态</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-1" type="checkbox">
                                <label for="check-item-1" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5331
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">安子轩</h6>
                            </div>
                        </td>
                        <td>2020-11-11</td>
                        <td>￥137.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success badge-dot m-r-10"></div>
                                <div>赞成</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-2" type="checkbox">
                                <label for="check-item-2" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5375
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">达里尔</h6>
                            </div>
                        </td>
                        <td>2020-11-06</td>
                        <td>￥322.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success badge-dot m-r-10"></div>
                                <div>赞成</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-3" type="checkbox">
                                <label for="check-item-3" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5362
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">阿七</h6>
                            </div>
                        </td>
                        <td>2020-11-01</td>
                        <td>￥543.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success badge-dot m-r-10"></div>
                                <div>赞成</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-4" type="checkbox">
                                <label for="check-item-4" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5365
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">卖女孩的小火柴</h6>
                            </div>
                        </td>
                        <td>2020-10-28</td>
                        <td>￥876.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-primary badge-dot m-r-10"></div>
                                <div>待定</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-5" type="checkbox">
                                <label for="check-item-5" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5213
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-5.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">励志</h6>
                            </div>
                        </td>
                        <td>2020-10-28</td>
                        <td>￥241.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success badge-dot m-r-10"></div>
                                <div>赞成</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-6" type="checkbox">
                                <label for="check-item-6" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5311
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">风一样的男人</h6>
                            </div>
                        </td>
                        <td>19 April 2019</td>
                        <td>￥872.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-danger badge-dot m-r-10"></div>
                                <div>拒绝</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-7" type="checkbox">
                                <label for="check-item-7" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5387
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">德祐</h6>
                            </div>
                        </td>
                        <td>18 April 2019</td>
                        <td>￥728.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success badge-dot m-r-10"></div>
                                <div>赞成</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-8" type="checkbox">
                                <label for="check-item-8" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5390
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">Emily Shaw</h6>
                            </div>
                        </td>
                        <td>16 April 2019</td>
                        <td>￥802.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-primary badge-dot m-r-10"></div>
                                <div>待定</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-9" type="checkbox">
                                <label for="check-item-9" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5317
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-9.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">大米</h6>
                            </div>
                        </td>
                        <td>12 April 2019</td>
                        <td>￥569.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success badge-dot m-r-10"></div>
                                <div>赞成</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input id="check-item-10" type="checkbox">
                                <label for="check-item-10" class="m-b-0"></label>
                            </div>
                        </td>
                        <td>
                            #5291
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-image avatar-sm m-r-10">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                </div>
                                <h6 class="m-b-0">Wyatt Wallace</h6>
                            </div>
                        </td>
                        <td>10 April 2019</td>
                        <td>￥132.00</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success badge-dot m-r-10"></div>
                                <div>赞成</div>
                            </div>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                <i class="anticon anticon-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                <i class="anticon anticon-delete"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/e-commerce-order-list.js"></script>
<!-- /gc:html -->