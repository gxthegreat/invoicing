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
		$jsonOutput	= array(
			'result'	=> '',
			'errMsg'	=> ''
		);

		$input		= new InvoicingInput( $request );

		if ( ! $input->isValid() )
		{
			$jsonOutput['errMsg']	= $input->getErrorMsg();
			return $this->json( $jsonOutput );
		}

		$invoice				= new Invoice( $input->get( InputKeys::CSV_DATA ) );
		$calculator				= new InvoiceCalculator( $input, $invoice );
		$jsonOutput['result']	= 'Invoice total: ' . $calculator->getInvoiceTotal();

		return $this->json( $jsonOutput );
	}
}
