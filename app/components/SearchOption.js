import React from 'react'
import { connect } from 'react-redux'
import { toggleOption } from '../actions'

let SearchOption = ({ option, active, onToggleOption }) => (
  <span onClick={() => onToggleOption(option.id)} className={`label ${active ? 'active' : ''}`}>{option.name}</span>
)

const mapStateToProps = ({ options }, { option }) => ({
  active: options[option.id]
})

SearchOption = connect(
  mapStateToProps,
  { onToggleOption: toggleOption }
)(SearchOption)

export default SearchOption
