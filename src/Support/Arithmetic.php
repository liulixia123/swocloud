<?php
namespace SwoCloud\Support;
class Arithmetic
{
	protected static $roundLastIndex = 0;
	/**
	* 轮询算法
	* 
	*/
	public static function round(array $list){
		$currentIndex = self::$roundLastIndex;//当前index
		$url = $list[$currentIndex];
		if($currentIndex + 1 > count($list) - 1){
		self::$roundLastIndex=0;
		}else{
		self::$roundLastIndex++;
		}
		return $url; //返回当前url
	}
	/**
	* 随机算法
	* 
	*/
	public static function random()
	{
	}
	// ... 其他算法
}
?>