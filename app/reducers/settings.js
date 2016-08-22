const settings = (state = {}, action) => {
  switch (action.type) {
    case 'UPDATE_SETTING':
      return {
        ...state,
        [action.id]: action.value
      }
    default:
      return state
  }
}

export default settings
