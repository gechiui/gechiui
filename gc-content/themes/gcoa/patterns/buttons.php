
<?php
/**
 * Title: 按钮 Buttons
 * Slug: gcoa/buttons
 * Categories: ui-elements
 * Keywords: 按钮 Buttons
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">按钮</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">UI元素</a>
            <span class="breadcrumb-item active">按钮</span>
        </nav>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">按钮</h4>
            </div>
            <div class="card-body">
                <p>Bootstrap包括几种预定义的按钮样式，每种样式都有自己的语义目的，还有一些附加功能用于更多控制。</p>
                <div class="m-t-25">
                    <button class="btn btn-default m-r-5">默认</button>
                    <button class="btn btn-primary m-r-5">主要</button>
                    <button class="btn btn-secondary m-r-5">次要</button>
                    <button class="btn btn-success m-r-5">成功</button>
                    <button class="btn btn-info m-r-5">信息</button>
                    <button class="btn btn-warning m-r-5">警告</button>
                    <button class="btn btn-danger m-r-5">危险</button>
                </div>
            </div>
        </div>   
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">彩色按钮</h4>
            </div>
            <div class="card-body">
                <p>使用 <code>.btn-tone</code> 制作彩色风格</p>
                <div class="m-t-25">
                    <button class="btn btn-primary btn-tone m-r-5">主要</button>
                    <button class="btn btn-secondary btn-tone m-r-5">次要</button>
                    <button class="btn btn-success btn-tone m-r-5">成功</button>
                    <button class="btn btn-info btn-tone m-r-5">信息</button>
                    <button class="btn btn-warning btn-tone m-r-5">警告</button>
                    <button class="btn btn-danger btn-tone m-r-5">危险</button>
                </div>
            </div>
        </div>  
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">标签按钮</h4>
            </div>
            <div class="card-body">
                <p>calss样式 <code>.btn</code> 与 <code>&lt;button&gt;</code> 元素一起使用. 也可以用于<code>&lt;a&gt;</code> or <code>&lt;input&gt;</code> 元素 (不同的浏览渲染效果不同).</p>
                <p>在 <code>&lt;a&gt;</code> 元素上使用按钮样式时，触发页面动作脚本 (比如折叠内容), 给这个链接加一个 <code>role="button"</code> 可以兼容老版本浏览器.</p>
                <div class="m-t-25">
                    <a class="btn btn-primary" href="#" role="button">链接</a>
                    <button class="btn btn-primary" type="submit">按钮</button>
                    <input class="btn btn-primary" type="button" value="输入">
                    <input class="btn btn-primary" type="submit" value="提交">
                    <input class="btn btn-primary" type="reset" value="还原">
                </div>
            </div>
        </div>  
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">按钮尺码</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p>applies <code>.btn-lg</code>, <code>.btn-sm</code> and <code>.btn-xs</code> to resize the button</p>
                        <div class="m-t-25">
                            <div class="m-b-10">
                                <button class="btn btn-default btn-lg m-r-5">btn-lg</button>
                                <button class="btn btn-primary btn-lg m-r-5">btn-lg</button>
                            </div>
                            <div class="m-b-10">
                                <button class="btn btn-default m-r-5">btn-md</button>
                                <button class="btn btn-primary m-r-5">btn-md</button>
                            </div>
                            <div class="m-b-10">
                                <button class="btn btn-default btn-sm m-r-5">btn-sm</button>
                                <button class="btn btn-primary btn-sm m-r-5">btn-sm</button>
                            </div>
                            <div class="m-b-10">
                                <button class="btn btn-default btn-xs m-r-5">btn-xs</button>
                                <button class="btn btn-primary btn-xs m-r-5">btn-xs</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>applies <code>.btn-block</code> to make button full width</p>
                        <div class="m-t-25">
                            <button class="btn btn-default btn-block">btn-block</button>
                        </div>
                        <div class="m-t-25">
                            <button class="btn btn-primary btn-block">btn-block</button>
                        </div>    
                    </div>
                </div> 
            </div>
        </div>    
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">按钮组</h4>
            </div>
            <div class="card-body">
                <p>在 <code>.btn-group</code>中用<code>.btn</code>包装一系列按钮.</p>
                <div class="m-t-25">
                    <div class="m-b-10">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default">
                                <span>左边</span>
                            </button>
                            <button type="button" class="btn btn-default">
                                <span>中间</span>
                            </button>
                            <button type="button" class="btn btn-default">
                                <span>右边</span>
                            </button>
                        </div>
                    </div>
                    <div class="m-b-10">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default">
                                <i class="anticon anticon-android font-size-15"></i>
                            </button>
                            <button type="button" class="btn btn-default">
                                <i class="anticon anticon-apple font-size-15"></i>
                            </button>
                            <button type="button" class="btn btn-default">
                                <i class="anticon anticon-windows font-size-15"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>      
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">图标按钮</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p>使用 <code>btn-icon</code> 创建一个图标按钮.</p>
                        <div class="m-t-25">
                            <button class="btn btn-icon btn-default">
                                <i class="anticon anticon-facebook"></i>
                            </button>
                            <button class="btn btn-icon btn-primary">
                                <i class="anticon anticon-google"></i>
                            </button>
                            <button class="btn btn-icon btn-secondary">
                                <i class="anticon anticon-dribbble"></i>
                            </button>
                            <button class="btn btn-icon btn-success">
                                <i class="anticon anticon-twitter"></i>
                            </button>
                            <button class="btn btn-icon btn-info">
                                <i class="anticon anticon-instagram"></i>
                            </button>
                            <button class="btn btn-icon btn-warning">
                                <i class="anticon anticon-youtube"></i>
                            </button>
                            <button class="btn btn-icon btn-danger">
                                <i class="anticon anticon-skype"></i>
                            </button>
                        </div>
                        <div class="m-t-25">
                            <p>使用 <code>btn-tone</code> 创建双色调样式.</p>
                            <div class="m-t-25">
                                <button class="btn btn-icon btn-default btn-tone">
                                    <i class="anticon anticon-facebook"></i>
                                </button>
                                <button class="btn btn-icon btn-primary btn-tone">
                                    <i class="anticon anticon-google"></i>
                                </button>
                                <button class="btn btn-icon btn-secondary btn-tone">
                                    <i class="anticon anticon-dribbble"></i>
                                </button>
                                <button class="btn btn-icon btn-success btn-tone">
                                    <i class="anticon anticon-twitter"></i>
                                </button>
                                <button class="btn btn-icon btn-info btn-tone">
                                    <i class="anticon anticon-instagram"></i>
                                </button>
                                <button class="btn btn-icon btn-warning btn-tone">
                                    <i class="anticon anticon-youtube"></i>
                                </button>
                                <button class="btn btn-icon btn-danger btn-tone">
                                    <i class="anticon anticon-skype"></i>
                                </button>  
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <p>使用 <code>.btn-rounded</code> 创建圆形样式</p>
                        <div class="m-t-25">
                            <button class="btn btn-icon btn-default btn-rounded">
                                <i class="anticon anticon-facebook"></i>
                            </button>
                            <button class="btn btn-icon btn-primary btn-rounded">
                                <i class="anticon anticon-google"></i>
                            </button>
                            <button class="btn btn-icon btn-secondary btn-rounded">
                                <i class="anticon anticon-dribbble"></i>
                            </button>
                            <button class="btn btn-icon btn-success btn-rounded">
                                <i class="anticon anticon-twitter"></i>
                            </button>
                            <button class="btn btn-icon btn-info btn-rounded">
                                <i class="anticon anticon-instagram"></i>
                            </button>
                            <button class="btn btn-icon btn-warning btn-rounded">
                                <i class="anticon anticon-youtube"></i>
                            </button>
                            <button class="btn btn-icon btn-danger btn-rounded">
                                <i class="anticon anticon-skype"></i>
                            </button>    
                        </div>
                        
                        <div class="m-t-25">
                            <p>使用 <code>.btn-tone</code> &amp; <code>.btn-rounded</code> 创建原型双色调样式</p>
                            <div class="m-t-25">
                                <button class="btn btn-icon btn-default btn-rounded btn-tone">
                                    <i class="anticon anticon-facebook"></i>
                                </button>
                                <button class="btn btn-icon btn-primary btn-rounded btn-tone">
                                    <i class="anticon anticon-google"></i>
                                </button>
                                <button class="btn btn-icon btn-secondary btn-rounded btn-tone">
                                    <i class="anticon anticon-dribbble"></i>
                                </button>
                                <button class="btn btn-icon btn-success btn-rounded btn-tone">
                                    <i class="anticon anticon-twitter"></i>
                                </button>
                                <button class="btn btn-icon btn-info btn-rounded btn-tone">
                                    <i class="anticon anticon-instagram"></i>
                                </button>
                                <button class="btn btn-icon btn-warning btn-rounded btn-tone">
                                    <i class="anticon anticon-youtube"></i>
                                </button>
                                <button class="btn btn-icon btn-danger btn-rounded btn-tone">
                                    <i class="anticon anticon-skype"></i>
                                </button>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">加载按钮</h4>
            </div>
            <div class="card-body">
                <p>使用<code>.is-loading</code> 创建一个加载按钮</p>
                <div class="m-t-25">
                    <div class="m-t-25">
                        <button class="btn btn-default is-loading m-r-5">
                            <i class="anticon anticon-loading m-r-5"></i>
                            <span>Loading</span>
                        </button>
                        <button class="btn btn-primary is-loading m-r-5">
                            <i class="anticon anticon-loading m-r-5"></i>
                            <span>Loading</span>
                        </button>
                        <div class="m-t-15">
                            <button class="btn btn-default btn-icon btn-rounded is-loading m-r-5">
                                <i class="anticon anticon-loading"></i>
                            </button>
                            <button class="btn btn-primary btn-icon btn-rounded is-loading m-r-5">
                                <i class="anticon anticon-loading"></i>
                            </button>
                        </div>
                        <div class="m-t-15">
                            <button id="trigger-loading-1" class="btn btn-default m-r-5">
                                <i class="anticon anticon-loading m-r-5"></i>
                                <i class="anticon anticon-poweroff m-r-5"></i>
                                <span>Click Me</span>
                            </button>
                            <button id="trigger-loading-2" class="btn btn-primary m-r-5">
                                <i class="anticon anticon-loading m-r-5"></i>
                                <span>Click Me</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->