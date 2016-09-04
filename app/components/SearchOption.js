import React, { PropTypes } from 'react'
import { connect } from 'react-redux'
import { toggleOption } from '../actions'

let SearchOption = ({ option, active, toggleOption }) => (
  <span onClick={() => toggleOption(option.id)}
        className={`label ${active ? 'active' : ''}`}>
    {option.name}
  </span>
)

SearchOption.propTypes = {
  option: PropTypes.object,
  active: PropTypes.boolean,
  toggleOption: PropTypes.func
}

const mapStateToProps = ({ options }, { option }) => ({
  active: options[option.id]
})

SearchOption = connect(
  mapStateToProps,
  { toggleOption }
)(SearchOption)

export default SearchOption
