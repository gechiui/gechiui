<?php
/**
* Title: 折叠页签 Accordion
* Slug: gcoa/accordion
* Categories: components
* Keywords: 折叠页签 Accordion
* Block Types: core/html
*/
?>
<!-- gc:html -->
<div class="page-header">
    <h2 class="header-title">折叠菜单</h2>
    <div class="header-sub-title">
        <nav class="breadcrumb breadcrumb-dash">
            <a href="#" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>首页</a>
            <a class="breadcrumb-item" href="#">控件</a>
            <span class="breadcrumb-item active">折叠菜单</span>
        </nav>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>默认折叠菜单</h4>
        <p>整个折叠菜单控件使用class样式 <code>.accordion</code> </p>
        <div class="m-t-25">
            <div class="accordion" id="accordion-default">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <a data-toggle="collapse" href="#collapseOneDefault">
                                <span>Collapsible Group Item #1</span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseOneDefault" class="collapse show" data-parent="#accordion-default">
                        <div class="card-body">
                            <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <a class="collapsed" data-toggle="collapse" href="#collapseTwoDefault">
                                <span>Collapsible Group Item #2</span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseTwoDefault" class="collapse" data-parent="#accordion-default">
                        <div class="card-body">
                            <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <a class="collapsed" data-toggle="collapse" href="#collapseThreeDefault">
                                <span>Collapsible Group Item #3</span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseThreeDefault" class="collapse" data-parent="#accordion-default">
                        <div class="card-body">
                            <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h4>无边框折叠菜单</h4>
        <p>一个没有变更样式的折叠菜单。</p>
        <div class="m-t-25">
            <div class="accordion borderless" id="accordion-borderless">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <a data-toggle="collapse" href="#collapseOneBorderless">
                                <span>Collapsible Group Item #1</span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseOneBorderless" class="collapse show" data-parent="#accordion-borderless">
                        <div class="card-body">
                            <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <a class="collapsed" data-toggle="collapse" href="#collapseTwoBorderless">
                                <span>Collapsible Group Item #2</span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseTwoBorderless" class="collapse" data-parent="#accordion-borderless">
                        <div class="card-body">
                            <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <a class="collapsed" data-toggle="collapse" href="#collapseThreeBorderless">
                                <span>Collapsible Group Item #3</span>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseThreeBorderless" class="collapse" data-parent="#accordion-borderless">
                        <div class="card-body">
                            <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /gc:html -->