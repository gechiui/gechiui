
<?php
/**
 * Title: 下拉菜单 Dropdown
 * Slug: gcoa/dropdown
 * Categories: components
 * Keywords: 下拉菜单 Dropdown
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">下拉菜单</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">下拉菜单</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基础下拉菜单</h4>
        <p>在<code>.dropdown</code>中包装下拉列表的触发按钮或链接，或声明 <code>position: relative;</code>.可以从 <code>&lt;a&gt;</code> 或 <code>&lt;button&gt;</code> 触发.</p>
        <div class="m-t-25">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle"  data-toggle="dropdown">
                    <span>下拉菜单</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">动作</a>
                    <a class="dropdown-item" href="#">下个动作</a>
                    <a class="dropdown-item" href="#">更多</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用上拉效果</h4>
        <p>通过向父元素添加 <code>.dropup</code> 触发菜单在元素上方显示.</p>
        <div class="m-t-25">
            <!-- 首页 dropup button -->
            <div class="btn-group dropup m-r-10">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    上拉菜单
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">动作</a>
                    <a class="dropdown-item" href="#">下个动作</a>
                    <a class="dropdown-item" href="#">更多</a>
                </div>
            </div>
            
            <!-- Split dropup button -->
            <div class="btn-group dropup">
                <button type="button" class="btn btn-primary">
                    分离式上拉菜单
                </button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">触发菜单</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">动作</a>
                    <a class="dropdown-item" href="#">下个动作</a>
                    <a class="dropdown-item" href="#">更多</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>右侧下拉菜单</h4>
        <p>通过向父元素添加<code>.dropright</code> 来触发元素右侧的下拉菜单。</p>
        <div class="m-t-25">
            <!-- 首页 dropright button -->
            <div class="btn-group dropright m-r-10">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    右侧下拉菜单
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">动作</a>
                    <a class="dropdown-item" href="#">下个动作</a>
                    <a class="dropdown-item" href="#">更多</a>
                </div>
            </div>
            
            <!-- Split dropright button -->
            <div class="btn-group dropright">
                <button type="button" class="btn btn-primary">
                    分离式右侧下拉菜单
                </button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">触发右侧下拉菜单</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">动作</a>
                    <a class="dropdown-item" href="#">下个动作</a>
                    <a class="dropdown-item" href="#">更多</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>左侧下拉菜单</h4>
        <p>通过向父元素添加<code>.dropleft</code> 来触发元素左侧的下拉菜单。</p>
        <div class="m-t-25">
            <!-- Default dropleft button -->
            <div class="btn-group dropleft m-r-10">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    左侧下拉菜单
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">动作</a>
                    <a class="dropdown-item" href="#">下个动作</a>
                    <a class="dropdown-item" href="#">更多</a>
                </div>
            </div>
            
            <!-- Split dropleft button -->
            <div class="btn-group dropleft">
                <div class="btn-group dropleft" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">触发左侧下拉菜单</span>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">动作</a>
                        <a class="dropdown-item" href="#">下个动作</a>
                        <a class="dropdown-item" href="#">更多</a>
                    </div>
                </div>
                <button type="button" class="btn btn-primary">
                    分离式左侧下拉菜单
                </button>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用动画效果</h4>
        <p>通过使用<code>.dropdown-animated</code> 和 <code>.dropdown</code>给下拉菜单添加动画效果.</p>
        <p><strong class="text-dark">注意:</strong> 动画效果仅适用于 <code>.dropdown</code> 。 <code>flip</code> 效果将被禁用. </p>
        <div class="m-t-25">
            <div class="dropdown dropdown-animated">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span>动画效果的下拉菜单</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">动作</a>
                    <a class="dropdown-item" href="#">下个动作</a>
                    <a class="dropdown-item" href="#">更多</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用向右缩放效果</h4>
        <p>添加 <code>.scale-right</code> 到 <code>.dropdown</code> 使用 <code>.dropdown-animated</code> 添加到下拉菜单的左下角.</p>
        <p><strong class="text-dark">注意:</strong> 动画效果仅适用于 <code>.dropdown</code> 。 <code>flip</code> 效果将被禁用. </p>
        <div class="m-t-25">
            <div class="dropdown dropdown-animated scale-right">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    向右缩放
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item" type="button">动作</button>
                    <button class="dropdown-item" type="button">下个动作</button>
                    <button class="dropdown-item" type="button">更多</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用向左缩放效果</h4>
        <p>添加 <code>.scale-left</code> 到 <code>.dropdown</code> 使用 <code>.dropdown-animated</code> 添加到下拉菜单的右下角.</p>
        <p><strong class="text-dark">注意:</strong> 动画效果仅适用于 <code>.dropdown</code> 。 <code>flip</code> 效果将被禁用. </p>
        <div class="m-t-25">
            <div class="dropdown dropdown-animated scale-left">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    向左缩放
                </button>
                <div class="dropdown-menu">
                    <button class="dropdown-item" type="button">动作</button>
                    <button class="dropdown-item" type="button">下个动作</button>
                    <button class="dropdown-item" type="button">更多</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>菜单标题</h4>
		<p>在任何下拉菜单中添加一个标题<code>.dropdown-header</code>来标记操作部分。</p>
        <div class="m-t-25">
            <div class="dropdown-menu float-none d-block relative" style="max-width: 180px">
                <h6 class="dropdown-header">下来菜单标题</h6>
                <a class="dropdown-item" href="#">动作</a>
                <a class="dropdown-item" href="#">下个动作</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>菜单分隔符</h4>
        <p>用分隔符将菜单项分组。</p>
        <div class="m-t-25">
            <div class="dropdown-menu float-none d-block relative" style="max-width: 180px">
                <a class="dropdown-item" href="#">动作</a>
                <a class="dropdown-item" href="#">下个动作</a>
                <a class="dropdown-item" href="#">更多</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">退出</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>菜单选择器</h4>
        <p>下拉菜单项添加 <code class="highlighter-rouge">.active</code> ，设置为<strong>选择器样式</strong>.</p>
        <div class="m-t-25">
            <div class="dropdown-menu float-none d-block relative" style="max-width: 180px">
                <a class="dropdown-item" href="#">常规链接</a>
                <a class="dropdown-item active" href="#">选择器链接</a>
                <a class="dropdown-item" href="#">常规链接</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>菜单项禁用</h4>
        <p>菜单项添加 <code class="highlighter-rouge">.disabled</code> 设置为 <strong>此菜单项被禁用</strong>.</p>
        <div class="m-t-25">
            <div class="dropdown-menu float-none d-block relative" style="max-width: 180px">
                <a class="dropdown-item" href="#">常规链接</a>
                <a class="dropdown-item disabled" href="#">禁用链接</a>
                <a class="dropdown-item" href="#">常规链接</a>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->