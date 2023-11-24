
<?php
/**
 * Title: 弹出框 Popover
 * Slug: gcoa/popover
 * Categories: components
 * Keywords: 弹出框 Popover
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">弹出框</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">弹出框</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基础弹出框</h4>
        <p>弹出框用法示例。</p>
		<p>注意：目前测试结果是部分Safari浏览器不兼容</p>
        <div class="m-t-25">
            <button type="button" class="btn btn-primary" data-toggle="popover" title="弹出框标题" data-content="这里是弹出框内容">单击可触发弹出窗口</button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>四个方向</h4>
        <p>有四个选项可用：顶部、右侧、底部和左侧。</p>
        <div class="m-t-25">
            <button type="button" class="btn btn-primary m-r-10" data-container="body" data-toggle="popover" data-placement="top" data-content="这里是弹出框内容">
                顶部
            </button>
            
            <button type="button" class="btn btn-secondary m-r-10" data-container="body" data-toggle="popover" data-placement="right" data-content="这里是弹出框内容">
                右侧
            </button>
            
            <button type="button" class="btn btn-success m-r-10" data-container="body" data-toggle="popover" data-placement="bottom" data-content="这里是弹出框内容">
                底部
            </button>
            
            <button type="button" class="btn btn-warning m-r-10" data-container="body" data-toggle="popover" data-placement="left" data-content="这里是弹出框内容">
                左侧
            </button>
        </div>
    </div>
</div>
<!-- /gc:html -->