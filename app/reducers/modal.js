import { combineReducers } from 'redux'

const isOpen = (state = false, action) => {
  switch (action.type) {
    case 'TOGGLE_MODAL':
      return !state
    default:
      return state
  }
}

const isParsing = (state = false, action) => {
  switch (action.type) {
    case 'PARSE_DOCUMENTS_REQUEST':
      return true
    case 'PARSE_DOCUMENTS_SUCCESS':
    case 'PARSE_DOCUMENTS_ERROR':
      return false
    default:
      return state
  }
}

export default combineReducers({
  isOpen,
  isParsing
})
