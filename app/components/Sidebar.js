import React from 'react'
import Role from './Role'

const roles = [ 'IM', 'PM', 'QM', 'RE', 'USE', 'ST', 'SA' ]

const Sidebar = () => (
  <div className="sidebar">
    <ul className="unstyled-list roles-list">
      {roles.map((role, index) => (
        <Role key={index} role={role} />
      ))}
    </ul>
  </div>
)

export default Sidebar
