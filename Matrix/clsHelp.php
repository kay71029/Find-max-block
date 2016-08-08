<?php

	class clsHelp{

		//1D -> 2D 
		public static function ConvertIndexToPosition($arrayData,$index){
	        $w = $index%sizeof($arrayData[0]);//取餘數
	        $h = floor($index/sizeof($arrayData[0]));  //無條件捨去(固定公式)
	        return array($h,$w);
	    }
	    
	    //2D -> 1D
	    public static function ConvertPositionToIndex($arrayData,$h,$w){
	        return $h*sizeof($arrayData[0])+$w;       //-1 for index fix 寬度
	    }

        //取得長寬 (格式[h,w])
	    public static function GetMatrixSize($arrayData){
	    	//[h,w]
	    	return array(sizeof($arrayData),sizeof($arrayData[0]));
	    }

	}
?>