const byId = (state = {}, action) => {
  if (action.response) {
    return {
      ...state,
      ...action.response.entities.documents
    }
  }

  return state
}

export default byId

export const getDocument = (state, id) => state[id]
