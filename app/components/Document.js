import React, { PropTypes } from 'react'

const getPath = (path, root) => (
  path.replace(root, '')
)

const getRole = (path) => (
  path.split("/")[0].split(" ").pop()
)

const Document = ({ file, root }) => {
  const path = getPath(file.path, root)
  const role = getRole(path)

  return (
    <li>
      <a href={`file://${file.path}`}>
        <span className={`icon icon-${file.extension}`}></span>
        <div className="file-name">
          {file.filename}
        </div>
        <div className="path">
          {path}
        </div>
        <span data-role={role}
              className="role">
          {role}
        </span>
      </a>
    </li>
  )
}

Document.propTypes = {
  file: PropTypes.object,
  root: PropTypes.string
}

export default Document
