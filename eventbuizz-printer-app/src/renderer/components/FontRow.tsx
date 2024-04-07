/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable */
// @ts-ignore
import { useState } from "react";

const FontRow = ({item, installedfont, installFont}:{item:any, installedfont:any, installFont:(path:string, filename:string, name:string)=>void}) => {
 const [installing, setInstalling] = useState(false);
  return (
    <div key={`${item.name}`} className="font-list-row">
                <div className="name">{item.name}</div>
                <div className="download">
                  {installedfont?.includes(item.name) ? (
                    'Installed'
                  ) :
                    installing ? (
                    <img className="loader" src={require('../img/downloading.gif')} alt="" />
                    ) :
                    (<button
                      type="button"
                      onClick={() => {
                        installFont(item.path, item.filename, item.name);
                        setInstalling(true);
                      }}
                      data-href={item.path}
                    >
                      Install
                    </button>)
                   }
                </div>
              </div>
  )
}

export default FontRow
