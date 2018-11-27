<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>心愿池</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <script src="/xiangyi/js/jquery-1.12.0.min.js"></script>
    <link rel="stylesheet" href="/xiangyi/css/XiangYi.css">
    <script src="/xiangyi/js/swiper.min.js"></script>
    <script src="/xiangyi/js/js.js"></script>
    <link rel="stylesheet" href="/xiangyi/css/swiper.min.css">
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_831267_ycv5dmyyqf.css">
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_831267_acuwfleafpa.css">
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_831267_2fzosq54oej.css">
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_831267_68xnl3qir3j.css">
    <style>
        body{
            overflow-x: hidden;
            background-color: #efefef;
        }
        .pool-no{
            width: 100vw;
            height: 10vh;
            color: #999999;
            text-align: center;
            line-height: 10vh;
            font-size: 4vw;
            display: none;
        }
        .swiper-slide {
            display: block;
            height: 100%;
            width:auto!important;
        }
        .wrap{
            width: 100vw;
            padding-bottom: 8.5vh;
        }
        .wishChislide{
            height: 100%;
            padding: 0 4vw;
            border-right: 1px solid #f6f6f6;
        }
        .chi-pic{
            width: auto;
            height: 3.4vh;
            float: left;
            margin: 3.05vh 1.5vw 3.05vh 0;
        }
        .wishChi-banner{
            height: 9.5vh;
            width:100%;
            background-color: #ffffff;
            position: fixed;
            left: 0;
            top: 8vh;
            z-index: 100;
        }
        .chi-p1{
            color: #878787;
            float: left;
            line-height: 9.5vh;
            font-size: 5.3vw;
            font-weight: bold;
        }
        .wish0{
            display: inline-block;
            height: 6vw;
            padding: 0.3vw 1vw 0 1vw;
            width: auto;
            font-size: 3.5vw;
            line-height: 6.2vw;
            border-radius: 5px;

        }
        .wish1{
            background-color: #e6f1ff;
            color:#348cf7;
            border: 0.1vw solid #348cf7;
        }
        .wish2{
            background-color: #fff3f3;
            color:#ff5656;
            border: 0.1vw solid #ff5656;
        }
        .wish3{
            background-color: #f4fff5;
            color:#50d084;
            border: 0.1vw solid #50d084;
        }
        .wish4{
            background-color: #fff5e8 ;
            color:#f78b34;
            border: 0.1vw solid #fea77c ;
        }
        .wish5{
            background-color: #fffbe8   ;
            color:#f7c234;
            border: 0.1vw solid #fede7c   ;
        }
        .wish6{
            background-color: #efe8ff   ;
            color:#7d34f7;
            border: 0.1vw solid #a17cfe   ;
        }
        .wish7{
            background-color: #e8ffff   ;
            color:#34b9df;
            border: 0.1vw solid #6dd9e3   ;
        }

        .student-foot{
            display: flex;
        }
        .student-foot li{
            float: left;
            flex-grow: 1;
            border-right: 1px solid #eeeeee;
            height: 8.5vh;
            padding: 0 2.5vw;
            overflow: hidden;
        }
        .student-foot li  .iconfont{
            float: left;
            font-size:4.5vw;
            margin-top: 2.8vh; 
         }
       .student-foot li img{
            height: 2vh;
            margin-top: 2.8vh;
            padding-left: 1vw;
        }
        .student-foot li p{
            font-size: 3.5vw;
            float: left;
            text-align: center;
        }
        .div{
            width: 31px;
            height: 30px;
            position:fixed;
            z-index: 99999;
        }
    </style>
</head>
<body>
<div class="div" id="div">
    <img src="/xiangyi/img/jiahao.png" alt="">
</div>
<script>
        var isClick=true;
            $('.div').each(function(index){
            $(this).on('touchstart', function(evt) {
                var e = event || evt;
                e.preventDefault();//阻止其他事件
                isClick=true;
            }).on('touchmove', function(evt) {
                var e = event || evt;
                e.preventDefault();//阻止其他事件
                // 如果这个元素的位置内只有一个手指的话
                //console.log(e.targetTouches)
                if (e.targetTouches.length == 1) {
                    var touch = e.targetTouches[0];  // 把元素放在手指所在的位置
                    $(this).css("left",(touch.pageX- parseInt($(this).width())/2 + 'px'));
                    $(this).css("top",(touch.pageY- parseInt($(this).height())/2 + 'px'));
                }
                isClick=false;
            }).on('touchend', function(evt) {
                var e = event || evt;
                e.preventDefault();//阻止其他事件
                if(isClick==true){window.location.href='/h5/teacher/release_wish.html'
                }
            })
        });
    
</script>    
<div class="wrap">
    <div class="msgAlert"></div>
    <div class="teacher-head">
        <img src="/xiangyi/img/xyc-top_03.png" alt="">
        <p>心愿之池</p>
    </div>
    <div class="wishChi-banner">
        <div class="swiper-container" id="swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="wishChislide  xyc-style" onclick="poolClick(this)" data-inx="0">
                        <img src="/xiangyi/img/xyc-blue_07.png" alt="" class="chi-pic">
                        <p class="chi-p1">全部心愿</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="1">
                        <img src="/xiangyi/img/swiper-color7.png" alt="" class="chi-pic">
                        <p class="chi-p1">购物</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="2">
                        <img src="/xiangyi/img/swiper-color6.png" alt="" class="chi-pic">
                        <p class="chi-p1">取邮件</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="3">
                        <img src="/xiangyi/img/swiper-color5.png" alt="" class="chi-pic">
                        <p class="chi-p1">校园出行</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="4">
                        <img src="/xiangyi/img/swiper-color4.png" alt="" class="chi-pic">
                        <p class="chi-p1">上门陪伴</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="5">
                        <img src="/xiangyi/img/swiper-color3.png" alt="" class="chi-pic">
                        <p class="chi-p1">整理资料</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="6">
                        <img src="/xiangyi/img/swiper-color2.png" alt="" class="chi-pic">
                        <p class="chi-p1">辅导手机应用</p>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="7">
                        <img src="/xiangyi/img/swiper-color1.png" alt="" class="chi-pic">
                        <p class="chi-p1">读报</p>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="wishChislide" onclick="poolClick(this)" data-inx="8">
                        <img src="/xiangyi/img/qt.png" alt="" class="chi-pic">
                        <p class="chi-p1">其他</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wish-cent" style="margin-top: 18.6vh">
        <ul class="wish-list">
            <!--<li class="wish-dwc">-->
                <!--<div class="qx_top">-->
                    <!--<div class="dwc-left">-->
                        <!--<img src="/xiangyi/img/dwc_ic1_03.png" alt="" class="dwc-pic">-->
                        <!--<p class="dwc-p1">发布人：郑老师</p>-->
                    <!--</div>-->
                    <!--<p class="qx-blue">认领心愿</p>-->
                <!--</div>-->
                <!--<div class="qx-bot">-->
                    <!--<p class="wish-p1">心愿类型：<span class="wish2 wish0">取快递</span></p>-->
                    <!--<p class="qx-txt">帮老师去群力第六大道，买5斤黄瓜，5斤土豆，20 斤大米，20斤白菜。</p>-->
                <!--</div>-->
                <!--<div class="dwc-bot">-->
                    <!--<div class="dwc-bot1" style="border-right: 1px solid #f6f6f6;">-->
                        <!--<img src="/xiangyi/img/dwc-phone_03.png" alt="" class="dwc-pone">-->
                        <!--<p class="dwc-num">15248975263</p>-->
                    <!--</div>-->
                    <!--<div class="dwc-bot1">-->
                        <!--<p class="dwc-date">截止时间：09-04  20:00</p>-->
                    <!--</div>-->
                <!--</div>-->
            <!--</li>-->
        </ul>
    </div>
    <div class="pool-no">没有更多数据了</div>
    <ul class="teacher-foot student-foot">
    <!-- <li onclick="jumpPage(this)" data-href="/h5/student/release_wish">
            <img src="/xiangyi/img/foot-white2_03.png" alt="" class="foot-pic">
            <p class="foot-p">发布心愿</p>
        </li> -->
        <li onclick="jumpPage(this)" data-href="/h5/teacher/wish_release">
            <i class="iconfont icon-jiaolian1"></i>
            <p class="foot-p">我的发布</p>
        </li>
        <li onclick="jumpPage(this)" data-href="/h5/teacher/wish_list">
            <i class="iconfont icon-xin"></i>
            <p class="foot-p">我的接受</p>
        </li>
        <li class="foot-style" data-href="/h5/teacher/wish_pool">
            <i class="iconfont icon-liwu1"></i>
            <p class="foot-p">心愿之池</p>
        </li>
        <li onclick="jumpPage(this)" data-href="/h5/teacher/index">
            <i class="iconfont icon-ren"></i>
            <p class="foot-p">个人中心</p>
        </li>
    </ul>
    <div class="page-loading">
        <img src="/xiangyi/img/loading.gif" alt="" class="loading-pic">
        <p>正在加载</p>
    </div>
</div>
</body>
</html>
<script>
    // 跳页
    function jumpPage(a) {
        var href=$(a).attr("data-href")
        window.location.href=href
    }
    function goBack() {
        window.history.go(-1)
    }

</script>
<script>
    // 滑动列表
    var swiper = new Swiper('#swiper', {
        slidesPerView: 'auto',
        loopedSlides:8,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
</script>
<script>
    // 滚动加载
   function poolClick(e) {
    //console.log(e);
       $(e).addClass("xyc-style");
       $(e).parents().siblings().find(".wishChislide").removeClass("xyc-style")
       var inx =$(e).attr("data-inx")
       ajaxlist(inx,true)
   }
   var loading = false;
   $('.wish-list').on("touchmove", function () {
       var scrollTop = $(document).scrollTop() + $(window).height();
       var scrollBottom = $(document).height() - 20;

       if (scrollTop > scrollBottom) {
           if (loading) {
               return;
           } else {
               var offset = $('.wish-list li').length
               var inx = $('.xyc-style').attr('data-inx')
               ajaxlist(inx,false,offset)
               loading = true;
               $(".page-loading").show()
           }
       }
   });

   function ajaxlist(inx,cl,offset) {
       if (cl){
           $('.wish-list').html('')
           $(".pool-no").hide()
       }
       $.ajax({
           url:"/h5/student/wish_pool",
           type:"get",
           data:{
               status:inx,
               offset:offset
           },
           success:function (res) {
               var html="";
               if(res){
                console.log(res);
                   for (var i=0;i<res.rows.length;i++) {
                       html += '<li class="wish-dwc">'
                       html += '                <div class="qx_top">'
                       html += '                    <div class="dwc-left">'
                       html += '                        <img src="'+res.rows[i].wu_headimgurl+'" alt="" class="dwc-pic">'
                       html += '                        <p class="dwc-p1">发布人：'+ res.rows[i].s_name +'</p>'
                       html += '                    </div>'
                       html += '                    <p class="qx-blue" onclick="cancel(this)" data-id="'+res.rows[i].id+'">认领心愿</p>'
                       html += '                </div>'
                       html +='    <div class="qx-bot">'

                       html +='          <p class="wish-p1">心愿类型：<span class="wish0 '
                       if(res.rows[i].level == "购物"){
                           html += 'wish1'
                       }
                       if(res.rows[i].level == "取快递"){
                           html += 'wish2'
                       }
                       if(res.rows[i].level == "校园出行"){
                           html += 'wish3'
                       }
                       if(res.rows[i].level == "上门陪伴"){
                           html += 'wish5'
                       }
                       if(res.rows[i].level == "整理资料"){
                           html += 'wish7'
                       }
                       if(res.rows[i].level == "辅导手机应用"){
                           html += 'wish4'
                       }
                       if(res.rows[i].level == "读报"){
                           html += 'wish6'
                       }
                        if(res.rows[i].level == "其他"){
                           html += 'wish6'
                       }
                       html +='">'+res.rows[i].level+'</span></p>'
                       html += '                    <p class="qx-txt">'+res.rows[i].content+'</p>'
                       html += '                </div>'
                       html += '                <div class="dwc-bot">'
                       html += '                    <div class="dwc-bot1" style="border-right: 1px solid #f6f6f6;width: 48%">'
                       html += '<a href="tel:'+res.rows[i].s_mobile+'">'
                       html += '                        <img src="/xiangyi/img/dwc-phone_03.png" alt="" class="dwc-pone">'
                       html += '                        <p class="dwc-num">'+res.rows[i].s_mobile+'</p>'
                       html +='</a>'
                       html +=  '                    </div>'
                       html += '                    <div class="dwc-bot1" style="width: 50%">'
                       html += '                        <p class="dwc-date">截止时间：'+res.rows[i].end_time+'</p>'
                       html +=  '                    </div>'
                       html +=  '                </div>'
                       html +=  '            </li>'
                   }
                   $('.wish-list').append(html)
               }
               if(res.rows.length == 0 || !res.rows){
                   loading = true;
                   $(".pool-no").show()
                   $(".page-loading").hide()
               }else {
                   loading = false;
                   $(".page-loading").hide()
               }
           }
       })

   }
   ajaxlist('0',true)
// 认领心愿请求
    function cancel(e) {
        var id = $(e).attr("data-id")
        $.ajax({
            url:"/h5/student/claim",
            type:"post",
            data:{
                id:id
            },
            success:function (res) {
                var idx=$(".wish-style").attr("data-idx")
                if (res.status == 1){
                    ajaxlist(idx,true)
                }else {
                    msgAlert(res.info)
                }

            }
        })
    }
</script>
<!-- <script>
        var isClick=true;
         $('.div').each(function(index){
        $(this).on('touchstart', function(evt) {
            var e = event || evt;
            e.preventDefault();//阻止其他事件
            isClick=true;
        }).on('touchmove', function(evt) {
            var e = event || evt;
            e.preventDefault();//阻止其他事件
            // 如果这个元素的位置内只有一个手指的话
            //console.log(e.targetTouches)
            if (e.targetTouches.length == 1) {
                var touch = e.targetTouches[0];  // 把元素放在手指所在的位置
                $(this).css("left",(touch.pageX- parseInt($(this).width())/2 + 'px'));
                $(this).css("top",(touch.pageY- parseInt($(this).height())/2 + 'px'));
            }
            isClick=false;
        }).on('touchend', function(evt) {
            var e = event || evt;
            e.preventDefault();//阻止其他事件
            if(isClick==true){window.location.href='/h5/teacher/release_wish.html'
            }
        })
    });

       </script> -->