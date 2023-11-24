<?php
/**
 * Title: 提醒 Alert
 * Slug: gcoa/alert
 * Categories: ui-elements
 * Keywords: 提醒 Alert
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">提醒框</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">UI元素</a>
            <span class="breadcrumb-item active">提醒框</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">基础提醒框</h4>
    </div>
    <div class="card-body">
        <p>提醒框支持任何长度的文本，以及可选的关闭按钮。为了获得正确的样式，请选用五个 <b class="text-dark">规定</b> 的样式 classes (例如： <code>.alert-success</code>).</p>    
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-default">
                    这是一个默认提醒框——看看吧！
                </div>
                <div class="alert alert-primary">
                    这是一个主要提醒框——看看吧！
                </div>
                <div class="alert alert-success">
                    这是一个成功提醒框——看看吧！
                </div>
                <div class="alert alert-info">
                    这是一个信息提醒框——看看吧！
                </div>
                <div class="alert alert-warning">
                    这是一个警告提醒框——看看吧！
                </div>
                <div class="alert alert-danger">
                    这是一个危险提醒框——看看吧！
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">关闭提醒框</h4>
    </div>
    <div class="card-body">
        <p>添加一个关闭按钮 和 class样式<code>.alert-dismissible</code> , 在提醒框右侧添加了一个可充填内容的 <code>.close</code> 按钮.</p>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-primary alert-dismissible fade show">
                    <strong>我去!</strong> 您应该查看下面的一些字段。
                    <button type="button" class="close" data-dismiss="alert" aria-label="关闭">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">提醒框HTML元素</h4>
    </div>
    <div class="card-body">
        <p>提醒框还可以包含额外的HTML元素，如标题、段落和分隔符。</p>
        <div class="alert alert-success" >
            <h4 class="alert-heading">干的漂亮!</h4>
            <p class="m-b-0">你成功阅读了这条重要的提醒信息。此示例文本将运行更长时间，以便您可以查看警报中的间距如何与此类内容配合使用。</p>
            <hr class="m-v-20">
            <p class="m-b-0">请保持代码整洁。</p>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">提醒框使用图标Icons</h4>
    </div>
    <div class="card-body">
        <p>使用图标的提醒框。</p>
        <div class="alert alert-success">
            <div class="d-flex align-items-center justify-content-start">
                <span class="alert-icon">
                    <i class="anticon anticon-check-o"></i>
                </span>
                <span>成功</span>
            </div>
        </div>
        <div class="alert alert-primary">
            <div class="d-flex align-items-center justify-content-start">
                <span class="alert-icon">
                    <i class="anticon anticon-info-o"></i>
                </span>
                <span>信息说明</span>
            </div>
        </div>
        <div class="alert alert-warning">
            <div class="d-flex align-items-center justify-content-start">
                <span class="alert-icon">
                    <i class="anticon anticon-exclamation-o"></i>
                </span>
                <span>警告</span>
            </div>
        </div>
        <div class="alert alert-danger">
            <div class="d-flex align-items-center justify-content-start">
                <span class="alert-icon">
                    <i class="anticon anticon-close-o"></i>
                </span>
                <span>错误</span>
            </div>
        </div>
        <div class="m-t-50">
            <div class="alert alert-success">
                <div class="d-flex justify-content-start">
                    <span class="alert-icon m-r-20 font-size-30">
                        <i class="anticon anticon-check-circle"></i>
                    </span>
                    <div>
                        <h5 class="alert-heading">成功</h5>
                        <p>文案成功的详细描述和建议。</p>
                    </div>
                </div>
            </div>
            <div class="alert alert-primary">
                <div class="d-flex justify-content-start">
                    <span class="alert-icon m-r-20 font-size-30">
                        <i class="anticon anticon-info-circle"></i>
                    </span>
                    <div>
                        <h5 class="alert-heading">信息说明</h5>
                        <p>关于文案的附加说明和信息。</p>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning">
                <div class="d-flex justify-content-start">
                    <span class="alert-icon m-r-20 font-size-30">
                        <i class="anticon anticon-exclamation-circle"></i>
                    </span>
                    <div>
                        <h5 class="alert-heading">警告</h5>
                        <p>这是一份关于文案的警示通知。</p>
                    </div>
                </div>
            </div>
            <div class="alert alert-danger">
                <div class="d-flex justify-content-start">
                    <span class="alert-icon m-r-20 font-size-30">
                        <i class="anticon anticon-close-circle"></i>
                    </span>
                    <div>
                        <h5 class="alert-heading">错误</h5>
                        <p>这是一条关于文案的错误信息。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->