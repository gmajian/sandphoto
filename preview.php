<?php
function getParameter($name, $type = 'get', $default = null)
{
    $value = $default;

    switch ($type) {
        case 'post':
            if (isset($_POST[$name])) {
                $value = sanitizeInput($_POST[$name]);
            }
            break;
        case 'get':
            if (isset($_GET[$name])) {
                $value = sanitizeInput($_GET[$name]);
            }
            break;
        case 'input':
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            if (isset($data[$name])) {
                $value = sanitizeInput($data[$name]);
            }
            break;
        case 'delete':
            parse_str(file_get_contents('php://input'), $_DELETE);
            if (isset($_DELETE[$name])) {
                $value = sanitizeInput($_DELETE[$name]);
            }
            break;
        // 可以添加其他类型的参数处理，如cookie等

        default:
            break;
    }

    return $value;
}

function sanitizeInput($input)
{
    // 对输入进行必要的过滤和清理，例如使用filter_var()函数或其他过滤方式
    // 如果你有特定的安全需求，可以在此对输入进行更严格的验证和处理
    $sanitizedInput = trim($input);
    $sanitizedInput = stripslashes($sanitizedInput);
    $sanitizedInput = htmlspecialchars($sanitizedInput);

    return $sanitizedInput;
}

require_once("sandphoto.inc");

$temp_path = __DIR__ . '/temp';

$filename = $temp_path . '/sample.jpg';

$target_type = getParameter('t');
$container_type = getParameter('c');
$bgcolorid = getParameter('b');

$cacheFilename = "preview-" . $target_type . "-" . $container_type . "-" . $bgcolorid . ".png";
$cachePath = $temp_path . "/" . $cacheFilename;

if (!file_exists($cachePath)) {
    $parser = new PhotoTypeParser();
    $parser->parse('phototype.txt');
    $cw = $parser->get_width($container_type);
    $ch = $parser->get_height($container_type);
    $tw = $parser->get_width($target_type);
    $th = $parser->get_height($target_type);
    $p = new Photo();
    $p->set_container_size($cw, $ch);
    $p->set_target_size($tw, $th);
    $n = $p->put_photo($filename, $bgcolorid);
    $p->preview_image($cachePath);
    exec('/usr/bin/optipng ' . $cachePath . " >/dev/null 2>/dev/null");
}
header("location: temp/" . $cacheFilename);
exit();

?>
