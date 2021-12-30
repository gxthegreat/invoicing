<?php

namespace App\Model;

/**
 * @brief	Class that holds a single CSV invoice content
 */
class Invoice
{
	public const INVOICE_TYPE		= 1;
	public const CREDIT_CARD_TYPE	= 2;
	public const DEBIT_CARD_TYPE	= 3;

	/**
	 * @var	array
	 */
	private $invoiceData;

	/**
	 * @param	array $invoiceData
	 */
	public function __construct( array $invoiceData )
	{
		$this->invoiceData	= $invoiceData;
	}

	/**
	 * @brief	Get all InvoiceRecords, generated based on the incoming invoice data but only if the data is valid as expected
	 *
	 * @return	\App\Model\InvoiceRecord[]
	 */
	public function getAll(): array
	{
		$invoiceRecords	= array();

		foreach ( $this->invoiceData as $invoiceDatum )
		{
			$record	= new InvoiceRecord( $invoiceDatum );

			if ( $record->isValid() )
			{
				$invoiceRecords[]	= $record;
			}
		}

		return $invoiceRecords;
	}

	/**
	 * @brief	Get the filtered by vat number InvoiceRecords, generated based on the incoming invoice data but only if the data is valid as expected
	 *
	 * @param	int $vatNumber
	 *
	 * @return	\App\Model\InvoiceRecord[]
	 */
	public function getByVatNumber( int $vatNumber ): array
	{
		$invoiceRecords	= array();

		foreach ( $this->invoiceData as $invoiceDatum )
		{
			$record	= new InvoiceRecord( $invoiceDatum );

			if ( $record->isValid() && $vatNumber === $record->getVatNumber() )
			{
				$invoiceRecords[]	= $record;
			}
		}

		return $invoiceRecords;
	}
}
