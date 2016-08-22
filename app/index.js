import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'
import createLogger from 'redux-logger'
import thunk from 'redux-thunk'
import reducer from './reducers'
import DocumentFinder from './components/DocumentFinder'

const logger = createLogger()

const initialState = {
  options: {
    caseSensitive: false
  },
  settings: {
    root: localStorage.getItem('root'),
    lastParsed: localStorage.getItem('lastParsed'),
    ignore: JSON.parse(localStorage.getItem('ignore'))
  }
}

const store = createStore(
  reducer,
  initialState,
  applyMiddleware(thunk, logger)
)

render(
  <Provider store={store}>
    <DocumentFinder />
  </Provider>,
  document.getElementById('root')
)
