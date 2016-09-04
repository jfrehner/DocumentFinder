import React, { PropTypes } from 'react'
import Modal from 'react-modal'
import { connect } from 'react-redux'
import { updateSetting, toggleModal, parseData } from '../actions'

class Settings extends React.Component {
  static propTypes = {
    modal: PropTypes.object,
    settings: PropTypes.object,
    updateSetting: PropTypes.func,
    parseData: PropTypes.func,
    toggleModal: PropTypes.func
  }

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
      borderRadius: 0,
      padding: '1.5em 1.5em 6em'
    }
  }

  render() {
    const { modal, settings, updateSetting, parseData, toggleModal } = this.props
    const parsed = new Date(settings.lastParsed)

    return (
      <Modal isOpen={modal.isOpen}
             style={Settings.modalStyle}>
        <div className={modal.isParsing ? 'is-parsing' : ''} />
        <div className="modal-group">
          <h3>
            Root Folder
          </h3>
          <p className="modal-description">
            Select the root path that contains your document repository.
          </p>
          <input type="text"
                 className="text-input full-width"
                 defaultValue={settings.root}
                 onBlur={(ev) => updateSetting('root', ev.target.value)} />
        </div>
        <div className="modal-group">
          <h3>
            Ignored Names
          </h3>
          <p className="modal-description">
            Enter a comma-separated list of folders or files to ignore. The items are matched as-is, so try not to include overly complex (i.e. multi-level) paths.
          </p>
          <input type="text"
                 className="text-input full-width"
                 defaultValue={settings.ignore.join(', ')}
                 onBlur={(ev) => updateSetting('ignore', ev.target.value.split(/, ?/))} />
        </div>
        <div className="modal-group">
          <h3>
            Parsed Data
          </h3>
          <p className="modal-description">
            Data was last parsed {`${parsed.getDate()}.${parsed.getMonth() + 1}.${parsed.getFullYear()}, ${parsed.getHours()}:${parsed.getMinutes()}`}.
          </p>
          <button data-button
                  onClick={() => parseData(settings)}>
            Parse Data
          </button>
        </div>
        <footer className="modal-footer">
          <button data-button
                  onClick={toggleModal}>
            Close
          </button>
        </footer>
      </Modal>
    )
  }
}

const mapStateToProps = ({ isParsing, settings, modal }) => ({
  isParsing: isParsing,
  settings: settings,
  modal: modal
})

export default connect(
  mapStateToProps,
  {
    updateSetting,
    toggleModal,
    parseData
  }
)(Settings)
