<?php

namespace App\Service;

use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;

class PDFGeneratorService
{
    private DompdfFactoryInterface $dompdfFactory;

    public function __construct(DompdfFactoryInterface $dompdfFactory)
    {
        $this->dompdfFactory = $dompdfFactory;
    }

    public function output(string $html): string
    {
        $dompdf = $this->dompdfFactory->create();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}