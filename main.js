var app = require('app')
var BrowserWindow = require('browser-window')
var shell = require('shell')

var mainWindow = null

app.on('window-all-closed', function() {
  app.quit()
})

app.on('ready', function () {
  mainWindow = new BrowserWindow({ width: 800, height: 600 })
  mainWindow.loadUrl('file://' + __dirname + '/index.html')

  mainWindow.on('closed', function() {
    mainWindow = null
  })

  mainWindow.webContents.on('will-navigate', function (event, url) {
    event.preventDefault()
    shell.openItem(decodeURI(url.replace('file://', '')))
  })
})