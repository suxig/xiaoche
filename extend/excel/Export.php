<?php

namespace excel;

/**
 * phpexcel 数据导出
 *
 * @author chunkuan<urcn@qq.com>
 */
class Export{

    private $objWriter;
    public $objPHPExcel;
    public $chars = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    /**
     * 初始化
     */
    public function __construct(){
        vendor('PHPExcel.PHPExcel');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $this->objPHPExcel = new \PHPExcel();
        $this->objWriter = new \PHPExcel_Writer_Excel5($this->objPHPExcel);
    }

    /**
     * 按照二维数组设置值
     */
    public function data($array = []){
        foreach($array as $key => $arr){
            foreach($arr as $k => $v){
                $this->objPHPExcel->getActiveSheet()->setCellValue($this->chars[$k] . ($key + 1), $v);
            }
        }
    }

    /**
     * 保存下载文件
     */
    public function save($file_name = ""){
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename=' . ($file_name? : '文件名称') . '.xls');
        header("Content-Transfer-Encoding:binary");
      // $this->objWriter->save(($file_name? : '文件名称') . '.xls');
        $this->objWriter->save('php://output');
    }

}
