
<?php
/**
 * Title: 排版 Typography
 * Slug: gcoa/typography
 * Categories: ui-elements
 * Keywords: 排版 Typography
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">排版 Typography</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">UI元素</a>
            <span class="breadcrumb-item active">排版 Typography</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">标题</h4>
    </div>
    <div class="card-body">
        <p>所有 HTML <code>&lt;h1&gt;</code> 到 <code>&lt;h6&gt;</code>, 都可以使用.</p>
        <div class="row">
            <div class="col-md-4">
                <div class="m-t-20">
                    <h1>h1. 标题 1</h1>
                    <h2>h2. 标题 2</h2>
                    <h3>h3. 标题 3</h3>
                    <h4>h4. 标题 4</h4>
                    <h5>h5. 标题 5</h5>
                    <h6>h6. 标题 6</h6>
                </div>
            </div>
            <div class="col-md-4">
                <div class="m-t-20">
                    <h1 class="h1 font-weight-light">h1. 标题 1</h1>
                    <h2 class="h2 font-weight-light">h2. 标题 2</h2>
                    <h3 class="h3 font-weight-light">h3. 标题 3</h3>
                    <h4 class="h4 font-weight-light">h4. 标题 4</h4>
                    <h5 class="h5 font-weight-light">h5. 标题 5</h5>
                    <h6 class="h6 font-weight-light">h6. 标题 6</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">自定义标题</h4>
    </div>
    <div class="card-body">
        <p>通过使用<code>small</code>元素使关键内容突出显示.</p>
        <h3>
            <span>主标题</span>
            <small class="text-muted">这里是副标题</small>
        </h3>
    </div>
</div>        
<div class="card">
    <div class="card-header">
        <h4 class="card-title">突出标题</h4>
    </div>
    <div class="card-body">
        <p>传统的标题元素被设计成最适合页面内容的部分。当你需要一个<strong>突出的标题</strong>，考虑使用一个更大的标题，稍微有点夸张的标题风格。 </p>
        <div>
            <span class="display-1">Display 1</span>
        </div>
        <div>
            <span class="display-2">Display 2</span>
        </div>
        <div>
            <span class="display-3">Display 3</span>
        </div>
        <div>
            <span class="display-4">Display 4</span>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">段落</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-5">
                <h5>段落文本</h5>
                <p>The ship is almost finished. Two or Three more things and we're in great shape. The sooner the better. Something's wrong here. No one has seen or knows anything about Threepio. He's been gone too long to have gotten lost. Relax</p>
                <p> I'll talk to Lando and see what I can find out. I don't trust Lando. Well, I don't trust him, either. But he is my friend. Besides, we'll soon be gone. And then you're as good as gone, aren't you?</p>
                <p>Put Captain Solo in the cargo hold. Artoo! Artoo! Where have you been? Turn around, you wooly...! Hurry, hurry!</p>
            </div>
            <div class="col-md-5 mr-auto ml-auto">
                <h5>突出段落</h5>
                <p class="mrg-btm-40">使用class样式 <code>.lead</code> 使段落更突出</p>
                <p class="lead">Excuse me, sir. Might I inqu-... Yes, sir? Do you know where Commander Skywalker is?</p>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">内联文本元素</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="m-b-30">
                    <h5>强调</h5>
                    <p>Oh, I told you it was<mark>dangerous</mark> here.</p>
                </div>
                <div class="m-b-30">
                    <h5>下划线</h5>
                    <p><u>Oh, I told you it was dangerous here.</u></p>
                </div>
                <div class="m-b-30">
                    <h5>横杠</h5>
                    <p><del>Oh, I told you it was dangerous here.</del></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="m-b-30">
                    <h5>小号字体</h5>
                    <p><small>Oh, I told you it was dangerous here.</small></p>
                </div>
                <div class="m-b-30">
                    <h5>英文字小写</h5>
                    <p class="text-lowercase">Oh, I told you it was dangerous here.</p>
                </div>
                <div class="m-b-30">
                    <h5>英文字大写</h5>
                    <p class="text-uppercase">Oh, I told you it was dangerous here.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="m-b-30">
                    <h5>首字母大写</h5>
                    <p class="text-capitalize">Oh, I told you it was dangerous here.</p>
                </div>
                <div class="m-b-30">
                    <h5>粗体</h5>
                    <p>Oh, I told you it was <strong>dangerous</strong> here.</p>
                </div>
                <div class="m-b-30">
                    <h5>斜体</h5>
                    <p>Oh, I told you it was <i>dangerous</i> here.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">文本实用样式</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <h5 class="m-b-20">文本颜色</h5>
                <p class="text-gray">Text Gray</p>
                <p class="text-body-color">Text Body Color</p>
                <p class="text-dark">Text Dark</p>
                <p class="text-primary">Text Primary</p>
                <p class="text-secondary">Text Secondary</p>
                <p class="text-success">Text Success</p>
                <p class="text-info">Text Info</p>
                <p class="text-warning">Text Warning</p>
                <p class="text-danger">Text Danger</p>
                <p class="text-white bg-dark d-inline-block">Text White</p>
            </div>
            <div class="col-md-3">
                <h5 class="m-b-20">字体重量</h5>
                <p class="font-weight-light">字体重量 Light</p>
                <p class="font-weight-normal">字体重量 Normal</p>
                <p class="font-weight-semibold">字体重量 Semibold</p>
                <p class="font-weight-bold">字体重量 Bold</p>
            </div>
            <div class="col-md-3">
                <h5 class="m-b-20">文本英文转换</h5>
                <p class="text-lowercase">Text Lowercase</p>
                <p class="text-uppercase">Text Uppercase</p>
                <p class="text-capitalize">Text Capitalize</p>
            </div>
            <div class="col-md-3">
                <h5 class="m-b-20">文本的封装与溢出</h5>
                <div class="badge badge-primary text-wrap" style="width: 6rem;">
                    This text should wrap.
                </div>
                <div class="text-nowrap border m-t-20" style="width: 8rem;">
                    This text should overflow the parent.
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">文本对齐</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="m-b-20">文本对齐</h5>
                <p class="text-left p-10 border">文本靠左</p>
                <p class="text-center p-10 border">文本居中</p>
                <p class="text-right p-10 border">文本靠右</p>
                <div class="p-h-10 p-v-15 border">
                    <p class="text-justify m-b-0">文本平铺-两边对齐: The ship is almost finished. Two or Three more things and we're in great shape. The sooner the better. Something's wrong here. No one has seen or knows anything about Threepio. He's been gone too long to have gotten lost. Relax</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->