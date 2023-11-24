<?php
/**
 * Title: 项目详情
 * Slug: gcoa/app-project-details
 * Categories: pages
 * Keywords: 项目详情
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="media align-items-center">
                            <div class="avatar avatar-image rounded">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/others/thumb-3.jpg" alt="">
                            </div>
                            <div class="m-l-10">
                                <h4 class="m-b-0">精灵社区后台UE</h4>
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-pill badge-blue">进行中</span>
                        </div>
                    </div>
                    <div class="m-t-40">
                        <h6>说明:</h6>
                        <p>Gulp.js 是一个自动化构建工具,开发者可以使用它在项目开发过程中自动执行常见任务。Gulp.js 是基于 Node.js 构建的,利用 Node.js 流的威力,你可以快速构建项目.</p>
                        <p>如何开始使用 gulp API 文档 - 学习 gulp 的输入和输出方式 CLI 文档 - 学习如何执行任务(task)以及如何使用一些编译工具 编写插件.</p>
                    </div>
                    <div class="d-md-flex m-t-30 align-items-center justify-content-between">
                        <div class="d-flex align-items-center m-t-10">
                            <span class="text-dark font-weight-semibold m-r-10 m-b-5">成员: </span>
                            <a class="m-r-5 m-b-5" href="javascript:void(0);" data-toggle="tooltip" title="安子轩">
                                <div class="avatar avatar-image" style="width: 30px; height: 30px;">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                </div>
                            </a>
                            <a class="m-r-5 m-b-5" href="javascript:void(0);" data-toggle="tooltip" title="达里尔">
                                <div class="avatar avatar-image" style="width: 30px; height: 30px;">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                </div>
                            </a>
                            <a class="m-r-5 m-b-5" href="javascript:void(0);" data-toggle="tooltip" title="阿七">
                                <div class="avatar avatar-image" style="width: 30px; height: 30px;">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                                </div>
                            </a>
                            <a class="m-r-5 m-b-5" href="javascript:void(0);" data-toggle="tooltip" title="卖女孩的小火柴">
                                <div class="avatar avatar-image" style="width: 30px; height: 30px;">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                </div>
                            </a>
                            <a class="m-r-5 m-b-5" href="javascript:void(0);" data-toggle="tooltip" title="风一样的男人">
                                <div class="avatar avatar-image" style="width: 30px; height: 30px;">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                                </div>
                            </a>
                            <a class="m-r-5 m-b-5" href="javascript:void(0);" data-toggle="tooltip" title="德祐">
                                <div class="avatar avatar-image" style="width: 30px; height: 30px;">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                </div>
                            </a>
                        </div>
                        <div class="m-t-10">
                            <span class="font-weight-semibold m-r-10 m-b-5 text-dark">日期: </span>
                            <span>2020-11-11</span>
                        </div>
                    </div>
                </div>
                <div class="m-t-30">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#project-details-tasks">任务 (8)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#project-details-comments">评论</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#project-details-attachment">附件</a>
                        </li>
                    </ul>
                    <div class="tab-content m-t-15 p-25">
                        <div class="tab-pane fade show active" id="project-details-tasks">
                            <div class="checkbox m-b-20">
                                <input id="task-1" type="checkbox">
                                <label for="task-1">公开任务（Public tasks） 从 gulpfile 中被导出（export），可以通过 gulp 命令直接调用。</label>
                            </div>
                            <div class="checkbox m-b-20">
                                <input id="task-2" type="checkbox">
                                <label for="task-2">私有任务（Private tasks） 被设计为在内部使用，通常作为 series() 或 parallel() 组合的组成部分。</label>
                            </div>
                            <div class="checkbox m-b-20">
                                <input id="task-3" type="checkbox">
                                <label for="task-3">使用WBS进行工作拆分</label>
                            </div>
                            <div class="checkbox m-b-20">
                                <input id="task-4" type="checkbox" checked="">
                                <label for="task-4">高效的媒体投放</label>
                            </div>
                            <div class="checkbox m-b-20">
                                <input id="task-5" type="checkbox" checked="">
                                <label for="task-5">表结构原型设计初稿</label>
                            </div>
                            <div class="checkbox m-b-20">
                                <input id="task-6" type="checkbox" checked="">
                                <label for="task-6">glob 是由普通字符和/或通配字符组成的字符串，用于匹配文件路径。</label>
                            </div>
                            <div class="checkbox m-b-20">
                                <input id="task-7" type="checkbox" checked="">
                                <label for="task-7">在一个字符串片段中匹配任意数量的字符，包括零个匹配。</label>
                            </div>
                            <div class="checkbox m-b-20">
                                <input id="task-8" type="checkbox" checked="">
                                <label for="task-8">在多个字符串片段中匹配任意数量的字符，包括零个匹配。 </label>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="project-details-comments">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item p-h-0">
                                    <div class="media m-b-15">
                                        <div class="avatar avatar-image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                        </div>
                                        <div class="media-body m-l-20">
                                            <h6 class="m-b-0">
                                                <a href="" class="text-dark">月老下岗</a>
                                            </h6>
                                            <span class="font-size-13 text-gray">2020-11-12</span>
                                        </div>
                                    </div>
                                    <p>gulp是一个自动化构建工具，主要用来设定程序自动处理静态资源的工作。简单的说，gulp就是用来打包项目的。</p>
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
                                    <p>前端的构建工具常见的有Grunt、Gulp、Webpack三种，Grunt比较老旧，功能少，更新少，插件少。</p>
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
                                    <p>关于如何在 gulp 中使用 glob 的知识都已经在在上面的文档中讲解了。如果你还希望获取更多进阶资料，请参考下面列出的部分.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="project-details-attachment">
                            <div class="file" style="min-width: 200px;">
                                <div class="media align-items-center">
                                    <div class="avatar avatar-icon avatar-cyan rounded m-r-15">
                                        <i class="anticon anticon-file-exclamation font-size-20"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Mockup.zip</h6>
                                        <span class="font-size-13 text-muted">7MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="file" style="min-width: 200px;">
                                <div class="media align-items-center">
                                    <div class="avatar avatar-icon avatar-blue rounded m-r-15">
                                        <i class="anticon anticon-file-word font-size-20"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Guideline.doc</h6>
                                        <span class="font-size-13 text-muted">128 KB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="file" style="min-width: 200px;">
                                <div class="media align-items-center">
                                    <div class="avatar avatar-icon avatar-gold rounded m-r-15">
                                        <i class="anticon anticon-file-image font-size-20"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Logo.png</h6>
                                        <span class="font-size-13 text-muted">128 KB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">活动</h4>
                </div>
                <div class="card-body">
                    <ul class="timeline timeline-sm">
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon avatar-sm avatar-cyan">
                                    <i class="anticon anticon-check"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <h6 class="m-b-0">卖女孩的小火柴</h6>
                                            <span class="text-muted font-size-13">
                                                <i class="anticon anticon-clock-circle"></i>
                                                <span class="m-l-5">10:44 PM</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-t-20">
                                        <p class="m-l-20">
                                            <span class="text-dark font-weight-semibold">完成任务 </span> 
                                            <span class="m-l-5"> 原型设计</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon avatar-sm avatar-blue">
                                    <i class="anticon anticon-link"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <h6 class="m-b-0">德克萨斯</h6>
                                            <span class="text-muted font-size-13">
                                                <i class="anticon anticon-clock-circle"></i>
                                                <span class="m-l-5">8:34 PM</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-t-20">
                                        <p class="m-l-20">
                                            <span class="text-dark font-weight-semibold">附件 </span> 
                                            <span class="m-l-5"> Mockup Zip</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon avatar-sm avatar-purple">
                                    <i class="anticon anticon-message"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <h6 class="m-b-0">安子轩</h6>
                                            <span class="text-muted font-size-13">
                                                <i class="anticon anticon-clock-circle"></i>
                                                <span class="m-l-5">8:34 PM</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-t-20">
                                        <p class="m-l-20">
                                            <span class="text-dark font-weight-semibold">评论  </span> 
                                            <span class="m-l-5"> '这不是我们的工作!'</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon avatar-sm avatar-purple">
                                    <i class="anticon anticon-message"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <h6 class="m-b-0">风一样的男人</h6>
                                            <span class="text-muted font-size-13">
                                                <i class="anticon anticon-clock-circle"></i>
                                                <span class="m-l-5">8:34 PM</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-t-20">
                                        <p class="m-l-20">
                                            <span class="text-dark font-weight-semibold">评论  </span> 
                                            <span class="m-l-5"> 'Hi，请在明天之前做这个'</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon avatar-sm avatar-red">
                                    <i class="anticon anticon-delete"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <h6 class="m-b-0">德祐</h6>
                                            <span class="text-muted font-size-13">
                                                <i class="anticon anticon-clock-circle"></i>
                                                <span class="m-l-5">8:34 PM</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-t-20">
                                        <p class="m-l-20">
                                            <span class="text-dark font-weight-semibold">删除</span> 
                                            <span class="m-l-5"> 一个文件</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-item-head">
                                <div class="avatar avatar-icon avatar-sm avatar-gold">
                                    <i class="anticon anticon-file-add"></i>
                                </div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="m-l-10">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-image">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                                        </div>
                                        <div class="m-l-10">
                                            <h6 class="m-b-0">阿七</h6>
                                            <span class="text-muted font-size-13">
                                                <i class="anticon anticon-clock-circle"></i>
                                                <span class="m-l-5">5:21 PM</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-t-20">
                                        <p class="m-l-20">
                                            <span class="text-dark font-weight-semibold">创建  </span> 
                                            <span class="m-l-5"> 这个项目</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->