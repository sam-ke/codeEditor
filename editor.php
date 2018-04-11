<!DOCTYPE html>
<html lang="en">
<head>
    <title>在线代码编辑器</title>
    <style type="text/css" media="screen">
        #editor {
            /*position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;*/
            width: 50%;
            height: 750px;
            float: left;
        }

        .result{
            width: 50%;
            height: 750px;
            float: left;
            overflow: scroll;
            background: #8F908A;
        }
        .r-content{
            margin: 10px;
            color: #fff;
        }

        .header{
            height: 45px;
            width: 100%;
            background: #438eb9;
        }
        .header .text{
            padding: 6px 30px;
            font-size:21px;
            color: #fff;
        }
        .editor{
            width: 100%;
        }

    </style>
</head>
<body style="margin: 0 0; background: #2F3129;">
    <div class="row">
        <div class="header">
            <div class="text">在线代码编辑器</div>
        </div>
        <div class="editor">
            <div id="editor"><?php
                if($defaultCode){
echo htmlspecialchars("<?php".$defaultCode."\r?>");
                }else {
?>
&lt;?php

    $aa=60;
    echo 'hello '.'xxx'.$aa."\n";
    echo date('Y-m-d H:i:s',time())."\n";
    echo "PHP版本:".phpversion();

?&gt;
                <?php
                    }
                ?>
            </div>
            <div class="result">
                <div class="r-content">
                    <div>
                        <button style="cursor: pointer;" class="run">运行</button>
                        <button style="cursor: pointer;" class="json-format">Json格式化</button>
                        <button style="cursor: pointer;" class="html-format">Html</button>
                        <hr/>
                    </div>
                    <div class="r-text">
                    </div>
                </div>
            </div>
            <script src="<?php echo $dir_prefix; ?>/src-noconflict/jquery-2.0.3.min.js" type="text/javascript" charset="utf-8"></script>
            <script src="<?php echo $dir_prefix; ?>/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
            <script src="<?php echo $dir_prefix; ?>/src-noconflict/ext-language_tools.js" type="text/javascript" charset="utf-8"></script>
            <script>

                var editor = ace.edit("editor");
                editor.setOptions({
                    enableBasicAutocompletion: true,
                    enableSnippets: true,
                    enableLiveAutocompletion: true
                });
                editor.setTheme("ace/theme/monokai");
                editor.getSession().setMode("ace/mode/php");

                function myGetCode() {
                    var arr = [];
                    $('#editor .ace_line').each(function () {
                        arr.push($(this).text());
                    })

                    return arr;
                }
                
            </script>
            
            
            <script>
                var timer = '';
                var url = '<?php echo $uri.$queryString; ?>';
                var _DATA_ = '';
                $('.run').click(function () {
                    var code = myGetCode();

                    loading();
                    $.ajax({
                        url : url,
                        type : 'post',
                        data : {"code" : code, 'from' : 'js'},
                        success : function (data) {
                            _DATA_ = data;
                            loading(true);
                            $('.r-text').html(data);
                        },
                        dataType : 'html',
                        error : function (data) {
                            _DATA_ = data.responseText;
                            loading(true);
                            $('.r-text').html(_DATA_);
                        }
                    });
                });

                $('.json-format').click(function () {
                    try {
                        var str = '';
                        str = _DATA_.replace(/.*?<br\/><hr\/>/, '')
                            .replace(/&quot;/g, '"');

                        obj = eval('(' + str + ')');
                    }catch(e){
                        alert('返回结果为非Json字符串，无法转换');
                        return false;
                    }
                    $('.r-text').html(objtostr(obj));
                });

                $('.html-format').click(function () {
                    $('.r-text').html(_DATA_);
                });

                function loading(close) {
                    clearInterval(timer);
                    var str = '';
                    if(! close){
                        var t = 1;
                        str = '正在解析代码，请稍后';
                        $('.r-text').html(str);
                        timer = setInterval(function(){
                            var mod = t%4;
                            var prefix = '';
                            for(var i=0; i<mod; i++){
                                prefix += ' . ';
                            }

                            $('.r-text').html(str+prefix);
                            t++;
                        }, 1000)
                    }else{
                        $('.r-text').html('');
                    }
                }


                function objtostr(infoObj, level=1)
                {
                    var
                        str = '',
                        next_level = 0;

                    next_level = level;
                    next_level++;

                    if(typeof(infoObj) == 'object'){
                        str += '<br/>'+getindent(next_level)+'-------level'+level+'-----------<br/>';
                        for(var key in infoObj){
                            str += getindent(next_level)+key + ' : ' + objtostr(infoObj[key], next_level) +'<br/>';
                        }
                    }else{
                        str += infoObj;
                    }

                    //获取缩进
                    function getindent(le)
                    {
                        var indent = '';
                        for(var i=0; i<le*6; i++){
                            indent += '&nbsp;'
                        }

                        return indent;
                    }

                    return str;
                }
            </script>
        </div>
    </div>
</body>
</html>
