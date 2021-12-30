import React, { useContext, useState } from 'react';
import { Button, Divider, FormControl, Grid, InputLabel, MenuItem, Select, TextField, Typography } from '@mui/material';
import { InvoiceContext } from '../context/InvoiceContext';

function InvoiceForm()
{
	const context	= useContext( InvoiceContext );
	const [invoice_file, setInvoiceFile]		= useState( null )
	const [outputCurrency, setOutputCurrency]	= useState( '' )
	const [euroRate, setEuroRate]				= useState( 1.0 )
	const [dollarRate, setDollarRate]			= useState( 1.0 )
	const [poundRate, setPoundRate]				= useState( 1.0 )
	const [vatNumber, setVatNumber]				= useState( 0 )

	const currencies	= [
		{ "Euro": "EUR" },
		{ "US Dollars": "USD" },
		{ "British Pound": "GBP" }
	]

	const onSubmit	= ( e ) => {
		e.preventDefault()
		const data	= {
			invoice_file: invoice_file,
			outputCurrency: outputCurrency,
			euroRate: euroRate,
			dollarRate: dollarRate,
			poundRate: poundRate,
			vatNumber: vatNumber
		}
		context.calculateInvoice( e, data )
	}

	return (
		<div>
			<Typography align="center" variant="h3">
				Calculate Invoicing
			</Typography>

			<Divider/><br/><br/>

			<form onSubmit={ onSubmit }>

				<Typography align="left" variant="h7">
					Upload your CSV invoice:&nbsp;
					<input accept="text/csv" type="file" onChange={ ( e ) => setInvoiceFile( e.target.files[0] ) } />
				</Typography>

				<br/><br/>

				<FormControl fullWidth size="small">
					<InputLabel>Output Currency</InputLabel>
					<Select displayEmpty onChange={ ( e ) => setOutputCurrency( e.target.value ) } defaultValue="">
						{ currencies.map( element => (
							<MenuItem
								value={ element[Object.keys( element )] + "" }
								key={ Object.keys( element )[0] }
							>
								{ Object.keys( element )[0] }
							</MenuItem>
						))}
					</Select>
				</FormControl>

				<br/><br/><br/>

				<div>Provide currency exchange rates:</div><br/>

				<Grid container direction="row" spacing={5}>
					<Grid item>
						<TextField
							type="number"
							size="small"
							defaultValue={1.0}
							onChange={ (e) => setEuroRate( Number( e.target.value ) ) }
							label="EURO"
						/>
					</Grid>
					<Grid item>
						<TextField
							type="number"
							size="small"
							defaultValue={1.0}
							onChange={ ( e ) => setDollarRate( Number( e.target.value ) ) }
							label="US Dollar"
						/>
					</Grid>
					<Grid item>
						<TextField
							type="number"
							size="small"
							defaultValue={1.0}
							onChange={ ( e ) => setPoundRate( Number( e.target.value ) ) }
							label="British Pound"
						/>
					</Grid>
				</Grid>

				<br/><br/>

				<TextField
					id="outlined-number"
					type="number"
					size="small"
					onChange={ ( e ) => setVatNumber( Number( e.target.value ) ) }
					label="Filter by VAT Number"
				/>

				<br/><br/><br/>

				<Button color="primary" variant="contained" onClick={ onSubmit }>Calculate</Button>
			</form>

			{ context.result && (
				<Typography m={5} border={1} borderRadius={5} borderColor="primary.main" color="primary.main" align="center" variant="h4">
					{ context.result }
				</Typography>
			)}

		</div>
	);
}

export default InvoiceForm;