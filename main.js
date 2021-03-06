'use strict'

const electron = require('electron')
const ipc = require('electron').ipcMain
const dialog = require('dialog')
const recursive = require('recursive-readdir')
const fs = require('fs')

const app = electron.app
const BrowserWindow = electron.BrowserWindow
const Shell = electron.shell
const Menu = electron.Menu

let mainWindow = null
let folder = null

ipc.on('parse', (event, args) => {
  let folder = args.root
  let ignore = args.ignore.filter(i => i !== '')

  const ignoreFunc = (file, stats) => {
    const allowed = [
      'docx',
      'doc',
      'xlsx',
      'xls',
      'pptx',
      'ppt',
      'pdf',
      'txt'
    ]

    let isValidFile = false

    if (file) {
      isValidFile = allowed.filter(a => file.indexOf(a) !== -1).length !== 0 && ignore.filter(i => file.indexOf(i) !== -1).length === 0
    }

    return !(stats.isDirectory() || isValidFile)
  }

  recursive(folder, [ignoreFunc], (err, files) => {
    let res = {
      documents: []
    }

    let nextId = 0

    files.forEach((f) => res.documents.push({
      id: nextId++,
      filename: f.substr(f.lastIndexOf('/') + 1),
      extension: f.substr(f.lastIndexOf('.') + 1),
      path: f
    }))

    fs.writeFile('data.js', JSON.stringify(res), () => {
      mainWindow.webContents.send('parsed')
    })
  })
})

app.on('window-all-closed', () => app.quit())

app.on('ready', () => {
  mainWindow = new BrowserWindow({
      width: 800,
      height: 600,
      center: true
  })

  mainWindow.loadURL('file://' + __dirname + '/index.html')

  mainWindow.on('closed', () => mainWindow = null)

  mainWindow.webContents.on('will-navigate', (event, url) => {
    event.preventDefault()
    Shell.openItem(decodeURI(url.replace('file://', '')))
  })

  const template = [
    {
      label: 'Monomo',
      submenu: [
        {
          label: 'About Monomo',
          selector: 'orderFrontStandardAboutPanel:'
        },
        {
          type: 'separator'
        },
        {
          label: 'Services',
          submenu: []
        },
        {
          type: 'separator'
        },
        {
          label: 'Hide Monomo',
          accelerator: 'Command+H',
          selector: 'hide:'
        },
        {
          label: 'Hide Others',
          accelerator: 'Command+Shift+H',
          selector: 'hideOtherApplications:'
        },
        {
          label: 'Show All',
          selector: 'unhideAllApplications:'
        },
        {
          type: 'separator'
        },
        {
          label: 'Quit',
          accelerator: 'Command+Q',
          click: function() { app.quit() }
        }
      ]
    },
    {
      label: 'Edit',
      submenu: [
        {
          label: 'Undo',
          accelerator: 'Command+Z',
          selector: 'undo:'
        },
        {
          label: 'Redo',
          accelerator: 'Shift+Command+Z',
          selector: 'redo:'
        },
        {
          type: 'separator'
        },
        {
          label: 'Cut',
          accelerator: 'Command+X',
          selector: 'cut:'
        },
        {
          label: 'Copy',
          accelerator: 'Command+C',
          selector: 'copy:'
        },
        {
          label: 'Paste',
          accelerator: 'Command+V',
          selector: 'paste:'
        },
        {
          label: 'Select All',
          accelerator: 'Command+A',
          selector: 'selectAll:'
        }
      ]
    },
    {
      label: 'Window',
      submenu: [
        {
          label: 'Minimize',
          accelerator: 'Command+M',
          selector: 'performMiniaturize:'
        },
        {
          label: 'Close',
          accelerator: 'Command+W',
          selector: 'performClose:'
        },
        {
          type: 'separator'
        },
        {
          label: 'Bring All to Front',
          selector: 'arrangeInFront:'
        }
      ]
    }
  ]

  let menu = Menu.buildFromTemplate(template)
  Menu.setApplicationMenu(menu)
})
