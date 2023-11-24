<?php
/**
 * Title: OA主页
 * Slug: gcoa/index
 * Categories: dashboard
 * Keywords: OA主页
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="media align-items-center">
                    <div class="avatar avatar-icon avatar-lg avatar-blue">
                        <i class="anticon anticon-dollar"></i>
                    </div>
                    <div class="m-l-15">
                        <h2 class="m-b-0">￥23,523</h2>
                        <p class="m-b-0 text-muted">利润</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="media align-items-center">
                    <div class="avatar avatar-icon avatar-lg avatar-cyan">
                        <i class="anticon anticon-line-chart"></i>
                    </div>
                    <div class="m-l-15">
                        <h2 class="m-b-0">+ 17.21%</h2>
                        <p class="m-b-0 text-muted">增长</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="media align-items-center">
                    <div class="avatar avatar-icon avatar-lg avatar-gold">
                        <i class="anticon anticon-profile"></i>
                    </div>
                    <div class="m-l-15">
                        <h2 class="m-b-0">3,685</h2>
                        <p class="m-b-0 text-muted">订单</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="media align-items-center">
                    <div class="avatar avatar-icon avatar-lg avatar-purple">
                        <i class="anticon anticon-user"></i>
                    </div>
                    <div class="m-l-15">
                        <h2 class="m-b-0">1,832</h2>
                        <p class="m-b-0 text-muted">客户</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>总收入</h5>
                    <div>
                        <div class="btn-group">
                            <button class="btn btn-default active">
                                <span>月</span>
                            </button>
                            <button class="btn btn-default">
                                <span>年</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="m-t-50" style="height: 330px">
                    <canvas class="chart" id="revenue-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="m-b-0">客户</h5>
                <div class="m-v-60 text-center" style="height: 200px">
                    <canvas class="chart" id="customers-chart"></canvas>
                </div>
                <div class="row border-top p-t-25">
                    <div class="col-4">
                        <div class="d-flex justify-content-center">
                            <div class="media align-items-center">
                                <span class="badge badge-success badge-dot m-r-10"></span>
                                <div class="m-l-5">
                                    <h4 class="m-b-0">350</h4>
                                    <p class="m-b-0 muted">新增</p>
                                </div>    
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex justify-content-center">
                            <div class="media align-items-center">
                                <span class="badge badge-secondary badge-dot m-r-10"></span>
                                <div class="m-l-5">
                                    <h4 class="m-b-0">450</h4>
                                    <p class="m-b-0 muted">复购</p>
                                </div>    
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex justify-content-center">
                            <div class="media align-items-center">
                                <span class="badge badge-warning badge-dot m-r-10"></span>
                                <div class="m-l-5">
                                    <h4 class="m-b-0">100</h4>
                                    <p class="m-b-0 muted">其他</p>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="m-b-0">￥17,267</h2>
                        <p class="m-b-0 text-muted">Avg.利润</p>
                    </div>
                    <div>
                        <span class="badge badge-pill badge-cyan font-size-12">
                            <span class="font-weight-semibold m-l-5">+5.7%</span>
                        </span>
                    </div>
                </div>
                <div class="m-t-50" style="height: 375px">
                     <canvas class="chart" id="avg-profit-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-8">
        <div class="card">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-center">
                    <h5>重点项目</h5>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-default">查看全部</a>
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>商品</th>
                                    <th>售价</th>
                                    <th>收入</th>
                                    <th style="max-width: 70px">剩余库存</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="avatar avatar-image rounded">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-9.jpg" alt="">
                                            </div>
                                            <div class="m-l-10">
                                                <span>灰色沙发</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>81</td>
                                    <td>￥1,912.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm w-100 m-b-0">
                                                <div class="progress-bar bg-success" style="width: 82%"></div>
                                            </div>
                                            <div class="m-l-10">
                                                82
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="avatar avatar-image rounded">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-10.jpg" alt="">
                                            </div>
                                            <div class="m-l-10">
                                                <span>灰色耳机</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>26</td>
                                    <td>￥1,377.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm w-100 m-b-0">
                                                <div class="progress-bar bg-success" style="width: 61%"></div>
                                            </div>
                                            <div class="m-l-10">
                                                61
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="avatar avatar-image rounded">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-11.jpg" alt="">
                                            </div>
                                            <div class="m-l-10">
                                                <span>木犀牛</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>71</td>
                                    <td>￥9,212.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm w-100 m-b-0">
                                                <div class="progress-bar bg-danger" style="width: 23%"></div>
                                            </div>
                                            <div class="m-l-10">
                                                23
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="avatar avatar-image rounded">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-12.jpg" alt="">
                                            </div>
                                            <div class="m-l-10">
                                                <span>红色椅子</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>79</td>
                                    <td>￥1,298.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm w-100 m-b-0">
                                                <div class="progress-bar bg-warning" style="width: 54%"></div>
                                            </div>
                                            <div class="m-l-10">
                                                54
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="avatar avatar-image rounded">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-13.jpg" alt="">
                                            </div>
                                            <div class="m-l-10">
                                                <span>手环</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>60</td>
                                    <td>￥7,376.00</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-sm w-100 m-b-0">
                                                <div class="progress-bar bg-success" style="width: 76%"></div>
                                            </div>
                                            <div class="m-l-10">
                                                76
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
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-b-0">最新上传</h5>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-default">查看全部</a>
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="m-b-25">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="media align-items-center">
                                <div class="font-size-35">
                                    <i class="anticon anticon-file-word text-primary"></i>
                                </div>
                                <div class="m-l-15">
                                    <h6 class="m-b-0">
                                        <a class="text-dark" href="javascript:void(0);">文档.doc</a>
                                    </h6>
                                    <p class="text-muted m-b-0">1.2MB</p>
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
                                        <i class="anticon anticon-download"></i>
                                        <span class="m-l-10">下载</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-b-25">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="media align-items-center">
                                <div class="font-size-35">
                                    <i class="anticon anticon-file-excel text-success"></i>
                                </div>
                                <div class="m-l-15">
                                    <h6 class="m-b-0">
                                        <a class="text-dark" href="javascript:void(0);">费用.xls</a>
                                    </h6>
                                    <p class="text-muted m-b-0">518KB</p>
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
                                        <i class="anticon anticon-download"></i>
                                        <span class="m-l-10">下载</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-b-25">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="media align-items-center">
                                <div class="font-size-35">
                                    <i class="anticon anticon-file-text text-secondary"></i>
                                </div>
                                <div class="m-l-15">
                                    <h6 class="m-b-0">
                                        <a class="text-dark" href="javascript:void(0);">收据.txt</a>
                                    </h6>
                                    <p class="text-muted m-b-0">355KB</p>
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
                                        <i class="anticon anticon-download"></i>
                                        <span class="m-l-10">下载</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-b-25">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="media align-items-center">
                                <div class="font-size-35">
                                    <i class="anticon anticon-file-word text-primary"></i>
                                </div>
                                <div class="m-l-15">
                                    <h6 class="m-b-0">
                                        <a class="text-dark" href="javascript:void(0);">项目要求.doc</a>
                                    </h6>
                                    <p class="text-muted m-b-0">1.6MB</p>
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
                                        <i class="anticon anticon-download"></i>
                                        <span class="m-l-10">下载</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-b-25">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="media align-items-center">
                                <div class="font-size-35">
                                    <i class="anticon anticon-file-pdf text-danger"></i>
                                </div>
                                <div class="m-l-15">
                                    <h6 class="m-b-0">
                                        <a class="text-dark" href="javascript:void(0);">App Flow.pdf</a>
                                    </h6>
                                    <p class="text-muted m-b-0">19.8MB</p>
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
                                        <i class="anticon anticon-download"></i>
                                        <span class="m-l-10">下载</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="media align-items-center">
                                <div class="font-size-35">
                                    <i class="anticon anticon-file-ppt text-warning"></i>
                                </div>
                                <div class="m-l-15">
                                    <h6 class="m-b-0">
                                        <a class="text-dark" href="javascript:void(0);">演示.ppt</a>
                                    </h6>
                                    <p class="text-muted m-b-0">2.7MB</p>
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
                                        <i class="anticon anticon-download"></i>
                                        <span class="m-l-10">下载</span>
                                    </button>
                                    <button class="dropdown-item" type="button">
                                        <i class="anticon anticon-delete"></i>
                                        <span class="m-l-10">删除</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-b-0">最新上传</h5>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-default">查看全部</a>
                    </div>
                </div>
                <div class="m-t-30">
                    <div class="overflow-y-auto scrollable relative" style="height: 437px">
                        <ul class="timeline p-t-10 p-l-10">
                            <li class="timeline-item">
                                <div class="timeline-item-head">
                                    <div class="avatar avatar-text avatar-sm bg-primary">
                                        <span>V</span>
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
                                    <div class="avatar avatar-text avatar-sm bg-success">
                                        <span>L</span>
                                    </div>                                                                
                                </div>
                                <div class="timeline-item-content">
                                    <div class="m-l-10">
                                        <h5 class="m-b-5">德克萨斯</h5>
                                        <p class="m-b-0">
                                            <span class="font-weight-semibold">附件 </span> 
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
                                    <div class="avatar avatar-text avatar-sm bg-warning">
                                        <span>E</span>
                                    </div>                                                                
                                </div>
                                <div class="timeline-item-content">
                                    <div class="m-l-10">
                                        <h5 class="m-b-5">安子轩</h5>
                                        <p class="m-b-0">
                                            <span class="font-weight-semibold">评论  </span> 
                                            <span class="m-l-5"> '这不是我们的工作!'</span>
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
                                    <div class="avatar avatar-text avatar-sm bg-primary">
                                        <span>R</span>
                                    </div>                                                                
                                </div>
                                <div class="timeline-item-content">
                                    <div class="m-l-10">
                                        <h5 class="m-b-5">风一样的男人</h5>
                                        <p class="m-b-0">
                                            <span class="font-weight-semibold">评论  </span> 
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
                                    <div class="avatar avatar-text avatar-sm bg-danger">
                                        <span>P</span>
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
                                    <div class="avatar avatar-text avatar-sm bg-secondary">
                                        <span>M</span>
                                    </div>                                                                
                                </div>
                                <div class="timeline-item-content">
                                    <div class="m-l-10">
                                        <h5 class="m-b-5">阿七</h5>
                                        <p class="m-b-0">
                                            <span class="font-weight-semibold">创建   </span> 
                                            <span class="m-l-5"> this project</span>
                                        </p>
                                        <span class="text-muted font-size-13">
                                            <i class="anticon anticon-clock-circle"></i>
                                            <span class="m-l-5">5:21 PM</span>
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
    <div class="col-md-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-b-0">任务</h5>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-sm btn-default">查看全部</a>
                    </div>
                </div>
            </div>
            <div class="m-t-10">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-today">今天</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-week">周</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-month">月</a>
                    </li>
                </ul>
                <div class="tab-content m-t-15">
                    <div class="tab-pane card-body fade show active" id="tab-today">
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-today-1" type="checkbox">
                                    <label for="task-today-1" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">定义用户和工作流</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">按不同的用户权限执行不同流程</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-today-2" type="checkbox" checked="">
                                    <label for="task-today-2" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">分配工作</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">使用WBS进行工作拆分</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-today-3" type="checkbox" checked="">
                                    <label for="task-today-3" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">扩展数据模型</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">增加用户组的数据模型结构r</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-today-4" type="checkbox">
                                    <label for="task-today-4" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">更改接口</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">增加用户组接口</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-today-5" type="checkbox">
                                    <label for="task-today-5" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">创建数据表</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">表结构原型设计初稿</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane card-body fade" id="tab-week">
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-week-1" type="checkbox">
                                    <label for="task-week-1" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">验证链接</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">查找死链接</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-week-2" type="checkbox">
                                    <label for="task-week-2" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">订单控制台</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">超额收益可视化图表</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-week-3" type="checkbox" checked="">
                                    <label for="task-week-3" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">自定义模板</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">你看到这里有天线宝宝吗</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-week-4" type="checkbox" checked="">
                                    <label for="task-week-4" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">批处理计划</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">一个非常小的舞台</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-week-5" type="checkbox" checked="">
                                    <label for="task-week-5" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">准备实施</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">导轨系统</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane card-body fade" id="tab-month">
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-month-1" type="checkbox">
                                    <label for="task-month-1" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">创建用户组</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">尼泊尔钻井</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-month-2" type="checkbox" checked="">
                                    <label for="task-month-2" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">设计线框</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">超额收益可视化图表</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-month-3" type="checkbox">
                                    <label for="task-month-3" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">自定义模板</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">我一定会加强注意</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-month-4" type="checkbox">
                                    <label for="task-month-4" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">管理层会议</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">手工制作独家精品</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="m-b-15">
                            <div class="d-flex align-items-center">
                                <div class="checkbox">
                                    <input id="task-month-5" type="checkbox" checked="">
                                    <label for="task-month-5" class="d-flex align-items-center">
                                        <span class="inline-block m-l-10">
                                            <span class="text-dark font-weight-semi-bold font-size-16">扩展数据模型</span>
                                            <span class="m-b-0 text-muted font-size-13 d-block">欧洲观赏水景</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/chartjs/Chart.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/dashboard-default.js"></script>
<!-- /gc:html -->