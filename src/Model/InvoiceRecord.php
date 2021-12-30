<?php

namespace App\Model;

use App\Input\InvoicingInput;

/**
 * @brief	Class that holds a single CSV invoice content row
 */
class InvoiceRecord
{
	/**
	 * @var	array
	 */
	private $invoiceRecordData;

	/**
	 * @var	bool
	 */
	private $isValid;

	/**
	 * @param	array $invoiceRecordData
	 */
	public function __construct( array $invoiceRecordData )
	{
		$this->invoiceRecordData	= $invoiceRecordData;
		$this->isValid				= $this->validate();
	}

	/**
	 * @return	bool
	 */
	public function isValid(): bool
	{
		return $this->isValid;
	}

	/**
	 * @return	array
	 */
	public function getAll(): array
	{
		return $this->invoiceRecordData;
	}

	/**
	 * @return	int
	 */
	public function getVatNumber(): int
	{
		return ( int ) $this->invoiceRecordData['Vat number'];
	}

	/**
	 * @return	int
	 */
	public function getType(): int
	{
		return ( int ) $this->invoiceRecordData['Type'];
	}

	/**
	 * @return	string
	 */
	public function getCurrency(): string
	{
		return $this->invoiceRecordData['Currency'];
	}

	/**
	 * @return	float
	 */
	public function getTotal(): float
	{
		return ( float ) $this->invoiceRecordData['Total'];
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * @return	bool
	 */
	private function validate(): bool
	{
		$validInvoiceTypes	= array( Invoice::INVOICE_TYPE, Invoice::CREDIT_CARD_TYPE, Invoice::DEBIT_CARD_TYPE );

		return	! empty( $this->invoiceRecordData['Customer'] )
				&& ! empty( $this->invoiceRecordData['Vat number'] )
				&& ! empty( $this->invoiceRecordData['Document number'] )
				&& ! empty( $this->invoiceRecordData['Type'] )
				&& ! empty( $this->invoiceRecordData['Currency'] )
				&& ! empty( $this->invoiceRecordData['Total'] )
				&& isset( $this->invoiceRecordData['Parent document'] )
				&& is_numeric( $this->invoiceRecordData['Vat number'] )
				&& is_numeric( $this->invoiceRecordData['Type'] )
				&& is_numeric( $this->invoiceRecordData['Total'] )
				&& in_array( $this->invoiceRecordData['Currency'], InvoicingInput::SUPPORTED_CURRENCIES )
				&& in_array( $this->invoiceRecordData['Type'], $validInvoiceTypes );
	}
}
