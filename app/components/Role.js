import React, { PropTypes } from 'react'
import { connect } from 'react-redux'
import { toggleRole } from '../actions'

let Role = ({ role, disabled, onToggleRole }) => (
  <li>
    <span onClick={() => onToggleRole(role)}
          className={disabled ? 'disabled' : ''}
          data-role={role}>
      {role}
    </span>
  </li>
)

Role.propTypes = {
  role: PropTypes.string,
  disabled: PropTypes.boolean,
  onToggleRole: PropTypes.func
}

const mapStateToProps = ({ selectedRole }, { role }) => ({
  disabled: selectedRole !== null && selectedRole !== role
})

Role = connect(
  mapStateToProps,
  { onToggleRole: toggleRole }
)(Role)

export default Role
