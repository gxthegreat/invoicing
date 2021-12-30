import React, { Component, createContext } from 'react';
import axios from 'axios';

export const InvoiceContext	= createContext();

class InvoiceContextProvider extends Component
{
	constructor( props )
	{
		super( props );
		this.state	= { result: '', errMsg: '' }
	}

	calculateInvoice( e, data )
	{
		e.preventDefault()

		const formData	= new FormData();

		formData.append( 'invoice_list', data.invoice_file )
		formData.append( 'output_currency', data.outputCurrency )
		formData.append( 'euro_rate', data.euroRate )
		formData.append( 'dollar_rate', data.dollarRate )
		formData.append( 'pound_rate', data.poundRate )
		formData.append( 'vat_number', data.vatNumber )

		axios.post( '/api/create_submit', formData, {
			headers: {
				'accept': 'application/json',
				'Content-Type': `multipart/form-data`,
			} } )
			.then( response => {
				this.setState( {
					result: response.data.result,
					errMsg: response.data.errMsg
				} )
			} ).catch( error => {
				console.log( error.message )
				this.setState( {
					errMsg: 'An unknown error has occurred'
				} )
		} )
	}

	render()
	{
		return (
			<InvoiceContext.Provider value={ {
				...this.state,
				calculateInvoice: this.calculateInvoice.bind( this ),
				setMessage: ( message ) => this.setState( { errMsg: message } )
			} }>
				{ this.props.children }
			</InvoiceContext.Provider>
		);
	}
}

export default InvoiceContextProvider;