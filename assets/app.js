import React, { Component } from 'react';
import * as ReactDOM from 'react-dom';
import InvoiceForm from './components/InvoiceForm';
import InvoiceContextProvider from './context/InvoiceContext';

class App extends Component
{
	render()
	{
		return (
			<InvoiceContextProvider>
				<InvoiceForm/>
			</InvoiceContextProvider>
		);
	}
}

ReactDOM.render( <App/>, document.getElementById( 'root' ) )
export default App;