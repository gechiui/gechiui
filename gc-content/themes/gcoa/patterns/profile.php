
<?php
/**
 * Title: 个人资料
 * Slug: gcoa/profile
 * Categories: pages
 * Keywords: 个人资料 Profile
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">个人资料</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">页面</a>
            <span class="breadcrumb-item active">个人资料</span>
        </nav>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-md-flex align-items-center">
                        <div class="text-center text-sm-left ">
                            <div class="avatar avatar-image" style="width: 150px; height:150px">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                            </div>
                        </div>
                        <div class="text-center text-sm-left m-v-15 p-l-30">
                            <h2 class="m-b-5">阿七</h2>
                            <p class="text-opacity font-size-13">@Marshallnich</p>
                            <p class="text-dark m-b-20">前端开发工程师, UI/UX 设计师</p>
                            <button class="btn btn-primary btn-tone">发消息</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="d-md-block d-none border-left col-1"></div>
                        <div class="col">
                            <ul class="list-unstyled m-t-10">
                                <li class="row">
                                    <p class="col-sm-4 col-4 font-weight-semibold text-dark m-b-5">
                                        <i class="m-r-10 text-primary anticon anticon-mail"></i>
                                        <span>邮件: </span> 
                                    </p>
                                    <p class="col font-weight-semibold"> Marshall123@gmail.com</p>
                                </li>
                                <li class="row">
                                    <p class="col-sm-4 col-4 font-weight-semibold text-dark m-b-5">
                                        <i class="m-r-10 text-primary anticon anticon-phone"></i>
                                        <span>电话: </span> 
                                    </p>
                                    <p class="col font-weight-semibold"> +12-123-1234</p>
                                </li>
                                <li class="row">
                                    <p class="col-sm-4 col-5 font-weight-semibold text-dark m-b-5">
                                        <i class="m-r-10 text-primary anticon anticon-compass"></i>
                                        <span>地址: </span> 
                                    </p>
                                    <p class="col font-weight-semibold"> Los Angeles, CA</p>
                                </li>
                            </ul>
                            <div class="d-flex font-size-22 m-t-15">
                                <a href="" class="text-gray p-r-20">
                                    <i class="anticon anticon-facebook"></i>
                                </a>        
                                <a href="" class="text-gray p-r-20">    
                                    <i class="anticon anticon-twitter"></i>
                                </a>
                                <a href="" class="text-gray p-r-20">
                                    <i class="anticon anticon-behance"></i>
                                </a> 
                                <a href="" class="text-gray p-r-20">   
                                    <i class="anticon anticon-dribbble"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5>灰客</h5>
                    <p>长期以来，读者在浏览页面布局时会被页面的可读内容分散注意力。使用Lorem Ipsum的意义在于，它或多或少具有字母的正态分布，而不是使用“这里有内容，这里有内容”。</p>
                    <hr>
                    <h5>技能</h5>
                    <h5 class="m-t-20">
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">Sketch</span>
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">Marvel</span>
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">Photoshop</span>
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">Illustrator</span>
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">Web Design</span>
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">Mobile App Design</span>
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">User Interface</span>
                        <span class="badge badge-pill badge-default font-weight-normal m-r-10 m-b-10">User Experience</span>
                    </h5>
                    <hr>
                    <h5>经验</h5>
                    <div class="m-t-20">
                        <div class="media m-b-30">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/adobe-thumb.png" alt="">
                            </div>
                            <div class="media-body m-l-20">
                                <h6 class="m-b-0">UI/UX 设计师, Adobe Inc.</h6>
                                <span class="font-size-13 text-gray">2018年6月</span>
                            </div>
                        </div>
                        <div class="media m-b-30">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/amazon-thumb.png" alt="">
                            </div>
                            <div class="media-body m-l-20">
                                <h6 class="m-b-0">产品开发, Amazon.com Inc.</h6>
                                <span class="font-size-13 text-gray">2017年6月 - 2018年6月</span>
                            </div>
                        </div>
                        <div class="media m-b-30">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/nvidia-thumb.png" alt="">
                            </div>
                            <div class="media-body m-l-20">
                                <h6 class="m-b-0">接口开发, Nvidia Corporation</h6>
                                <span class="font-size-13 text-gray">Jul-2016 - Jul 2017</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>教育</h5>
                    <div class="m-t-20">
                        <div class="media m-b-30">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/cambridge-thumb.png" alt="">
                            </div>
                            <div class="media-body m-l-20">
                                <h6 class="m-b-0">剑桥大学社会创新硕士</h6>
                                <span class="font-size-13 text-gray">2012年6月 -2016年6月</span>
                            </div>
                        </div>
                        <div class="media m-b-30">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/phillips-academy-thumb.png" alt="">
                            </div>
                            <div class="media-body m-l-20">
                                <h6 class="m-b-0">Phillips Academy</h6>
                                <span class="font-size-13 text-gray">2005年6月 - 2011年6月</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5>评价 (18)</h5>
                    <div class="m-t-20">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item p-h-0">
                                <div class="media m-b-15">
                                    <div class="avatar avatar-image">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                    </div>
                                    <div class="media-body m-l-20">
                                        <h6 class="m-b-0">
                                            <a href="" class="text-dark">有奶便是娘</a>
                                        </h6>
                                        <span class="font-size-13 text-gray">2020-11-12</span>
                                    </div>
                                </div>
                                <span>爱上你不是我的错，都是你胸大腰细惹的祸。</span>
                                <div class="star-rating m-t-15">
                                    <input type="radio" id="star1-5" name="rating-1" value="5" checked disabled/><label for="star1-5" title="5 star"></label>
                                    <input type="radio" id="star1-4" name="rating-1" value="4" disabled/><label for="star1-4" title="4 star"></label>
                                    <input type="radio" id="star1-3" name="rating-1" value="3" disabled/><label for="star1-3" title="3 star"></label>
                                    <input type="radio" id="star1-2" name="rating-1" value="2" disabled/><label for="star1-2" title="2 star"></label>
                                    <input type="radio" id="star1-1" name="rating-1" value="1" disabled/><label for="star1-1" title="1 star"></label>
                                </div>
                            </li>
                            <li class="list-group-item p-h-0">
                                <div class="media m-b-15">
                                    <div class="avatar avatar-image">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-9.jpg" alt="">
                                    </div>
                                    <div class="media-body m-l-20">
                                        <h6 class="m-b-0">
                                            <a href="" class="text-dark">大米</a>
                                        </h6>
                                        <span class="font-size-13 text-gray">2020-11-12</span>
                                    </div>
                                </div>
                                <span>爱上你不是我的错，都是你胸大腰细惹的祸。</span>
                                <div class="star-rating m-t-15">
                                    <input type="radio" id="star2-5" name="rating-2" value="5" disabled/><label for="star2-5" title="5 star"></label>
                                    <input type="radio" id="star2-4" name="rating-2" value="4" checked disabled/><label for="star2-4" title="4 star"></label>
                                    <input type="radio" id="star2-3" name="rating-2" value="3" disabled/><label for="star2-3" title="3 star"></label>
                                    <input type="radio" id="star2-2" name="rating-2" value="2" disabled/><label for="star2-2" title="2 star"></label>
                                    <input type="radio" id="star2-1" name="rating-2" value="1" disabled/><label for="star2-1" title="1 star"></label>
                                </div>
                            </li>
                            <li class="list-group-item p-h-0">
                                <div class="media m-b-15">
                                    <div class="avatar avatar-image">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                    </div>
                                    <div class="media-body m-l-20">
                                        <h6 class="m-b-0">
                                            <a href="" class="text-dark">已成灰</a>
                                        </h6>
                                        <span class="font-size-13 text-gray">2020-11-12</span>
                                    </div>
                                </div>
                                <span>爱上你不是我的错，都是你胸大腰细惹的祸。</span>
                                <div class="star-rating m-t-15">
                                    <input type="radio" id="star3-5" name="rating-3" value="5" checked disabled/><label for="star3-5" title="5 star"></label>
                                    <input type="radio" id="star3-4" name="rating-3" value="4" disabled/><label for="star3-4" title="4 star"></label>
                                    <input type="radio" id="star3-3" name="rating-3" value="3" disabled/><label for="star3-3" title="3 star"></label>
                                    <input type="radio" id="star3-2" name="rating-3" value="2" disabled/><label for="star3-2" title="2 star"></label>
                                    <input type="radio" id="star3-1" name="rating-3" value="1" disabled/><label for="star3-1" title="1 star"></label>
                                </div>
                            </li>
                        </ul> 
                    </div>  
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>相关好友</h5>
                    <div class="m-t-30">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                            </div>
                            <div class="m-l-10">
                                <h5 class="m-b-0">
                                    <a href="" class="text-dark">安子轩</a>
                                </h5>
                                <span class="font-size-13 text-gray">UI/UX 设计师</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-30">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                            </div>
                            <div class="m-l-10">
                                <h5 class="m-b-0">
                                    <a href="" class="text-dark">达里尔</a>
                                </h5>
                                <span class="font-size-13 text-gray">软件工程师</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-30">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                            </div>
                            <div class="m-l-10">
                                <h5 class="m-b-0">
                                    <a href="" class="text-dark">阿七</a>
                                </h5>
                                <span class="font-size-13 text-gray">商品 Manager</span>
                            </div>
                        </div>
                    </div>
                    <div class="m-t-30">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-image">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                            </div>
                            <div class="m-l-10">
                                <h5 class="m-b-0">
                                    <a href="" class="text-dark">风一样的男人</a>
                                </h5>
                                <span class="font-size-13 text-gray">数据分析师</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5>项目</h5>
                    <div class="m-t-20">
                        <div class="m-b-20 p-b-20 border-bottom">
                            <div class="media align-items-center m-b-15">
                                <div class="avatar avatar-image">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/coffee-app-thumb.jpg" alt="">
                                </div>
                                <div class="media-body m-l-20">
                                    <h5 class="m-b-0">
                                        <a href="" class="text-dark">Coffee Finder App</a>
                                    </h5>
                                </div>
                            </div>
                            <p>It is a long established fact that a reader will be distracted by the readable content.</p>
                            <div class="d-inline-block">
                                <a class="m-r-5" data-toggle="tooltip" title="Eugene Jordan" href="">
                                    <div class="avatar avatar-image avatar-sm">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                                    </div>
                                </a>
                                <a class="m-r-5" data-toggle="tooltip" title="Pamela" href="">
                                    <div class="avatar avatar-image avatar-sm">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                    </div>
                                </a>
                            </div>
                            <div class="float-right">
                                <span class="badge badge-pill badge-blue font-size-12 p-h-10">进行中</span>
                            </div>
                        </div>
                        <div class="m-b-20 p-b-20 border-bottom">
                            <div class="media align-items-center m-b-15">
                                <div class="avatar avatar-image">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/weather-app-thumb.jpg" alt="">
                                </div>
                                <div class="media-body m-l-20">
                                    <h5 class="m-b-0">
                                        <a href="" class="text-dark">Weather App</a>
                                    </h5>
                                </div>
                            </div>
                            <p>It is a long established fact that a reader will be distracted by the readable content.</p>
                            <div class="d-inline-block">
                                <a class="m-r-5" data-toggle="tooltip" title="有奶便是娘" href="">
                                    <div class="avatar avatar-image avatar-sm">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                    </div>
                                </a>
                                <a class="m-r-5" data-toggle="tooltip" title="大米" href="">
                                    <div class="avatar avatar-image avatar-sm">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-9.jpg" alt="">
                                    </div>
                                </a>
                                <a class="m-r-5" data-toggle="tooltip" title="已成灰" href="">
                                    <div class="avatar avatar-image avatar-sm">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                    </div>
                                </a>
                            </div>
                            <div class="float-right">
                                <span class="badge badge-pill badge-cyan font-size-12 p-h-10">Completed</span>
                            </div>
                        </div>
                        <div class="m-b-20 p-b-20 border-bottom">
                            <div class="media align-items-center m-b-15">
                                <div class="avatar avatar-image">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/music-app-thumb.jpg" alt="">
                                </div>
                                <div class="media-body m-l-20">
                                    <h5 class="m-b-0">
                                        <a href="" class="text-dark">Music App</a>
                                    </h5>
                                </div>
                            </div>
                            <p>Protein, iron, and calcium are some of the nutritional benefits associated with cheeseburgers.</p>
                            <div class="d-inline-block">
                                <a class="m-r-5" data-toggle="tooltip" title="达里尔" href="">
                                    <div class="avatar avatar-image avatar-sm">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                    </div>
                                </a>
                                <a class="m-r-5" data-toggle="tooltip" title="卖女孩的小火柴" href="">
                                    <div class="avatar avatar-image avatar-sm">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                    </div>
                                </a>
                            </div>
                            <div class="float-right">
                                <span class="badge badge-pill badge-cyan font-size-12 p-h-10">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->