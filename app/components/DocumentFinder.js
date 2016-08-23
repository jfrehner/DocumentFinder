import React from 'react'
import Sidebar from './Sidebar'
import Search from './Search'
import DocumentList from './DocumentList'
import Settings from './Settings'

const DocumentFinder = () => (
  <div className="app">
    <Sidebar />
    <div className="content-wrapper">
      <Search />
      <DocumentList />
    </div>
    <Settings />
  </div>
)

export default DocumentFinder
