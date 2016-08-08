<?php
  include_once('clsHelp.php');

class clsMatrix{
    private $originData;
    private $resultData;
    
    function __construct(){
    }
    function __destruct(){
        
    }

    
    function copyData($origin)
    {
        //複製資料 
        $this->originData=$origin;   
        //初始化結果
        $this ->resultData =new ArrayObject($this->originData);
        //重新設定資料
        $this->resetMatrix($this ->resultData ,0);
    }
    
    //core
    function process(){

        //建立暫存結果
        /*EX:
        input
            1 1 0 0 0
            0 0 0 1 1
            0 0 1 1 1 
        output
            2 2 0 0 0
            0 0 0 5 5
            0 0 5 5 5 
        */

        $tempData =$this->genSegmentMatrix($this->originData);

        //掃描最大值
        /*
        input
            2 2 0 0 0
            0 0 0 5 5
            0 0 5 5 5 
        output
            5
        */
        $maxValue =$this->getMaxValue($tempData);
            
        //產生結果
        /*
         input
            2 2 0 0 0
            0 0 0 5 5
            0 0 5 5 5 
            -----5
        
         output
            0 0 0 0 0
            0 0 0 1 1
            0 0 1 1 1 
         
        */
        $this->resultData=$this->genResultMatrix($tempData,$maxValue);

        }

    //region SURRPOT FOR CORE [beg]

        //取得分割的結果
        function genSegmentMatrix($Matrix){
           
            list($height,$width)=clsHelp::GetMatrixSize($Matrix);

            //掃描整個Matrix
            for($h=0;$h<$height;$h++){
                for($w=0;$w<$width;$w++){
                    if($Matrix[$h][$w]==1){
                        //轉換為1D的座標
                        $index= clsHelp::ConvertPositionToIndex($Matrix,$h,$w);
                        //取得Patch (區域的索引)
                        $patchIndex=$this->getPatchIndex($Matrix,$index);
                        //設定資料
                        $Matrix=$this->setPatch($Matrix,$patchIndex,count($patchIndex));
                    }
                }
            }
            return $Matrix;
        }

        function setPatch($Matrix,$patchIndex,$value){
            //取得長寬
            list($height,$width)=clsHelp::GetMatrixSize($Matrix);
            foreach ($patchIndex as $index) {
                //取得座標值
                list($h,$w) = clsHelp::ConvertIndexToPosition($Matrix,$index);
                //強制更新數據
                $Matrix[$h][$w]= $value;
            }
            return $Matrix;
        }

        //取得Patch
        function getPatchIndex($Matrix,$index){            
            $Patch = array();//已經走過的資料
            $QueueRing= array(); //柱列,準備走訪的內容
            array_push($QueueRing, $index);//save self; init
            do{
                //echo 'processing...<br/>';//for debug
                //依據目前的Ring取得下一層的Ring 
                $QueueRing = $this->getRing($Matrix,$QueueRing);  //以ring 取得
                if(count($QueueRing)>0){
                    //將新取得的內容 與 已走訪的內容做 [差集]
                    $QueueRing = array_diff($QueueRing, $Patch); 
                    //因為差集運算會導致部分Index重編，故以sort重新排序
                    sort($QueueRing); //DO NOT REMOVE
                    //將目前的Ring合併到已走訪的內容中 [聯結]
                    $Patch=array_merge($Patch,$QueueRing);
                    //刪除重複的key值
                    $Patch=array_unique($Patch);
                    //print_r($QueueRing);echo "<br/>"; //for debug 
                }
            }while(count($QueueRing)>0);
            //結束回圈，重新排序後
            sort($Patch);
            return $Patch;
        }
        //取得環狀
        function getRing($Matrix,$ring){
            $r = array();
            foreach ($ring as $index) {                
               //依序取得ring-list 中的鄰居，並產生聯集
               $r=array_merge($r,$obj= $this->getNeighbor($Matrix,$index));
            }           
            return array_unique($r); //刪除重複的內容
        }
        //取得鄰居資料(index)
        function getNeighbor($Matrix,$index){
            $r = array();
            //將index 轉換為 h,w 座標
            list($h,$w) = clsHelp::ConvertIndexToPosition($Matrix,$index);
            //取得array 的極限
            list($height,$width)=clsHelp::GetMatrixSize($Matrix);
            //檢察另據的值是否為1
            //up
            if($h>0 && $Matrix[$h-1][$w]==1){
                array_push($r, clsHelp::ConvertPositionToIndex($Matrix,$h-1,$w));
            }
            //down
            if($h<$height-1 && $Matrix[$h+1][$w]==1){
                array_push($r, clsHelp::ConvertPositionToIndex($Matrix,$h+1,$w));
            }
            //left
            if($w>0 && $Matrix[$h][$w-1]==1){
                array_push($r, clsHelp::ConvertPositionToIndex($Matrix,$h,$w-1));
            }
            if($w<$width-1 && $Matrix[$h][$w+1]==1){
                array_push($r, clsHelp::ConvertPositionToIndex($Matrix,$h,$w+1));
            }
            //回傳資料
            return $r;
        }

     

    //找出最大的資料
    function getMaxValue($temp){
        $max = -1;  //給一個最小值
        list($height,$width)=clsHelp::GetMatrixSize($temp);
        //掃描矩陣;
        for($h=0;$h<$height;$h++){
            for($w=0;$w<$width;$w++){
                if($temp[$h][$w]>$max){
                    $max= $temp[$h][$w];
                }
            }//end for for-loop ($w)
        }//end for  for-loop ($h)

        return $max;
    }

    //產生結果矩陣 
    function genResultMatrix($temp,$max){

        $result = new ArrayObject($temp);  //實體複製        
        $this->resetMatrix($result ,0);    //重設資料

        list($height,$width)=clsHelp::GetMatrixSize($temp);

         //計算
        for($h=0;$h<$height;$h++){
           for($w=0;$w<$width;$w++){
            if($temp[$h][$w]==$max){
                $result[$h][$w] = 1;
            }
             }//end for for-loop ($w)
         }//end for  for-loop ($h)
         
         return $result;
     }

     //直接顯示結果
    function drawResult(){
        $this->drawMatrix($this->resultData);
    }

    /*SUPPORT FUNCTION*/
     

    //重新設定整個矩陣
    function resetMatrix($Matrix,$value){
        //計算寬高
        list($height,$width)=clsHelp::GetMatrixSize($Matrix);

        for($h=0;$h<$height;$h++){
           for($w=0;$w<$width;$w++){
            $Matrix[$h][$w]=$value;
           }  
        }

        return $Matrix;
    }

    //繪製HTML的顯示資料
    function drawMatrix($Matrix){
        list($height,$width)=clsHelp::GetMatrixSize($Matrix);

        for($h=0;$h<$height;$h++){
           for($w=0;$w<$width;$w++){
               echo $Matrix [$h][$w]."&nbsp;&nbsp;&nbsp;&nbsp;";
             }//劃出寬
             echo "<br/>";   
        }//先劃出高
     }

}


?>