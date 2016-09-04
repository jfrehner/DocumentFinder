const options = (state = {}, action) => {
  switch (action.type) {
    case 'TOGGLE_OPTION':
      return {
        ...state,
        [action.id]: !state[action.id]
      }
    default:
      return state
  }
}

export default options
