<?php

namespace App\Model;

use App\Input\InputKeys;
use App\Input\InvoicingInput;

/**
 * @brief	Model that takes care of calculating the total amount of the invoice
 */
class InvoiceCalculator
{
	/**
	 * @var	\App\Input\InvoicingInput
	 */
	private $input;

	/**
	 * @var	\App\Model\Invoice
	 */
	private $invoice;

	/**
	 * @param	\App\Input\InvoicingInput $input
	 * @param	\App\Model\Invoice $invoice
	 */
	public function __construct( InvoicingInput $input, Invoice $invoice )
	{
		$this->input	= $input;
		$this->invoice	= $invoice;
	}

	/**
	 * @brief	Get the total amount of the invoice, based on the data, received from the request
	 *
	 * @return	string
	 */
	public function getInvoiceTotal(): string
	{
		$vatNumber		= $this->input->get( InputKeys::VAT_NUMBER );
		$outputCurrency	= $this->input->get( InputKeys::OUTPUT_CURRENCY );

		$invoiceRecords	= $vatNumber !== 0
						? $this->invoice->getByVatNumber( $vatNumber )
						: $this->invoice->getAll();

		$total			= $this->getTotal( $invoiceRecords );

		return $total . ' ' . $outputCurrency;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * @brief	Calculate the total amount, based on the selected currency, the rate of the other currencies and the invoice type
	 *
	 * @param	\App\Model\InvoiceRecord[] $invoiceRecords
	 *
	 * @throws	\InvalidArgumentException
	 * 			When the invoice record's type is not supported
	 *
	 * @return	float
	 */
	private function getTotal( array $invoiceRecords ): float
	{
		$total	= 0;

		foreach ( $invoiceRecords as $invoiceRecord )
		{
			$recordType		= $invoiceRecord->getType();
			$recordTotal	= $invoiceRecord->getTotal() * $this->getRateByRecordCurrency( $invoiceRecord->getCurrency() );

			switch ( $recordType )
			{
				case Invoice::INVOICE_TYPE:
				case Invoice::DEBIT_CARD_TYPE:
					$total	+= $recordTotal;
					break;
				case Invoice::CREDIT_CARD_TYPE:
					$total	-= $recordTotal;
					break;
				default:
					throw new \InvalidArgumentException( 'The invoice record type ' . $recordType .  ' is not supported' );
			}
		}

		return $total;
	}

	/**
	 * @brief	Determine the currency rate, based on the set currency in the invoice record
	 *
	 * @param	string $recordCurrency
	 *
	 * @throws	\InvalidArgumentException
	 * 			When the invoice record's currency is not supported
	 *
	 * @return	float
	 */
	private function getRateByRecordCurrency( string $recordCurrency ): float
	{
		switch ( $recordCurrency )
		{
			case 'EUR':
				return $this->input->get( InputKeys::EURO_RATE );
			case 'USD':
				return $this->input->get( InputKeys::DOLLAR_RATE );
			case 'GBP':
				return $this->input->get( InputKeys::POUND_RATE );
			default:
				throw new \InvalidArgumentException( 'The invoice record currency ' . $recordCurrency .  ' is not supported' );
		}
	}
}
