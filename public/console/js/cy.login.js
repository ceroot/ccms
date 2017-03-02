/**
 * 
 * @authors SpringYang (ceroot@163.com)
 * @date    2017-01-14 15:01:26
 * @version $Id$
 */

 $(function () {
    var storage     = window.localStorage;  // html5 本地存储对象
    var username    = $('input[name="username"]');
    var password    = $('#password');
    var remember    = $('#remember');
    var button      = $('button');
    var verifyArea  = $('.verify-area');


    
    // 本地存储数据判断[html5/cookie]
    if(storage){
        if(storage.getItem('username')){
            username.val(storage.getItem('username'));
            password.focus();
            remember.prop('checked',true);
        }else{
            username.focus();
        }

        if(storage.getItem('error_num')){
            if(storage.getItem('error_num')>3){
                verifyArea.show();
            }
        }
    }else{
        if($.cookie(COOKIE_NAME)){
            username.val($.cookie(COOKIE_NAME));
            password.focus();
            remember.prop('checked',true);
        }else{
            username.focus();
        }

        if($.cookie(COOKIE_ERROR)>3){
            verifyArea.show();
        }
    }

    // checkbox样式
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
    
    // 密码框失焦检查是否显示验证码
    $('body').on('blur', '#password', function(event) {
        event.preventDefault();
        $.ajax({
            url: showverifyUrl,
            type: 'POST',
            dataType: 'json',
        })
        .done(function(data) {
            if(data==1){
                verifyArea.show();
            }
        });
    });

    // 登录提交
    $('body').on('click', 'button[type="submit"]', function(event) {
        event.preventDefault();
        var _form     = $('form');
        var issubmit  = true;

        _form.find('.input-area .form-group:visible').each(function(index, el) {
            var input  = $(this).find('input');
            if(!input.val()){
                input.css('border','1px #ff0000 solid');
                if(issubmit){
                    input.focus();
                }
                issubmit  = false;
            }else{
                input.removeAttr('style');
            }
        });

        if(!issubmit){
            layer.msg('请填写必填项',function(){});
            return false;
        }

        var loading = layer.load();
        if (remember.prop('checked')==true) {
            if(storage){
                storage.setItem('username',username.val());
            }else{
                $.cookie(COOKIE_NAME,username.val(), { path: '/', expires: 15 });
            }
        }else{
            if(storage){
                storage.removeItem('username');
            }else{
                $.cookie(COOKIE_NAME, null, { path: '/' });  //删除cookie
            }
        }
        
        button.prop('disabled',true).text('登录中…');
        $.ajax({
            url: window.location,
            type: 'POST',
            dataType: 'json',
            data: _form.serialize(),
        })
        .done(function(data) {
            console.log(data);
            if(data.status==1){
                layer.msg(data.info);
                layer.close(loading);
                if(getQueryString('backurl')){
                    var jumpurl  = getQueryString('backurl');
                }else{
                    var jumpurl  = data.url;
                }
                console.log(jumpurl);
                window.location.href = jumpurl;
            }else{
                layer.msg(data.info,function(){});
                layer.close(loading);
                button.prop('disabled',false).text('登录');
                // console.log('登录');
            }

            if(storage){
                storage.setItem('error_num',data.error_num);
            }else{
                $.cookie(COOKIE_ERROR,data.error_num, { path: '/', expires: 15 });
            }

            if(data.show_code==1){
                verifyArea.show();
                changeCode();
            }else{
                verifyArea.hide();
            }
        })
        .fail(function() {
            layer.close(loading);
            console.log('fail');
            button.prop('disabled',false).text('登录');
        })
        .always(function() {
            console.log("complete");
        });
        
        return false;
    });
    
    // 点击切换验证码
    $('body').on('click', '.captcha-area img', function(event) {
        event.preventDefault();
        changeCode();
    });

});

particlesJS('particles-js',{
    "particles": {
        "number": {
            "value": 100,
            "density": {
                "enable": true,
                "value_area": 800
            }
        },
        "color": {
            "value": "#ffffff"
        },
        "shape": {
            "type": "circle", // "circle", "edge" or "triangle"
            "stroke": {
                "width": 0,
                "color": "#000000"
            },
            "polygon": {
                "nb_sides": 5
            },
            // "image": {
            //     "src": "img/github.svg",
            //     "width": 100,
            //     "height": 100
            // }
        },
        "opacity": {
            "value": 0.5,
            "random": false,
            "anim": {
                "enable": false,
                "speed": 1,
                "opacity_min": 0.1,
                "sync": false
            }
        },
        "size": {
            "value": 4,
            "random": true,
            "anim": {
                "enable": false,
                "speed": 80,
                "size_min": 0.1,
                "sync": false
            }
        },
        "line_linked": {
            "enable": true,
            "distance": 100,
            "color": "#ffffff",
            "opacity": 0.4,
            "width": 2
        },
        "move": {
            "enable": true,
            "speed": 4,
            "direction": "none",
            "random": false,
            "straight": false,
            "out_mode": "out",
            "bounce": false,
            "attract": {
                "enable": false,
                "rotateX": 600,
                "rotateY": 1200
            }
        }
    },
    "interactivity": {
        "detect_on": "canvas",
        "events": {
            "onhover": {
                "enable": true,
                //"mode": "grab" //"grab" "bubble" "repulse"
            },
            "onclick": {
                "enable": true,
                "mode": "push"
            },
            "resize": true
        },
        "modes": {
            // "grab": {
            //     "distance": 800,
            //     "line_linked": {
            //         "opacity": 1
            //     }
            // },
            // "bubble": {
            //     "distance": 800,
            //     "size": 80,
            //     "duration": 2,
            //     "opacity": 0.8,
            //     "speed": 3
            // },
            // "repulse": {
            //     "distance": 400,
            //     "duration": 0.4
            // },
            "push": {
                "particles_nb": 12
            },
            "remove": {
                "particles_nb": 2
            }
        }
    },
    "retina_detect": true
});

// 取得时间函数
function genTimestamp(){
    var time = new Date();
    return time.getTime();
}

// 切换验证码函数
function changeCode(){
    var imgsrc = $('.captcha-area img').attr('src');
    $('.captcha-area img').attr('src',imgsrc + '?t='+genTimestamp());
}

// 取得 url 参数
function getQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}