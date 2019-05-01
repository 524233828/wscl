<!DOCTYPE>
<html>
<head>
    <title>会员卡</title>
    <script type="text/javascript">var yyuc_jspath = "/js/";</script>
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/yyucadapter.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="/css/mwm/card/card.css" media="all"/>
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"
          name="viewport">
    <meta name="format-detection" content="telephone=no">
</head>

<body id="card" ondragstart="return false;" onselectstart="return false;">
<section class="body">
    <div id="overlay" style="position:fixed;z-index:100;"></div>
    <div class="cardcenter">
        <!--如果没领卡-->
        @if ($is_get_card == 0)
        <div class="msk">
            <p class="explain2">
                <a id="showcard" class="receive" href="javascript:void(0)">领取您的新会员卡</a>
                <span>中国黄金专卖店会员卡</span>
            </p>
        </div>
        @endif
        <!--如果没领卡-->
        <div class="card">
            <img class="cardbg" src="/uploads/{{$image_url}}">
            <!-- <img id="cardlogo" class="logo" src="/upload/auto/2018/11/15/bb2bb19a5849bbb40360167895e5ae57.jpg"> -->
            <h1 style="color: #FFFFFF">中国黄金专卖店</h1>
            <strong class="pdo verify" style="color: #FFFFFF">
                <span id="cdnb" style="text-align: right;margin-top: 15px;"><em>{{$card_no}}</em></span>
            </strong>
        </div>
        <!--  
        <div id="masklayer" class="masklayer off" ontouchmove="return true;" onclick="$(this).toggleClass('on');">
            <script>
                var isAndroid = navigator.userAgent.toLowerCase().indexOf("android");
                document.write(isAndroid>-1?"<img src='http://stc.weimob.com/img/instruction_android.png' alt='' />":"<img src='http://stc.weimob.com/img/instruction_iphone.png' alt='' />");
            </script>
        </div>
         -->
        <p class="explain">
            <span>使用时向服务员出示此卡</span>
        </p>
    </div>
    <div class="cardexplain">
        <!--会员积分信息-->
        <div class="jifen-box">
            <ul class="zongjifen">
                <li>
                    <a href="javascript:;">

                            <p>&nbsp;</p>
                            <span>&nbsp;</span>

                    </a>
                </li>
                <li>
                    <a href="javascript:;">

                            <p>剩余积分</p>
                            <span>{{$score}}分</span>

                    </a>
                </li>
                <li>
                    <a href="javascript:;">
                        <p>&nbsp;</p>
                        <span>&nbsp;</span>
                    </a>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
        {{--<ul class="round">--}}
            {{--<li><a href="hykmm-sm-640.html#mp.weixin.qq.com"><span>会员卡说明</span></a></li>--}}
            {{--<li><a href="hykmd.html#=mp.weixin.qq.com"><span>适用门店电话及地址</span></a></li>--}}
        {{--</ul>--}}
        {{--<ul class="round">--}}
            {{--<li class="addr">--}}
                {{--<a href="http://api.map.baidu.com/marker?location=24.807991,113.604893&title=韶关市浈江区风度南路步行街82号&content=中国黄金专卖店&output=html&src=weiba|weiweb">--}}
                    {{--<span>地址: 韶关市浈江区风度南路步行街82号</span>--}}
                {{--</a>--}}
            {{--</li>--}}
            {{--<li class="tel">--}}
                {{--<a href="tel:0751-8888899">--}}
                    {{--<span>电话: 0751-8888899</span>--}}
                {{--</a>--}}
            {{--</li>--}}
        {{--</ul>--}}
    </div>

    <div class="plugback">
        <a href="javascript:history.back(-1)">
            <div class="plugbg themeStyle">
                <span class="plugback"></span>
            </div>
        </a>
    </div>
    <!--输入框-->
    <div class="window" id="windowcenter"
         style="height:auto!important;max-height:1000px!important;bottom:inherit!important;">
        <div id="title" class="wtitle">领卡信息<span class="close" id="alertclose"></span></div>
        <div class="content">
            <div id="txt">填写真实的姓名以及电话号码，即可获得会员卡，享受会员特权。</div>
            <script>
                var navg = navigator.userAgent.toLowerCase(), html = '';
                if (navg.match(/(ipad)|(iphone)/i)) {
                    html = '<p><input name="truename" value="" class="px" id="un" type="text" placeholder="请输入您的姓名" ontouchstart="event.preventDefault();this.focus();this.select();" /></p>\
						<p><input name="tel" class="px" id="tel" value="" type="tel" placeholder="请输入您的电话" ontouchstart="event.preventDefault();this.focus();this.select();" /></p>\
												';
                } else {
                    html = '<p><input name="truename" value="" class="px" id="un" type="text" placeholder="请输入您的姓名" /></p>\
						<p><input name="tel" class="px" id="tel" value="" type="tel" placeholder="请输入您的电话" /></p>\
												';
                }
                document.write(html);
            </script>
            <input type="button" value="确 定" name="确 定" class="txtbtn" id="windowclosebutton">
        </div>
    </div>


    <style>
        .masklayer {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 2000;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            text-align: right;
        }

        .masklayer.on {
            display: block;
        }

        .masklayer img {
            margin-top: 10px;
            margin-right: 30px;
            width: 160px;
        }
    </style>
</section>
<script type="text/javascript">
    $(function () {
        $('#alertclose').click(function () {
            $('#windowcenter').hide();
        });
        $("#showcard").click(function () {
            $('#windowcenter').slideDown();
        });
        $('#windowclosebutton').click(function () {
            var un = $.trim($('#un').val());
            var tel = $.trim($('#tel').val());
            if (un == '' || tel == '') {
                tusi('请完善用户信息');
                return;
            }
            ajax('/api/get_card', {tel: tel, un: un, id: {{$wxid}} }, function (m) {
                if (m == 'rep') {
                    location.reload(true);
                }
                tusi('领取成功');
                //window.location.reload();
                //$('#applyBtn').show().find('a').html('会员卡SN：'+m).attr('href','javascript:;');
                //$('#xxsjdiv').hide();
                location.reload(true);
            });
        });
        $('#qdzjfclick').click(function () {
            ajax('hykmm-qdl-640.html', {rid: ''}, function (m) {
                if (m == 'not') {
                    tusi('你还不是会员');
                } else if (m != "") {
                    var r = '签到成功,积分+1';
                    tusi(r);
                    setTimeout(function () {
                        location.reload(true);
                    }, 888);

                }
            });
        });
    });


</script>
</body>
</html>