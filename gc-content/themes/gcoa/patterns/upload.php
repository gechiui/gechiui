
<?php
/**
 * Title: 文件选择器 Upload
 * Slug: gcoa/upload
 * Categories: components
 * Keywords: 文件选择器 Upload
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">文件选择器</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">文件选择器</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基础文件选择器</h4>
        <p>这是一个比较粗糙的文件选择器</p>
        <div class="m-t-25">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile1">
                <label class="custom-file-label" for="customFile1">Choose file</label>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>华为云版本</h4>
        <p>参考使用华为云企业认证时的效果</p>
        <div class="m-t-25">
                <input type="file" id="customFile2" style="display:none">
                <label for="customFile2">
                    <div class="media align-items-center co-bg" for="customFile2">
                         <div  class="media align-items-center co-business">
                               <i class="h1 anticon anticon-plus-circle" style="margin: 0 auto;"></i>
                         </div>
                     </div>
                </label>
        </div>
    </div>
</div>
<!-- /gc:html -->