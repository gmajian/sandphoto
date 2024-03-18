<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>证件照片排版工具</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="sandphoto.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        form {
            margin-top: 20px;
        }

        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<p>
    是不是觉得冲印证件照片特别贵？教你一个省钱的方法：在一张6寸的照片上排版多张证件照，然后只要花几角钱就能洗出一堆证件照片了。
    怎么？不会使用Photoshop排版？懒得去排版？没关系，我也是懒得每次用Photoshop排版了，就自己写了个程序，你来试用一下吧！
</p>

<form id="sandphotoform" action="/sandphoto.php" method="POST" enctype="multipart/form-data">
    <strong>第一步</strong>, 选择你要冲洗的证件照片的尺寸：
    <?php
    require_once('sandphoto.inc');
    $parser = new PhotoTypeParser();
    $parser->parse('phototype.txt');
    $parser->render_select('target_type', 0, 8);
    echo '<br>';
    print('<strong>第二步</strong>, 选择用多大的照片冲洗（一般选择6寸的就好，这个价格最合适）:');
    $parser->render_select('container_type', 8);
    ?>

    <br>
    <strong>第三步</strong>, 选择分割线的颜色：

    <div>
        <input id="bgcolorid" type="radio" name="bgcolorid" value="blue" checked> 蓝色
        <input id="bgcolorid" type="radio" name="bgcolorid" value="white"> 白色
        <input id="bgcolorid" type="radio" name="bgcolorid" value="gray"> 灰色
    </div>

    将来的照片就是这个样子的：<br>

    <img id="previewImg" alt="预览图像">
    <br>
    <strong>第四步</strong>, 选择你的证件照片：

    <input id="filename" type="file" name="filename" accept="image/*" required>

    <br>
    <strong>最后一步</strong>, 点击下载，照片就可以去冲印了：

    <input type="submit" value="下载">

    <p>如果你的照片比较大，上传会花一些时间，别着急，请耐心等待。</p>
</form>
</body>
</html>