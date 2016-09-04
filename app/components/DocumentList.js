import React, { PropTypes } from 'react'
import { connect } from 'react-redux'
import { getVisibleDocuments, getIsStale } from '../reducers'
import { fetchDocuments } from '../actions'
import Document from './Document'

class DocumentList extends React.Component {
  static propTypes = {
    documents: PropTypes.arrayOf(PropTypes.object),
    isStale: PropTypes.boolean,
    isParsing: PropTypes.boolean,
    root: PropTypes.string,
    role: PropTypes.string,
    fetchDocuments: PropTypes.func
  }

  componentDidMount() {
    this.props.fetchDocuments(this.props.role || 'all')
  }

  componentDidUpdate(oldProps) {
    const { isParsing, isStale, role, fetchDocuments } = this.props

    if (isParsing) {
      return
    }

    if (isStale || oldProps.role !== role) {
      fetchDocuments(role || 'all')
    }
  }

  render() {
    const { documents, root } = this.props

    return (
      <div className="content">
        <ul className="files-list unstyled-list">
          {documents.map(document => (
            <Document key={document.id}
                      file={document}
                      root={root} />
          ))}
        </ul>
      </div>
    )
  }
}

const mapStateToProps = (state) => ({
  documents: getVisibleDocuments(state, state.selectedRole || 'all', state.query, state.options),
  isStale: getIsStale(state, state.selectedRole || 'all'),
  isParsing: state.modal.isParsing,
  root: state.settings.root,
  role: state.selectedRole
})

export default connect(
  mapStateToProps,
  { fetchDocuments }
)(DocumentList)
