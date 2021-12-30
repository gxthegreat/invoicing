<?php

namespace App\Input;

use Symfony\Component\HttpFoundation\Request;

/**
 * @brief	Input that takes care for validating and returning the request data
 */
class InvoicingInput
{
	public const SUPPORTED_CURRENCIES	= array( 'EUR', 'USD', 'GBP' );

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	private $request;

	/**
	 * @var	bool
	 */
	private $isValid;

	/**
	 * @var	string
	 */
	private $errorMsg	= '';

	/**
	 * @var	array
	 */
	private $data		= array();

	/**
	 * @param	\Symfony\Component\HttpFoundation\Request $request
	 */
	public function __construct( Request $request )
	{
		$this->request	= $request;
		$this->isValid	= $this->validate();
	}

	/**
	 * @return	bool
	 */
	public function isValid(): bool
	{
		return $this->isValid;
	}

	/**
	 * @brief	In case of an error, get the error message. Will be empty string if input is valid
	 *
	 * @return	string
	 */
	public function getErrorMsg(): string
	{
		return $this->errorMsg;
	}

	/**
	 * @return array
	 */
	public function getAll(): array
	{
		return $this->data;
	}

	/**
	 * @param	mixed $key
	 *
	 * @return	mixed
	 */
	public function get( $key )
	{
		if ( ! array_key_exists( $key, $this->data ) )
		{
			throw new \InvalidArgumentException( 'Invalid ' . get_class( $this ) . ' input key: ' . $key );
		}

		return $this->data[$key];
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * @return	bool
	 */
	private function validate(): bool
	{
		if ( ! $this->request->files->has( 'invoice_list' ) )
		{
			$this->setErrorMsg( 'Invoicing file needs to be uploaded' );
			return false;
		}

		$fileContent	= $this->request->files->get( 'invoice_list' )->getContent();
		$requestParams	= $this->request->request->all();

		if ( empty( $requestParams['output_currency'] ) || ! is_string( $requestParams['output_currency'] ) )
		{
			$this->setErrorMsg( 'Please chose an output currency' );
			return false;
		}

		$outputCurrency	= $requestParams['output_currency'];
		if ( ! in_array( $outputCurrency, self::SUPPORTED_CURRENCIES ) )
		{
			$this->setErrorMsg( 'This currency is not supported. Please chose one of the ' . implode( ', ', self::SUPPORTED_CURRENCIES ) );
			return false;
		}

		if (
			! isset( $requestParams['euro_rate'], $requestParams['dollar_rate'], $requestParams['pound_rate'] )
			|| ! is_numeric( $requestParams['euro_rate'] ) || ! is_numeric( $requestParams['dollar_rate'] ) || ! is_numeric( $requestParams['pound_rate'] )
			|| $requestParams['euro_rate'] <= 0 || $requestParams['dollar_rate'] <= 0 || $requestParams['pound_rate'] <= 0

		) {
			$this->setErrorMsg( 'Either the currency rates are not set or they are not valid positive numbers' );
			return false;
		}

		if ( isset( $requestParams['vat_number'] ) && ! is_numeric( $requestParams['vat_number'] ) )
		{
			$this->setErrorMsg( 'The VAT number is expected to be a valid number' );
			return false;
		}

		$this->data	= array(
			InputKeys::CSV_DATA			=> $this->getCsvData( $fileContent ),
			InputKeys::OUTPUT_CURRENCY	=> $outputCurrency,
			InputKeys::EURO_RATE		=> ( float ) $requestParams['euro_rate'],
			InputKeys::DOLLAR_RATE		=> ( float ) $requestParams['dollar_rate'],
			InputKeys::POUND_RATE		=> ( float ) $requestParams['pound_rate'],
			InputKeys::VAT_NUMBER		=> ( int ) $requestParams['vat_number']
		);

		return true;
	}

	/**
	 * @param	string $errMsg
	 *
	 * @return	void
	 */
	private function setErrorMsg( string $errMsg ): void
	{
		$this->errorMsg	= 'Error! ' . $errMsg;
	}

	/**
	 * @brief	Parse the csv data from string to an associative array
	 *
	 * @details	Since there can be trailing rows before and after the content, make sure to combine only the matching rows
	 *
	 * @param	string $csvString
	 *
	 * @return	array
	 */
	private function getCsvData( string $csvString ): array
	{
		$csvArray	= array_map( 'str_getcsv', explode( "\n", $csvString ) );
		$firstValue	= array_shift( $csvArray );

		foreach( $csvArray as &$value )
		{
			if ( count( $firstValue ) === count( $value ) )
			{
				$value	= array_combine( $firstValue, $value );
			}
		}

		return $csvArray;
	}
}
