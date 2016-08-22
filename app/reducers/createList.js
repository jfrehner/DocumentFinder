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

  return combineReducers({ ids })
}

export default createList

export const getIds = (state) => state.ids
