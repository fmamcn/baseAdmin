<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Image;

class Ueditor extends AdminBase
{
	protected $noAuth = [
        'index',
        'uploadImage',
        'uploadFile',
        'saveRemote',
        'upBase64'
    ];

    protected function _initialize()
    {
        parent::_initialize();
    }

	public function index(){

        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./static/ueditor/config.json")), true);
        $action = $_GET['action'];

        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG);
                break;
            /* 上传图片 */
            case 'uploadimage':
		        $fieldName = $CONFIG['imageFieldName'];
		        $result = $this->uploadImage();
		        break;
            /* 上传文件 */
            case 'uploadfile':
		        $fieldName = $CONFIG['fileFieldName'];
		        $result = $this->uploadFile();
                break;
            case 'upbase64':
		        $fieldName = $CONFIG['fileFieldName'];
		        $result = $this->upBase64();
                break;
            /* 抓取远程文件 */
            case 'catchimage':
		    	$config = array(
			        "pathFormat" => $CONFIG['catcherPathFormat'],
			        "maxSize" => $CONFIG['catcherMaxSize'],
			        "allowFiles" => $CONFIG['catcherAllowFiles'],
			        "oriName" => "remote.png"
			    );
			    $fieldName = $CONFIG['catcherFieldName'];
			    /* 抓取远程图片 */
			    $list = array();
			    isset($_POST[$fieldName]) ? $source = $_POST[$fieldName] : $source = $_GET[$fieldName];
				
			    foreach($source as $imgUrl){
			        $info = json_decode($this->saveRemote($config,$imgUrl),true);
			        array_push($list, array(
			            "state" => $info["state"],
			            "url" => $info["url"],
			            "size" => $info["size"],
			            "title" => htmlspecialchars($info["title"]),
			            "original" => htmlspecialchars($info["original"]),
			            "source" => htmlspecialchars($imgUrl)
			        ));
			    }

			    $result = json_encode(array(
			        'state' => count($list) ? 'SUCCESS':'ERROR',
			        'list' => $list
			    ));
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if(isset($_GET["callback"])){
            if(preg_match("/^[\w_]+$/", $_GET["callback"])){
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            }else{
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        }else{
            echo $result;
        }
	}

    public function uploadImage()
    {
        try {
            $file = $this->request->file('file');
            $file_date = date('Ymd', time());
            $upload_image = unserialize(config('upload_image'));
            $info = $file->validate(['size' => $upload_image['upload_size'] * 1024 * 1024, 'ext' => 'jpg,jpeg,png,gif'])->rule('uniqid')->move(ROOT_PATH . 'web' . DS . 'uploads' . DS . 'image' . DS . $file_date);
            if ($info) {                
                return json_encode(['code' => 1, 'state' => 'SUCCESS', 'title' => $info->getInfo('name'), 'url' => '/uploads/image/' . $file_date . '/' . str_replace('\\', '/', $info->getSaveName())]);
            } else {
                return json_encode(['code' => 0, 'state' => $file->getMessage(), 'msg' => $file->getError()]);
            }
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'state' => $e->getMessage(), 'msg' => $e->getMessage()]);
        }
    }

    public function uploadFile()
    {
        try {
            $upload_file = unserialize(config('upload_image'));
            $file = $this->request->file('file');
            $file_date = date('Ymd', time());
            $info = $file->validate(['size'=>$upload_file['upload_size'] * 1024 * 1024,'ext' => $upload_file['upload_ext']])->rule('uniqid')->move(ROOT_PATH . 'web' . DS . 'uploads' . DS . 'file' . DS . $file_date);
            if ($info) {
                return json_encode(['code' => 1, 'state' => 'SUCCESS', 'title' => $info->getInfo('name'), 'url' => '/uploads/file/' . $file_date . '/' . str_replace('\\', '/', $info->getSaveName())]);
            } else {
                return json_encode(['code' => 0, 'state' => $file->getMessage(), 'msg' => $file->getError()]);
            }
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'state' => $e->getMessage(), 'msg' => $e->getMessage()]);
        }
    }

    //抓取远程图片
	private function saveRemote($config, $fieldName){
	    $imgUrl = htmlspecialchars($fieldName);
	    $imgUrl = str_replace("&amp;","&",$imgUrl);

	    //http开头验证
	    if(strpos($imgUrl,"http") !== 0){
	        $data=array(
		        'state' => '链接不是http链接',
		    );
	        return json_encode($data);
	    }
	    //获取请求头并检测死链
	    $heads = get_headers($imgUrl);
	    if(!(stristr($heads[0],"200") && stristr($heads[0],"OK"))){
	        $data=array(
		        'state' => '链接不可用',
		    );
	        return json_encode($data);
	    }
	    //格式验证(扩展名验证和Content-Type验证)
	    $fileType = strtolower(strrchr($imgUrl,'.'));
	    if(!in_array($fileType,$config['allowFiles']) || stristr($heads['Content-Type'],"image")){
	        $data=array(
		        'state' => '链接contentType不正确',
		    );
	        return json_encode($data);
	    }

	    //打开输出缓冲区并获取远程图片
	    ob_start();
	    $context = stream_context_create(
	        array('http' => array(
	            'follow_location' => false // don't follow redirects
	        ))
	    );
	    readfile($imgUrl,false,$context);
	    $img = ob_get_contents();
	    ob_end_clean();
	    preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);

	    $dirname = './uploads/image/' . date('Ymd', time()) . '/';
	    $file['oriName'] = $m ? $m[1] : "";
	    $file['filesize'] = strlen($img);
	    $file['ext'] = strtolower(strrchr($config['oriName'],'.'));
	    $file['name'] = uniqid() . $file['ext'];
	    $file['fullName'] = $dirname . $file['name'];
	    $fullName = $file['fullName'];

	    //检查文件大小是否超出限制
	    if($file['filesize'] >= ($config["maxSize"])){
  		    $data=array(
			    'state' => '文件大小超出网站限制',
		    );
		    return json_encode($data);
	    }

	    //创建目录失败
	    if(!file_exists($dirname) && !mkdir($dirname, 0777, true)){
  		    $data=array(
			    'state' => '目录创建失败',
		    );
		    return json_encode($data);
	    }else if(!is_writeable($dirname)){
  		    $data=array(
			    'state' => '目录没有写权限',
		    );
		    return json_encode($data);
	    }

	    //移动文件
	    if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
  		    $data=array(
			    'state' => '写入文件内容错误',
		    );
		    return json_encode($data);
	    }else{ 
            //移动成功
	        $data=array(
			    'state' => 'SUCCESS',
			    'url' => substr($file['fullName'],1),
			    'title' => $file['name'],
			    'original' => $file['oriName'],
			    'type' => $file['ext'],
			    'size' => $file['filesize'],
		    );
	    }		
	    return json_encode($data);
	}

    /*
	 * 处理base64编码的图片上传
	 * 例如：涂鸦图片上传
	*/
	private function upBase64($config,$fieldName){
	    $base64Data = $_POST[$fieldName];
	    $img = base64_decode($base64Data);

	    $dirname = './uploads/image/' . date('Ymd', time()) . '/';
	    $file['filesize'] = strlen($img);
	    $file['oriName'] = $config['oriName'];
	    $file['ext'] = strtolower(strrchr($config['oriName'], '.'));
	    $file['name'] = uniqid() . $file['ext'];
	    $file['fullName'] = $dirname . $file['name'];
	    $fullName = $file['fullName'];

 	    //检查文件大小是否超出限制
	    if($file['filesize'] >= ($config["maxSize"])){
  		    $data=array(
			    'state' => '文件大小超出网站限制',
		    );
		    return json_encode($data);
	    }

	    //创建目录失败
	    if(!file_exists($dirname) && !mkdir($dirname, 0777, true)){
	        $data=array(
			    'state' => '目录创建失败',
		    );
		    return json_encode($data);
	    }else if(!is_writeable($dirname)){
	        $data=array(
			    'state' => '目录没有写权限',
		    );
		    return json_encode($data);
	    }

	    //移动文件
	    if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
            $data=array(
		        'state' => '写入文件内容错误',
		    );
	    }else{
            //移动成功	       
	        $data=array(
			    'state' => 'SUCCESS',
			    'url' => substr($file['fullName'],1),
			    'title' => $file['name'],
			    'original' => $file['oriName'],
			    'type' => $file['ext'],
			    'size' => $file['filesize'],
		    );
	    }
		
	    return json_encode($data);
	}
	
}
