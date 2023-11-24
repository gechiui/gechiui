<?php
/**
 * Title: 产品详情
 * Slug: gcoa/app-e-commerce-products
 * Categories: pages
 * Keywords: 产品详情
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header no-gutters has-tab">
    <div class="d-md-flex m-b-15 align-items-center justify-content-between">
        <div class="media align-items-center m-b-15">
            <div class="avatar avatar-image rounded" style="height: 70px; width: 70px">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-16.jpg" alt="">
            </div>
            <div class="m-l-15">
                <h4 class="m-b-0">男士西服</h4>
                <p class="text-muted m-b-0">编号: #5325</p>
            </div>
        </div>
        <div class="m-b-15">
            <button class="btn btn-primary">
                <i class="anticon anticon-edit"></i>
                <span>编辑</span>
            </button>
        </div>
    </div>
    <ul class="nav nav-tabs" >
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#product-overview">商品详情</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#product-images">商品图片</a>
        </li>
    </ul>
</div>
<div class="container-fluid">
    <div class="tab-content m-t-15">
        <div class="tab-pane fade show active" id="product-overview" >
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <i class="font-size-40 text-success anticon anticon-smile"></i>
                                <div class="m-l-15">
                                    <p class="m-b-0 text-muted">10 星</p>
                                    <div class="star-rating m-t-5">
                                        <input type="radio" id="star3-5" name="rating-3" value="5" checked disabled/><label for="star3-5" title="5 star"></label>
                                        <input type="radio" id="star3-4" name="rating-3" value="4" disabled/><label for="star3-4" title="4 star"></label>
                                        <input type="radio" id="star3-3" name="rating-3" value="3" disabled/><label for="star3-3" title="3 star"></label>
                                        <input type="radio" id="star3-2" name="rating-3" value="2" disabled/><label for="star3-2" title="2 star"></label>
                                        <input type="radio" id="star3-1" name="rating-3" value="1" disabled/><label for="star3-1" title="1 star"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <i class="font-size-40 text-primary anticon anticon-shopping-cart"></i>
                                <div class="m-l-15">
                                    <p class="m-b-0 text-muted">售价</p>
                                    <h3 class="m-b-0 ls-1">1,521</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <i class="font-size-40 text-primary anticon anticon-message"></i>
                                <div class="m-l-15">
                                    <p class="m-b-0 text-muted">评价</p>
                                    <h3 class="m-b-0 ls-1">27</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <i class="font-size-40 text-primary anticon anticon-stock"></i>
                                <div class="m-l-15">
                                    <p class="m-b-0 text-muted">库存</p>
                                    <h3 class="m-b-0 ls-1">152</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">基础信息</h4>
                    <div class="table-responsive">
                        <table class="product-info-table m-t-20">
                            <tbody>
                                <tr>
                                    <td>价格:</td>
                                    <td class="text-dark font-weight-semibold">￥199.00</td>
                                </tr>
                                <tr>
                                    <td>类别:</td>
                                    <td>	上衣</td>
                                </tr>
                                <tr>
                                    <td>品牌:</td>
                                    <td>H&M</td>
                                </tr>
                                <tr>
                                    <td>税率:</td>
                                    <td>10%</td>
                                </tr>
                                <tr>
                                    <td>状态:</td>
                                    <td>
                                        <span class="badge badge-pill badge-cyan">在售</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">选项信息</h4>
                    <div class="table-responsive">
                        <table class="product-info-table m-t-20">
                            <tbody>
                                <tr>
                                    <td>尺码:</td>
                                    <td>S, M, L, XL</td>
                                </tr>
                                <tr>
                                    <td>颜色:</td>
                                    <td class="d-flex">
                                        <span class="d-flex align-items-center m-r-20">
                                            <span class="badge badge-dot product-color m-r-5" style="background-color: #4c4e69"></span>
                                            <span>深蓝</span>
                                        </span>
                                        <span class="d-flex align-items-center m-r-20">
                                            <span class="badge badge-dot product-color m-r-5" style="background-color: #868686"></span>
                                            <span>灰</span>
                                        </span>
                                        <span class="d-flex align-items-center m-r-20">
                                            <span class="badge badge-dot product-color m-r-5" style="background-color: #8498c7"></span>
                                            <span>灰蓝</span>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>身形:</td>
                                    <td>紧身</td>
                                </tr>
                                <tr>
                                    <td>材质:</td>
                                    <td>聚酯纤维</td>
                                </tr>
                                <tr>
                                    <td>发货地:</td>
                                    <td>哥伦比亚</td>
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">商品 说明</h4>
                </div>
                <div class="card-body">
                    <p>梭织面料西装上衣，腰身收窄，七分袖有褶缝，前襟配一粒纽扣和金属链，附暗侧袋。有衬里。此款西装上衣含部分再生聚酯纤维。.</p>
                    <p>构成 氨纶 1%，粘纤 16%，聚酯纤维 83% 衬里: 聚酯纤维 100%</p>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="product-images">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/assets/images/others/product-1.jpg" alt="">
                        </div>
                        <div class="col-md-3">
                            <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/assets/images/others/product-2.jpg" alt="">
                        </div>
                        <div class="col-md-3">
                            <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/assets/images/others/product-3.jpg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->