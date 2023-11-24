<?php
/**
 * Title: 设置
 * Slug: gcoa/setting
 * Categories: pages
 * Keywords: 设置 Setting
 * Block Types: core/html
 */
?>
<!-- gc:html -->
<div class="page-header no-gutters has-tab">
    <h2 class="font-weight-normal">设置</h2>
    <ul class="nav nav-tabs" >
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-account">账户</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-network">网站</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-notification">消息</a>
        </li>
    </ul>
</div>
<div class="container">
    <div class="tab-content m-t-15">
        <div class="tab-pane fade show active" id="tab-account" >
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">基础信息</h4>
                </div>
                <div class="card-body">
                    <div class="media align-items-center">
                        <div class="avatar avatar-image  m-h-10 m-r-15" style="height: 80px; width: 80px">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/avatars/thumb-3.jpg" alt="">
                        </div>
                        <div class="m-l-20 m-r-20">
                            <h5 class="m-b-5 font-size-18">修改头像</h5>
                            <p class="opacity-07 font-size-13 m-b-0">
                                推荐尺寸: <br>
                                120x120 文件大小: 5MB
                            </p>
                        </div>
                        <div>
                            <button class="btn btn-tone btn-primary">上传</button>
                        </div>
                    </div>
                    <hr class="m-v-25">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-semibold" for="userName">用户名:</label>
                                <input type="text" class="form-control" id="userName" placeholder="用户名" value="阿七">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-semibold" for="email">电子邮件:</label>
                                <input type="password" class="form-control" id="email" placeholder="电子邮件" value="@marshallnich">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold" for="phoneNumber">手机号:</label>
                                <input type="text" class="form-control" id="phoneNumber" placeholder="手机号">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold" for="dob">出生日期:</label>
                                <input type="text" class="form-control" id="dob" placeholder="出生日期">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="font-weight-semibold" for="language">语言</label>
                                <select id="language" class="form-control">
                                    <option>英语</option>
                                    <option>法语</option>
                                    <option>德语</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">修改密码</h4>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-semibold" for="oldPassword">旧密码:</label>
                                <input type="password" class="form-control" id="oldPassword" placeholder="旧密码">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-semibold" for="newPassword">新密码:</label>
                                <input type="password" class="form-control" id="newPassword" placeholder="新密码">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-semibold" for="confirmPassword">确认密码:</label>
                                <input type="password" class="form-control" id="confirmPassword" placeholder="确认密码">
                            </div>
                            <div class="form-group col-md-3">
                                <button class="btn btn-primary m-t-30">修改</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">详细地址</h4>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label class="font-weight-semibold" for="fullAddress">完整地址:</label>
                                <input type="text" class="form-control" id="fullAddress" placeholder="完整地址">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-semibold" for="stateCity">省市:</label>
                                <input type="text" class="form-control" id="stateCity" placeholder="省市">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="font-weight-semibold" for="language">国家</label>
                                <select id="language-2" class="form-control">
                                    <option>中国</option>
                                    <option>美国</option>
                                    <option>法国</option>
                                    <option>韩国</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-network">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">网站接入</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon" style="color: #4267b1; background: rgba(66, 103, 177, 0.1)">
                                                <i class="anticon anticon-facebook"></i>
                                            </div>
                                            <div class="font-size-15 font-weight-semibold m-l-15">Facebook</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <label class="m-b-0">https://facebook.com</label>
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-fb" checked="">
                                                <label for="switch-fb"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon" style="color: #fff; background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%,#d6249f 60%,#285AEB 90%)">
                                                <i class="anticon anticon-instagram"></i>
                                            </div>
                                            <div class="font-size-15 font-weight-semibold m-l-15">Instagram</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-inst">
                                                <label for="switch-inst"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon" style="color: #1ca1f2; background: rgba(28, 161, 242, 0.1)">
                                                <i class="anticon anticon-twitter"></i>
                                            </div>
                                            <div class="font-size-15 font-weight-semibold m-l-15">Twitter</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <label class="m-b-0">https://twitter.com</label>
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-tw" checked="">
                                                <label for="switch-tw"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon" style="color: #d8487e; background: rgba(216, 72, 126, 0.1)">
                                                <i class="anticon anticon-dribbble"></i>
                                            </div>
                                            <div class="font-size-15 font-weight-semibold m-l-15">Dribbble</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-dr">
                                                <label for="switch-dr"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon" style="color: #323131; background: rgba(50, 49, 49, 0.1)">
                                                <i class="anticon anticon-github"></i>
                                            </div>
                                            <div class="font-size-15 font-weight-semibold m-l-15">Github</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <label class="m-b-0">https://github.com</label>
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-gh" checked="">
                                                <label for="switch-gh"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon" style="color: #0174af; background: rgba(1, 116, 175, 0.1)">
                                                <i class="anticon anticon-linkedin"></i>
                                            </div>
                                            <div class="font-size-15 font-weight-semibold m-l-15">Linkedin</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <label class="m-b-0">https://linkedin.com</label>
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-ln" checked="">
                                                <label for="switch-ln"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon" style="color: #005ef7; background: rgba(0, 94, 247, 0.1)">
                                                <i class="anticon anticon-dropbox"></i>
                                            </div>
                                            <div class="font-size-15 font-weight-semibold m-l-15">Dropbox</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-db">
                                                <label for="switch-db"></label>
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
        <div class="tab-pane fade" id="tab-notification">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">消息设置</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon avatar-blue">
                                                <i class="anticon anticon-user"></i>
                                            </div>
                                            <div class="m-l-15">
                                                <h5 class="font-weight-semibold m-b-0">每个人都可以找到我</h5>
                                                <p class="m-b-0 font-weight-normal">允许在公众号上找到我。</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-config-1" checked>
                                                <label for="switch-config-1"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon avatar-cyan">
                                                <i class="anticon anticon-mobile"></i>
                                            </div>
                                            <div class="m-l-15">
                                                <h5 class="font-weight-semibold m-b-0">每个人都可以联系我</h5>
                                                <p class="m-b-0 font-weight-normal">允许任何人联系我。</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-config-2" checked> 
                                                <label for="switch-config-2"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon avatar-gold">
                                                <i class="anticon anticon-environment"></i>
                                            </div>
                                            <div class="m-l-15">
                                                <h5 class="font-weight-semibold m-b-0">显示我的位置</h5>
                                                <p class="m-b-0 font-weight-normal">打开位置，您可以探索周围的东西。</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-config-3">
                                                <label for="switch-config-3"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon avatar-purple">
                                                <i class="anticon anticon-mail"></i>
                                            </div>
                                            <div class="m-l-15">
                                                <h5 class="font-weight-semibold m-b-0">电子邮件通知</h5>
                                                <p class="m-b-0 font-weight-normal">接收每日电子邮件通知。</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-config-4" checked>
                                                <label for="switch-config-4"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon avatar-red">
                                                <i class="anticon anticon-question"></i>
                                            </div>
                                            <div class="m-l-15">
                                                <h5 class="font-weight-semibold m-b-0">未知来源</h5>
                                                <p class="m-b-0 font-weight-normal">允许从未知来源下载。</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-config-5">
                                                <label for="switch-config-5"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon avatar-green">
                                                <i class="anticon anticon-swap"></i>
                                            </div>
                                            <div class="m-l-15">
                                                <h5 class="font-weight-semibold m-b-0">数据同步</h5>
                                                <p class="m-b-0 font-weight-normal">允许数据与云服务器同步。</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-config-6" checked>
                                                <label for="switch-config-6"></label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item p-h-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-icon avatar-orange">
                                                <i class="anticon anticon-usergroup-add"></i>
                                            </div>
                                            <div class="m-l-15">
                                                <h5 class="font-weight-semibold m-b-0">团体邀请</h5>
                                                <p class="m-b-0 font-weight-normal">允许任何团体邀请</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="switch m-t-5 m-l-10">
                                                <input type="checkbox" id="switch-config-7" checked>
                                                <label for="switch-config-7"></label>
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
    </div>
</div>
<!-- /gc:html -->