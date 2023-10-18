<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    
    private Dompdf $dompdf;

    public function __construct()
    {
        $this->dompdf = new Dompdf();
        
        $options = new Options();
        $options->set('defaultFont', 'Roboto');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('dompdf_dpi', '300');
        $options->set('A4', 'portrait');
        $options->set('isJavascriptEnabled', true);

        $this->dompdf->setOptions($options);
    }

    public function generate_pdf($html){
    
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();

        $this->dompdf->stream("testpdf.pdf", [
            "Attachment" => false
        ]);
    }
}