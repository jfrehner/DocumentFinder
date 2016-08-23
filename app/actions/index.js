import axios from 'axios'
import { normalize } from 'normalizr'
import * as schema from './schema'
import { readFile } from 'fs'

export const toggleRole = (role) => ({
  type: 'TOGGLE_ROLE',
  role
})

export const toggleOption = (id) => ({
  type: 'TOGGLE_OPTION',
  id
})

export const queryChange = (text) => ({
  type: 'QUERY_CHANGE',
  text
})

export const toggleModal = () => ({
  type: 'TOGGLE_MODAL'
})

export const updateSetting = (key, value) => (dispatch) => {
  localStorage.setItem(key, JSON.stringify(value))

  return dispatch({
    type: 'UPDATE_SETTING',
    key,
    value
  })
}

const filterByRole = (documents, role) => {
  switch (role) {
    case 'all':
      return documents
    default:
      return documents.filter(doc => doc.path.indexOf(role) !== -1)
  }
}

export const fetchDocuments = (role) => (dispatch) => {
  dispatch({
    type: 'FETCH_DOCUMENTS_REQUEST',
    role
  })

  return readFile('data.js', 'utf8', (err, data) => {
    if (err) {
      dispatch({
        type: 'FETCH_DOCUMENTS_ERROR',
        role,
        error: err
      })
    } else {
      dispatch({
        type: 'FETCH_DOCUMENTS_SUCCESS',
        role,
        response: normalize(filterByRole(JSON.parse(data).documents, role), schema.arrayOfDocuments)
      })
    }
  })
}
