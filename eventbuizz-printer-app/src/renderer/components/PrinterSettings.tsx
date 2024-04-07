import React, {useState} from 'react'

const PrinterSettings = (props:any) => {
    const printerSettings = JSON.parse(localStorage.getItem('printerSettings') || '{}');
    const [orientation, setOrientation] = useState(printerSettings.orientation ? printerSettings.orientation :"potrait");
    const [pageSize, setPageSize] = useState(printerSettings.pageSize ? {width:printerSettings.pageSize.width, height:printerSettings.pageSize.height} : {width:55 , height:86 });
    const [margin, setMargin] = useState(printerSettings.margin ? {top:printerSettings.margin.top, bottom:printerSettings.margin.bottom, left:printerSettings.margin.left, right:printerSettings.margin.right} : {top:0,bottom:0,left:0,right:0});
    const [duplex, setDuplex] = useState(printerSettings.duplex ? printerSettings.duplex : "false");

    const onSubmit = () =>{
        let data={
            orientation,
            pageSize,
            margin,
            duplex
        };
        localStorage.setItem('printerSettings', JSON.stringify(data));
        props.setSettingsOpen(!props.settingsOpen);
    };
  return (
    <div className='modal'>
        <div className="modal-content">
            <div className='modal-header'>
                <div>
                    <h4 style={{fontWeight: 600}}>
                        Printer Settings
                    </h4>
                </div>
                <div>
                    <a className='modal-close' style={{fontWeight: 600}} onClick={()=>{ props.setSettingsOpen(!props.settingsOpen); }}>
                        x
                    </a>
                </div>
            </div>
            
            <div className="form-row" style={{padding:"5px 0px"}}>
                    <label
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                    }}
                    htmlFor="duplex"
                    >
                    <span style={{fontWeight: 600}} >Orientation:</span>
                    </label>
                    <div style={{display:"flex", justifyContent:"flex-start", alignItems:"center", }}>
                        <div style={{display:"flex", justifyContent:"space-between", alignItems:"center", paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                        }}>Potrait</span>
                            <input type="checkbox" name="potrait" value="potrait" checked={orientation === "potrait" ? true :false} id="duplex" onChange={()=>{setOrientation("potrait")}} />
                        </div>
                        <div style={{display:"flex", justifyContent:"space-between", alignItems:"center",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                         }}>Lanscape</span>
                            <input type="checkbox" name="landscape" checked={orientation === "landscape" ? true :false} value="lanscape" id="duplex" onChange={()=>{setOrientation("landscape")}} />
                        </div>
                    </div>
            </div>
            <div className="form-row" style={{padding:"5px 0px"}} >
                    <label
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                    }}
                    htmlFor="duplex"
                    >
                    <span style={{fontWeight: 600}}>Page Size:(mm)</span>
                    </label>
                    <div style={{display:"flex", justifyContent:"flex-start", alignItems:"center", }}>
                        <div style={{display:"flex", flexDirection:"column", alignItems:"flex-start",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                        }}>Width</span>
                            <input type="number" name="duplex"  id="duplex" value={pageSize.width} onChange={(e)=>{setPageSize({...pageSize, width:parseFloat(e.target.value) })}} />
                        </div>
                        <div style={{display:"flex", flexDirection:"column", alignItems:"flex-start",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                         }}>Height</span>
                            <input type="number" name="duplex"  id="duplex" value={pageSize.height} onChange={(e)=>{setPageSize({...pageSize, height:parseFloat(e.target.value) })}} />
                        </div>
                    </div>
            </div>
            <div className="form-row" style={{padding:"5px 0px"}} >
                    <label
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                    }}
                    htmlFor="duplex"
                    >
                    <span style={{fontWeight: 600}} >Margin:(Pixels)</span>
                    </label>
                    <div style={{display:"flex", justifyContent:"flex-start", alignItems:"center", }}>
                        <div style={{display:"flex", flexDirection:"column", alignItems:"flex-start",paddingRight: 8,}}>
                                <span style={{ paddingRight: 8, fontSize: 12,
                            }}>Top</span>
                                <input type="number" name="duplex"  id="duplex" value={margin.top} onChange={(e)=>{setMargin({...margin, top:parseFloat(e.target.value) })}}  />
                            </div>
                        <div style={{display:"flex", flexDirection:"column", alignItems:"flex-start",paddingRight: 8,}}>
                                <span style={{ paddingRight: 8, fontSize: 12,
                            }}>Bottom</span>
                                <input type="number" name="duplex"  id="duplex" value={margin.bottom} onChange={(e)=>{setMargin({...margin, bottom:parseFloat(e.target.value) })}}  />
                            </div>
                        
                    </div>
                    <div style={{display:"flex", justifyContent:"flex-start", alignItems:"center", }}>
                        <div style={{display:"flex", flexDirection:"column", alignItems:"flex-start",paddingRight: 8,}}>
                        <span style={{ paddingRight: 8, fontSize: 12,
                            }}>Right</span>
                                <input type="number" name="duplex"  value={margin.right} id="duplex" onChange={(e)=>{setMargin({...margin, right:parseFloat(e.target.value) })}}  />
                            </div>
                        <div style={{display:"flex", flexDirection:"column", alignItems:"flex-start",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                                            }}>Left</span>
                            <input type="number" name="duplex"  value={margin.left} id="duplex" onChange={(e)=>{setMargin({...margin, left:parseFloat(e.target.value) })}}  />
                        </div>
                    </div>
            </div>
            <div className="form-row" style={{padding:"5px 0px"}} >
                    <label
                    style={{
                        display: 'flex',
                        alignItems: 'center',
                    }}
                    htmlFor="duplex"
                    >
                    <span style={{fontWeight: 600}}>Duplex mode:</span>
                    </label>
                    <div style={{display:"flex", justifyContent:"flex-start", alignItems:"center", }}>
                        <div style={{display:"flex", justifyContent:"space-between", alignItems:"center",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                        }}>Simplex</span>
                            <input type="checkbox" name="duplex" value="simplex" id="duplex" checked={duplex === "simplex" ? true :false} onChange={()=>{setDuplex("simplex")}} />
                        </div>
                        <div style={{display:"flex", justifyContent:"space-between", alignItems:"center",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                         }}>ShortEdge</span>
                            <input type="checkbox" name="duplex" value="shortEdge" checked={duplex === "shortEdge" ? true :false} id="duplex" onChange={()=>{setDuplex("shortEdge")}} />
                        </div>
                        <div style={{display:"flex", justifyContent:"space-between", alignItems:"center",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                         }}>LongEdge</span>
                            <input type="checkbox" name="duplex" value="longEdge" checked={duplex === "longEdge" ? true :false} id="duplex" onChange={()=>{setDuplex("longEdge")}} />
                        </div>
                        <div style={{display:"flex", justifyContent:"space-between", alignItems:"center",paddingRight: 8,}}>
                            <span style={{ paddingRight: 8, fontSize: 12,
                         }}>False</span>
                            <input type="checkbox" name="duplex" value="false" checked={duplex === "false" ? true :false} id="duplex" onChange={()=>{setDuplex("false")}} />
                        </div>
                    </div>
            </div>
            <div className="form-row" style={{padding:"5px 0px"}} >
                    <button type='button' onClick={()=>{onSubmit()}} >Save Settings</button>
            </div>
        </div>
    </div>
  )
}

export default PrinterSettings