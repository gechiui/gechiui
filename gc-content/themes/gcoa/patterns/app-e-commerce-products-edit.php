<?php
/**
 * Title: 产品编辑
 * Slug: gcoa/app-e-commerce-products-edit
 * Categories: pages
 * Keywords: 产品编辑
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/select2/select2.css" rel="stylesheet">
<form>
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
                    <i class="anticon anticon-save"></i>
                    <span>保存</span>
                </button>
            </div>
        </div>
        <ul class="nav nav-tabs" >
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#product-edit-basic">基础信息</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#product-edit-option">选项信息</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#product-edit-description">说明</a>
            </li>
        </ul>
    </div>
    <div class="tab-content m-t-15">
        <div class="tab-pane fade show active" id="product-edit-basic" >
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productName">商品名称</label>
                        <input type="text" class="form-control" id="productName" placeholder="商品名称" value="男士西服">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productPrice">价格</label>
                        <input type="text" class="form-control" id="productPrice" placeholder="Price" value="$ 199">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productCategory">类别</label>
                        <select class="custom-select" id="productCategory">
                            <option value="cloths" selected>上衣</option>
                            <option value="homeDecoration">家居装饰</option>
                            <option value="电子产品">电子产品</option>
                            <option value="jewellery">珠宝</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productBrand">品牌</label>
                        <input type="text" class="form-control" id="productBrand" placeholder="Brand" value="H&M">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="product状态">状态</label>
                        <select class="custom-select" id="product状态">
                            <option value="inStock" selected>在售</option>
                            <option value="outOfStock">缺货</option>
                            <option value="pending">待定</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="product-edit-option">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productSize">尺码</label>
                        <select class="select2" id="productSize" multiple="multiple">
                            <option value="40" selected>40</option>
                            <option value="42" selected>42</option>
                            <option value="44">44</option>
                            <option value="46">46</option>
                            <option value="48">48</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productColors">颜色</label>
                        <select class="select2" id="productColors" multiple="multiple">
                            <option value="db" selected>深蓝</option>
                            <option value="g" selected>灰</option>
                            <option value="gb" selected>灰蓝</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productFit">身形</label>
                        <select class="custom-select" id="productFit">
                            <option value="skinny" selected>紧身</option>
                            <option value="slim">苗条</option>
                            <option value="regular">常规</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productMaterial">材质</label>
                        <select class="select2" id="productMaterial" multiple="multiple">
                            <option value="polyester" selected>聚酯纤维</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" for="productShip">发货地</label>
                        <select class="custom-select" id="productShip">
                            <option value="columbia" selected>哥伦比亚</option>
                            <option value="brazil">巴西</option>
                            <option value="chile">中国</option>
                            <option value="argentina">加拿大</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="product-edit-description">
            <div class="card">
                <div class="card-body">
                    <div id="productDescription">
                        <p>梭织面料直筒外套，缺角翻领设计，附翻盖嵌线前袋。有衬里。</p>
						<p>构成 氨纶 1%，粘纤 16%，聚酯纤维 83% 衬里: 聚酯纤维 100%  </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/select2/select2.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/quill/quill.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/e-commerce-product-edit.js"></script>
<!-- /gc:html -->