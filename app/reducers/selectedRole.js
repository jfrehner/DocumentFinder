const selectedRole = (state = null, action) => {
  switch (action.type) {
    case 'TOGGLE_ROLE':
      return state !== action.role ? action.role : null
    default:
      return state
  }
}

export default selectedRole
