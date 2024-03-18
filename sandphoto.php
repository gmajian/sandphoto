<?php
require_once("sandphoto.inc");
// 检查上传的文件类型和大小
$allowedTypes = ["image/png", "image/jpeg", "image/jpg", "image/bmp", "image/tiff", "image/pjpeg"];
$maxFileSize = 8000000;

if (isset($_FILES["filename"]["type"]) && in_array($_FILES["filename"]["type"], $allowedTypes)
    && isset($_FILES["filename"]["size"]) && $_FILES["filename"]["size"] < $maxFileSize) {

    if ($_FILES["filename"]["error"] > 0) {
        header("Content-Type: text/html");
        print("上传文件错误!");
        return;
    } else {
        $filename = $_FILES["filename"]["tmp_name"];

        // 获取目标类型和容器类型
        $targetType = isset($_POST["target_type"]) ? $_POST["target_type"] : "";
        $containerType = isset($_POST["container_type"]) ? $_POST["container_type"] : "";

        // 进行类型解析
        $parser = new PhotoTypeParser();
        $parser->parse('phototype.txt');

        // 获取容器和目标尺寸
        $cw = $parser->get_width($containerType);
        $ch = $parser->get_height($containerType);
        $tw = $parser->get_width($targetType);
        $th = $parser->get_height($targetType);

        // 创建 Photo 对象并设置尺寸
        $p = new Photo();
        $p->set_container_size($cw, $ch);
        $p->set_target_size($tw, $th);

        // 处理图片并生成缩略图
        $n = $p->put_photo($filename, isset($_POST["bgcolorid"]) ? $_POST["bgcolorid"] : "blue");

        // 渲染图像和预览图像
        // $p->render_image();
        // $p->preview_image($filename);

        // 下载图像
        $downloadName = $n . "张" . $parser->get_name($targetType) . "[以" . $parser->get_name($containerType) . "冲洗].jpg";
        $p->download_image($downloadName);
    }
}

?>
