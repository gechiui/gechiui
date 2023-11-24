
<?php
/**
 * Title: 鼠标经过提示 tooltips
 * Slug: gcoa/tooltips
 * Categories: components
 * Keywords: 鼠标经过提示 tooltips
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">工具提示</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">工具提示</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>链接添加工具提示</h4>
        <p>将鼠标悬停在以下链接上可查看工具提示：</p>
        <div class="m-t-25">
            <div class="p-25 border rounded">
                <p>Tight pants next level keffiyeh <a href="#" data-toggle="tooltip" title="Default tooltip">you probably</a> haven't heard of them. Photo booth beard raw denim letterpress vegan messenger bag stumptown. Farm-to-table seitan, mcsweeney's fixie sustainable quinoa 8-bit american apparel <a href="#" data-toggle="tooltip" title="Another tooltip">have a</a> terry richardson vinyl chambray. Beard stumptown, cardigans banh mi lomo thundercats. Tofu biodiesel williamsburg marfa, four loko mcsweeney's cleanse vegan chambray. A really ironic artisan <a href="#" data-toggle="tooltip" title="Another one here too">whatever keytar</a>, scenester farm-to-table banksy Austin <a href="#" data-toggle="tooltip" title="" data-original-title="The last tip!">twitter handle</a> freegan cred raw denim single-origin coffee viral.</p>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>显示位置</h4>
        <p>将鼠标悬停在下面的按钮上可查看四个工具提示方向：顶部、右侧、底部和左侧</p>
        <div class="m-t-25">
            <button type="button" class="btn btn-primary m-r-10" data-toggle="tooltip" data-placement="top" title="Tooltip on top">
                顶部
            </button>
            <button type="button" class="btn btn-secondary m-r-10" data-toggle="tooltip" data-placement="right" title="Tooltip on right">
                右侧
            </button>
            <button type="button" class="btn btn-success m-r-10" data-toggle="tooltip" data-placement="bottom" title="Tooltip on bottom">
                底部
            </button>
            <button type="button" class="btn btn-warning m-r-10" data-toggle="tooltip" data-placement="left" title="Tooltip on left">
                左侧
            </button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用 HTML</h4>
        <p>使用自定义的 HTML</p>
        <div class="m-t-25">
            <button type="button" class="btn btn-primary" data-toggle="tooltip" data-html="true" title="<em>Tooltip</em> <u>with</u> <b>HTML</b>">
                使用 HTML
            </button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>禁用元件</h4>
        <p>具有<code>disabled</code>属性的元素是不可交互的，这意味着用户不能聚焦、悬停或单击它们来触发工具提示（或弹出窗口）。作为一种解决方法，您需要从包装器<code>&lt;div&gt;</code> 或 <code>&lt;span&gt;</code>触发工具提示，最好使用<code>tabindex="0"</code>使键盘可聚焦，并覆盖禁用元素上的<code>pointer-events</code>。
		</p>
        <div class="m-t-25">
            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled tooltip">
                <button class="btn btn-primary" style="pointer-events: none;" type="button" disabled>禁用按钮</button>
            </span>
        </div>
    </div>
</div>
<!-- /gc:html -->