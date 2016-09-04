const query = (state = '', action) => {
  switch (action.type) {
    case 'QUERY_CHANGE':
      return action.text
    default:
      return state
  }
}

export default query
