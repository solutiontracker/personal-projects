/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable */
// @ts-ignore
import { FC, useEffect, useState, useCallback, useRef } from 'react';
import { Outlet } from 'react-router-dom';
import {fabric} from 'fabric';
import socketIOClient from 'socket.io-client';
import QRCode from 'easyqrcodejs';
import { queueRangeOptions, convertBools, ratio2cssType } from '../utils/helper';
import { LoginResponseData, Terminal } from '../types/LoginDataTypes';
import { Printer, PrintJsonData } from '../types/PrinterDataTypes';
import Sidebar from './Sidebar';

const Dashboard: FC = () => {
  const qrCodeRef = useRef(null);
  const printerSettings = JSON.parse(localStorage.getItem('printerSettings') || '{}');

  const [badgeSvg, setbadgeSvg] = useState<string | null>(null);
  const [terminals, setTerminals] = useState<Terminal[] | null>(null);
  const [printers, setPrinters] = useState<Printer[] | null>(null);
  const [connectToServer, setConnectToServer] = useState<boolean>(false);
  const [queueRanges] = useState<string[]>(queueRangeOptions);
  const [terminal, setTerminal] = useState<string | null>(localStorage.getItem('terminal'));
  const [printer, setPrinter] = useState<string | null>(localStorage.getItem('printer'));
  const [queueRange, setQueueRange] = useState<string | null>(localStorage.getItem('queueRange'));
  const [printTimeline, setPrintTimeline] = useState<string[]>([]);
  const [printHistory, setPrintHistory] = useState<any[]>([]);
  const [PrinterQueueCount, setPrinterQueueCount] = useState<number>(0);
  const [badgeHeight, setBadgeHeight] = useState<number>(0);
  const [badgeWidth, setBadgeWidth] = useState<number>(0);

  // const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);
  const socket = socketIOClient('https://devsocket.eventbuizz.com:3400');

  const getLoggedInUserData = useCallback((): null | LoginResponseData => {
    const localData: null | string = localStorage.getItem('data');
    if (localData) {
      return JSON.parse(localData).data;
    }
    return null;
  }, []);

  const makeCode = useCallback((url, height, width): null | LoginResponseData => {
    const qrcode = new QRCode(qrCodeRef.current, {
      text: url,
      width,
      height
    });
    return qrcode._oDrawing._elCanvas.toDataURL("image/png");
  }, []);

  const getPrinterList = useCallback((): void => {
    window.electron.ipcRenderer.send('printers');
    window.electron.ipcRenderer.on('printers', (data: Printer[]) => {
      setPrinters(data);
      console.log(data);
    });
  }, []);

  const onAddCircle = (data) => {
    const Badgejson: any = convertBools(JSON.parse(data.badge_json_data));
    console.log(Badgejson);
    Badgejson.objects.forEach(
      (element: {
        type: string;
        text: string;
        width: string;
        height: string;
        fontSize: string;
        formName: string;
        innerHTML: any;
      }) => {
        if (element.type === 'textbox' && element.formName !== 'isCustomText') {
          const textBlock = document.getElementById('textblock');
          const formname = element.formName;
          // console.log(element);
          // console.log(formname);
          // console.log(data[formname]);
          textBlock.innerHTML = data[formname] ? data[formname] : element.text;
          textBlock.style.fontSize = `${(element.fontSize * element.scaleX)}px`;
          element.text = data[formname] ? data[formname] : '';
          if (formname === 'Textfield') {
            element.text = data.textfield ? data.textfield : '';
          }
          if (formname === 'Textfield_1') {
            element.text = data.textfield_1 ? data.textfield_1 : '';
          }
          if (textBlock.clientWidth > element.width) {
            element.fontSize *= element.width / (textBlock.clientWidth + 1);
          }
        }
        else if(element.type === 'image' && (element.backgroundColor === 'badgeQRCode_1' || element.backgroundColor === 'badgeQRCode')){
          if(element.backgroundColor === 'badgeQRCode') element.src = makeCode(data.barcode, element.height, element.width);
          if(element.backgroundColor === 'badgeQRCode_1') element.src = makeCode(data.barcode_1, element.height, element.width);
        }
        else if(element.type === 'image' && element.backgroundColor === 'IsLogo'){
          if(element.backgroundColor === 'IsLogo') element.src = data.logo;
        }
      }
    );
    // Badgejson.objects.forEach(element => {
    //   console.log(typeof element.text, element.type, element.formName);
    // });

      const canvas = new fabric.Canvas('badgeCanvas');
      canvas.loadFromJSON(Badgejson, ()=>{
        canvas.selection = false;
        canvas.renderAll();
        canvas.forEachObject(function (o: any) {
              o.selectable = false;
            });
        const svg = canvas.toSVG();
        setbadgeSvg(svg);
        console.log(badgeSvg);
        const printerSettings = JSON.parse(localStorage.getItem('printerSettings') || '{}');
        window.electron.ipcRenderer.send('print', { svg, printer, printerSettings, badgeHeight, badgeWidth });
        window.electron.ipcRenderer.on('get-printer-jobs', (res: any) => {
          if (res.jobs) {
            setPrinterQueueCount(res.jobs.length);
          }
        });
      });
  };

  useEffect(() => {
    const data = getLoggedInUserData();
    if (data) {
      setTerminals(data.terminals);
    }
    getPrinterList();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    // eslint-disable-next-line @typescript-eslint/no-shadow
console.log(connectToServer);
    let interval: any;
    if (connectToServer) {
      const userData = getLoggedInUserData();
      socket.on('event', (response: any) => {
        console.log("onEvent");
        if (Number(response?.response?.status) === 1) {
          const json: PrintJsonData = JSON.parse(
            response?.response?.job_detail
          );
          console.log(json);
          const d = new Date();
          setPrintTimeline([
            `Print Badge at ... ${d.toLocaleTimeString()}`,
            ...printTimeline,
          ]);
          setPrintHistory([
            {
              attendee: json.name,
              date: d.toLocaleString(),
              badgeName: json.badge_name,
            },
            ...printHistory,
          ]);
          const Bhw = ratio2cssType(Number(json.width), Number(json.height));
          setBadgeHeight(Bhw.height);
          setBadgeWidth(Bhw.width);
          console.log(Bhw, "badge");
          console.log(badgeWidth);
          onAddCircle(json);
          // console.log(json);
        } else {
          const d = new Date();
          setPrintTimeline([
            `No badge to Print at ... ${d.toLocaleTimeString()}`,
            ...printTimeline,
          ]);
        }
      });

      interval = setInterval(() => {
        // console.log(PrinterQueueCount, 'count');
        if (queueRange && PrinterQueueCount < parseFloat(queueRange)) {
        console.log("dafasdf");

          socket.emit('event', {
            env: 'local',
            event_id: userData?.event.id,
            terminal,
            type: '',
          });
          // console.log('This will run every second!');
        }
      }, 1000);
    }
    return () => {
      if (connectToServer) {
        clearInterval(interval);
      }
      socket.disconnect();
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [socket]);

  return (
    <div className="dashboard-area">
      <Sidebar connectToServer={connectToServer} />
      <Outlet
        context={{
          setPrinter,
          setPrinterQueueCount,
          setQueueRange,
          setTerminal,
          setConnectToServer,
          badgeSvg,
          terminals,
          queueRanges,
          queueRange,
          terminal,
          printers,
          printer,
          connectToServer,
          printTimeline,
          printHistory,
          socket
        }}
      />

      <div style={{ visibility:'hidden', width:`613px`, height:`1000px`, position:'absolute'}}>
        <div id="textblock" />
        <div ref={qrCodeRef} />
         <canvas id="badgeCanvas" width={613} height={1000} />
      </div>
    </div>
  );
};

export default Dashboard;
