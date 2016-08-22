import { combineReducers } from 'redux'
import selectedRole from './selectedRole'
import options from './options'
import settings from './settings'
import query from './query'
import byId, * as fromById from './byId'
import createList, * as fromList from './createList'

let roleFilters = {}

;[ 'all', 'IM', 'PM', 'QM', 'RE', 'USE', 'ST', 'SA' ].forEach(role => {
  roleFilters[role] = createList(role)
})

const listByRole = combineReducers(roleFilters)

export default combineReducers({
  byId,
  listByRole,
  selectedRole,
  options,
  settings,
  query
})

export const getVisibleDocuments = (state, role, query, options) => {
  const ids = fromList.getIds(state.listByRole[role])
  const documents = ids.map(id => fromById.getDocument(state.byId, id))

  if (!query.trim()) {
    return documents
  }

  return documents.filter(document => {
    let filename = document.filename

    if (!options.caseSensitive) {
      query = query.toLowerCase()
      filename = filename.toLowerCase()
    }

    return filename.indexOf(query) !== -1
  })
}
