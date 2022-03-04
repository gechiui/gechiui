
//手机注册验证JS脚本
//宫叔
//20210611
( function( $ ){
    var $phone; //手机号，演示用，同时用来防止获取验证码后修改手机号码的作弊行为
    var time=0; //60秒用的变量，这里先预设判断按钮使用
    var timer; //计时器
    class FormValidation {

        static init() {

            $( "#mobileform" ).validate({
                ignore: ':hidden:not(:checkbox)',
                errorElement: 'div',
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                rules: {
                    user_mobile: {
                        required: true,
                        isMobile : true
                    },
                    sms_code: {
                        required: true,
                        minlength: 6
                    },
                    user_pass: {
                        required: true,
                        rangelength: [6, 16]

                    }
                },
                messages: {
                    user_mobile: {
                        required: "请输入手机号",
                        minlength : "确认手机不能小于11个数字",
                        isMobile : "请正确填写您的手机号码"
                    },
                    sms_code: {
                      required: "请输入短信验证码",
                      minlength : "验证码为6位数字"
                    },
                    user_pass: {
                      required: "请输入6-16位密码，英文+数字组合",
                      rangelength: "请输入6-16位密码，英文+数字组合"

                    }

                  }
            });
        }
    }

    // 自定义手机格式验证
    jQuery.validator.addMethod("isMobile", function(value, element) {
    var length = value.length;
    var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
    var $a = this.optional(element) || (length == 11 && mobile.test(value));
    if($a && time==0){
        $( "#verify" ).attr("disabled",false);
    }else{
        $( "#verify" ).attr("disabled",true);
    }
    return $a;
    }, "请正确填写您的手机号码");

    // 发验证码倒计时
    $( "#verify" ).click(function () {
        $phone = $( "#user_mobile" ).val();//获取输入的电话号
        smscode_ajax($phone); //异步发短信
        $(this).attr("disabled",true);//点击获取验证码后，禁用该按钮，开始倒计时
        time = 60;//倒计时时间，自定义
        var $this = $(this);//备份，定时器是异步的，此this非彼this
        timer = setInterval(function () {
          time--;//开始倒计时
          if (time == 0) {//当倒计时为0秒时，关闭定时器，更改按钮显示文本并设置为可以点击
            clearInterval(timer);
            $this.text('获取短信验证码');
            $this.attr("disabled",false);
            return;
          }
          $this.text('已发送, 还剩' + time + "S");//显示剩余秒数


        }, 1000);//定时器1秒走1次，每次减1

    });
    $(() => { FormValidation.init(); });

    // AJAX发短信
    function smscode_ajax($mobile) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: admin_ajax_url,
            timeout: 10000, //ajax请求超时时间10s
            data: { mobile: $mobile }, //post数据,手机号+验证码
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 1) {
                    //短信发送成功
                    //alert(data.msg);
                }else{
                    reset_verify();
                    alert(data.msg);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                reset_verify();
                alert('短信网络异常，请联系客服');
            }
        });
    }
    //对发验证码的按钮进行重置
    function reset_verify(){
        clearInterval(timer);
        time=0;
        $( "#verify" ).attr("disabled",false);
        $( "#verify" ).text('重新发送');
    }
})(jQuery);