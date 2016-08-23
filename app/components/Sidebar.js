import React from 'react'
import { connect } from 'react-redux'
import Role from './Role'
import { toggleModal } from '../actions'

const roles = [ 'IM', 'PM', 'QM', 'RE', 'USE', 'ST', 'SA' ]

let Sidebar = ({ toggleModal }) => (
  <div className="sidebar-wrapper">
    <div className="sidebar">
      <ul className="unstyled-list roles-list">
        {roles.map((role, index) => (
          <Role key={index} role={role} />
        ))}
      </ul>
    </div>
    <button data-button className="modal-toggle" onClick={() => toggleModal()}><span className="icon-gear"></span></button>
  </div>
)

Sidebar.propTypes = {
  toggleModal: React.PropTypes.func
}

Sidebar = connect(
  null,
  { toggleModal }
)(Sidebar)

export default Sidebar
