
<?php
/**
 * Title: 发票
 * Slug: gcoa/invoice
 * Categories: pages
 * Keywords: 发票 Invoice
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">发票</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">页面</a>
            <span class="breadcrumb-item active">发票</span>
        </nav>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-body">
            <div id="invoice" class="p-h-30">
                <div class="m-t-15 lh-2">
                    <div class="inline-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo.png" alt="">
                        <address class="p-l-10">
                            <span class="font-weight-semibold text-dark">格尺Ai, Inc.</span><br>
                            <span>9498 Harvard Street</span><br>
                            <span>Fairfield, Chicago Town 06824</span><br>
                            <abbr class="text-dark" title="Phone">Phone:</abbr>
                            <span>(123) 456-7890</span>
                        </address>
                    </div>
                    <div class="float-right">
                        <h2>INVOICE</h2>
                    </div>
                </div>
                <div class="row m-t-20 lh-2">
                    <div class="col-sm-9">
                        <h3 class="p-l-10 m-t-10">发票 To:</h3>
                        <address class="p-l-10 m-t-10">
                            <span class="font-weight-semibold text-dark">Genting Holdings.</span><br>
                            <span>8626 Maiden Dr. </span><br>
                            <span>Niagara Falls, New York 14304</span>
                        </address>
                    </div>
                    <div class="col-sm-3">
                        <div class="m-t-80">
                            <div class="text-dark text-uppercase d-inline-block">
                                <span class="font-weight-semibold text-dark">发票 No :</span></div>
                            <div class="float-right">#1668</div>
                        </div>
                        <div class="text-dark text-uppercase d-inline-block">
                            <span class="font-weight-semibold text-dark">日期 :</span>
                        </div>
                        <div class="float-right">25/7/2018</div>
                    </div>
                </div>
                <div class="m-t-20">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Items</th>
                                    <th>Quantity</th>
                                    <th>价格</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>1</th>
                                    <td>Asus Zenfone 3 Zoom ZE553KL Dual Sim (4GB, 64GB)</td>
                                    <td>2</td>
                                    <td>￥450.00</td>
                                    <td>￥900.00</td>
                                </tr>
                                <tr>
                                    <th>2</th>
                                    <td>HP Pavilion 15-au103TX 15.6˝ Laptop Red</td>
                                    <td>1</td>
                                    <td>￥550.00</td>
                                    <td>￥550.00</td>
                                </tr>
                                <tr>
                                    <th>3</th>
                                    <td>Canon EOS 77D</td>
                                    <td>1</td>
                                    <td>￥875.00</td>
                                    <td>￥875.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row m-t-30 lh-1-8">
                        <div class="col-sm-12">
                            <div class="float-right text-right">
                                <p>Sub - Total amount: ￥2,325</p>
                                <p>vat (10%) : ￥232 </p>
                                <hr>
                                <h3><span class="font-weight-semibold text-dark">Total :</span> ￥2,557.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-30 lh-2">
                        <div class="col-sm-12">
                            <div class="border-bottom p-v-20">
                                <p class="text-opacity"><small>In exceptional circumstances, Financial Services can provide an urgent manually processed special cheque. Note, however, that urgent special cheques should be requested only on an emergency basis as manually produced cheques involve duplication of effort and considerable staff resources. Requests need to be supported by a letter explaining the circumstances to justify the special cheque payment.</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="row m-v-20">
                        <div class="col-sm-6">
                            <img class="img-fluid text-opacity m-t-5" width="100" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo/logo.png" alt="">
                        </div>
                        <div class="col-sm-6 text-right">
                            <small><span class="font-weight-semibold text-dark">Phone:</span> (123) 456-7890</small>
                            <br>
                            <small>support@themenate.com</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->