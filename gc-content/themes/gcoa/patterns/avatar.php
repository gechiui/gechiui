
<?php
/**
 * Title: 头像 Avatar
 * Slug: gcoa/avatar
 * Categories: ui-elements
 * Keywords: 头像 Avatar
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">头像 Avatar</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">UI元素</a>
            <span class="breadcrumb-item active">头像</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基本头像</h4>
        <p>头像有两个形状可供选择</p>
        <div class="m-t-25">
            <div class="avatar avatar-icon m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-square">
                <i class="anticon anticon-user"></i>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>头像尺寸</h4>
        <p>添加 <code>.avatar-lg</code> 或 <code>.avatar-sm</code> 以获取其他尺寸。 </p>
        <div class="m-t-25">
            <div class="avatar avatar-icon avatar-lg m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-sm m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>图片头像</h4>
        <p>在头像中使用图片。</p>
        <div class="m-t-25">
            <div class="avatar avatar-image m-r-10">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
            </div>
            <div class="avatar avatar-image m-r-10">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
            </div>
            <div class="avatar avatar-image m-r-10">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>文字头像</h4>
        <p>头像最多可以容纳2个字符的文本。</p>
        <div class="m-t-25">
            <div class="avatar avatar-text bg-primary m-r-10">
                <span>L</span>
            </div>
            <div class="avatar avatar-text bg-success m-r-10">
                <span>格</span>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>头像徽章</h4>
        <p>头像可以有一个状态徽章来指示。</p>
        <div class="m-t-25">
            <div class="avatar avatar-icon avatar-badge avatar-square m-r-10">
                <i class="anticon anticon-user"></i>
                <span class="badge badge-indicator badge-danger"></span>
            </div>
            <div class="avatar avatar-text avatar-badge avatar-square m-r-10">
                <span>JS</span>
                <span class="badge badge-indicator badge-danger">5</span>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>双色调头像</h4>
        <p>我们预设了一系列双色调的头像风格，用于不同场景的使用</p>
        <div class="m-t-25">
            <div class="avatar avatar-icon avatar-magenta m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-red m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-volcano m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-orange m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-gold m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-lime m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-green m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-cyan m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-blue m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-geekblue m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
            <div class="avatar avatar-icon avatar-purple m-r-10">
                <i class="anticon anticon-user"></i>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->