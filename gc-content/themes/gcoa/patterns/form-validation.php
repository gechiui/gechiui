
<?php
/**
 * Title: 表单验证
 * Slug: gcoa/form-validation
 * Categories: forms
 * Keywords: 表单验证
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">表单验证</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">表单</a>
            <span class="breadcrumb-item active">表单验证</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>验证样式</h4>
        <p>可以使用<code>.is-invalid</code> 和 <code>.is-valid</code>来指示无效和有效的表单字段。注意，这些类也支持<code>.invalid-feedback</code>。</p>
        <div class="m-t-25" style="max-width: 1000px">
            <form>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationServer01">验证通过</label>
                        <input type="text" class="form-control is-valid" id="validationServer01" placeholder="First name" value="Mark" required>
                        <div class="valid-feedback">
                            很棒!
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationServer02">无效</label>
                        <input type="text" class="form-control is-invalid" id="validationServer02" placeholder="Last name" required>
                        <div class="invalid-feedback">
                            请输入有效值
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>jQuery 验证</h4>
        <p>这个jQuery插件使表单验证变得容易，同时仍然提供大量的自定义选项。更多使用信息，请参考 <a href="https://jqueryvalidation.org/validate/" target="_blank">jQuery Validation</a> </p>
        <div class="m-t-25">
            <form id="form-validation">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">必填*</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputRequired" placeholder="必填*">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">最小长度</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputMinLength" placeholder="输入至少8个字符">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">最大长度</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputMaxLength" placeholder="输入最多8个字符">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">范围长度</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputRangeLength" placeholder="输入2到6个字符" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">最小值</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputMinValue" placeholder="输入不小于8的数字">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">最大值</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputMaxValue" placeholder="输入不大于6的数字">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">范围值</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputRangeValue" placeholder="输入6到12之间的数字">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">电子邮件</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputEmail" placeholder="输入有效的电子邮件">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">密码</label>
                    <div class="col-md-5">
                        <input id="password" type="text" class="form-control" name="inputPassword" placeholder="输入你的密码">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">确认密码</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="inputPasswordConfirm" placeholder="再次输入密码">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">URL</label>
                    <div class="col-md-5">
                        <input type="url" class="form-control" name="inputUrl" placeholder="输入有效的URL地址" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label control-label">数字</label>
                    <div class="col-md-5">
                        <input type="url" class="form-control" name="inputDigit" placeholder="输入数字" required>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/jquery-validation/jquery.validate.min.js">&lt;/script></code></pre>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/form-validation.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/jquery-validation/localization/messages_zh.js"></script>
<!-- /gc:html -->