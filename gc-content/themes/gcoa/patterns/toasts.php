
<?php
/**
 * Title: 气泡 Toasts
 * Slug: gcoa/toasts
 * Categories: components
 * Keywords: 气泡 Toasts
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">气泡</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">气泡</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基础</h4>
        <p>气泡可以根据需要灵活使用，所需的标记非常少。至少我们需要一个单独的元素来包含你的“气泡”内容，并强烈建议使用一个关闭按钮。</p>
        <div class="m-t-25">
            <div class="toast fade show">
                <div class="toast-header">
                    <i class="anticon anticon-info-circle text-primary m-r-5"></i>
                    <strong class="mr-auto">Bootstrap</strong>
                    <small>11分钟前</small>
                    <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    Hello, world! 这是一个气泡信息.
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>堆积</h4>
        <p>当您有多个气泡消息时，我们默认以可读的方式垂直堆叠它们。</p>
        <div class="m-t-25">
            <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="anticon anticon-info-circle text-primary m-r-5"></i>
                    <strong class="mr-auto">Bootstrap</strong>
                    <small class="text-muted">刚刚</small>
                    <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    See? Just like this.
                </div>
            </div>
                
            <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="anticon anticon-info-circle text-primary m-r-5"></i>
                    <strong class="mr-auto">Bootstrap</strong>
                    <small class="text-muted">2秒前</small>
                    <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    Heads up, toasts will stack automatically
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>通知气泡</h4>
        <p>一个有效的通知气泡. 应用位置 class样式<code>.notification-toast</code>. 通知气泡 class样式: <code>top-right</code>, <code>top-middle</code>, <code>top-left</code>, <code>bottom-right</code>, <code>bottom-middle</code>, <code>bottom-left</code></p>
        <div class="m-t-25">
            <button id="notification-toast-btn" class="btn btn-primary" onclick="showToast()">触发</button>
            <div class="notification-toast top-right" id="notification-toast"></div>
        </div>
    </div>
</div>
<!-- /gc:html -->