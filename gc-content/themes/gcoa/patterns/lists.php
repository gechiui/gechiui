
<?php
/**
 * Title: 列表 Lists
 * Slug: gcoa/lists
 * Categories: ui-elements
 * Keywords: 列表 Lists
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">列表 Lists</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">UI元素</a>
            <span class="breadcrumb-item active">列表 Lists</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基本示例</h4>
        <p>最基本的列表组是一个不排序列表。</p>
        <div class="m-t-25">
            <ul class="list-group" style="max-width: 400px">
                <li class="list-group-item">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
            </ul>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>节点选择器</h4>
        <p>添加 <code>.active</code> 到一个<code>.list-group-item</code> 指示当前活动的选择.</p>
        <div class="m-t-25">
            <ul class="list-group" style="max-width: 400px">
                <li class="list-group-item active">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
            </ul>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>禁用节点</h4>
        <p>添加 <code>.disabled</code> 到一个 <code>.list-group-item</code> 使节点 <em>显示为</em> 禁用. 注意某些带有 <code>.disabled</code> 的元素还需要自定义 JavaScript 来完全禁用点击事件 (如：链接).</p>
        <div class="m-t-25">
            <ul class="list-group" style="max-width: 400px">
                <li class="list-group-item disabled">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
            </ul>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>链接和按钮</h4>
        <p>添加 <code>.list-group-item-action</code>可以使 <code>&lt;链接&gt;</code> 或 <code>&lt;按钮&gt;</code> 具有悬停, 禁用, 和 点击的效果。我们将这些伪类（pseudo-classes）分开，以确保由非交互元素（如<code>&lt;li&gt;</code>或<code>&lt;div&gt;</code>）组成的列表组不会提供点击或提示。</p>
        <div class="m-t-25">
            <div class="list-group" style="max-width: 400px">
                <a href="#" class="list-group-item list-group-item-action active">
                    Cras justo odio
                </a>
                <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
                <a href="#" class="list-group-item list-group-item-action">Morbi leo risus</a>
                <a href="#" class="list-group-item list-group-item-action">Porta ac consectetur ac</a>
                <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1" aria-disabled="true">Vestibulum at eros</a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>去边框</h4>
        <p>添加 <code>.list-group-flush</code> 删除边框和圆角，使列表组在父容器（例如：卡片）中边到边呈现。</p>
        <div class="m-t-25">
            <ul class="list-group list-group-flush" style="max-width: 400px">
                <li class="list-group-item">Cras justo odio</li>
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
            </ul> 
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>列表添加多媒体</h4>
        <p>包含多媒体（如：图片）内容的列表</p>
        <div class="m-t-25">
            <ul class="list-group" style="max-width: 400px">
                <li class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                        </div>
                        <div class="m-l-10">
                            <div class="m-b-0 text-dark font-weight-semibold">安子轩</div>
                            <div class="m-b-0 opacity-07 font-size-13">commented on your post's</div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                        </div>
                        <div class="m-l-10">
                            <div class="m-b-0 text-dark font-weight-semibold">达里尔</div>
                            <div class="m-b-0 opacity-07 font-size-13">commented on your post's</div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                        </div>
                        <div class="m-l-10">
                            <div class="m-b-0 text-dark font-weight-semibold">阿七</div>
                            <div class="m-b-0 opacity-07 font-size-13">commented on your post's</div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>自适应表格</h4>
        <p>使用List制作一个表格，可以在移动端呈现很好的自适应性</p>
        <div class="m-t-25">
            <ul class="list-unstyled m-t-10">
                <li class="row">
                    <div class="col-12 m-t-20">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="#">
                                    <div  class="row">
                                        <p class="col-sm-4 text-dark">主题</p>
                                        <p class="col-sm-2 text-dark">作者</p>
                                        <p class="col-sm-2 text-dark">提交时间</p>
                                        <p class="col-sm-2 text-dark">状态</p>
                                        <p class="col-sm-2 text-dark">操作</p>
                                    </div>
                                 </a>
                            </li>
                            <li class="list-group-item">
                                <a href="https://www.gechiai.com/h-pd-11.html">
                                    <div  class="row">
                                        <p class="col-sm-4 text-dark">天眼查API（用友版）</p>
                                        <p class="col-sm-2">巴拉啦</p>
                                        <p class="col-sm-2">2021-01-04</p>
                                        <p class="col-sm-2">待审核</p>
                                        <p class="col-sm-2">
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                            <i class="anticon anticon-edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                            <i class="anticon anticon-delete"></i>
                                        </button>
                                        </p>
                                    </div>
                                 </a>
                            </li>
                            <li class="list-group-item">
                                <a href="https://www.gechiai.com">
                                    <div  class="row">
                                        <p class="col-sm-4 text-dark">电子营业执照企业应用接入</p>
                                        <p class="col-sm-2">巴拉啦</p>
                                        <p class="col-sm-2">2021-01-04</p>
                                        <p class="col-sm-2">待审核</p>
                                        <p class="col-sm-2">
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                            <i class="anticon anticon-edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                            <i class="anticon anticon-delete"></i>
                                        </button>
                                        </p>
                                    </div>
                                 </a>
                            </li>
                            <li class="list-group-item">
                                <a href="https://www.gechiai.com/h-pd-19.html">
                                    <div  class="row">
                                        <p class="col-sm-4 text-dark">微信智能营销机器人</p>
                                        <p class="col-sm-2">巴拉啦</p>
                                        <p class="col-sm-2">2021-01-04</p>
                                        <p class="col-sm-2">待审核</p>
                                        <p class="col-sm-2">
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                            <i class="anticon anticon-edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                            <i class="anticon anticon-delete"></i>
                                        </button>
                                        </p>
                                    </div>
                                 </a>
                            </li>
                            <li class="list-group-item">
                                <a href="https://www.gechiai.com/h-pd-18.html">
                                    <div  class="row">
                                        <p class="col-sm-4 text-dark">闪验-手机号一键登录</p>
                                        <p class="col-sm-2">巴拉啦</p>
                                        <p class="col-sm-2">2021-01-04</p>
                                        <p class="col-sm-2">待审核</p>
                                        <p class="col-sm-2">
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right" >
                                            <i class="anticon anticon-edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                            <i class="anticon anticon-delete"></i>
                                        </button>
                                        </p>
                                    </div>
                                 </a>
                            </li>
                            <li class="list-group-item">
                                <a href="https://www.gechiai.com/h-pd-16.html">
                                    <div  class="row">
                                        <p class="col-sm-4 text-dark">短信通道 低至0.04</p>
                                        <p class="col-sm-2">巴拉啦</p>
                                        <p class="col-sm-2">2021-01-04</p>
                                        <p class="col-sm-2">待审核</p>
                                        <p class="col-sm-2">
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded pull-right">
                                            <i class="anticon anticon-edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-hover btn-sm btn-rounded">
                                            <i class="anticon anticon-delete"></i>
                                        </button>
                                        </p>
                                    </div>
                                 </a>
                            </li>
                        </ul> 
                    </div>
                </li>
            </ul>
            <div class="row">
                <div class="col-sm-12">
                        <ul class="pagination"  style=" float:right;">
                            <li class="page-item next disabled">
                                <a href="#"  class="page-link">上一页</a>
                            </li>
                            <li class="page-item active">
                                <a href="#"  class="page-link">1</a>
                            </li>
                            <li class="page-item">
                                <a href="#"  class="page-link">2</a>
                            </li>
                            <li class="page-item next disabled">
                                <a href="#" class="page-link">下一页</a>
                            </li>
                        </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->