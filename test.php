<html>
<head>
<meta charset="UTF-8">
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="sandphoto.js"></script>
</head>
<body>

<p>
是不是觉得冲印证件照片特别贵？ 教你一个省钱的方法： 在一张6寸的照片上排版多张证件照， 然后只要花几角钱就能洗出一堆证件照片了。
怎么？ 不会使用Photoshop排版？ 懒得去排版？ 没关系， 我也是懒得每次用Photoshop排版了， 就自己写了个程序，你来试用一下吧！

</p>

<form id="sandphotoform" action="/sandphoto/sandphoto.php" method="POST" enctype="multipart/form-data"><strong>第一步</strong>, 选择你要冲洗的证件照片的尺寸：
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

<input id="bgcolorid" type="radio" name="bgcolorid" value="blue" checked="checked" /> 蓝色 <input id="bgcolorid" type="radio" name="bgcolorid" value="white" />白色 <input id="bgcolorid" type="radio" name="bgcolorid" value="gray" /> 灰色


将来的照片就是这个样子的：<br>

<img id="previewImg" alt="" />
<br>
<strong>第四步</strong>, 选择你的证件照片：

<input id="filename" type="file" name="filename" />

<br>
<strong>最后一步</strong>, 点击下载，照片就可以去冲印了：

<input type="submit" value="下载" />

如果你的照片比较大， 上传会花一些时间， 别着急， 请耐心等待。

</form>如果大家有什么建议，请留言。 也欢迎帮助宣传本站。 本站的地址是：<a href="http://www.sandcomp.com/blog/sandphoto/">http://www.sandcomp.com/blog/sandphoto/</a>
</body>
</html>
