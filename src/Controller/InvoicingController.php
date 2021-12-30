<?php

namespace App\Controller;

use App\Input\InputKeys;
use App\Input\InvoicingInput;
use App\Model\Invoice;
use App\Model\InvoiceCalculator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_invoicing")
 */
class InvoicingController extends AbstractController
{
	/**
	 * @Route("/create_submit", name="create_submit", methods={"POST"})
	 */
	public function createSubmit( Request $request ): Response
	{
		$input	= new InvoicingInput( $request );

		if ( ! $input->isValid() )
		{
			return $this->json( array(
				'result'	=> $input->getErrorMsg()
			) );
		}

		$invoice	= new Invoice( $input->get( InputKeys::CSV_DATA ) );
		$calculator	= new InvoiceCalculator( $input, $invoice );

		return $this->json([
			'result'	=> 'Invoice total: ' . $calculator->getInvoiceTotal()
		]);
	}
}
