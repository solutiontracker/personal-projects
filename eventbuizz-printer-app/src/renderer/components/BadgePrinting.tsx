/* eslint-disable global-require */
import { FC, useState } from 'react';
import { useOutletContext } from 'react-router-dom';
import PrintBadge from './PrintBadge';
import { Terminal } from '../types/LoginDataTypes';
import PrinterSettings from './PrinterSettings';

const BadgePrinting: FC = () => {
  const {
    setPrinter,
    setPrinterQueueCount,
    setQueueRange,
    setTerminal,
    setConnectToServer,
    badgeSvg,
    terminals,
    terminal,
    queueRanges,
    queueRange,
    printers,
    printer,
    printTimeline,
    connectToServer,
    socket,
  } = useOutletContext<any>();
  const [connectionError, setConnectionError] = useState(false);
  const [settingsOpen, setSettingsOpen] = useState(false);
  return (
    <>
      <div className="printing-section">
        <h4>Badge printing</h4>
        <div className="printing-panel">
          <div className="form-row">
            <label htmlFor="a">
              <span className="title">Terminal</span>
              <select
                id="a"
                value={terminal}
                onChange={(e) => {
                  setTerminal(e.currentTarget.value);
                  localStorage.setItem('terminal', e.currentTarget.value);
                }}
              >
                {terminals?.map((current_terminal: Terminal) => {
                  return (
                    <option
                      key={current_terminal.id}
                      value={current_terminal.name}
                    >
                      {current_terminal.name}
                    </option>
                  );
                })}
              </select>
            </label>
          </div>
          <div className="form-row">
            <label htmlFor="b">
              <span className="title">Queue</span>
              <select
                id="b"
                value={queueRange}
                onChange={(e) => {
                  setQueueRange(e.currentTarget.value);
                  localStorage.setItem('queueRange', e.currentTarget.value);
                }}
              >
                <option value="0">Please Select</option>
                {queueRanges?.map((current_queueRange: string) => {
                  return (
                    <option
                      key={current_queueRange}
                      value={current_queueRange}
                    >
                      {current_queueRange}
                    </option>
                  );
                })}
              </select>
            </label>
          </div>
          <div className="form-row">
            <label htmlFor="c">
              <span className="title">Select Printer</span>
              <select
                id="c"
                value={printer}
                onChange={(e) => {
                  setPrinter(e.currentTarget.value);
                  localStorage.setItem('printer', e.currentTarget.value);
                  setPrinterQueueCount(0);
                  window.electron.ipcRenderer.send(
                    'printer-selected',
                    e.currentTarget.value
                  );
                  window.electron.ipcRenderer.on(
                    'get-printer-jobs',
                    (data: any) => {
                      if (data.jobs) {
                        setPrinterQueueCount(data.jobs.length);
                      }
                    }
                  );
                }}
              >
                <option value="0">Please Select</option>
                {printers?.map((current_printer: any) => {
                  return (
                    <option
                      key={current_printer.name}
                      value={current_printer.name}
                    >
                      {current_printer.name}
                    </option>
                  );
                })}
              </select>
            </label>
          </div>
          
          <div className="form-row">
            <button
              type="button"
              onClick={()=>{setSettingsOpen(!settingsOpen)}}
            >
              Printer Settings
            </button>
          </div>
          <div className="form-row">
            <button
              onClick={() => {
                if (
                  printer !== null &&
                  queueRange !== null &&
                  terminal !== null &&
                  socket.connected
                ) {
                  setConnectionError(false);
                  setConnectToServer(!connectToServer);
                } else {
                  setConnectionError(true);
                }
              }}
              type="button"
            >
              {!connectToServer
                ? 'CONNECT TO SERVER'
                : 'DISCONNECT FROM SERVER'}
            </button>
            {connectionError && (
              <div className="error">
                Could not connect make sure terminal, queue and printer are
                selected{' '} or the printer server is running...
              </div>
            )}
          </div>
          <div className="form-row">
            <h4>Message form server</h4>
            <div className="printer-logs">
              {printTimeline.map((t: string) => {
                return (
                  <div key={t} className="list">
                    {t}
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
      <div className="preview-section">
        <h4>Badge preview</h4>
        <PrintBadge data={badgeSvg}  />
      </div>
      {settingsOpen && <PrinterSettings setSettingsOpen={setSettingsOpen} settingsOpen={settingsOpen} />}
    </>
  );
};

export default BadgePrinting;
