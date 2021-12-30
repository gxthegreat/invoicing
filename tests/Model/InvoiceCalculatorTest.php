<?php

namespace App\Tests\Model;

use App\Input\InputKeys;
use App\Model\Invoice;
use App\Model\InvoiceCalculator;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceCalculatorTest extends KernelTestCase
{
	/**
	 * @var	\App\Input\InvoicingInput|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $input;

	/**
	 * @var	\App\Model\Invoice|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $invoice;

	/**
	 * @var	\App\Model\InvoiceRecord|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $invoiceRecord1;

	/**
	 * @var	\App\Model\InvoiceRecord|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $invoiceRecord2;

	/**
	 * @var	\App\Model\InvoiceRecord|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $invoiceRecord3;

	/**
	 * @var	\App\Model\InvoiceCalculator
	 */
	private $calculator;

	public function testGetInvoiceTotalWithoutVatNumber(): void
	{
		$inputValueMap	= array(
			array( InputKeys::VAT_NUMBER, 0 ),
			array( InputKeys::OUTPUT_CURRENCY, 'USD' ),
			array( InputKeys::EURO_RATE, 1.2 ),
			array( InputKeys::DOLLAR_RATE, 1.0 ),
			array( InputKeys::POUND_RATE, 0.9 )
		);

		$this->input->method( 'get' )->willReturnMap( $inputValueMap );

		$invoiceRecords	= array( $this->invoiceRecord1, $this->invoiceRecord2, $this->invoiceRecord3 );
		$this->invoice->expects( $this->once() )->method( 'getAll' )->willReturn( $invoiceRecords );

		$this->assertSame( '130 USD', $this->calculator->getInvoiceTotal() );
	}

	public function testGetInvoiceTotalWithVatNumberSet(): void
	{
		$vatNumber		= 123456789;
		$inputValueMap	= array(
			array( InputKeys::VAT_NUMBER, $vatNumber ),
			array( InputKeys::OUTPUT_CURRENCY, 'USD' ),
			array( InputKeys::EURO_RATE, 1.2 ),
			array( InputKeys::DOLLAR_RATE, 1.0 ),
			array( InputKeys::POUND_RATE, 0.9 )
		);

		$this->input->method( 'get' )->willReturnMap( $inputValueMap );

		$invoiceRecords	= array( $this->invoiceRecord1 );
		$this->invoice->expects( $this->once() )->method( 'getByVatNumber' )->willReturn( $invoiceRecords );

		$this->assertSame( '100 USD', $this->calculator->getInvoiceTotal() );
	}

	/**
	 * @dataProvider	getInvoiceTotalProvider
	 */
	public function testGetInvoiceTotalExpectsExceptionOnInvalidInvoiceRecordData( string $currency, int $invoiceType ): void
	{
		$invoiceRecord	= $this->createMock( '\App\Model\InvoiceRecord' );

		$inputValueMap	= array(
			array( InputKeys::VAT_NUMBER, 0 ),
			array( InputKeys::OUTPUT_CURRENCY, 'USD' ),
			array( InputKeys::EURO_RATE, 1.2 ),
			array( InputKeys::DOLLAR_RATE, 1.0 ),
			array( InputKeys::POUND_RATE, 0.9 )
		);

		$this->input->method( 'get' )->willReturnMap( $inputValueMap );

		$invoiceRecord->method( 'getType' )->willReturn( $invoiceType );
		$invoiceRecord->method( 'getTotal' )->willReturn( 100.0 );
		$invoiceRecord->method( 'getCurrency' )->willReturn( $currency );

		$this->invoice->method( 'getAll' )->willReturn( array( $invoiceRecord ) );

		$this->expectException( '\InvalidArgumentException' );

		$calculator	= new InvoiceCalculator( $this->input, $this->invoice );

		$calculator->getInvoiceTotal();
	}

	public function getInvoiceTotalProvider(): array
	{
		return array(
			array( 'BGN',	Invoice::INVOICE_TYPE ),
			array( 'JPY',	Invoice::CREDIT_CARD_TYPE ),
			array( 'USD',	0 ),
			array( 'USD',	5 )
		);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected function setUp(): void
	{
		$this->input			= $this->createMock( '\App\Input\InvoicingInput' );
		$this->invoice			= $this->createMock( '\App\Model\Invoice' );
		$this->invoiceRecord1	= $this->createMock( '\App\Model\InvoiceRecord' );
		$this->invoiceRecord2	= clone $this->invoiceRecord1;
		$this->invoiceRecord3	= clone $this->invoiceRecord1;

		$this->invoiceRecord1->method( 'getType' )->willReturn( Invoice::INVOICE_TYPE );
		$this->invoiceRecord1->method( 'getTotal' )->willReturn( 100.0 );
		$this->invoiceRecord1->method( 'getCurrency' )->willReturn( 'USD' );

		$this->invoiceRecord2->method( 'getType' )->willReturn( Invoice::CREDIT_CARD_TYPE );
		$this->invoiceRecord2->method( 'getTotal' )->willReturn( 200.0 );
		$this->invoiceRecord2->method( 'getCurrency' )->willReturn( 'EUR' );

		$this->invoiceRecord3->method( 'getType' )->willReturn( Invoice::DEBIT_CARD_TYPE );
		$this->invoiceRecord3->method( 'getTotal' )->willReturn( 300.0 );
		$this->invoiceRecord3->method( 'getCurrency' )->willReturn( 'GBP' );

		$this->calculator	= new InvoiceCalculator( $this->input, $this->invoice );
	}
}
