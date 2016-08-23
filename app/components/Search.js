import React from 'react'
import { connect } from 'react-redux'
import { queryChange } from '../actions'
import SearchOption from './SearchOption'

const options = [{
  id: 'caseSensitive',
  name: 'Case Sensitive',
  active: false
}]

let Search = ({ onQueryChange }) => (
  <div className="input-wrapper">
    <input className="search-input" type="text" placeholder="Enter a document name" autoFocus onChange={(ev) => onQueryChange(ev.target.value)} />
    <div className="search-options">
      {options.map((option, index) => (
        <SearchOption key={index} option={option} />
      ))}
    </div>
  </div>
)

Search.propTypes = {
  onQueryChange: React.PropTypes.func
}

Search = connect(
  null,
  { onQueryChange: queryChange }
)(Search)

export default Search
