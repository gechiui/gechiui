
<?php
/**
 * Title: 滑块与进度条
 * Slug: gcoa/alert
 * Categories: components
 * Keywords: 滑块与进度条 Components
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/nouislider/nouislider.min.css" rel="stylesheet">
<div class="page-header">
    <h2 class="header-title">滑块与进度</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">滑块与进度</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基础进度条</h4>
        <p>进度条控件由两个HTML元素构建，一些CSS用于设置宽度，以及一些属性，确保您可以堆叠进度条、设置它们的动画以及在它们上面放置文本标签。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-7">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用背景色</h4>
        <p>使用背景色改变进度条的外观。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-7">
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>多重进度条</h4>
        <p>如果需要，在进度条控件中包含多个进度。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-7">
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用滑块</h4>
        <p>这是一个有范围的滑块。它提供了大量的功能，而且体积小、重量轻、体积小，非常适合在许多受支持的设备上使用，包括iPhone、iPad、Android设备和Windows（Phone）8手机、平板电脑和多功能一体机。当然，它也适用于台式机！</p>
        <div class="row">
            <div class="col-md-7">
                <div class="m-t-25">
                    <div class="m-b-40">
                        <div id="horizon-primary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>垂直滑块</h4>
        <p>这是一个垂直滑块效果.</p>
        <div class="row">
            <div class="col-md-7">
                <div class="m-t-25">
                    <div class="d-inline-block m-l-30" style="height: 200px">
                        <div id="vertical-default"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>范围滑块</h4>
        <p>2个滑块的组合应用.</p>
        <div class="row">
            <div class="col-md-7">
                <div class="m-t-25">
                    <div id="range-slider"></div>
                    <div class="d-flex justify-content-between m-t-10">
                        <p class="d-inline-block"><b>最小值: </b> <span id="range-min"></span></p>
                        <p class="pull-right"><b>最大值: </b> <span id="range-max"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>刻度滑块</h4>
        <p>给进度条添加一个刻度.</p>
        <div class="row">
            <div class="col-md-7">
                <div class="m-t-25">
                    <div id="step-slider" class="m-b-40"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/nouislider/nouislider.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/slider.js"></script>
<!-- /gc:html -->