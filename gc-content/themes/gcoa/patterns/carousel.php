
<?php
/**
 * Title: 轮播图 Carousel
 * Slug: gcoa/carousel
 * Categories: components
 * Keywords: 轮播图 Carousel
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">轮播图</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">轮播图</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>幻灯片轮播</h4>
        <p>这是一个只有幻灯片的轮播图效果. 幻灯片使用 <code>.d-block</code> 和 <code>.w-100</code> 用以防止浏览器默认对齐.</p>
        <div class="m-t-25 m-h-auto" style="max-width: 700px">
            <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-12.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-14.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-13.jpg" class="d-block w-100" alt="...">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用控制器</h4>
        <p>添加上一个和下一个的控制器:</p>
        <div class="m-t-25 m-h-auto" style="max-width: 700px">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-12.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-14.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-13.jpg" class="d-block w-100" alt="...">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleControls" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="sr-only">上一页</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleControls" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="sr-only">下一页</span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用指示灯</h4>
        <p>将指示灯添加到轮播图中.</p>
        <div class="m-t-25 m-h-auto" style="max-width: 700px">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-12.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-13.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-14.jpg" class="d-block w-100" alt="...">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="sr-only">上一页</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="sr-only">下一页</span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>使用字幕</h4>
        <p>在<code>.carousel-item</code>中使用<code>.carousel-caption</code>使元素可以添加到幻灯片中。 用<code>.d-none</code>隐藏字幕，用<code>.d-md-block</code>显示字幕。</p>
        <div class="m-t-25 m-h-auto" style="max-width: 700px">
            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-12.jpg" class="d-block w-100" alt="...">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>First slide label</h5>
                            <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-14.jpg" class="d-block w-100" alt="...">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Second slide label</h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-13.jpg" class="d-block w-100" alt="...">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Third slide label</h5>
                            <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleCaptions" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="sr-only">上一页</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleCaptions" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="sr-only">下一页</span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>淡入淡出效果</h4>
        <p>添加 <code>.carousel-fade</code> 到幻灯片中，翻页时可出现淡入淡出效果.</p>
        <div class="m-t-25 m-h-auto" style="max-width: 700px">
            <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-12.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-14.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/img-13.jpg" class="d-block w-100" alt="...">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleFade" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="sr-only">上一页</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleFade" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="sr-only">下一页</span>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->