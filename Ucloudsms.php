<?php

/*
 * 在ucloud官方的版本中，只有python的sdk可供调用，现提供php的sdk发送短信
 * @author newjueqi( http://blog.csdn.net/newjueqi )
 * 
 使用方法：
(1)在config.php中加入下面的配置
把$_ucloud_public_key,$_ucloud_private_key 替换为相应的key

(2)调用的方法如下

$ucloud=new ucloudsms();
$ucloud->sendSms("xxxxxxxx","hello"); //把"xxxxxxxx"替换为你的手机号

 * 
 */

include_once "Httpclient.php";

class ucloudsms
{
	private $_ucloud_public_key="";
	private $_ucloud_private_key="";	
	
	private $_param=array();
	
    function __construct()
    {
        $this->_param['public_key']=$this->_ucloud_public_key;
        $this->_param['region_id']=1;
        $this->_param['zone_id']=1;

    }
	
    /**
     * 生成access_token
     */
	private function verfy(  ){
	
		ksort($this->_param);
		$params_data = "";
		
		foreach( $this->_param as $key=>$value ){
			$params_data=$params_data.$key.$value;
		}
		
		$params_data = $params_data.$this->_ucloud_private_key;
		return sha1($params_data);
	}
	
	/**
	 * 对发送的内容进行处理
	 * @param unknown_type $content
	 */
	private function encodeContent($content){
		
		$content=urlencode($content);
		return str_replace("%", "\x", $content);		
	}
	
	
	/**
	 * 发送sms的方法
	 * @param unknown_type $mobile
	 * @param unknown_type $content
	 */
	public function sendSms($mobile=array(),$content){
		
		if( !is_array($mobile) ){
			$mobile=array($mobile);
		}
		
        $this->_param['phone']=json_encode($mobile);
        $this->_param['content']=$content;
        
        //获取access_token
        $this->_param['access_token']=$this->verfy();
        
        //对发送的内容进行处理
        $this->_param['content']= $this->encodeContent($content);
        
        $response = HttpClient::quickPost("http://api.ucloud.cn/monitor/sendsms", $this->_param); 
        
		if( $response['ret_code'] ){
			echo "send sms success";
			return true;
		}else{
			echo $response['ret_code'];
			return false;
		}		
	}
}

/* Location: */
