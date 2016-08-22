import axios from 'axios'
import { normalize } from 'normalizr'
import * as schema from './schema'

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

  return axios.get('data.js')
    .then(res => {
      dispatch({
        type: 'FETCH_DOCUMENTS_SUCCESS',
        role,
        response: normalize(filterByRole(res.data.documents, role), schema.arrayOfDocuments)
      })
    }).catch(err => {
      dispatch({
        type: 'FETCH_DOCUMENTS_ERROR',
        role,
        error: err
      })
    })
}
