<?php

namespace App\Tests\Model;

use App\Model\Invoice;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceTest extends KernelTestCase
{
	private const VAT_NUM_1	= 123456789;
	private const VAT_NUM_2	= 987654321;

	/**
	 * @var	array
	 */
	private $invoiceData	= array(
		array(
			'Customer'			=> 'Customer 1',
			'Vat number'		=> self::VAT_NUM_1,
			'Document number'	=> 'Document number 1',
			'Type'				=> Invoice::INVOICE_TYPE,
			'Currency'			=> 'USD',
			'Total'				=> 400.0,
			'Parent document'	=> 'Parent document 1'
		),
		array(
			'Customer'			=> 'Customer 2',
			'Vat number'		=> self::VAT_NUM_2,
			'Document number'	=> 'Document number 2',
			'Type'				=> Invoice::CREDIT_CARD_TYPE,
			'Currency'			=> 'EUR',
			'Total'				=> 100.0,
			'Parent document'	=> 'Parent document 2'
		)
	);

	public function testGetAll(): void
	{
		$invoice		= new Invoice( $this->invoiceData );
		$invoiceRecords	= $invoice->getAll();

		$this->assertCount( 2, $invoiceRecords );

		foreach ( $invoiceRecords as $record )
		{
			$this->assertInstanceOf( '\App\Model\InvoiceRecord', $record );
		}
	}

	/**
	 * @dataProvider	getByVatNumberProvider
	 */
	public function testGetByVatNumber( int $vatNum, int $expectedRecords ): void
	{
		$invoice		= new Invoice( $this->invoiceData );
		$invoiceRecords	= $invoice->getByVatNumber( $vatNum );

		$this->assertCount( $expectedRecords, $invoiceRecords );

		foreach ( $invoiceRecords as $record )
		{
			$this->assertInstanceOf( '\App\Model\InvoiceRecord', $record );
		}
	}

	public function getByVatNumberProvider(): array
	{
		return array(
			array( self::VAT_NUM_1,	1 ),
			array( self::VAT_NUM_2,	1 ),
			array( 111222333,		0 )
		);
	}
}
