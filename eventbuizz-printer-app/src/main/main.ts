/* eslint global-require: off, no-console: off, promise/always-return: off */

/**
 * This module executes inside of electron's main process. You can start
 * electron renderer process from here and communicate with the other processes
 * through IPC.
 *
 * When running `npm run build` or `npm run build:main`, this file is compiled to
 * `./src/main.js` using webpack. This gives us some performance wins.
 */
import path from 'path';
import { app, BrowserWindow, shell, ipcMain } from 'electron';
import { autoUpdater } from 'electron-updater';
import log from 'electron-log';
import printer from 'printer';
import { resolveHtmlPath } from './util';


const fs = require('fs');
const axios = require('axios');
const fontList = require('font-list')
const installfont = require('installfont');


export default class AppUpdater {
  constructor() {
    log.transports.file.level = 'info';
    autoUpdater.logger = log;
    autoUpdater.checkForUpdatesAndNotify();
  }
}

let mainWindow: BrowserWindow | null = null;

ipcMain.on('ipc-example', async (event, arg) => {
  const msgTemplate = (pingPong: string) => `IPC test: ${pingPong}`;
  event.reply('ipc-example', msgTemplate('pong'));
});

if (process.env.NODE_ENV === 'production') {
  const sourceMapSupport = require('source-map-support');
  sourceMapSupport.install();
}

const isDevelopment =
  process.env.NODE_ENV === 'development' || process.env.DEBUG_PROD === 'true';

if (isDevelopment) {
  require('electron-debug')();
}

const installExtensions = async () => {
  const installer = require('electron-devtools-installer');
  const forceDownload = !!process.env.UPGRADE_EXTENSIONS;
  const extensions = ['REACT_DEVELOPER_TOOLS'];

  return installer
    .default(
      extensions.map((name) => installer[name]),
      forceDownload
    )
    .catch(console.log);
};

const createWindow = async () => {
  if (isDevelopment) {
    await installExtensions();
  }

  const RESOURCES_PATH = app.isPackaged
    ? path.join(process.resourcesPath, 'assets')
    : path.join(__dirname, '../../assets');

  const getAssetPath = (...paths: string[]): string => {
    return path.join(RESOURCES_PATH, ...paths);
  };

  mainWindow = new BrowserWindow({
    show: false,
    width: 1050,
    minWidth: 1024,
    height: 728,
    minHeight: 715,
    vibrancy: 'dark',
    transparent: true,
    icon: getAssetPath('icon.png'),
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true,
      experimentalFeatures: false,
    },
  });
  mainWindow.loadURL(resolveHtmlPath('index.html'));

  mainWindow.on('ready-to-show', () => {
    if (!mainWindow) {
      throw new Error('"mainWindow" is not defined');
    }
    if (process.env.START_MINIMIZED) {
      mainWindow.minimize();
    } else {
      mainWindow.show();
    }
  });

  mainWindow.on('closed', () => {
    mainWindow = null;
  });

  // const menuBuilder = new MenuBuilder(mainWindow);
  // menuBuilder.buildMenu();
  mainWindow.setMenuBarVisibility(true);
  // Open urls in the user's browser
  mainWindow.webContents.setWindowOpenHandler((edata) => {
    shell.openExternal(edata.url);
    return { action: 'deny' };
  });

  // Remove this if your app does not use auto updates
  // eslint-disable-next-line
  new AppUpdater();
};

/**
 * Add event listeners...
 */

 const appPath = () => {
  switch(process.platform) {
    case 'darwin': {
      return path.join(process.env.HOME, 'Library', 'Application Support');
    }
    case 'win32': {
      return process.env.APPDATA;
    }
    case 'linux': {
      return process.env.HOME;
    }
    default:{
      return '';
    }
  }
}

const logData = (data:any) =>{
  const logPath = path.join(appPath(), "\\", `eventbuizz-printer/log.txt`);
  if (!fs.existsSync(logPath)) {
    fs.writeFile(logPath , '', function (err) {
      if (err) throw err;
      console.log('Saved!');
    });
}
fs.appendFile(logPath, `${data} \n`,  (err:any) => {
  if (err) {
    throw err
  }
  console.log('File is updated.')
});
} 

ipcMain.on("printers", async (event) => {
  event.reply("printers", printer.getPrinters());
});

ipcMain.on("print", async (event, args) => {
  const printerSettings = args.printerSettings;
  const fullPath = path.join(appPath(), "\\", `eventbuizz-printer/printBadge.html`);
  
  const contents = `<html><head><style>*{padding:0;margin:0} body{width:${parseInt(printerSettings.pageSize.width * Math.round(3.7795275591))}px; height:${parseInt(printerSettings.pageSize.height * Math.round(3.7795275591))}px;} @media print {-webkit-print-color-adjust: exact;}</style></head><body>${args.svg}</body></html>`;
  fs.writeFileSync(fullPath, contents);
  let options = {
    silent: true,
    deviceName: args.printer,
    printBackground: true,
    color: true,
    margin: {
      marginType: 'custom',
      top:printerSettings.top ? printerSettings.top : 0,
      bottom:printerSettings.bottom ? printerSettings.bottom : 0,
      right:printerSettings.right ? printerSettings.right : 0,
      left:printerSettings.left ? printerSettings.left : 0,
    },
    landscape: (printerSettings.orientation &&  printerSettings.orientation === "landscape") ? true : false,
    pagesPerSheet: 1,
    collate: true,
    generateDraftData: true,
    printToPDF: true,
    shouldPrintBackgrounds: false,
    shouldPrintSelectionOnly: false,
    rasterizePDF: false,
    isFirstRequest: false,
    previewModifiable: true,
    printWithCloudPrint: false,
    printWithPrivet: false,
    printWithExtension: false,
    copies: 1,
    header: '',
    footer: '',
  };



  if(printerSettings.pageSize){
    options= {...options, pageSize:{
      height: parseInt(parseInt(printerSettings.pageSize.height) * 1000),
      width: parseInt(parseInt(printerSettings.pageSize.width) * 1000),
    },};
  }
  

  if(printerSettings.duplex && printerSettings.duplex !== "false"){
    options= {...options, duplexMode:printerSettings.duplex};
  }
  
  const workWindow = new BrowserWindow({
    show: false,
    minWidth: 360,
    minHeight: 613,
    width:parseInt(printerSettings.pageSize.width * Math.round(3.7795275591)),
    skipTaskbar:true,
    autoHideMenuBar:true,
    height:parseInt(printerSettings.pageSize.height * Math.round(3.7795275591)),
    webPreferences: {
      nodeIntegration: true,
      contextIsolation: true,
    }
  });

  workWindow.webContents.loadFile(fullPath);
    let p1 = new Promise((resolve) => workWindow.webContents.on('did-finish-load', resolve))
    let p2 = new Promise((resolve) => workWindow.on('ready-to-show', resolve))
    Promise.all([p1, p2])
      .then(() => {

        logData("loaded and ready to show");
        
        console.log('resolved') // did both promises reslove?
        workWindow.webContents.print(options, (success, failureReason) => {
          logData("inside print");
          if (!success) {
            logData("not_success");
            const contents = failureReason;
            logData(contents);
          } 
          if(success){
            event.reply("get-printer-jobs", printer.getPrinter(args.printer));
            logData("success");
          }
          logData("before close");
          workWindow.close();
        });
      })

});

ipcMain.on("printer-selected", async (event, arg) => {
  event.reply("get-printer-jobs", printer.getPrinter(arg));
});

ipcMain.on("get-installed-fonts", async (event, arg) => {
  fontList.getFonts({ disableQuoting: true })
  .then((fonts:any) => {
      event.reply("get-installed-fonts", {fonts});
    })
    .catch((err:any) => {
        console.log(err)
    })
  });

ipcMain.on("download-and-font", async (event, args) => {
  const dir = path.join(appPath(), "\\", `eventbuizz-printer/fonts`);
  if (!fs.existsSync(dir)){
    fs.mkdirSync(dir);
  }
  if(!fs.existsSync(path.join(appPath(), "\\", `eventbuizz-printer/fonts/${args.filename}`))){
  const writer = fs.createWriteStream(path.join(appPath(), "\\", `eventbuizz-printer/fonts/${args.filename}`));
  axios({
    method:"GET",
    url:args.path,
    headers:args.headers,
    responseType: 'stream'
  })
    .then((response:any) => {
      return new Promise((resolve, reject) => {
        response.data.pipe(writer);
        let error = null;
        writer.on('error', err => {
          error = err;
          writer.close();
          reject(err);
        });
        writer.on('close', () => {
          if (!error) {
            resolve(true);
          }
        });
      });
    })
    .catch((error:any) => {
      console.log(error);
    });
  }
    installfont(path.join(appPath(), "\\", `eventbuizz-printer/fonts/${args.filename}`), function(err) {
      if(err) console.log(err, err.stack);
      event.reply("download-and-font", args.name);
    });
});

app.on('window-all-closed', () => {
  // Respect the OSX convention of having the application in memory even
  // after all windows have been closed
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app
  .whenReady()
  .then(() => {
    createWindow();
    app.on('activate', () => {
      // On macOS it's common to re-create a window in the app when the
      // dock icon is clicked and there are no other windows open.
      if (mainWindow === null) createWindow();
    });
  })
.catch(console.log);
