
class Core {

    constructor() {
        this.sideNav();
        this.pfScrollBar();
        this.tooltipInit();
        this.popOverInit();
        this.toastInit();
    }
    
    sideNav() {
        (function( $ ) {
          $(function() {
              const appLayout =  $( document.body );
              const isFolded = 'folded';
              const isExpand = 'is-expand';
              const active = 'active';
              const drodpDownItem = '.side-nav .side-nav-menu .nav-item .dropdown-menu li'

              
                  if ($(drodpDownItem).hasClass('active')) {
                      $( drodpDownItem + '.' + active).parent().parent().addClass('open')
                  }

              $('.side-nav .side-nav-menu li a').on('click', (e) => {
                  const $this = $(e.currentTarget);
                  
                  if ($this.parent().hasClass("open")) {

                      $this.parent().children('.dropdown-menu').slideUp(200, ()=> {
                          $this.parent().removeClass("open");
                      });

                  } else {
                      $this.parent().parent().children('li.open').children('.dropdown-menu').slideUp(200);
                      $this.parent().parent().children('li.open').children('a').removeClass('open');
                      $this.parent().parent().children('li.open').removeClass("open");
                      $this.parent().children('.dropdown-menu').slideDown(200, ()=> {
                          $this.parent().addClass("open");
                      });
                  }
              });

              $('.header .nav-left .desktop-toggle').on('click', () => {
                  appLayout.toggleClass(isFolded)
              });

              $('.header .nav-left .mobile-toggle').on('click', () => {
                  appLayout.toggleClass(isExpand)
              });
          });
        })(jQuery);
        
    } 

    pfScrollBar() {
        var value = jQuery('.scrollable');
        if(value>0) {
            new PerfectScrollbar('.scrollable');
        }
    }
    
    tooltipInit() {
        jQuery('[data-toggle="tooltip"]').tooltip()
    }

    popOverInit() {
        jQuery('[data-toggle="popover"]').popover({
            trigger: 'focus'
        })
    }

    toastInit() {
        jQuery('.toast').toast();
    }
}

class GC_JS extends Core {

    constructor () {
        super()
        this.initThemeConfig()
    }

    initThemeConfig() {
        themeConfigurator()
    }
}

jQuery(() => {
   window.GC_JS = new GC_JS();
});

function themeConfigurator() {
    (function( $ ) {
      $(function() {
            jQuery(document).on('change', 'input[name="header-theme"]', ()=>{
                const context = $('input[name="header-theme"]:checked').val();
                console.log(context)
                jQuery(".app").removeClass (function (index, className) {
                    return (className.match (/(^|\s)is-\S+/g) || []).join(' ');
                }).addClass( 'is-'+ context );
            });

            jQuery('#side-nav-theme-toogle').on('change', (e)=> {
                jQuery('.app .layout').toggleClass("is-side-nav-dark");
                e.preventDefault();
            });
            
            jQuery('#side-nav-fold-toogle').on('change', (e)=> {
                jQuery('.app .layout').toggleClass("is-folded");
                e.preventDefault();
            });
      });
    })(jQuery);
}


