import React from 'react'
import Modal from 'react-modal'
import { connect } from 'react-redux'
import { ipcRenderer } from 'electron'
import { updateSetting, toggleModal } from '../actions'

class Settings extends React.Component {
  static modalStyle = {
    overlay: {
      zIndex: 100
    },
    content: {
      top: 0,
      left: 0,
      right: 0,
      bottom: 0,
      border: 0,
      backgroundColor: '#fff',
      padding: '1.5em'
    }
  }

  render() {
    const { settings, onUpdateSetting } = this.props
    const lastParsed = new Date(settings.lastParsed)

    return (
      <Modal isOpen={this.props.modal}
             style={Settings.modalStyle}>
        <div className="modal-group">
          <h3>Root Folder</h3>
          <p className="modal-description">Select the root path that contains your document repository.</p>
          <input type="text" className="text-input full-width" defaultValue={settings.root} onBlur={(ev) => onUpdateSetting('root', ev.target.value)} />
        </div>
        <div className="modal-group">
          <h3>Ignored Names</h3>
          <p className="modal-description">Enter a comma-separated list of folders or files to ignore. The items are matched as-is, so try not to include overly complex (i.e. multi-level) paths.</p>
          <input type="text" className="text-input full-width" defaultValue={settings.ignore.join(', ')} onBlur={(ev) => onUpdateSetting('ignore', ev.target.value.split(/, ?/))} />
        </div>
        <div className="modal-group">
          <h3>Parsed Data</h3>
          <p className="modal-description">Data was last parsed {`${lastParsed.getDate()}.${lastParsed.getMonth() + 1}.${lastParsed.getFullYear()}, ${lastParsed.getHours()}:${lastParsed.getMinutes()}`}.</p>
          <button data-button onClick={() => ipcRenderer.send('parse', settings)}>Parse Data</button>
        </div>
        <footer className="modal-footer">
          <button data-button onClick={() => this.props.toggleModal()}>Close</button>
        </footer>
      </Modal>
    )
  }
}

const mapStateToProps = (state) => ({
  settings: state.settings,
  modal: state.modal
})

export default connect(
  mapStateToProps,
  {
    onUpdateSetting: updateSetting,
    toggleModal
  }
)(Settings)
