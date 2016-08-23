import { combineReducers } from 'redux'

const createList = (role) => {
  const ids = (state = [], action) => {
    switch (action.type) {
      case 'FETCH_DOCUMENTS_SUCCESS':
        return role === action.role ? action.response.result : state
      default:
        return state
    }
  }

  const isStale = (state = false, action) => {
    switch (action.type) {
      case 'PARSE_DOCUMENTS_SUCCESS':
      case 'PARSE_DOCUMENTS_ERROR':
        return true
      case 'FETCH_DOCUMENTS_SUCCESS':
      case 'FETCH_DOCUMENTS_ERROR':
        return false
      default:
        return state
    }
  }

  return combineReducers({
    ids,
    isStale
  })
}

export default createList

export const getIds = (state) => state.ids
export const getIsStale = (state) => state.isStale
