<?php

namespace util;

class Form
{
    /**
     * 百度编辑器
     * @param int $textareaid 字段名
     * @param int $toolbar 标准型 full 简洁型 basic
     */
    public static function editor($textareaid = 'content', $toolbar = 'full', $height = 500, $retainOnlyLabelPasted = 'true')
    {
        $string = "";
        //加载编辑器所需JS，多编辑器字段防止重复加载
        if (!defined('EDITOR_INIT')) {
            $string .= '
                <script type="text/javascript">
                var editorURL = "' . url('admin/ueditor/index') . '";
                </script>
                <script type="text/javascript" charset="utf-8" src="/static/ueditor/ueditor.config.js"></script>
                <script type="text/javascript" charset="utf-8" src="/static/ueditor/ueditor.all.min.js"></script>
                <script type="text/javascript" charset="utf-8" src="/static/ueditor/lang/zh-cn/zh-cn.js"></script>';
            define('EDITOR_INIT', 1);
        }
        //编辑器类型 图标工具栏标准和简洁
        if ($toolbar == 'basic') {
            $toolbar = "['fullscreen', 'Source', '|', 'Undo', 'Redo', '|','FontSize','Bold', 'forecolor', 'Italic', 'Underline', 'Link',  '|', 'simpleupload', 
                 'ClearDoc', 'CheckImage']";
        } elseif ($toolbar == 'full') {
            $toolbar = "['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', 'lineheight', '|', 'paragraph', 'fontsize', 'indent', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'link', 'unlink', 'simpleupload', 'insertimage', 'attachment', 'horizontal', 'inserttable']";
        } else {
            $toolbar = "[]";
        }
        $string .= "\r\n<script type=\"text/javascript\">\r\n";
        $string .= "var ue_{$textareaid} = UE.getEditor('{$textareaid}',{initialFrameHeight:{$height},retainOnlyLabelPasted:{$retainOnlyLabelPasted},autoHeightEnabled:false,toolbars:[$toolbar]});";
        $string .= "\r\n</script>";
        return $string;
    }


    /**
     * 图片上传
     * @param string $name 表单名称
     * @param string $multiple 是否多图片
     * @param string $fold 图片或文件
     */
    public static function upload($name, $multiple = 0, $fold = 0)
    {
        $ext = empty($fold) ? config('upload.image_ext') : config('upload.file_ext');
        $service = empty($fold) ? '?action=uploadimage' : '?action=uploadfile';
        $accept = empty($fold) ? 'images' : 'file';
        $multiSelection = empty($multiple) ? 'false' : 'true';
        
        $string = '<script>';

        $string .= "
                    layui.use(function(){
                        var $ = layui.$;
                        var upload = layui.upload;
                        var notify = layui.notify;
                        upload.render({
                            elem: '#" . $name . "',
                            url: '" . url('ueditor/index') . $service . "',
                            accept: '" . $accept . "',
                            exts: '" . $ext . "',
                            multiple: '" . $multiSelection . "',
                            done: function(res, index, upload){
                                if(res.code == 1){";
                    if ($multiple) {
                    $string .= "
                                    $(\"#" . $name . "List\").append('<span><img src=\"'+ res.url +'\"><input type=\"hidden\" name=\"" . $name . "[]\" value=\"'+ res.url +'\"><i class=\"layui-icon layui-icon-clear\"></i></span>');
                                    $(\"#" . $name . "List span i\").on(\"click\", function(){
                                        $(this).parent().remove();
                                    })
                                ";
                            } else {
                                $string .= "
                                    $(\"input[name=" . $name . "]\").val(res.url);
                                ";
                            }
                            $string .= "} else {
                                    notify.error(res.msg);
                                }
                            }
                        });";
                        if ($multiple) {
                        $string .= "$(\"#" . $name . "List span i\").on(\"click\", function(){
                            $(this).parent().remove();
                        })";
                        }
                    $string .= "});
                </script>
";
        return $string;
    }
}
