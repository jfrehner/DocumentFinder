import React from 'react'
import Sidebar from './Sidebar'
import Search from './Search'
import DocumentList from './DocumentList'

const DocumentFinder = () => (
  <div className="app">
    <Sidebar />
    <div className="content-wrapper">
      <Search />
      <DocumentList />
    </div>
  </div>
)

export default DocumentFinder
