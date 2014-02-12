<?php
include("sandphoto.inc");


if ((($_FILES["filename"]["type"] == "image/png")
|| ($_FILES["filename"]["type"] == "image/jpeg")
|| ($_FILES["filename"]["type"] == "image/bmp")
|| ($_FILES["filename"]["type"] == "image/tiff")
|| ($_FILES["filename"]["type"] == "image/pjpeg"))
&& ($_FILES["filename"]["size"] < 8000000))
{
	if ($_FILES["filename"]["error"] > 0)
	{
		header("Content-Type: text/html");
		print("上传文件错误!");
		return;
	}
	else
	{
		$filename = $_FILES["filename"]["tmp_name"];

		$target_type = $_POST["target_type"];
		$container_type = $_POST["container_type"];
		//print "type:$container_type";
		$parser = new PhotoTypeParser();
		$parser->parse('phototype.txt');
		$cw = $parser->get_width($container_type);
		$ch = $parser->get_height($container_type);
		$tw = $parser->get_width($target_type);
		$th = $parser->get_height($target_type);
		$p = new Photo();
		$p->set_container_size($cw, $ch);
		$p->set_target_size($tw, $th);
		$n = $p->put_photo($filename, $_POST["bgcolorid"]);
		// $p->render_image();
		// $p->preview_image();
		$download_name = $n ."张" . $parser->get_name($target_type) . "[以" . $parser->get_name($container_type) . "冲洗].jpg";
		$p->download_image( $download_name );

	}
}


?>
