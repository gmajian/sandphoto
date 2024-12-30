<?php

/**
 * 图像处理类
 */
class Photo
{
    // 定义常量
    const CM_PER_INCH = 2.54; // 每英寸厘米数
    const DPI = 300; // 图像分辨率
    private $container_width; // 容器宽度
    private $container_height; // 容器高度
    private $im; // 图像资源
    private $target_width; // 目标宽度
    private $target_height; // 目标高度

    public function __construct()
    {
        $this->im = null;
    }

    /**
     * 设置容器尺寸
     * @param float $width 宽度(cm)
     * @param float $height 高度(cm)
     */
    public function set_container_size(float $width, float $height): void
    {
        $this->container_width = $this->get_pixel_from_CM($width);
        $this->container_height = $this->get_pixel_from_CM($height);
    }

    /**
     * 交换容器宽高
     */
    public function rotate_container(): void
    {
        $tmp = $this->container_width;
        $this->container_width = $this->container_height;
        $this->container_height = $tmp;
    }

    /**
     * 创建空白图像
     * @param string $bgcolorid 背景颜色ID
     */
    public function create_empty_image(string $bgcolorid): void
    {
        $this->im = imagecreatetruecolor($this->container_width, $this->container_height);
        $color = $this->get_color($bgcolorid);
        imageresolution($this->im, self::DPI);
        imagefilledrectangle($this->im, 0, 0, $this->container_width - 1, $this->container_height - 1, $color);

        $text = "www.nbfuli.com";
        $white = imagecolorallocate($this->im, 255, 255, 255);
        imagestring($this->im, 3, $this->container_width - strlen($text) * 7.5, $this->container_height - 20, $text, $white);
    }

    /**
     * 获取颜色值
     * @param string $bgcolorid 颜色ID
     * @return int 颜色值
     */
    private function get_color(string $bgcolorid): int
    {
        $color = match ($bgcolorid) {
            "white" => imagecolorallocate($this->im, 255, 255, 255),
            "blue" => imagecolorallocate($this->im, 0, 0, 120),
            "gray" => imagecolorallocate($this->im, 128, 128, 128),
            "red" => imagecolorallocate($this->im, 200, 0, 0),
            "green" => imagecolorallocate($this->im, 0, 100, 0),
            default => imagecolorallocate($this->im, 0, 0, 120),
        };
        return $color;
    }

    /**
     * 将单位从厘米转换为像素
     * @param float $value 厘米值
     * @return int 像素值
     */
    private function get_pixel_from_CM(float $value): int
    {
        $result = $value * self::DPI / self::CM_PER_INCH;
        return (int)floor($result);
    }

    /**
     * 设置目标尺寸
     * @param float $w 宽度(cm)
     * @param float $h 高度(cm)
     */
    public function set_target_size(float $w, float $h): void
    {
        $this->target_width = $this->get_pixel_from_CM($w);
        $this->target_height = $this->get_pixel_from_CM($h);
    }

    /**
     * 将指定路径的图片进行处理,并在容器中放置多个缩略图
     * @param string $path 图片路径
     * @param string $bgcolorid 背景颜色ID
     * @return int 缩略图数量
     */
    public function put_photo(string $path, string $bgcolorid = "blue"): int
    {
        $size = getimagesize($path);
        $w = $size[0];
        $h = $size[1];

        // 裁剪源图片
        $ratio1 = $w / $this->target_width;
        $ratio2 = $h / $this->target_height;
        if ($ratio1 > $ratio2) {
            $cut_w = ($this->target_width * $ratio2);
            $cut_h = $h;
            $cut_x = ($w - $cut_w) / 2;
            $cut_y = 0;
        } else {
            $cut_h = ($this->target_height * $ratio1);
            $cut_w = $w;
            $cut_y = ($h - $cut_h) / 2;
            $cut_x = 0;
        }

        $GAP = 5; // 缩略图间隔

        $wn = floor($this->container_width / ($this->target_width + $GAP));
        $hn = floor($this->container_height / ($this->target_height + $GAP));

        $wn2 = floor($this->container_height / ($this->target_width + $GAP));
        $hn2 = floor($this->container_width / ($this->target_height + $GAP));

        if ($wn2 * $hn2 > $wn * $hn) {
            $this->rotate_container();
            $wn = $wn2;
            $hn = $hn2;
        }

        $w_start = ($this->container_width - $wn * ($this->target_width + $GAP) + $GAP) / 2;
        $h_start = ($this->container_height - $hn * ($this->target_height + $GAP) + $GAP) / 2;
        $this->create_empty_image($bgcolorid);

        $im2 = $this->imagecreatefromany($path);
        $temp = imagecreatetruecolor($this->target_width, $this->target_height);
        imagecopyresampled($temp, $im2, 0, 0, $cut_x, $cut_y, $this->target_width, $this->target_height, $cut_w, $cut_h);

        for ($i = 0; $i < $wn; $i++) {
            $pos_x = $w_start + ($this->target_width + $GAP) * $i;
            for ($j = 0; $j < $hn; $j++) {
                $pos_y = $h_start + ($this->target_height + $GAP) * $j;
                imagecopy($this->im, $temp, $pos_x, $pos_y, 0, 0, $this->target_width, $this->target_height);
            }
        }

        imagedestroy($im2);
        imagedestroy($temp);
        return $wn * $hn;
    }

    /**
     * 输出图像
     */
    public function render_image(): void
    {
        header("Content-Type: image/jpeg");
        imagejpeg($this->im, null, 100);
    }

    /**
     * 下载图像
     * @param string $filename 文件名
     */
    public function download_image(string $filename): void
    {
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = urlencode($filename);
        header("Content-Type: application/octet-stream");
        if (preg_match("/MSIE/", $ua)) {
            header("Content-Disposition: attachment; filename=\"" . $encoded_filename . "\"");
        } else {
            header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        }
        imagejpeg($this->im, null, 100);
    }

    /**
     * 生成预览图像
     * @param string $filename 文件名
     */
    public function preview_image(string $filename): void
    {
        $width = 500;
        $height = 500;
        $ratio1 = $this->container_width / $width;
        $ratio2 = $this->container_height / $height;
        $ratio = ($ratio1 > $ratio2) ? $ratio1 : $ratio2;
        $w = floor($this->container_width / $ratio);
        $h = floor($this->container_height / $ratio);
        $im = imagecreatetruecolor($w, $h);
        imagecopyresampled($im, $this->im, 0, 0, 0, 0, $w, $h, $this->container_width, $this->container_height);
        header("Content-Type: image/png");
        imagepng($im, $filename);
        imagedestroy($im);
    }

    public function __destruct()
    {
        if ($this->im !== null) {
            imagedestroy($this->im);
        }
    }

    /**
     * 根据文件扩展名创建图像资源
     * @param string $filepath 文件路径
     * @return resource|bool 图像资源或false
     */
    public function imagecreatefromany(string $filepath)
    {
        $type = exif_imagetype($filepath);
        $allowedTypes = [
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_BMP,
        ];
        if (!in_array($type, $allowedTypes, true)) {
            return false;
        }
        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($filepath),
            IMAGETYPE_PNG => imagecreatefrompng($filepath),
            IMAGETYPE_BMP => imagecreatefrombmp($filepath),
            default => false,
        };
    }
}

/**
 * 图像类型解析器
 */
class PhotoTypeParser
{
    private $count; // 条目数
    private $name_array; // 名称数组
    private $width_array; // 宽度数组
    private $height_array; // 高度数组

    /**
     * 解析文件
     * @param string $path 文件路径
     */
    public function parse(string $path): void
    {
        $fh = fopen($path, "r");
        $this->count = 0;
        while (($line = fgets($fh)) !== false) {
            $data = explode(',', $line);
            if (count($data) !== 3) {
                continue;
            }

            $this->name_array[$this->count] = $data[0];
            $this->width_array[$this->count] = (float)$data[1];
            $this->height_array[$this->count] = (float)$data[2];
            $this->count++;
        }
        fclose($fh);
    }

    /**
     * 获取名称
     * @param int $i 索引
     * @return string 名称
     */
    public function get_name(int $i): string
    {
        return $this->name_array[$i];
    }

    /**
     * 获取宽度
     * @param int $i 索引
     * @return float 宽度
     */
    public function get_width(int $i): float
    {
        return $this->width_array[$i];
    }

    /**
     * 获取高度
     * @param int $i 索引
     * @return float 高度
     */
    public function get_height(int $i): float
    {
        return $this->height_array[$i];
    }

    /**
     * 渲染选择框
     * @param string $name 表单名称
     * @param float $min_size 最小尺寸
     * @param float $max_size 最大尺寸
     */
    public function render_select(string $name, float $min_size = 0, float $max_size = 100000): void
    {
        printf("<select name='%s' id='%s'>", $name, $name);
        for ($i = 0; $i < $this->count; $i++) {
            if ($this->width_array[$i] > $min_size && $this->width_array[$i] < $max_size) {
                printf(
                    "<option value='%d'>%s (%.2fcm * %.2fcm)</option>",
                    $i,
                    $this->name_array[$i],
                    $this->width_array[$i],
                    $this->height_array[$i]
                );
            }
        }
        print("</select>");
    }
}