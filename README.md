# GeChiIUI 格尺・后台开发框架 OA协同 企业门户 审批流 建站

## 产品介绍

GeChiIUI（格尺・后台开发框架），是一款开源的团队协同办公系统框架，面向中小团体。GeChiIUI主要功能文章、文件文档管理，表单管理，审批流，知识库。拖拽操作简单方便。并可搭建博客、企业建站、帮助中心、电商等。支持多团队、多站点模式，可用于集团应用、SaaS系统服务。

### 模板演示：

- [https://sitedemos.gechiui.com/gcoa/](https://sitedemos.gechiui.com/gcoa/)

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

UI部分采用Bootstrap+SCSS+ES6

- [http://admintemp.gechiui.com/](http://admintemp.gechiui.com/)



### 主题
1. OA协同主题
2. 在线文档主题
3. 自适应建站主题

### 插件支持
1. 表单
2. 审批流
3. 自定义字段
4. 自适应模板 （200多个）
5. 在线商城
6. 代码高亮
等20多个插件

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

## 商务合作微信：

- 我们接受多种形式的赞助，比如商业版授权、广告投放、API接入、服务集成等
- 欢迎开发者加盟共同维护产品
- 欢迎学生创业者、个人创业者成为我们的实施交付伙伴
![](https://www.gechiui.com/gc-content/images/wechat.jpeg "宫叔微信")

## 使用须知

### ✅允许

- 个人学习使用
- 允许用于学习、毕业设计等
- 允许进行商业使用，请自觉遵守使用协议，如需要专业版插件，推荐购买(https://www.gechiui.com/pro/)
- 如需要纸质商业版授权许可，可与官方或开发者的商务人员联络
- 请遵守 Apache License2.0及以上版本协议，再次开源请注明出处
- 希望大家多多支持原创作品


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