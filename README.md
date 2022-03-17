# 格尺建站系统 网站 博客 商城 表单 （PHP+MySQL）

## 产品介绍

格尺建站系统 GeChiIUI.com 是国人开发的网络信息发布系统，可搭建博客、企业建站、资源站、帮助中心、电商等。支持多站点模式，可用于集团建站、SaaS系统服务。内置主题与插件市场，可扩展更多的第三方服务。

管理后台UI部分采用Bootstrap+SCSS+ES6，适合开发者高度定制魔改。

格尺建站系统GeChiUI在WordPress基础上去广告、去探针、去监控、防堵墙、并自建的国内资源（主题+插件）服务器，更适合国人使用，也不限于海外站点建设。
源码全中文，无需语言包安装（如需英文版，请安装英文语言包）
全新管理后台UI模型
全新支持页面UI拖拽设计与模板（Bate）

### 模板演示：

- [https://sitedemos.gechiui.com/](https://sitedemos.gechiui.com/)

### 生产环境安装包 git：
如果服务器支持git 可以直接使用git命令在服务器上安装
如果底层修改代码，此包也是开源的，可以对PHP文件直接修改。
```
- git clone https://gitee.com/gechiui/gechiui.git
```

### 开发工程包 git：

工程包用于开发者使用，修改PHP、JS、CSS及管理NPM组件
```
- git clone https://gitee.com/gechiui/gechiui-develop.git
```

### 后台UI模型用例：

管理后台UI部分采用Bootstrap+SCSS+ES6

- [http://admintemp.gechiui.com/](http://admintemp.gechiui.com/)


### V6.0更新

1. 源码全中文，无需语言包安装

2. 全新支持页面UI拖拽设计与模板（Bate）

3. 面向高级开发者，提供专属NPM私有库与工程包


### 主题
1. 自适应H5主题
2. 帮助中心主题

### 插件支持
1. 自适应模板 （200多个）
2. 在线商城
3. 可拖拽页面生成器
4. 可拖拽表单
5. 自定义字段

### 个性化功能
1、邮箱+手机号注册登录，支持短信验证码功能，内置用友短信服务插件
2、支持RBAC（Role-Based Access Control）基于角色的访问控制
3、用户功能地图，方便用户快速索引功能菜单
4、主题与插件市场，众多国产化插件与服务
5、专业版插件的在线购买、安装及升级功能
6、支持REST-API，APPKey安全授权
7、支持多站点模式

### 功能说明
1、后台地址：/gc-admin.php
2、开启多站点模式：gc-config.php 尾部添加 define('GC_ALLOW_MULTISITE',true);
3、站点地图访问地址：/gc-sitemap.xml
4、feed地址：/feed
GeChiUI能让您省却对后台技术的担心，集中精力做好网站的内容。

## 单站点伪静态设置

```
location /
{
	 try_files $uri $uri/ /index.php?$args;
}

rewrite /gc-admin$ $scheme://$host$uri/ permanent;
```
## 多站点

###开启多站点（SaaS模式）

系统安装成功后，修改` gc-config.php `文件，在尾部添加 ` define('GC_ALLOW_MULTISITE',true); `
保存后，刷新管理后台，在“工具”菜单中，新增加了“网络设置”功能，点击按流程执行即可

### 多站点伪静态设置
```
rewrite ^/([_0-9a-zA-Z-]+/)?gc-admin$ /$1gc-admin/ permanent;
if (-f $request_filename){
	set $rule_2 1;
}
if (-d $request_filename){
	set $rule_2 1;
}
if ($rule_2 = "1"){
#ignored: “-” thing used or unknown variable in regex/rew
}
#宝塔SSL证书访问直接访问
rewrite /.well-known/acme-challenge/(.*)$ /.well-known/acme-challenge/$1 last;
rewrite ^/([_0-9a-zA-Z-]+/)?(gc-(content|admin|includes).*) /$2 last;
rewrite ^/([_0-9a-zA-Z-]+/)?((assets).*) /$2 last;
rewrite ^/([_0-9a-zA-Z-]+/)?(.*.php)$ /$2 last;
rewrite /. /index.php last;
```

## 联系我们

产品官网：[https://www.gechiui.com](https://www.gechiui.com)
开发者QQ群：619571887
![](https://www.gechiui.com/gc-content/images/qq.jpeg "开发者QQ群")

## 商务合作微信：

- 我们接受多种形式的赞助，比如商业版授权、广告投放、API接入、服务集成等
- 欢迎开发者加盟共同维护产品
- 欢迎学生创业者、个人创业者成为我们的实施交付伙伴
![](https://www.gechiui.com/gc-content/images/wechat.jpeg "宫叔微信")

## 使用须知

### ✅允许

- 个人学习使用
- 允许用于学习、毕设等
- 允许进行商业使用，请自觉遵守使用协议，如需要专业版插件，推荐购买(https://www.gechiui.com/shop/)
- 如需要纸质商业版授权许可，可与官方或开发者的商务人员联络
- 请遵守 Apache License2.0及以上版本协议，再次开源请注明出处
- 希望大家多多支持原创作品

## 如果成为GeChiUI的开发者

### 要求

- 有基础的服务器管理、PHP、MySQL等技术开发功底
- 参加GeChiUI的技术培训
- 可以独立处理客户需求、定制、开发的需求
- 可以帮助客户代维系统
- 每年需缴纳5000元的培训认证费用

### 获得的权益

- 自研的插件或主题，发布到官网服务器
- 批量获得专业版插件的许可进行分发
- 可修订官网产品代码，并提交评审

## 功能截图演示

### 登录页面
![](https://www.gechiui.com/gc-content/images/login.png "登录")
### 后台首页
![](https://www.gechiui.com/gc-content/images/home.png "后台首页")
### 文章可拖拽（区块）编辑器
![](https://www.gechiui.com/gc-content/images/post_edit.png "区块编辑器")
### 界面可拖拽编辑器
![](https://www.gechiui.com/gc-content/images/post_edit.png "界面编辑器")
### 界面可拖拽编辑器
![](https://www.gechiui.com/gc-content/images/page_edit.png "界面编辑器")
### 插件市场
![](https://www.gechiui.com/gc-content/images/plugins_add.png "插件市场")
