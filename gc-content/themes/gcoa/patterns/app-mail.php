<?php
/**
 * Title: 邮箱
 * Slug: gcoa/app-mail
 * Categories: pages
 * Keywords: 邮箱
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="mail-wrapper">
    <div class="mail-nav" id="mail-nav">
        <div class="p-h-25 m-t-25">
            <div class="p-b-15 d-md-none d-inline-block">
                <a class="text-dark font-size-18 mail-close-nav" href="javascript:void(0);">
                    <i class="anticon anticon-menu-fold"></i>
                </a>
            </div>
            <button class="btn btn-primary w-100 mail-open-compose">
                <i class="anticon anticon-edit"></i>
                <span class="m-l-5">写邮件</span>
            </button>
        </div>
        <div class="p-v-15">
            <ul class="menu nav flex-column">
                <li class="nav-item">
                    <a href="" class="nav-link active">
                        <i class="anticon anticon-inbox"></i>
                        <span>收件箱</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <i class="anticon anticon-mail"></i>
                        <span>发送的邮件</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <i class="anticon anticon-file-done"></i>
                        <span>草稿</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <i class="anticon anticon-star"></i>
                        <span>收藏</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <i class="anticon anticon-delete"></i>
                        <span>回收</span>
                    </a>
                </li>
            </ul>
            <ul class="menu nav flex-column m-t-25">
                <li class="nav-item">
                    <h6 class="nav-link d-inline-block">标签</h6>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <div class="d-flex align-items-center m-r-10">
                            <span class="badge badge-success badge-dot m-r-10"></span>
                            <span>工作</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <div class="d-flex align-items-center m-r-10">
                            <span class="badge badge-danger badge-dot m-r-10"></span>
                            <span>私人</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">
                        <div class="d-flex align-items-center m-r-10">
                            <span class="badge badge-warning badge-dot m-r-10"></span>
                            <span>重要</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div id="mail-list" class="mail-content">
        <div class="p-h-10 p-v-5 d-md-none d-inline-block">
            <a class="text-dark font-size-18 mail-open-nav" href="javascript:void(0);" >
                <i class="anticon anticon-menu-unfold"></i>
            </a>
        </div>
        <div class="row d-flex align-items-center justify-content-between p-10">
            <div class="col-md-2 d-none d-md-flex align-items-baseline m-b-10">
                <div class="checkbox d-inline-block">
                    <input id="checkAll" type="checkbox">
                    <label for="checkAll"></label>
                </div>
                <select class="custom-select">
                    <option selected>筛选条件</option>
                    <option value="All">全部</option>
                    <option value="Unread">未读</option>
                    <option value="日期">日期</option>
                    <option value="Name">姓名</option>
                </select>
            </div>
            <div class="d-flex align-items-center col-md-3 m-b-10">
                <div class="input-affix m-r-10">
                    <i class="prefix-icon anticon anticon-search"></i>
                    <input type="text" class="form-control" placeholder="搜索">
                </div>
                <button class="btn btn-icon btn-default">
                    <i class="anticon anticon-reload"></i>
                </button>
            </div>
        </div>
        <div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-1" type="checkbox">
                    <label for="mail-checkbox-1"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">安子轩</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">全体人员</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray"> Hi 米格，附件是项目进度表请您过目，还请您对项目进度表进行评估，将不合理的资源安排调整一下，明天我要发给客户过目，谢谢 </span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>12:06PM</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-2" type="checkbox">
                    <label for="mail-checkbox-2"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-2.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">达里尔</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">你在做什么?</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray"> Hi 达里尔, 非常高兴的通知您，您的晋升报告已经申请通过了，请您下周一上午前往员工大厅调整您的职级</span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>5:34AM</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-3" type="checkbox">
                    <label for="mail-checkbox-3"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">阿七</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">交个我吧</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray"> 阿七! 这个开发工作交给我吧，估计10天左右就可以完成，不过有个事情需要您帮筹备一下，帮我准备格尺Ai 后台模板. </span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>2月6日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-4" type="checkbox">
                    <label for="mail-checkbox-4"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-4.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">卖女孩的小火柴</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">My Brother</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">Fredo, you're my older brother, and I love you. But don't ever take sides with anyone against the Family again.</span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>2月4日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-5" type="checkbox">
                    <label for="mail-checkbox-5"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-5.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">励志</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">Major Keys to Success</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">Major key, don’t fall for the trap, stay focused. It’s the ones closest to you that want to see you fail. </span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>2月3日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-6" type="checkbox">
                    <label for="mail-checkbox-6"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-6.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">风一样的男人</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">Take my coffee</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">Caramelization saucer robust aftertaste decaffeinated qui aged. Caramelization black white black wings, mocha americano white half and half variety latte.</span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>2月2日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-7" type="checkbox">
                    <label for="mail-checkbox-7"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-7.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">德祐</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">The cat was chasing the mouse scratch</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">Poop in the plant pot. Shove bum in owner's face like camera lens. Licks your face play time, or pooping rainbow while flying i</span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>1月29日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-8" type="checkbox">
                    <label for="mail-checkbox-8"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-8.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">Emily Shaw</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">Put A Cheeseburger</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">Epic cheeseburgers come in all kinds of manifestations, but we want them in and around our mouth no matter what. </span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>1月29日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-9" type="checkbox">
                    <label for="mail-checkbox-9"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-9.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">Shane Hawkins</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">BLUTH</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">The Man Inside Me seems well reviewed. George Bush doesn't care about black puppets. No borders, no limits… go ahead, touch the Cornballer… you know best?</span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>1月17日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-10" type="checkbox">
                    <label for="mail-checkbox-10"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-10.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">Wyatt Wallace</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">Pretty sweet, right?</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">Cotton candy topping chupa chups pudding dessert cake muffin gummi bears jelly beans. Lemon drops jelly beans powder apple pie jelly-o macaroon cake.</span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>1月16日</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mail-list">
                <div class="checkbox d-inline-block">
                    <input id="mail-checkbox-11" type="checkbox">
                    <label for="mail-checkbox-11"></label>
                </div>
                <table class="table list-info">
                    <tr>
                        <td class="list-sender">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image avatar-sm">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-11.jpg" alt="">
                                </div>
                                <h6 class="m-l-10 m-b-0">Alice Matthews</h6>
                            </div>
                        </td>
                        <td class="list-content">
                            <div class="list-msg">
                                <span class="list-title">Believe it or not?</span>
                                <span class="m-h-5 d-none d-lg-inline-block"> - </span>
                                <span class="list-text text-gray">Believe it or not I'm walking on air. I never thought I could feel so free. Flying away on a wing and a prayer.</span>
                            </div>
                        </td>
                        <td class="list-date">
                            <span>1月15日</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="m-t-20 text-right">
            <ul class="pagination justify-content-end">
                <li class="page-item"><a class="page-link" href="#">上一页</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">下一页</a></li>
            </ul>
        </div>
    </div>
    <div id="mail-content" class="mail-content d-none">
        <div class="d-lg-flex align-items-center justify-content-between">
            <div class="media align-items-center m-b-15">
                <a id="back" class="text-gray m-r-15 font-size-18" href="javascript:void(0);">
                    <i class="anticon anticon-left-circle"></i>
                </a>
                <div class="avatar avatar-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-1.jpg" alt="">
                </div>
                <div class="m-l-10">
                    <h6 class="m-b-0">安子轩</h6>
                    <span class="text-muted font-size-13">To: nathan@themenate.com</span>
                </div>
            </div>
            <div class="d-flex align-items-center m-b-15 p-l-30">
                <span class="text-gray m-r-15">12:06PM</span>
                <a class="text-gray font-size-18 m-r-20">
                    <i class="fas fa-reply"></i>
                </a>
                <a class="text-gray font-size-18 m-r-20">
                    <i class="far fa-star"></i>
                </a>
                <a class="text-gray font-size-18 m-r-20">
                    <i class="far fa-trash-alt"></i>
                </a>
            </div>
        </div>
        <div class="m-t-30 p-h-30">
            <h4>All flight trooper</h4>
            <div class="m-t-30">
                <p>Hi Erin,</p>
                <p>Somebody's coming. Oh! Luke! Where's Leia? What? She didn't come back? I thought she was with you. We got separated. Hey, we better go look for her. Take the squad ahead. We'll meet at the shield generator at 0300. Come on, Artoo. We'll need your scanners. Don't worry, Master Luke. We know what to do. And you said it was pretty here. Ugh!.</p>
                <p>This can't be! Artoo, you're playing the wrong message. There will be no bargain. We're doomed. I will not give up my favorite decoration. I like Captain Solo where he is. Artoo, look! Captain Solo. And he's still frozen in carbonite. What could possibly have come over Master Luke. Is it something I did? He never expressed any unhappiness with my work. Oh! Oh! Hold it! Ohh!</p>
                <p>Your fleet has lost. And your friends on the Endor moon will not survive. There is no escape, my young apprentice. </p>
                <p>Moruth Doole, </p>
            </div>
            <div class="m-t-30">
                <a class="file" href="" style="min-width: 200px">
                    <div class="media align-items-center">
                        <div class="m-r-15 font-size-30">
                            <i class="anticon anticon-file-pdf text-danger"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Prospectus.doc</h6>
                            <span class="font-size-13 text-muted">1MB</span>
                        </div>
                    </div>
                </a>
                <a class="file" href="" style="min-width: 200px">
                    <div class="media align-items-center">
                        <div class="m-r-15 font-size-30">
                            <i class="anticon anticon-file-excel text-success"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Financial_Report.xls</h6>
                            <span class="font-size-13 text-muted">652KB</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div id="mail-compose" class="mail-content d-none">
        <div class="p-b-15 m-r-15 d-md-none d-inline-block">
            <a class="text-dark font-size-18 mail-open-nav" href="javascript:void(0);">
                <i class="anticon anticon-menu-unfold"></i>
            </a>
        </div>
        <h5 class="m-b-20">新邮件</h5>
        <form >
            <div class="form-group">
                <input type="text" class="form-control" placeholder="收件人">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="主题">
            </div>
            <div id="mail-compose-editor"></div>
        </form>
        <div class="text-right m-t-25">
            <button class="m-r-10 btn btn-default mail-close-compose">放弃</button>
            <button class="btn btn-primary">发送</button>
        </div>
    </div>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendors.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/vendors/quill/quill.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/pages/mail.js"></script>
<!-- /gc:html -->