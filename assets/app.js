import React, { Component } from 'react';
import * as ReactDOM from 'react-dom';
import InvoiceForm from './components/InvoiceForm';
import InvoiceContextProvider from './context/InvoiceContext';
import AppSnackbar from './components/AppSnackbar';

class App extends Component
{
	render()
	{
		return (
			<InvoiceContextProvider>
				<InvoiceForm/>
				<AppSnackbar/>
			</InvoiceContextProvider>
		);
	}
}

ReactDOM.render( <App/>, document.getElementById( 'root' ) )
export default App;