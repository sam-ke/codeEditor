# codeEditor 可任意嵌入的PHP在线代码编辑器
文本编辑器采用开源公共组件，为了节省空间只保留了php语言解析
其他语言，通过如下链接可获取
https://github.com/ajaxorg/ace-builds/tree/master/src-min-noconflict

# 接入方式

```php

    /**
    * 在任何一个PHP框架的控制器中，添加如下Action
    * 按照框架规则访问 URL?_token=sdkfdaaafnJHUqoa 即可渲染出编辑器
    */
    public function runcodeAction()
    {
        if($token = $_REQUEST['_token'] == 'sdkfdaaafnJHUqoa'){
        
                //如果是非，自动加载，您需要自行require 类库文件ce.php
                $ce = new Leb_plugin_codeEditor_ce();
                $ce->show();
            }else{
                dd('未授权');
        }
    }
```

# 效果

![plot](./images/editor.jpg)