import React, { useContext } from 'react';
import { Button, Snackbar, SnackbarContent } from '@mui/material';
import { InvoiceContext } from '../context/InvoiceContext';

function AppSnackbar()
{
	const context = useContext( InvoiceContext );

	return (
		<Snackbar open={ context.errMsg !== '' }>
				<SnackbarContent style={ { backgroundColor: 'red', whiteSpace: 'pre' } } message={ context.errMsg } action={ [
					<Button color={ 'inherit' } onClick={ () => { context.setMessage( '' ) } } key={ 'dismiss' }>Dismiss</Button>
				] }/>
		</Snackbar>
	);
}

export default AppSnackbar;