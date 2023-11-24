
<?php
/**
 * Title: 表单布局
 * Slug: gcoa/form-layouts
 * Categories: forms
 * Keywords: 表单布局
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">表单布局</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">表单</a>
            <span class="breadcrumb-item active">表单布局</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>表单组</h4>
        <p>使用 <code>.form-group</code> 类向表单添加某些结构的最简单方法。它提供了一个灵活的类，鼓励对标签、控件、可选帮助文本和表单验证消息进行正确分组。默认情况下，它只应用<code>margin-bottom</code>，但它根据需要在<code>.form-inline</code>中获取其他样式。它与<code>&lt;fieldset&gt;</code>、<code>&lt;div&gt;</code>或几乎任何其他元素一起使用。</p>
        <div class="m-t-25" style="max-width: 500px">
            <form>
                <div class="form-group">
                    <label for="formGroupExampleInput">Example label</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Example input">
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput2">Another label</label>
                    <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Another input">
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>表单网格布局</h4>
        <p>使用我们的网格类可以构建更复杂的表单。对于需要多个列、不同宽度和其他对齐选项的表单布局，请使用这些选项。</p>
        <div class="m-t-25" style="max-width: 500px">
            <form>
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="First name">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Last name">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>表单行</h4>
        <p>您还可以将<code>.row</code>替换为<code>.form-row</code>,，这是我们标准网格行的变体，它覆盖了默认的插槽，以获得更紧凑的布局。</p>
        <div class="m-t-25" style="max-width: 500px">
            <form>
                <div class="form-row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="First name">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Last name">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>复杂示例</h4>
        <p>也可以使用网格系统创建更复杂的布局。</p>
        <div class="m-t-25" style="max-width: 700px">
            <form>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Email</label>
                        <input type="email" class="form-control" id="inputEmail4" placeholder="电子邮件">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">密码</label>
                        <input type="password" class="form-control" id="inputPassword4" placeholder="密码">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputAddress">Address</label>
                    <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                </div>
                <div class="form-group">
                    <label for="inputAddress2">Address 2</label>
                    <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputCity">City</label>
                        <input type="text" class="form-control" id="inputCity">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputState">State</label>
                        <select id="inputState" class="form-control">
                            <option selected>Choose...</option>
                            <option>...</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputZip">Zip</label>
                        <input type="text" class="form-control" id="inputZip">
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <input id="gridCheck" type="checkbox">
                        <label for="gridCheck">Check me out</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Sign in</button>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>表单横向对齐</h4>
        <p>通过添加<code>.row</code>类来成组，并使用<code>.col-*-*</code>类来指定标签和控件的宽度。一定要将 <code>.col-form-label</code>添加到<code>&lt;label&gt;</code>中，使它们与相关联的表单控件垂直居中。</p>
        <p>有时，您可能需要使用margin或padding属性来创建所需的完美对齐。例如：我们删除了单选框的 <code>padding-top</code>以更好地对齐文本基线。</p>
        <div class="m-t-25" style="max-width: 700px">
            <form>
                <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="电子邮件">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">密码</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="inputPassword3" placeholder="密码">
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <label class="col-form-label col-sm-2 pt-0">Radios</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <input type="radio" name="gridRadios" id="gridRadios1" value="option1" checked>
                                <label for="gridRadios1">
                                    First radio
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="gridRadios" id="gridRadios2" value="option2">
                                <label for="gridRadios2">
                                    Second radio
                                </label>
                            </div>
                            <div class="radio">
                                <input type="radio" name="gridRadios" id="gridRadios3" value="option3" disabled>
                                <label for="gridRadios3">
                                    Third disabled radio
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <div class="col-sm-2">Checkbox</div>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <input type="checkbox" id="gridCheck1">
                            <label for="gridCheck1">
                            Example checkbox
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">Sign in</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>列宽</h4>
        <p>如前面的示例所示，我们的网格系统允许您将任意数量的<code>.col</code>放入<code>.row</code>或<code>.form-row</code>。它们将在它们之间平均分配可用宽度。您也可以选择列的一个子集来占用更多或更少的空间，而剩余的<code>.col</code>则使用特定的列类（如<code>.col-7</code>）平均分割其余部分。</p>
        <div class="m-t-25" style="max-width: 700px">
            <form>
                <div class="form-row">
                    <div class="col-7">
                        <input type="text" class="form-control" placeholder="City">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="State">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Zip">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>自动列宽</h4>
        <p>下面的示例使用flexbox公共样式将内容垂直居中，并将 <code class="highlighter-rouge">.col</code>更改为 <code class="highlighter-rouge">.col-auto</code>，这样列只占用所需的空间。换句话说，列的大小取决于内容。</p>
        <div class="m-t-25">
            <form>
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="sr-only" for="inlineFormInput">Name</label>
                        <input type="text" class="form-control mb-2" id="inlineFormInput" placeholder="Jane Doe">
                    </div>
                    <div class="col-auto">
                        <label class="sr-only" for="inlineFormInputGroup">用户名</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">@</div>
                            </div>
                            <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="用户名">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="checkbox mb-2">
                            <input class="form-check-input" type="checkbox" id="autoSizingCheck">
                            <label class="form-check-label" for="autoSizingCheck">
                                Remember me
                            </label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /gc:html -->