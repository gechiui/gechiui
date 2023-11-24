
<?php
/**
 * Title: JS图表
 * Slug: gcoa/chartjs
 * Categories: charts
 * Keywords: JS图表
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">Chart Js</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">图表</a>
            <span class="breadcrumb-item active">Chart Js</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>线图</h4>
        <p>线图是绘制直线上数据点的一种方法。通常，它用于显示趋势数据，或比较两个数据集。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <canvas class="chart" id="line-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartjs/Chart.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>堆叠区域图</h4>
        <p>线图可以通过改变y轴上的设置来启用堆叠来配置成堆叠的区域图。堆叠区域图可用于显示一个数据趋势如何由多个较小的部分组成。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <canvas class="chart" id="stacked-area-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartjs/Chart.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>条形图</h4>
        <p>条形图提供了一种显示以垂直条形条表示的数据值的方法。它有时用于显示趋势数据，以及并排比较多个数据集。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <canvas class="chart" id="bar-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartjs/Chart.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>雷达图</h4>
        <p>雷达图是一种显示多个数据点及其之间变化的方式。它们通常可用于比较两个或多个不同数据集的点。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-3 mx-auto">
                    <canvas class="chart" id="radar-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartjs/Chart.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>圆环图</h4>
        <p>饼图可能是最常用的图表。它们被分成段，每个段的弧线显示每段数据的比例值。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-4 mx-auto">
                    <canvas class="chart" id="donut-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartjs/Chart.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>极地图</h4>
        <p>饼图可能是最常用的图表。它们被分成段，每个段的弧线显示每段数据的比例值。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-3 mx-auto">
                    <canvas class="chart" id="polar-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartjs/Chart.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/chartjs/Chart.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/chartjs.js"></script>
<!-- /gc:html -->