/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable */
// @ts-ignore
import { useEffect, useState, useCallback } from 'react';
import FontRow from './FontRow';

const InstallFonts = () => {
  const [font, setfont] = useState([]);
  const [installedfont, setInstalledfont] = useState<string[]>([]);

  const getBadgesFonts = () => {
    const tok = JSON.parse(localStorage.getItem('data') || '{}');
    if (tok.status === 1) {
      const requestOptions = {
        method: 'GET',
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${tok.data.access_token}`,
        },
      };
      fetch(
        // `${process.env.REACT_APP_API_URL}/api/v2/badges/getBadgeFonts`,
        `https:apidev.eventbuizz.com/api/v2/badges/getBadgeFonts`,
        requestOptions
      )
        .then((response) => response.json())
        .then((data) => {
          setfont(data);
        })
        .catch((error) => {
          console.log(error);
        });
    }
  };

  const getInstalledFonts = useCallback((): void => {
    window.electron.ipcRenderer.send('get-installed-fonts');
    window.electron.ipcRenderer.on('get-installed-fonts', (data: any) => {
      setInstalledfont(data.fonts);
      console.log(data);
    });
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    getInstalledFonts();
    getBadgesFonts();

  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const installFont = useCallback((path:string,filename:string, name:string) => {

    const tok = JSON.parse(localStorage.getItem('data'));
    if (tok.status === 1) {
      const headers = {
        'Access-Control-Allow-Origin': '*',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${tok.data.access_token}`,
      };
      window.electron.ipcRenderer.send('download-and-font', {
        headers,
        path,
        filename,
        name,
      });
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  window.electron.ipcRenderer.on('download-and-font', (data: any) => {
    setInstalledfont([...installedfont, data]);
  });


  return (
    <>
      <div className="preview-section">
        <h4>Fonts on Server</h4>
        <div className="font-list-wrapper">
          {font &&
            font.map((item: any, k: number) => (
              <FontRow installFont={installFont} installedfont={installedfont} item={item} key={item.name}/>
            ))}
        </div>
      </div>
    </>
  );
};

export default InstallFonts;
