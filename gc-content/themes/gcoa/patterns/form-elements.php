
<?php
/**
 * Title: 表单元素
 * Slug: gcoa/form-elements
 * Categories: forms
 * Keywords: 表单元素
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/select2/select2.css" rel="stylesheet">
<link href="<?php echo get_template_directory_uri(); ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">
<div class="page-header">
    <h2 class="header-title">表单元素</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">表单</a>
            <span class="breadcrumb-item active">表单元素</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>基础输入框</h4>
        <p> <code>&lt;input&gt;</code>, <code>&lt;select&gt;</code> 和 <code>&lt;textarea&gt;</code>使用<code>.form-control</code>用于一般的外观、聚焦状态、大小的调整。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control m-b-15" placeholder="基础输入框">
                    <input type="text" class="form-control" placeholder="禁用输入框" disabled="">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>输入框大小</h4>
        <p>使用 <code>.form-control-lg</code> 和 <code>.form-control-sm</code>设置高度。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-4">
                    <input class="form-control form-control-lg m-b-10" type="text" placeholder=".form-control-lg">
                    <input class="form-control m-b-10" type="text" placeholder="默认">
                    <input class="form-control form-control-sm" type="text" placeholder=".form-control-sm">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>输入框组</h4>
        <p>在输入框的任一侧放置一个附加项或按钮。你也可以在输入框的两边各放一个。记住将<code>&lt;label&gt;</code>放在输入框组之外。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-5">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">@</span>
                        </div>
                        <input type="text" class="form-control" placeholder="用户名" aria-label="用户名" aria-describedby="basic-addon1">
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="收件人用户名" aria-label="收件人用户名" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2">@example.com</span>
                        </div>
                    </div>
                    
                    <label for="basic-url">你的网站地址</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon3">https://</span>
                        </div>
                        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                    </div>
                    
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="text" class="form-control" aria-label="金额 (to the nearest dollar)">
                        <div class="input-group-append">
                            <span class="input-group-text">.00</span>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">多行文本</span>
                        </div>
                        <textarea class="form-control" aria-label="多行文本"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>输入框组大小</h4>
        <p>将相对的表单大小类添加到<code>.input-group</code>本身，其中的内容将自动调整大小，无需在每个元件上重复表单控件大小类。</p>
        <p><strong>不支持输入框组单个原件的调整</strong></p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">Small</span>
                        </div>
                        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                        
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">默认</span>
                        </div>
                        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                    </div>
                        
                    <div class="input-group input-group-lg">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-lg">大号</span>
                        </div>
                        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>输入框缀</h4>
        <p>给输入框添加前缀或后缀。</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-affix m-b-10">
                        <i class="prefix-icon anticon anticon-search"></i>
                        <input type="text" class="form-control" placeholder="Icon前缀">
                    </div>
                    <div class="input-affix m-b-10">
                        <input type="text" class="form-control" placeholder="Icon后缀">
                        <i class="suffix-icon anticon anticon-eye"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>复选框</h4>
        <p>复选框的用法</p>
        <div class="m-t-25">
            <div class="checkbox">
                <input id="checkbox1" type="checkbox" checked="">
                <label for="checkbox1">Checked</label>
            </div>
            <div class="checkbox">
                <input id="checkbox2" type="checkbox">
                <label for="checkbox2">Uncheck</label>
            </div>
            <div class="checkbox">
                <input id="checkbox3" type="checkbox" disabled="">
                <label for="checkbox3">Disabled Unchecked</label>
            </div>
            <div class="checkbox">
                <input id="checkbox4" type="checkbox" checked="" disabled="">
                <label for="checkbox4">Disabled Checked</label>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>单选框</h4>
        <p>单选框的用法</p>
        <div class="m-t-25">
            <div class="radio">
                <input id="radio1" name="radioDemo" type="radio" checked="">
                <label for="radio1">Checked</label>
            </div>
            <div class="radio">
                <input id="radio2" name="radioDemo" type="radio">
                <label for="radio2">Uncheck</label>
            </div>
            <div class="radio">
                <input id="radio3" name="radioDemo1" type="radio" disabled="">
                <label for="radio3">Disabled Unchecked</label>
            </div>
            <div class="radio">
                <input id="radio4" name="radioDemo1" type="radio" checked="" disabled="">
                <label for="radio4">Disabled Checked</label>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>开关</h4>
        <p>开关的用法</p>
        <div class="m-t-25">
            <div class="form-group d-flex align-items-center">
                <div class="switch m-r-10">
                    <input type="checkbox" id="switch-1" checked="">
                    <label for="switch-1"></label>
                </div>
                <label>Checked</label>
            </div>
            <div class="form-group d-flex align-items-center">
                <div class="switch m-r-10">
                    <input type="checkbox" id="switch-2">
                    <label for="switch-2"></label>
                </div>
                <label>Uncheck</label>
            </div>
            <div class="form-group d-flex align-items-center">
                <div class="switch m-r-10">
                    <input type="checkbox" id="switch-3" disabled="">
                    <label for="switch-3"></label>
                </div>
                <label>Disabled</label>
            </div>
            <div class="form-group d-flex align-items-center">
                <div class="switch d-inline m-r-10">
                    <input type="checkbox" id="switch-4" disabled="" checked="">
                    <label for="switch-4"></label>
                </div>
                <label>Disabled Checked</label>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>Select2</h4>
        <p>Select2为您提供了一个可定制的选择框，支持搜索、标记、远程数据集、无限滚动和许多其他高度使用的选项。有关更多使用信息，请参阅 <a href="https://select2.org/" target="_blank">Select2</a></p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-4">
                    <!-- Single select boxes -->
                    <div class="m-b-15">
                        <select class="select2" name="state">
                            <option value="AP">Apples</option>
                            <option value="NL">Nails</option>
                            <option value="BN">Bananas</option>
                            <option value="HL">Helicopters</option>
                        </select>
                    </div>
                    <!-- Multiple select boxes -->
                    <div class="m-b-15">
                        <select class="select2" name="states[]" multiple="multiple">
                            <option value="AP">Apples</option>
                            <option value="NL">Nails</option>
                            <option value="BN">Bananas</option>
                            <option value="HL">Helicopters</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page css -->
&lt;link href="assets/vendors/select2/select2.css" rel="stylesheet">

&lt;!-- page js -->
&lt;script src="assets/vendors/select2/select2.min.js">&lt;/script></code></pre>
        </div>
        <div class="code-example">
            <pre><code class="language-js"><script type="text/plain">$('.select2').select2();</script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>文件浏览器</h4>
        <p>这个文件选择器是比较糙的，如果你想选择文件的附加功能和获取文件名，需要额外的编写JavaScript。
		</p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-7">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>Bootstrap日期选择插件</h4>
        <p>Bootstrap提供了一个灵活的日期选择插件。有关更多使用信息，请参阅 <a href="https://bootstrap-datepicker.readthedocs.io/en/stable/" target="_blank">bootstrap-datepicker</a></p>
        <div class="m-t-25">
            <div class="row">
                <div class="col-md-4">
                    <!-- 首页 日期picker-->
                    <div class="form-group">
                        <label>默认日期选择插件</label>
                        <div class="input-affix m-b-10">
                            <i class="prefix-icon anticon anticon-calendar"></i>
                            <input type="text" class="form-control datepicker-input" placeholder="Pick a date">
                        </div>
                    </div>

                    <!-- 日期范围选择-->
                    <div class="form-group">
                        <label>日期范围选择</label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control datepicker-input" name="start" placeholder="From">
                            <span class="p-h-10">to</span>
                            <input type="text" class="form-control datepicker-input" name="end" placeholder="To">
                        </div>
                    </div>
                </div>
                <div class="col-md-8">

                    <!-- 日历-->
                    <label>日历</label>
                    <div data-provide="datepicker-inline"></div>
                </div>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page css -->
&lt;link href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet">

&lt;!-- page js -->
&lt;script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js">&lt;/script></code></pre>
        </div>
        <div class="code-example">
            <pre><code class="language-js"><script type="text/plain">$('.datepicker-input').datepicker();</script></code></pre>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>Quill 编辑器</h4>
        <p>Quill是一个免费的、开源的富文本编辑器，它是为现代web而构建的。凭借其模块化的架构和富有表现力的API，它可以完全定制以满足任何需要。有关更多使用信息，请参阅 <a href="https://quilljs.com/docs/quickstart/" target="_blank">Quill</a></p>
        <div class="m-t-25">
            <div id="editor">
                <p>Hello World!</p>
                <p>Some initial <strong>bold</strong> text</p>
                <p><br></p>
            </div>
        </div>
        <div class="code-example">
            <pre><code class="language-markup">&lt;!-- page js -->
&lt;script src="assets/vendors/quill/quill.min.js">&lt;/script></code></pre>
        </div>
        <div class="code-example">
            <pre><code class="language-js"><script type="text/plain">new Quill('#editor', {
theme: 'snow'
});</script></code></pre>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/select2/select2.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/quill/quill.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/form-elements.js"></script>
<!-- /gc:html -->