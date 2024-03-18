$(document).ready(function () {
    $("#target_type, #container_type, #sandphotoform input[type='radio']").on("change click", updatePreview);
    $("#sandphotoform").on("submit", checkForm);
    updatePreview();
});

function updatePreview() {
    const $targetType = $("#target_type option:selected").val();
    const $containerType = $("#container_type option:selected").val();
    const $bgcolorid = $("#sandphotoform input[type='radio']:checked").val();

    if ($targetType && $containerType && $bgcolorid) {
        const previewUrl = `/preview.php?t=${$targetType}&c=${$containerType}&b=${$bgcolorid}`;
        $("#previewImg").attr("src", previewUrl);
    }
}

function checkForm() {
    const fileInput = $("#filename")[0];

    if (!fileInput.files.length) {
        alert("请选择照片后再试");
        return false;
    }

    const file = fileInput.files[0];
    const allowedTypes = /\.(jpe?g|png|tiff?)$/i;

    if (!allowedTypes.test(file.name)) {
        alert("只支持 JPEG, JPG, PNG, TIFF 文件格式");
        return false;
    }

    return true;
}