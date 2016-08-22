import React from 'react'
import { connect } from 'react-redux'
import { getVisibleDocuments } from '../reducers'
import { fetchDocuments } from '../actions'
import Document from './Document'

class DocumentList extends React.Component {
  componentDidMount() {
    this.props.fetchDocuments(this.props.role || 'all')
  }

  componentDidUpdate(oldProps) {
    if (oldProps.role !== this.props.role) {
      this.props.fetchDocuments(this.props.role || 'all')
    }
  }

  render() {
    const { documents, root } = this.props

    return (
      <div className="content">
        <ul className="files-list unstyled-list">
          {documents.map(document => (
            <Document key={document.id} file={document} root={root} />
          ))}
        </ul>
      </div>
    )
  }
}

const mapStateToProps = (state) => ({
  documents: getVisibleDocuments(state, state.selectedRole || 'all', state.query, state.options),
  root: state.settings.root,
  role: state.selectedRole
})

export default connect(
  mapStateToProps,
  { fetchDocuments }
)(DocumentList)
