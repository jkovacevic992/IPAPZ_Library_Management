<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/19/19
 * Time: 11:48 AM
 */

namespace App\Controller;

use App\Entity\Borrowed;
use App\Entity\BorrowedBooks;
use App\Entity\OnDeliveryTransaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OnDeliveryTransactionController extends AbstractController
{

    /**
     * @Route("/employee/invoice/{id}", name="invoice")
     * @param                           Borrowed $borrowed
     * @param                           EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function createInvoice(Borrowed $borrowed, EntityManagerInterface $entityManager)
    {


        $array =  self::onDeliveryTransaction($borrowed, $borrowed->getBorrowedBooks());
        $borrowed->setPaymentMethod('On Delivery');
        $entityManager->persist($borrowed);
        $entityManager->persist($array['onDeliveryTransaction']);
        $entityManager->flush();


        self::createDomPdf(
            $borrowed,
            $array['lateFee'],
            $array['onDeliveryTransaction'],
            $array['issueDate'],
            $entityManager
        );
        $this->addFlash('success', 'Invoice successfully generated!');
        return $this->redirectToRoute('borrowed_books');
    }

    public function createDomPdf(
        $borrowed,
        $lateFee,
        $onDeliveryTransaction,
        $issueDate,
        EntityManagerInterface $entityManager
    ) {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView(
            'pdf/mypdf.html.twig',
            [
                'title' => 'Something',
                'borrowed' => $borrowed,
                'lateFee' => $lateFee,
                'onDeliveryTransaction' => $onDeliveryTransaction,
                'issueDate' => $issueDate
            ]
        );

        $user =  $user = $entityManager->find(User::class, $borrowed->getUser());
        $pdfName = rand(1, 100) . $user->getId() . rand(1, 100000) . '.pdf';
        $domPdf->loadHtml($html);

        $domPdf->setPaper('A4', 'portrait');

        $domPdf->render();

        $output = $domPdf->output();

        file_put_contents('../public/pdf/'. $pdfName, $output);
    }

    /**
     * @Route("/employee/single_book_invoice/{id}/{borrowedBooks}", name="single_book_invoice")
     * @param BorrowedBooks $borrowedBooks
     * @param                           Borrowed $borrowed
     * @param                           EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function singleBookInvoice(
        Borrowed $borrowed,
        BorrowedBooks $borrowedBooks,
        EntityManagerInterface $entityManager
    ) {
        $borrowedBooks->setPaid(true);
        $array =  self::onDeliveryTransaction($borrowed, [1]);
        $entityManager->persist($borrowed);
        $entityManager->persist($borrowedBooks);
        $entityManager->persist($array['onDeliveryTransaction']);
        $entityManager->flush();

        self::createDomPdf(
            $borrowed,
            $array['lateFee'],
            $array['onDeliveryTransaction'],
            $array['issueDate'],
            $entityManager
        );
        $this->addFlash('success', 'Invoice successfully generated!');
        return $this->redirectToRoute('books_details', ['id' => $borrowed->getId()]);
    }

    public function onDeliveryTransaction(Borrowed $borrowed, $bookNumber)
    {

        $onDeliveryTransaction = new OnDeliveryTransaction();
        $issueDate = new \DateTime('now');
        $issueDate->format('d.M.yy');

        $time = $borrowed->getReturnDate();
        $timeDiff = date_diff(new \DateTime('now'), $time)->d;
        $lateFee = sprintf("%.2f", 0.00);
        if ($time < new \DateTime('now')) {
            $lateFee = sprintf("%.2f", $timeDiff * 0.5 * count($bookNumber));
        }

        $onDeliveryTransaction->setUser($borrowed->getUser());
        $onDeliveryTransaction->setComplete(true);
        $onDeliveryTransaction->setAmount($lateFee);

        return ['lateFee' => $lateFee, 'issueDate' => $issueDate, 'onDeliveryTransaction' => $onDeliveryTransaction];
    }

    /**
     * @Route("/employee/see_invoice/{id}", name="see_invoice")
     * @param Borrowed $borrowed
     * @throws \Exception
     */
    public function seeInvoice(Borrowed $borrowed)
    {
        $time = $borrowed->getReturnDate();
        $timeDiff =date_diff(new \DateTime('now'), $time)->d;
        $lateFee = sprintf("%.2f", 0.00);
        if ($time < new \DateTime('now')) {
            $lateFee = sprintf("%.2f", $timeDiff * 0.5 * count($borrowed->getBorrowedBooks()));
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView(
            'pdf/invoice.html.twig', [
            'title' => 'Something',
            'borrowed' => $borrowed,
            'lateFee' => $lateFee
            ]
        );
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream(
            "mypdf.pdf", [
            'Attachment' => false
            ]
        );
    }
}
