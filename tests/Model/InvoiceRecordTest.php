<?php

namespace App\Tests\Model;

use App\Model\Invoice;
use App\Model\InvoiceRecord;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceRecordTest extends KernelTestCase
{
	private const CUSTOMER		= 'Customer 1';
	private const VAT_NUMBER	= 123456789;
	private const DOC_NUMBER	= 'Document number';
	private const TYPE			= Invoice::INVOICE_TYPE;
	private const CURRENCY		= 'USD';
	private const TOTAL			= 400.0;
	private const PARENT_DOC	= 'Parent document';

	/**
	 * @var	\App\Model\InvoiceRecord
	 */
	private $record;

	/**
	 * @var	array
	 */
	private $recordData	= array(
		'Customer'			=> self::CUSTOMER,
		'Vat number'		=> self::VAT_NUMBER,
		'Document number'	=> self::DOC_NUMBER,
		'Type'				=> self::TYPE,
		'Currency'			=> self::CURRENCY,
		'Total'				=> self::TOTAL,
		'Parent document'	=> self::PARENT_DOC,
	);

	public function testIsValid(): void
	{
		$this->assertTrue( $this->record->isValid() );
	}

	/**
	 * @dataProvider	invalidInvoiceRecordDataProvider
	 */
	public function testIsValidWithInvalidData( string $key, $value ): void
	{
		$this->recordData[$key]	= $value;

		$record	= new InvoiceRecord( $this->recordData );

		$this->assertFalse( $record->isValid() );
	}

	public function testGetAll(): void
	{
		$this->assertSame( $this->recordData, $this->record->getAll() );
	}

	public function testGetVatNumber(): void
	{
		$this->assertSame( self::VAT_NUMBER, $this->record->getVatNumber() );
	}

	public function testGetType(): void
	{
		$this->assertSame( self::TYPE, $this->record->getType() );
	}

	public function testGetCurrency(): void
	{
		$this->assertSame( self::CURRENCY, $this->record->getCurrency() );
	}

	public function testGetTotal(): void
	{
		$this->assertSame( self::TOTAL, $this->record->getTotal() );
	}

	public function invalidInvoiceRecordDataProvider(): array
	{
		return array(
			array( 'Customer',			'' ),
			array( 'Vat number',		0 ),
			array( 'Vat number',		'invalid vat num' ),
			array( 'Vat number',		array() ),
			array( 'Document number',	'' ),
			array( 'Type',				0 ),
			array( 'Type',				5 ),
			array( 'Type',				'invalid type' ),
			array( 'Type',				array() ),
			array( 'Currency',			'' ),
			array( 'Currency',			'invalid type' ),
			array( 'Currency',			'BGN' ),
			array( 'Currency',			123 ),
			array( 'Currency',			array() ),
			array( 'Total',				0 ),
			array( 'Total',				'invalid type' ),
			array( 'Total',				array() )
		);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected function setUp(): void
	{
		$this->record	= new InvoiceRecord( $this->recordData );
	}
}
