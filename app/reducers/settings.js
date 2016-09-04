const settings = (state = {}, action) => {
  switch (action.type) {
    case 'UPDATE_SETTING':
      return {
        ...state,
        [action.key]: action.value
      }
    default:
      return state
  }
}

export default settings
