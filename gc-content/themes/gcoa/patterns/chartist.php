
<?php
/**
 * Title: 数据图表
 * Slug: gcoa/chartist
 * Categories: charts
 * Keywords: 数据图表
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">数据图表</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">图表</a>
            <span class="breadcrumb-item active">数据图表</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>线图</h4>
        <p>一个有三个系列的简单线图的例子。您可以实时编辑此示例。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="simple-line-chart"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartist/chartist.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>散射图</h4>
        <p>这个高级示例使用线图绘制散点图。数据对象使用函数样式随机机制创建。有一个移动第一响应配置，使用响应选项在小屏幕上显示更少的标签。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="line-scatter-chart"></div>
                </div>
            </div>
        </div>
    </div>
</div> 
<div class="card">
    <div class="card-body">
        <h4>带面积的线图</h4>
        <p>该图表使用 showArea 选项绘制线条、点以及区域形状。使用低选项指定一个固定的下界，使区域扩展。您还可以使用 areaBase 属性指定用于确定区域形状基位置的数据值（默认为 0）。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="line-chart-area"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartist/chartist.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div> 
<div class="card">
    <div class="card-body">
        <h4>双极条形图</h4>
        <p>范围限制设置的双极条形图，设置有低和高。还有一个插值函数，用于跳过每个奇数网格线/标签。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="bi-polar-bar"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartist/chartist.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div> 
<div class="card">
    <div class="card-body">
        <h4>堆叠条形图</h4>
        <p>您还可以通过在配置中使用stackBars属性，将条形图设置为轻松地将系列条形图堆叠在一起。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="stacked-bar"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartist/chartist.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>水平柱状图</h4>
        <p>创建水平条形图非常简单。不需要学习新的图表类型，只需传递一个额外的选项就足够了。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="horizontal-bar"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartist/chartist.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>仪表图</h4>
        <p>这个饼图使用 donut, startAngle and total来绘制。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="gauge-chart"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartist/chartist.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>圆环图</h4>
        <p>这个饼图使用donut 和 donutSolid来绘制圆环图。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="ct-chart" id="donut-chart"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/chartist/chartist.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/chartist/chartist.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/chartist.js"></script>
<!-- /gc:html -->