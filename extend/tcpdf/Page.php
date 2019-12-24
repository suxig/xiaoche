<?php

namespace tcpdf;

/**
 * PDF生成
 *
 * @author chunkuan<urcn@qq.com>
 * 
 */
class Page {

    private $font = 'droidsansfallback';

    public function __construct($page_title = '') {
        vendor('tcpdf.config.tcpdf_config');
        vendor('tcpdf.tcpdf');
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR); //设置创建者
        $pdf->SetAuthor('Geticsen'); //作者
//        $pdf->SetTitle('TCPDF Example 038'); //标题
//        $pdf->SetSubject('TCPDF Tutorial');
//        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        $pdf->SetHeaderData('../../public/images/logo.png', 40, $page_title, 'senlear社区',[0,0,0],[200,200,200]);
        $pdf->setHeaderFont(Array($this->font, '', 10));
        $pdf->SetFooterData([0,0,0],[200,200,200]);
        $pdf->setFooterFont(Array($this->font, '', 10));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, PDF_MARGIN_TOP, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->setLanguageArray([
            'a_meta_charset' => 'UTF-8',
            'a_meta_dir' => 'ltr',
            'a_meta_language' => 'en',
            'w_page' => 'page'
        ]);

        // set font
        $pdf->SetFont($this->font, '', 10);
        // add a page
        $pdf->AddPage();
        $this->pdf = $pdf;
    }

    /**
     * 设置html
     */
    public function html($html) {
        $this->pdf->SetFont($this->font, '', 12);
        $this->pdf->writeHTML($html, true, 0, true, true);
    }

    /**
     * 下载pdf
     */
    public function down($filename = '') {
        empty($filename) && $filename = time().'.pdf';
        $this->pdf->Output($filename, 'D');
        exit;
    }

}
