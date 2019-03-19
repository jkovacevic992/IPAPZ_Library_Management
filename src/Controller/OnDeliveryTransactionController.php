<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/19/19
 * Time: 11:48 AM
 */

namespace App\Controller;


use App\Entity\Borrowed;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OnDeliveryTransactionController extends AbstractController
{

    /**
     * @Route("/employee/invoice/{id}", name="invoice")
     * @param Borrowed $borrowed
     *
     */
    public function createInvoice(Borrowed $borrowed)
    {

        $time = $borrowed->getReturnDate();
        $timeDiff =date_diff(new \DateTime('now'), $time)->d;
        $lateFee = sprintf("%.2f",0.00);
        if($time < new \DateTime('now')) {
            $lateFee = sprintf("%.2f", $timeDiff * 0.5 * count($borrowed->getBorrowedBooks()));
        }
            $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','Arial');

        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('pdf/mypdf.html.twig',[
            'title' => 'Something',
            'borrowed' => $borrowed,
            'lateFee' => $lateFee
        ]);

        $domPdf->loadHtml($html);

        $domPdf->setPaper('A4','portrait');

        $domPdf->render();

        $domPdf->stream("mypdf.pdf",[
            'Attachment' => false
        ]);

    }
}