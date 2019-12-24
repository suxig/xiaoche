<?php
namespace excel;

/**
 * phpexcel 数据导入
 *
 * @author chunkuan<urcn@qq.com>
 */
class Import
{
    
    public $objPHPExcel;
    private $fileTruePath;


    /**
     * 初始化
     */
    public function __construct()
    {
        vendor('PHPExcel.PHPExcel');
        Vendor("PHPExcel.PHPExcel.IOFactory");
        $this->objPHPExcel = new \PHPExcel();
    }
    
    public function read($file_path)
    {
        $this->fileTruePath = '.' . $file_path;
        
        if (!file_exists($this->fileTruePath)) {
            return ['error' => 1, 'info' => '文件不存在'];
        }
        
         //读取本地文件
        $type = strtolower(pathinfo($this->fileTruePath, PATHINFO_EXTENSION));
        if ($type == 'xlsx' || $type == 'xls') {
            $excelData = self::readExcel();
        } elseif ($type == 'csv') {
            $excelData = self::readCsv();
        }
        return $excelData;
    }
    
    /**
     * 读取Excel文件（xls,xlsx）
     */
    protected function readExcel()
    {
        $PHPReader = \PHPExcel_IOFactory::createReader('Excel2007');
        if (!$PHPReader->canRead($this->fileTruePath)) {
            $PHPReader = \PHPExcel_IOFactory::createReader('Excel5');
            if (!$PHPReader->canRead($this->fileTruePath)) {
                return ['error' => 1, 'info' => '当前文件里没有信息'];
            }
        }
        $PHPReader->setReadDataOnly(true);
        $objPHPExcel = $PHPReader->load($this->fileTruePath);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex =  \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] = (string) $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }
    
    /**
     * 读取CSV文件（csv）
     */
    protected function readCsv()
    {
        $PHPReader = \PHPExcel_IOFactory::createReader('CSV')
                ->setDelimiter(',')
                ->setInputEncoding('GBK')
                ->setEnclosure('"')
               // ->setLineEnding("\r\n")
                ->setSheetIndex(0);

        $objPHPExcel = $PHPReader->load($this->fileTruePath);
        $excel_data = $objPHPExcel->getSheet()->toArray();
        $excelData = [];
        for ($row = 0; $row < count($excel_data); $row++) {
            $excelData[$row+1] = $excel_data[$row];
        }
        return $excelData;
    }
    
}
