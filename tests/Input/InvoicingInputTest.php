<?php

namespace App\Tests\Input;

use App\Input\InputKeys;
use App\Input\InvoicingInput;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoicingInputTest extends KernelTestCase
{
	private const OUTPUT_CURRENCY	= 'USD';
	private const EURO_RATE			= 1.2;
	private const DOLLAR_RATE		= 1.0;
	private const POUND_RATE		= 0.8;
	private const VAT_NUMBER		= 123456789;

	/**
	 * @var	\PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\Request
	 */
	private $request;

	/**
	 * @var	\PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\HttpFoundation\FileBag
	 */
	private $fileBag;

	/**
	 * @var	\App\Input\InvoicingInput
	 */
	private $input;

	/**
	 * @var	array $requestParams
	 */
	private $requestParams	= array(
		'output_currency'	=> self::OUTPUT_CURRENCY,
		'euro_rate'			=> self::EURO_RATE,
		'dollar_rate'		=> self::DOLLAR_RATE,
		'pound_rate'		=> self::POUND_RATE,
		'vat_number'		=> self::VAT_NUMBER
	);

	public function testIsValid(): void
	{
		$this->assertTrue( $this->input->isValid() );
	}

	public function testIsValidWithMissingFile(): void
	{
		$fileBag	= $this->createMock( '\Symfony\Component\HttpFoundation\FileBag' );

		$fileBag->method( 'has' )->willReturn( false );

		$this->request->files	= $fileBag;

		$input	= new InvoicingInput( $this->request );

		$this->assertFalse( $input->isValid() );
		$this->assertSame( 'Error! Invoicing file needs to be uploaded', $input->getErrorMsg() );
	}

	/**
	 * @dataProvider	invalidOutputCurrencyProvider
	 */
	public function testIsValidWithInvalidOutputCurrency( $outputCurrency, string $errMsg ): void
	{
		$paramBag	= $this->createMock( '\Symfony\Component\HttpFoundation\ParameterBag' );
		$file		= $this->createMock( 'Symfony\Component\HttpFoundation\File\UploadedFile' );

		$file->method( 'getContent' )->willReturn( file_get_contents( __DIR__ . '/Fixture/data.csv' ) );

		$this->fileBag->method( 'has' )->willReturn( true );
		$this->fileBag->method( 'get' )->with( 'invoice_list' )->willReturn( $file );

		$this->requestParams['output_currency'] = $outputCurrency;

		$paramBag->method( 'all' )->willReturn( $this->requestParams );

		$this->request->files	= $this->fileBag;
		$this->request->request	= $paramBag;

		$input	= new InvoicingInput( $this->request );

		$this->assertFalse( $input->isValid() );
		$this->assertStringStartsWith( $errMsg, $input->getErrorMsg() );
	}

	/**
	 * @dataProvider	invalidCurrencyRatesProvider
	 */
	public function testIsValidWithInvalidCurrencyRates( $euroRate, $dollarRate, $poundRate ): void
	{
		$paramBag	= $this->createMock( '\Symfony\Component\HttpFoundation\ParameterBag' );
		$file		= $this->createMock( 'Symfony\Component\HttpFoundation\File\UploadedFile' );

		$file->method( 'getContent' )->willReturn( file_get_contents( __DIR__ . '/Fixture/data.csv' ) );

		$this->fileBag->method( 'has' )->willReturn( true );
		$this->fileBag->method( 'get' )->with( 'invoice_list' )->willReturn( $file );

		$this->requestParams['euro_rate']   = $euroRate;
		$this->requestParams['dollar_rate'] = $dollarRate;
		$this->requestParams['pound_rate']  = $poundRate;

		$paramBag->method( 'all' )->willReturn( $this->requestParams );

		$this->request->files	= $this->fileBag;
		$this->request->request	= $paramBag;

		$input	= new InvoicingInput( $this->request );

		$this->assertFalse( $input->isValid() );
		$this->assertSame( 'Error! Either the currency rates are not set or they are not valid positive numbers', $input->getErrorMsg() );
	}

	/**
	 * @dataProvider	invalidVatNumberProvider
	 */
	public function testIsValidWithInvalidVatNumber( $vatNumber ): void
	{
		$paramBag	= $this->createMock( '\Symfony\Component\HttpFoundation\ParameterBag' );
		$file		= $this->createMock( 'Symfony\Component\HttpFoundation\File\UploadedFile' );

		$file->method( 'getContent' )->willReturn( file_get_contents( __DIR__ . '/Fixture/data.csv' ) );

		$this->fileBag->method( 'has' )->willReturn( true );
		$this->fileBag->method( 'get' )->with( 'invoice_list' )->willReturn( $file );

		$this->requestParams['vat_number'] = $vatNumber;

		$paramBag->method( 'all' )->willReturn( $this->requestParams );

		$this->request->files	= $this->fileBag;
		$this->request->request	= $paramBag;

		$input	= new InvoicingInput( $this->request );

		$this->assertFalse( $input->isValid() );
		$this->assertSame( 'Error! The VAT number is expected to be a valid number', $input->getErrorMsg() );
	}

	public function testGetErrorMsg(): void
	{
		$this->assertSame( '', $this->input->getErrorMsg() );
	}

	public function testGetAll(): void
	{
		$inputData	= $this->input->getAll();

		$this->assertIsArray( $inputData );
		$this->assertIsArray( $inputData[InputKeys::CSV_DATA] );
		$this->assertSame( self::OUTPUT_CURRENCY, $inputData[InputKeys::OUTPUT_CURRENCY] );
		$this->assertSame( self::EURO_RATE, $inputData[InputKeys::EURO_RATE] );
		$this->assertSame( self::DOLLAR_RATE, $inputData[InputKeys::DOLLAR_RATE] );
		$this->assertSame( self::POUND_RATE, $inputData[InputKeys::POUND_RATE] );
		$this->assertSame( self::VAT_NUMBER, $inputData[InputKeys::VAT_NUMBER] );
	}

	public function testGet(): void
	{
		$this->assertIsArray( $this->input->get( InputKeys::CSV_DATA ) );
		$this->assertSame( self::OUTPUT_CURRENCY, $this->input->get( InputKeys::OUTPUT_CURRENCY ) );
		$this->assertSame( self::EURO_RATE, $this->input->get( InputKeys::EURO_RATE ) );
		$this->assertSame( self::DOLLAR_RATE, $this->input->get( InputKeys::DOLLAR_RATE ) );
		$this->assertSame( self::POUND_RATE, $this->input->get( InputKeys::POUND_RATE ) );
		$this->assertSame( self::VAT_NUMBER, $this->input->get( InputKeys::VAT_NUMBER ) );
	}

	public function invalidOutputCurrencyProvider(): array
	{
		return array(
			array( '',		'Error! Please chose an output currency' ),
			array( 123,		'Error! Please chose an output currency' ),
			array( array(),	'Error! Please chose an output currency' ),
			array( 'BGN',	'Error! This currency is not supported' ),
			array( 'JPY',	'Error! This currency is not supported' )
		);
	}

	public function invalidCurrencyRatesProvider(): array
	{
		return array(
			array( 'invalid rate',	self::DOLLAR_RATE,	self::POUND_RATE ),
			array( array(),			self::DOLLAR_RATE,	self::POUND_RATE ),
			array( 0,				self::DOLLAR_RATE,	self::POUND_RATE ),
			array( -20,				self::DOLLAR_RATE,	self::POUND_RATE ),
			array( self::EURO_RATE,	'invalid rate', 	self::POUND_RATE ),
			array( self::EURO_RATE,	array(),			self::POUND_RATE ),
			array( self::EURO_RATE,	0,					self::POUND_RATE ),
			array( self::EURO_RATE,	-20,				self::POUND_RATE ),
			array( self::EURO_RATE,	self::DOLLAR_RATE,	'invalid rate' ),
			array( self::EURO_RATE,	self::DOLLAR_RATE,	array() ),
			array( self::EURO_RATE,	self::DOLLAR_RATE,	0 ),
			array( self::EURO_RATE,	self::DOLLAR_RATE,	-20 )
		);
	}

	public function invalidVatNumberProvider(): array
	{
		return array(
			array( 'invalid vat number' ),
			array( array() )
		);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	protected function setUp(): void
	{
		$this->request	= $this->createMock( '\Symfony\Component\HttpFoundation\Request' );
		$this->fileBag	= $this->createMock( '\Symfony\Component\HttpFoundation\FileBag' );
		$paramBag		= $this->createMock( '\Symfony\Component\HttpFoundation\ParameterBag' );
		$file			= $this->createMock( 'Symfony\Component\HttpFoundation\File\UploadedFile' );

		$file->method( 'getContent' )->willReturn( file_get_contents( __DIR__ . '/Fixture/data.csv' ) );

		$this->fileBag->method( 'has' )->willReturn( true );
		$this->fileBag->method( 'get' )->with( 'invoice_list' )->willReturn( $file );
		$paramBag->method( 'all' )->willReturn( $this->requestParams );

		$this->request->files	= $this->fileBag;
		$this->request->request	= $paramBag;

		$this->input			= new InvoicingInput( $this->request );
	}
}
