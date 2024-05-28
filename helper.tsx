import React from "react";

const image_ext = ['SPRITE2', 'BIF', 'AFPHOTO', 'ICON', 'SNAGX', 'PSDC', 'PXD', 'LRPREVIEW', 'GIF', 'XPM', 'PSD', 'YSP', 'JPS', 'PNG', 'BPG', 'SPRITE', 'AVATAR', 'JXL', 'SPR', 'TGA', 'FLIF', 'SPRITE3', 'TPF', 'HDR', 'JPEG', 'WEBP', 'SAI', 'DDS', 'TN3', 'PISKEL', 'CT', 'JPG', 'IPICK', 'DRP', 'CLIP', 'PI2', 'LZP', 'VICAR', 'G3', 'SUMO', 'ASE', 'OC4', 'USERTILE-MS', 'FF', 'TBN', 'ACCOUNTPICTURE-MS', 'PIXELA', 'PPP', 'SKITCH', 'WBC', 'SLD', 'DIB', 'ITC2', 'APM', 'PCX', 'PAT', 'TFC', 'PM', 'WBZ', 'XCF', 'HEIF', 'PPF', 'FITS', 'PSDX', 'EXR', 'DJVU', 'LIP', 'BMP', 'PDN', 'CPC', 'CDC', 'KRA', 'OZJ', 'TIFF', 'PWP', 'MPF', 'MSP', '2BP', 'JPC', 'ASEPRITE', 'SNAG', 'TM2', 'ECW', 'KDK', 'POV', 'OTA', 'NOL', 'PI2', 'SPP', 'PDD', 'ARR', 'PMG', 'ICN', 'PIC', 'PFI', 'PSP', 'KFX', 'PTEX', '73I', 'KTX', 'DRZ', 'FIL', 'GRO', 'BLZ', 'VNA', 'NLM', 'WIC', 'PNC', 'CMR', '8PBS', 'PNI', 'MNG', 'JPF', 'PX', 'APNG', 'CDG', 'RGF', 'STEX', 'RPF', 'TG4', 'I3D', 'TN', 'RSR', 'FPX', 'VPE', 'FAC', 'FBM', 'PSPIMAGE', 'HEIC', 'IWI', 'MDP', 'DGT', 'MDP', 'PSB', 'AWD', 'BMQ', 'PPM', 'J2K', 'JPE', 'CPT', 'ABM', 'JNG', 'THM', 'PGM', 'PSE', 'OTB', 'SPH', 'JBIG2', 'CPD', 'VRPHOTO', 'LMNR', 'CALS', 'PP5', 'PNS', 'WB0', 'PTG', 'BMZ', 'WB2', 'GGR', 'OZT', 'GRY', 'CAN', 'LJP', 'APS', 'G3N', '8CI', 'WBM', 'JLS', 'GMBCK', 'KTX2', 'TIF', 'MPO', '001', 'PBM', 'INSP', 'INFO', 'LBM', 'MCS', 'PIC', 'SIG', 'WBMP', 'HDP', 'VRIMG', 'SID', 'PE4', 'CE', 'PICTCLIPPING', 'XBM', 'BTI', 'JXR', 'WDP', 'INT', 'PJPG', 'DJV', 'SR', 'JPG_LARGE', 'GROB', 'RCL', 'MBM', 'VDA', 'ALBM', 'IPV', 'RIX', 'JBF', 'OTI', 'PNT', 'ILBM', 'LIF', 'APD', 'OC3', 'AGP', 'QTIF', 'KDI', 'PZS', 'TEX', 'UFO', 'HF', 'VSS', 'NCD', 'SPA', 'RTL', 'SUP', 'GIM', 'RIF', 'SIG', 'PROCREATE', 'JPX', 'TIF', 'JP2', 'PVR', 'AWD', 'PXD', 'PRW', 'WI', 'AIS', 'PGF', 'JIA', 'DTW', 'WB1', 'GP4', 'RLI', 'S2MV', 'FACE', 'APX', 'QMG', 'DCM', 'NEO', 'V', 'HPI', 'PXM', 'PI3', 'OCI', 'JIF', 'BMX', '8CA', 'SNAGPROJ', 'BMC', 'THUMB', 'ICA', 'SAI2', 'ITHMB', 'KODAK', 'PCD', 'RIFF', 'PSF', '411', 'CID', 'JPG2', 'SDR', 'TEXTURE', 'TARGA', 'WMP', 'MAX', 'RGB', 'T2B', 'GBR', 'GIH', 'BM2', 'MNR', 'CIMG', 'AFX', 'PZP', 'MBM', 'SFF', 'J2C', 'PICNC', 'NWM', 'MIX', 'QTI', 'SAR', 'DIC', 'TJP', 'SFC', 'BLKRT', 'SPE', 'RLE', 'GMSPR', 'WPB', 'TAAC', 'TNY', 'CD5', 'PICT', 'SRF', 'DMI', 'PZA', 'HRF', 'SKM', 'ICB', 'MYL', 'PIXADEX', 'OZB', 'VIFF', 'SVSLIDE', 'OC5', 'ART', 'FSTHUMB', 'OPLC', 'JBIG', 'PJP', 'SPIFF', 'HDRP', 'URT', 'HR', 'FPPX', 'PM3', 'SVS', 'GCDP', 'FPOS', 'PYXEL', 'WBP', 'SPJ', 'GPD', 'PC1', 'KIC', 'PC2', 'LB', 'AVB', 'AVIF', 'AGIF', 'THM', 'CIN', 'PXR', 'ARW', 'ZIF', 'DPX', 'ZVI', 'ORA', 'SUN', '9PNG', 'CUT', 'PSPBRUSH', 'J', 'POP', 'DICOM', 'SVA', 'DT2', 'SGD', 'DCX', 'MSK', 'PANO', 'XWD', 'ACORN', 'DDT', 'GFIE', 'NCR', 'SCU', 'BSS', 'SIM', 'U', 'SCP', 'CPG', 'MAC', 'JPG_SMALL', 'JTF', 'FAL', 'JPD', 'JB2', 'PAP', 'CIT', 'CAL', 'SHG', 'ODI', 'JFI', 'RCU', 'CAM', 'PXZ', 'AIC', 'QIF', 'SFW', 'MET', '360', 'PAC', 'JFIF', 'JPG_ORIG', 'OE6', 'PJPEG', 'WVL', 'SOB', 'TPS', 'TUB', 'FPG', 'EPP', 'SBP', 'UGA', 'MXI', 'IPX', 'BS', 'JPG-LARGE', 'JBR', 'SPC', 'JIFF', 'INK', 'JWL', 'MRB', 'SEP', 'UGOIRA', 'MAT', 'VFF', 'MIFF', 'PI1', 'SMP', 'PTX', 'PTK', 'TB0', 'PNTG', 'TN1', 'POV', '8XI', 'RSB', 'GFB', 'IMG', 'RS', 'RVG', 'RRI', 'IVR', 'OMF', 'SPU', 'SKYPEEMOTICONSET', 'MRXS', 'JBG', 'ADC', 'Y', 'RAS', 'CSF', 'MIC', 'SGI', 'YUV', 'PTX', 'SUNIFF', 'ARTWORK', 'SCG', 'TN2', 'PIC', 'SID', 'DM3', 'UPF', 'G3F', 'DC6', 'PAM', 'XFACE', 'T2K', 'MIPMAPS', 'ZIF', 'PE4', 'SCN', 'PFR', 'TSR', 'PTS', 'PNM', 'VIC', 'DC2', 'ICPR', 'SCN', 'SCN', 'VMU', 'BRN', 'NDPI', 'PC3', 'MIP', 'JAS', 'C4', 'FAX', 'COLZ', 'RIC', 'WBD', 'PP4', 'CPX', 'NCT', 'ACR', 'GVRS', 'NPSD', 'PSDB', '1SC', 'VDOC', 'PBS', 'PXICON', 'FRM', 'DVL', 'IMJ', 'BRK', 'SCT', 'CPBITMAP', 'PI6', 'CPS', 'BW', 'VST', 'STE', 'BRT', 'BMF', 'PIX', 'PTX', 'KPG', 'WPE', 'RGB', 'RGBA', 'BRUSH', 'LDW', 'PIX', 'JPG_MEDIUM', 'JPG_THUMB', 'IC3', 'IPHOTOPROJECT', 'IVUE', 'JBMP', 'PIX', 'TPI', 'IC2', 'SCI', 'PAL', 'PI5', 'PI4', 'TRIF', 'DDB', 'IC1', '@EX', 'CR2', 'RW2', 'ERF', 'NRW', 'RAF', 'NEF', 'SRF', 'ARW', 'RWZ', 'EIP', 'BAY', 'DCR', 'DNG', 'RAW', 'CRW', '3FR', 'K25', 'CS1', 'MEF', 'DNG', 'ORF', 'KDC', 'ARI', 'SR2', 'MOS', 'CR3', 'FFF', 'MFW', 'SRW', 'J6I', 'RWL', 'X3F', 'KC2', 'MRW', 'PEF', 'IIQ', 'CXI', 'NKSC', 'MDC', 'SVG', 'FCM', 'SVGZ', 'VSTM', 'VECTORNATOR', 'AI', 'VSDX', 'CDR', 'GVDESIGN', 'EP', 'AFDESIGN', 'EPS', 'VSTX', 'XAR', 'WMF', 'DPR', 'POBJ', 'FH10', 'CDDZ', 'FH4', 'PAT', 'FH9', 'CMX', 'CSY', 'GDRAW', 'LMK', 'FT9', 'DRW', 'CDD', 'PS', 'EPSF', 'FH8', 'FXG', 'FH7', 'SLDDRT', 'IGX', 'VSD', 'INK', 'EMZ', 'DRAWIT', 'INK', 'FHD', 'SSK', 'DPP', 'PLT', 'TEXEMZ', 'SCV', 'SK', 'PMG', 'PFD', 'DRW', 'DRAWIO', 'OTG', 'AC6', 'ODG', 'PEN', 'SVM', 'AIT', 'PD', 'STD', 'WPG', 'VSDM', 'CDMZ', 'CDS', 'PUPPET', 'RDL', 'PLT', 'HPGL', 'CVX', 'FT8', 'EGC', 'MVG', 'CDX', 'HPG', 'SMF', 'FIG', 'PSID', 'DIA', 'CDRAPP', 'CDTX', 'GLOX', 'GSD', 'MGC', 'VML', 'ASY', 'YDR', 'FH11', 'SKETCH', 'IMD', 'IDEA', 'ESC', 'TPL', 'CVS', 'FH5', 'JSL', 'FH6', 'FH3', 'MMAT', 'MGTX', 'SK1', 'YLC', 'NODES', 'SHAPES', 'DRAWING', 'CDMTZ', 'MGCB', 'SK2', 'GSTENCIL', 'CVG', 'VST', 'TNE', 'DHS', 'CVI', 'TLC', 'SNAGSTYLES', 'SXD', 'WPI', 'EZDRAW', 'MP', 'PIXIL', 'OVR', 'GRAFFLE', 'DXB', 'EMF', 'DED', 'SDA', 'STN', 'VEC', 'WMZ', 'ABC', 'CLARIFY', 'SVF', 'SKETCHPAD', 'CV5', 'FIF', 'CGM', 'AF3', 'CIL', 'AC5', 'OVP', 'FMV', 'HVIF', 'FTN', 'CDX', 'MGMX', 'AF2', 'CNV', 'CDSX', 'FT10', 'DCS', 'DDRW', 'PFV', 'HPL', 'UFR', 'DESIGN', 'FS', 'INK', 'XMMAP', 'ARTB', 'GKS', 'AWG', 'CDMM', 'FT7', 'ART', 'PL', 'DSF', 'DPX', 'ZGM', 'GEM', 'HGL', 'NDTX', 'XMMAT', 'YAL', 'EPGZ', 'GTEMPLATE', 'NDX', 'NDB', 'AMDN', 'MGMT', 'DSG', 'QCC', 'MGMF', 'CDLX', 'CCX', 'XPR', 'CDMT', 'FT11', 'PCS', 'VBR', 'CDT', 'COR', 'CWT', 'GLS', 'IGT', 'MGS', 'CAG', 'PWS', 'NAP', 'P', 'E57', 'MESH', 'BBMODEL', 'MD5ANIM', 'REAL', 'HIPNC', 'THING', 'CRZ', 'C4D', 'SMD', 'PMX', 'DFF', 'DAE', 'FSH', 'MAKERBOT', 'PHY', 'M3D', 'BLEND', 'ATM', 'MC5', 'DUF', 'BLK', 'ZT', 'IV', 'CFG', 'MDL', 'LXF', 'FX', 'IGI', 'XAF', 'X', 'NM', 'MIX', 'AN8', 'VPD', '3DS', 'MU', 'MDX', 'P3D', 'V3D', 'CSO', 'MTZ', 'CG', 'FLT', 'X3D', 'VOX', '3D2', '3MF', 'OBP', 'MA', 'IVE', '3DXML', 'PSA', 'BR7', 'MD5MESH', 'MD5CAMERA', 'PPZ', 'TME', 'P3L', 'REALITY', 'HDZ', 'M3D', 'TRACE', 'WFT', 'ATL', 'IRR', 'GMF', 'DSV', 'USD', 'AMF', 'CMDB', 'DDP', 'PP2', 'FACEFX', 'TILT', 'PRM', 'GRS', 'LLM', 'ANIMSET', 'GH', 'P4D', 'T3D', 'SKP', 'DS', 'BIP', 'Z3D', 'CCP', 'ANM', 'MESH', 'PSK', 'OBJ', 'MSH', 'PKG', 'LXO', 'SDB', 'ANIM', 'W3D', '3DA', 'PZ2', '3D4', 'XMF', 'OFF', 'PKG', 'TRI', 'P5D', 'FCP', 'MNM', 'PL0', 'CCB', 'MXM', 'MGF', 'DES', '3DM', 'CHR', 'PRC', 'DWF', 'MB', 'MQO', 'CG3', 'MRML', 'XSI', 'MEB', 'SIS', 'MSH', 'T3D', 'P3M', 'SH3D', 'A8S', 'PZZ', 'GLTF', 'MXS', 'FBX', '3DP', 'SC4MODEL', 'RCS', 'A3D', 'A2C', 'X3G', 'MDD', 'PMD', 'NIF', 'ANIM', 'BLD', 'N3D', 'ALBUM', 'LND', 'OBZ', 'CRF', 'MS3D', 'LTZ', 'FG', 'HRZ', 'FCZ', 'HLSL', 'FPF', 'B3D', 'TCN', 'HIP', 'STP', 'IFC', 'USDZ', 'MCZ', 'VSH', 'OL', '3DL', 'FXT', 'CGFX', 'BRO', '3DMF', 'SH3F', 'PLY', 'PRO', 'CHR', 'MTL', 'SHP', 'CMF', 'GMT', 'ARM', 'LWS', 'BVH', 'BIF', 'IGES', 'MC', 'KFM', 'GRN', 'FXL', 'KTZ', 'PAR', 'MUD', 'XR', 'BR4', 'SGN', 'SRF', 'CM2', 'WRP', 'CBDDLP', 'UMS', 'FXS', 'DSA', 'WOW', 'AREXPORT', 'C3D', 'U3D', 'MTX', 'VUE', 'S', 'VVD', 'SBSAR', 'F3D', 'D3D', 'ACT', 'MAX', 'YDL', 'GLF', 'CPY', 'VMD', 'PMD', 'EGG', 'VS', 'PL2', 'VP', 'BR6', 'GEO', 'KMC', 'QC', 'DSB', 'AOF', 'VEG', 'CAL', 'BIO', 'CMOD', 'CSM', 'IGS', 'MAXC', 'BRG', 'PGAL', 'CSD', 'GMMOD', 'P3R', 'C3Z', 'FIG', 'FXM', 'VISUAL_PROCESSED', 'TVM', 'WRL', '3DF', 'VOB', 'NFF', 'PZ3', 'LWO', 'PRV', 'CR2', 'M3', 'IK', 'XPR', 'DAZ', 'SESSION', 'GLB', 'ARFX', 'MDG', 'JCD', 'SMC', 'V3V', 'MESH', 'PRO', 'TMD', 'VISUAL', 'VRL', '3DC', 'DSE', 'DBM', 'XOF', 'S3G', 'DN', 'PIGM', 'CLARA', 'IRRMESH', 'FNC', 'GHX', 'ARPATCH', 'ARPROJPKG', 'FRY', 'PRIMITIVES_PROCESSED', 'THL', 'KMCOBJ', 'DRF', 'DMC', '3DW', 'FPJ', 'FP', 'HXN', 'FXA', 'DIF', 'SKL', 'HD2', 'DSF', 'P2Z', 'MC6', 'FC2', 'CMZ', 'J3O', 'P21', 'VTX', 'CAS', 'AOI', 'PL1', 'PAT', 'BR5', 'DDD', 'LP', 'BR3', 'FP3', 'MAT', 'XRF', 'CMS', '3DC', 'SBFRES', 'N2', 'SI', 'PREFAB', '3DX', 'JAS', 'RFT', 'DFS', 'GLM', 'BSK', 'XMM', 'V3O', 'TRI', 'TS1', 'TPS', 'LDM', 'RAD', 'STO', 'MPJ', 'EXP', '3D', 'OCT', 'DBS', 'DSI', 'FSQ', 'LPS', 'SM', 'PIGS', 'OGF', 'YAODL', 'VSO', 'FUN', 'ATF', 'XV0', 'DSO', '3DMK', 'DBL', 'PRIMITIVES', 'MP', 'ARPROJ', 'HR2', 'DSI', 'PREVIZ', '3DV', 'GLSL', 'MCX-8', 'MCSG', 'ASAT', 'CSF', 'TMO', 'FPE', 'MOT', 'IGM', 'CAL', 'TGO', 'CHRPARAMS', 'XSF', 'STC', 'XAF', 'VAC', 'CGA', 'RAD', 'CAF', '3DON', 'LT2', 'RDS', 'R3D', 'ANIMSET_INGAME', 'FACEFX_INGAME', 'DSD', 'BTO', 'FBM', 'DBC', 'VMO', 'TDDD', 'BRK', 'NSBTA', 'FUSE', 'WRZ', 'ANIM', 'RAY', 'RIG'];

const compressed_ext = ['MINT', 'HTMI', 'MPKG', 'TPSR', 'ARDUBOY', 'ICE', 'TBZ', 'SIFZ', 'COMPPKGHAUPTWERKRAR', 'RAR', 'XAPK', 'RTE', 'FZPZ', 'B6Z', 'PUP', 'GZIP', 'NPK', 'SY_', 'PKGTARXZ', 'SIT', 'DEB', 'DZ', 'DL_', 'TARXZ', 'ZPI', 'S00', '7Z', 'BZ2', 'PKG', 'BNDL', 'PF', 'B1', 'UFSUZIP', 'WA', 'SFG', 'DAR', 'QDA', '7Z002', 'SQX', 'SMPF', 'CB7', 'TZST', 'JEX', 'GZA', 'HBE', 'R2', 'VMCZ', 'ITA', 'PA', 'LZM', 'ECS', 'ZST', 'CTX', 'UHA', 'PAR', 'CBR', 'VIP', 'JSONLZ4', 'F', 'R00', 'ZIP', 'OPK', 'F3Z', 'LEMON', 'REV', 'TAZ', 'PAK', 'KGB', 'ARCHIVER', 'MEMO', 'PWA', 'ZL', 'PIT', 'NEX', 'RPM', 'A02', 'XIP', 'PCV', 'CBT', 'BA', 'TARLZMA', 'TBZ2', 'SFX', 'CDZ', 'Z03', 'JARPACK', 'C00', 'CBZ', 'BUNDLE', 'WHL', 'ZIX', 'HKI', 'S7Z', 'ARI', 'APZ', 'TARLZ', 'PIZ', 'GZ', 'CXARCHIVE', 'SITX', 'AR', '7Z001', 'ARK', 'TGS', 'LZMA', 'TARGZ', 'TX_', 'R01', 'SPD', 'ARC', 'AYT', 'LZ4', 'EPI', 'Z00', 'A01', 'GMZ', 'KEXTRACTION', 'IPK', 'CZIP', 'BZ', 'BH', 'ACE', 'SEA', 'SFS', 'DD', 'WAR', 'CBA', 'SDC', 'TARBZ2', 'XZ', 'SDOC', 'FDP', 'PEA', 'ALZ', 'WASTICKERS', 'SNB', '0', 'PACKAGE', 'Z', 'TGZ', 'VOCA', 'SHAR', 'R03', 'S02', 'Q', 'APEX', 'VRPACKAGE', 'GCA', 'OAR', 'ZZ', 'P19', 'XOPP', 'LPKG', 'OZ', 'UBZ', 'C10', 'CTZ', 'SDN', 'XX', 'LZ', 'SH', 'R30', 'R0', 'MZP', 'C01', 'RK', 'RNC', 'BZIP2', 'ZIPX', 'SNAPPY', 'S01', 'RZ', 'YZ', 'SPT', 'PUP', 'ARJ', 'A00', 'CAR', 'JHH', 'LHZD', 'HPKG', 'PAQ8P', 'FP8', 'GZI', 'XMCDZ', 'PAR2', 'LZH', 'MLPROJ', 'VPK', 'ZFSENDTOTARGET', '000', 'JGZ', 'XEZ', 'LIBZIP', 'RP9', 'XAR', 'ZSPLIT', 'LBR', 'SPA', 'Z04', 'SHR', 'PET', 'TCX', 'IADPROJ', 'RSS', 'J', 'SREP', 'Z02', 'HYP', 'LHA', 'SDOCX', 'ZOO', 'PAX', 'ZZ', 'LNX', 'NZ', 'DGC', 'SPL', 'MZP', 'P7Z', 'R04', 'AGG', 'HKI3', 'COMPPKG_HAUPTWERK_RAR', 'SPM', 'HKI1', 'EFW', 'PSZ', 'SHK', 'ZI', 'BZA', 'WDZ', 'HBC', 'WARC', 'SAR', 'PIMA', 'XOJ', 'SNZ', 'NAR', 'ISX', 'MD', 'CPGZ', 'IZE', 'ZW', 'CPT', 'AIN', 'EDZ', 'ZAP', 'PBI', 'PKZ', 'ARH', 'LZO', 'PAQ8F', 'LQR', 'GZ2', 'HBC2', 'TXZ', 'EGG', 'WUX', 'IPG', 'LAYOUT', 'SI', 'PKPASS', 'Z01', 'VSI', 'B64', 'ZI_', 'PACKGZ', 'TG', 'BZIP', 'MBZ', 'TARZ', 'UC2', 'HPK', 'XZM', 'C02', 'PRS', 'TLZ', 'XEF', 'SNAGITSTAMPS', 'ZIM', 'KZ', 'MOU', 'PAE', 'UZIP', 'PAQ6', 'JIC', 'DAF', 'UZED', 'TZ', 'YZ1', 'VWI', 'PUZ', 'WAFF', 'R02', 'BDOC', 'TRS', 'WOT', 'DIST', 'PIM', 'ISH', 'R21', 'HA', 'LZX', 'VEM', 'PVMZ', 'SEN', 'TLZMA', 'CP9', 'STPROJ', 'HKI2', 'S09', 'WLB', 'PAQ8L', 'PAQ7', 'P01', 'TARGZ2', 'ECAR', 'SQF', 'UFDR', 'VMS', 'R1', 'FCX', 'SFM', 'WICK', 'BOO', 'SBX', 'VFS', 'MOZLZ4', 'MOVPKG', 'ZED', 'PAQ8', 'Y', 'SBX', 'SQZ', 'ECSBX', 'GAR'];

const video_ext = ['TY+', 'KINE', 'SWF', 'PZ', 'AEP', 'PRPROJ', 'MKV', 'SFD', 'DRP', 'PIV', 'PSV', 'MEPS', 'INP', 'SER', 'ANM', 'VII', 'SCM', 'PLOT', 'VPROJ', 'VEG', 'WLMP', 'MSDVD', 'WEBM', 'STR', 'MXF', 'MSWMM', 'AEC', 'BIK', 'DCR', 'MP4', 'WPL', 'AMC', 'SCM', 'DIR', 'BK2', 'VPJ', 'DCR', 'SRT', 'KDENLIVE', 'FBR', 'VOB', 'MSE', 'RMVB', 'SUB', 'FLC', 'SBT', 'EVO', 'CLPI', 'SCREENFLOW', 'VP6', '3GP', 'REC', 'SSF', 'PAC', 'IFO', 'DMSM', 'FCP', 'MPEG', 'BIN', 'CAMPROJ', 'VSP', 'VTT', 'META', 'WMV', 'ALPX', 'IVR', 'M4U', 'WMMP', 'MPV', '264', 'DPA', 'MVD', 'DB2', 'PSH', 'M2TS', 'TSV', 'TRP', 'GTS', 'MEDIA', 'AEPX', 'CINE', 'MANI', 'D3V', 'AMX', 'DMX', 'MGV', 'MVP', 'HDMOV', 'RMS', 'FLV', 'ISMV', '3GP2', 'MP4INFOVID', 'VC1', 'ASF', 'VIDEO', 'OGV', 'VIV', 'TVSHOW', 'PMF', 'CPVC', 'ZM2', 'RCUT', 'AV1', 'JTV', 'VDR', 'MEPX', 'SWI', 'M4S', 'ARCUT', 'SIV', 'G2M', 'NCOR', 'ALE', 'MYS', 'DXR', 'MP4V', 'IDX', 'TS', 'MOB', 'TREC', 'DAT', 'RM', 'CME', 'DV4', 'SCC', 'MJ2', 'PDS', 'DREAM', 'M2T', 'MP5', 'RCD', 'IVA', 'PSB', 'DMSM3D', 'MPEG4', 'MPROJ', 'BNP', 'ZMV', 'MNV', 'DZM', 'CAMREC', 'VID', 'THEATER', 'MJPG', 'SMV', 'TIX', 'M4V', 'MPG', 'WP3', 'STX', 'AWLIVE', 'F4P', '3MM', 'GFP', 'VRO', 'VGZ', 'MTS', 'DZP', 'MOV', 'DASH', 'H264', 'QTCH', 'CST', 'SEDPRJ', 'PPJ', 'XMV', 'DZT', 'KTN', 'AVS', 'IRCP', 'FBR', 'AVV', 'CAMV', 'FFD', 'DMB', 'INT', 'M1V', 'ZM3', 'IZZ', 'MV', 'PIC', 'MMV', 'TMV', 'WVM', 'IZZY', 'LVIX', 'FLI', 'AVB', 'MMP', 'MEP', 'M75', 'SFVIDCAP', '264', 'DAV', 'PLAYLIST', 'WVX', 'AVCHD', 'CPI', 'SBK', 'DVR', '3G2', 'MP2V', 'JDR', 'KMPROJECT', 'MOVIE', 'M15', 'DV', 'MVP', 'RDB', 'SFERA', 'TP', 'KMV', 'BSF', 'M2P', '60D', 'XVID', 'WM', 'SAN', '890', 'MPSUB', 'DDAT', 'D2V', 'AAF', 'F4V', '3GPP', 'MPEG2', 'ISM', '3GPP2', 'DVR-MS', 'GVI', 'MPV2', 'VEP', 'YUV', 'XESC', 'MK3D', 'PDRPROJ', 'QTL', 'WVE', 'F4F', '3P2', 'SQZ', 'HDV', 'VP3', 'MVEX', 'MP21', 'M4F', 'DCK', 'JSS', 'XLMV', 'TSP', 'R3D', 'RSX', 'PRO', 'DNC', 'BU', 'MPL', 'VCR', 'BDMV', 'DIVX', 'XML', 'OGM', 'LRV', 'MOI', 'SWT', 'PHOTOSHOW', 'WCP', 'NUV', 'RV', 'SMK', 'SPL', 'OGX', 'AVI', 'G64', 'DPG', 'WRF', 'RMD', 'TVLAYER', 'TDT', 'MVE', 'DVDMEDIA', 'RUM', 'EXO', 'LREC', 'MPE', 'WOT', '787', 'CED', 'FLIC', 'MTV', 'M2A', 'VP7', 'QTZ', 'TIVO', 'WMD', 'BDT3', 'BMC', 'NUT', 'ARF', 'V264', 'K3G', 'LSX', 'MOOV', 'AQT', 'TPD', 'AVE', 'JMV', 'RMP', 'AETX', 'DMSD', 'F4M', 'WXP', 'PREL', 'TVS', 'NFV', 'HEVC', 'NVC', 'Y4M', 'ASX', 'IMOVIEPROJ', 'AEGRAPHIC', 'MJP', 'MOVIE', 'TTXT', 'IRF', 'AJP', 'FTC', 'BDM', 'PXV', 'M4E', 'M2V', 'SSM', 'MSH', 'TDA3MT', 'M21', 'PGI', 'RVL', 'CMPROJ', 'DVX', 'AVD', 'PLPROJ', 'ISMC', 'TOD', 'VLAB', 'ZM1', 'VSE', 'FLH', 'M21', 'EVO', 'CMMTPL', 'PRTL', 'RVID', 'PEG', 'MPG4', 'WMX', 'TTML', 'LFPACKAGE', 'PLOTDOC', 'IVF', 'SUB', 'FVM', 'MV8', 'SMIL', 'VCV', 'BMK', 'N3R', 'ORV', 'RCREC', 'WTV', 'GXF', 'SBZ', 'BVR', 'SDV', 'ZOOM', 'TPR', 'FPDX', 'IMOVIELIBRARY', 'OTRKEY', 'ZEG', 'PJS', 'AVP', 'IMOVIEMOBILE', 'AXV', 'TVRECORDING', 'MPGINDEX', 'VBC', 'QT', 'VIVO', 'AMV', 'AV', 'MPEG1', 'SEC', 'MT2S', 'GIFV', 'EDL', 'MVC', 'PAR', 'VCPF', 'BOX', 'G64X', 'YOG', 'XFL', 'PNS', 'W32', 'QTM', 'RMD', 'GCS', 'ROQ', 'VIX', 'NSV', 'CLK', 'CREC', 'XEL', 'PVR', 'THP', 'FCPROJECT', 'DV-AVI', 'AM', 'PSSD', 'RAVI', 'SMI', 'MPL', 'ANIM', 'SSA', 'BS4', 'M1PG', 'DLX', 'BLZ', 'VS4', 'SEC', 'MPC', 'PROQC', 'NTP', 'SEQ', 'VR', 'VFZ', 'BYU', 'VFW', 'QSV', 'FCARCH', 'INSV', 'HKM', 'MPLS', 'DIF', 'MVB', 'MXV', 'MODD', 'MOD', 'FFM', 'TP0', 'VDO', 'VF', 'EZT', 'VEM', 'TBC', 'BIX', 'VMD', 'WSVE', 'VIEWLET', 'GL', 'GOM', 'USF', 'IMOVIEPROJECT', 'SPRYZIP', 'CMREC', 'AET', 'CMMP', 'PRO4DVD', 'AECAP', 'AXM', 'DMSS', 'VDX', 'XEJ', 'SCN', 'MPG2', 'CVC', 'EXP', 'SVI', 'SMI', 'SEQ', 'MOFF', 'IVS', 'LSF', 'H261', 'CIP', 'CMV', 'DCE', 'FBZ', 'JV', 'FVT', 'MJPEG', 'PVA', 'EYETV', 'LXF', 'AVM', 'STL', 'H265', 'WGI', 'VID', 'MQV', 'AVS', 'AVR', 'RCPROJECT', 'RMV', 'TY', 'GVP', 'QTINDEX', 'TDX', 'WFSP', 'ISMCLIP', 'ANX', 'BDT2', 'VMLF', 'CX3', 'VMLT', 'AVS', 'ANYDESK', 'EXI', 'PMP', 'CDXL', 'BIK2', 'MVY', 'EL8', 'PCLX', 'CAMTEMPLATE', 'VSR', 'PRO5DVD', 'DRC', 'RTS', 'DMSD3D', 'EYE', 'DAD', 'CAM', 'VP5', 'SKM', 'MP21', 'TID', 'RP', 'QSMD', 'MPF', 'JTS', 'SML', 'FLX', 'H263', 'KUX', 'MOVIE', 'JNR', 'VSH', 'LVF', 'VFT', 'AV3', 'GRASP', 'AVC', 'RPL', 'PMV', 'H262', 'RTS', 'DSY', 'ML20', 'CEL', 'RL2', 'MVI', 'P64', 'PAF', 'TGV', 'TGQ', 'PJR'];

const ltrim = (str: string, chr: string): string => {
    const rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+');
    return str.replace(rgxtrim, '');
}

const in_array = function (value: any, needle: Array<any>): boolean {
    if (Array.isArray(needle)) {
        const count = needle?.filter((item: any) => item === value)?.length;
        if (count > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//Find all childs of item
function childrens(items: Array<any>, array: Array<any>): any {
    for (let i = 0; i < items?.length; i++) {
        const item = items[i];
        if (item?.children?.length > 0) {
            childrens(item?.children, array);
        }
        if (item?.id !== undefined) {
            array.push(item?.id);
        } else {
            array.push(item?.key);
        }
    }
    return array;
}

//Find tree element and its hirerchy
function findById(subMenuItems: Array<any>, id: string): any {
    if (subMenuItems) {
        for (let i = 0; i < subMenuItems.length; i++) {
            if (subMenuItems[i]?.id == id || subMenuItems[i]?.key == id) {
                return subMenuItems[i];
            }
            const found = findById(subMenuItems[i]?.children, id);
            if (found) return found;
        }
    }
}

//All current page items
function getCurrentPageAllItems(items: Array<any>): any {
    const array = [];
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        array?.push(item?.id);
    }
    return array;
}

const keywordFilter = (nodes: Array<any>, keyword: string, color?: boolean): any => {
    const newNodes = [];
    for (const n of nodes) {
        if (n.children) {
            const nextNodes = keywordFilter(n.children, keyword);
            if (nextNodes.length > 0) {
                n.children = nextNodes;
            } else if (n?.text?.toLowerCase().includes(keyword.toLowerCase())) {
                n.children = nextNodes.length > 0 ? nextNodes : [];
            }
            if (
                nextNodes.length > 0 ||
                n?.text?.toLowerCase().includes(keyword.toLowerCase())
            ) {
                n.label = getHighlightText(n.id, n.text, keyword);
                newNodes.push(n);
            }
        } else {
            if (n?.text?.toLowerCase().includes(keyword.toLowerCase())) {
                n.label = getHighlightText(n.id, n.text, keyword);
                newNodes.push(n);
            }
        }
    }
    return newNodes;
}

const getHighlightText = (id: any, text: string, keyword: string): any => {
    const startIndex = text.toLowerCase().indexOf(keyword.toLowerCase());
    return startIndex !== -1 ? (
        <React.Fragment>
            <span data-key={'folder-' + id}>
                {text.substring(0, startIndex)}
                <span style={{ background: "#ffec43", color: "#6a5f00", borderRadius: "2px" }} data-key={'folder-' + id}>
                    {text.substring(startIndex, startIndex + keyword.length)}
                </span>
                {text.substring(startIndex + keyword.length)}
            </span>
        </React.Fragment>
    ) : (
        <span>{text}</span>
    );
};

//Remove item from array
const removeItemFromArray = (array: Array<any>, key: string): Array<any> => {
    return array.filter((item: any) => item !== key);
}

const removeItemFromArrayByKey = (array: any, keyValue: any) => {
    return array.filter((item: any, key: any) => key !== keyValue);
}

//Recursive search
function addElementToArray(nodes: Array<any>, childrens: Array<any>, key: string): Array<any> {
    const newNodes = [];
    for (const n of nodes) {
        if (n.children) {
            const nextNodes = addElementToArray(n.children, childrens, key);
            if (nextNodes.length > 0) {
                n.children = nextNodes;
            } else {
                n.children = nextNodes.length > 0 ? nextNodes : [];
            }
            newNodes.push(n);
        } else {
            if (n?.key === key) {
                n.children = childrens;
            }
            newNodes.push(n);
        }
    }
    return newNodes;
}

const wordpressExtendMediaAttachmentsBrowser = function () {
    if ((window as any).wp && void 0 !== (window as any).wp.media && void 0 !== (window as any).wp.media.view && void 0 !== (window as any).wp.media.view.AttachmentFilters) {
        const AttachmentsBrowser = (window as any).wp.media.view.AttachmentsBrowser;
        (window as any).wp.media.view.AttachmentsBrowser = (window as any).wp.media.view.AttachmentsBrowser.extend({
            createToolbar: function () {
                (window as any).appLocalizer.attachmentsBrowser = this, AttachmentsBrowser.prototype.createToolbar.call(this);
            }
        })
    }
}

const generateRandomInteger = function (min: any, max: any) {
    return Math.floor(min + Math.random() * (max + 1 - min))
}

//Update tree element
const updateArrayValue = (nodes: Array<any>, id: any, key: string, value?: any): any => {
    const newNodes = [];
    for (const n of nodes) {
        if (n.children && n.children?.length > 0) {
            const nextNodes = updateArrayValue(n.children, id, key, value);
            if (nextNodes.length > 0) {
                n.children = nextNodes;
            } else {
                n.children = [];
            }
            if (n?.id === id || n?.ID === id) {
                n[key] = value;
            }
            newNodes.push(n);

        } else {
            if (n?.id === id || n?.ID === id) {
                n[key] = value;
            }
            newNodes.push(n);
        }
    }
    return newNodes;
}

const updateAttachmentValues = (nodes: Array<any>, attachments: any, key?: any, value?: any): any => {
    const newNodes = [];
    for (const n of nodes) {
        console.log(n)
        if (in_array(`attachment-${n?.ID}`, attachments) || in_array(`attachment-${n?.id}`, attachments)) {
            n[key] = value;
        }
        newNodes.push(n);
    }
    return newNodes;
}

function getGridData(ref: any) {

    // calc computed style
    const gridComputedStyle = (window as any).getComputedStyle(ref);

    if (gridComputedStyle !== undefined) {
        return {
            // get number of grid rows
            gridRowCount: gridComputedStyle.getPropertyValue("grid-template-rows").split(" ").length,
            // get number of grid columns
            gridColumnCount: gridComputedStyle.getPropertyValue("grid-template-columns").split(" ").length,
            // get grid row sizes
            gridRowSizes: gridComputedStyle.getPropertyValue("grid-template-rows").split(" ").map(parseFloat),
            // get grid column sizes
            gridColumnSizes: gridComputedStyle.getPropertyValue("grid-template-columns").split(" ").map(parseFloat)
        }
    } else {
        return {}
    }

}

function reminderOfTotalByInterval(total: any, interval: any) {
    let counter = 0;
    do {
        if ((counter + interval) <= total) {
            counter += interval;
        } else {
            break;
        }

    }
    while (counter <= total);

    return counter;
}

function startsWith(str: any, word: any) {
    return str.lastIndexOf(word, 0) === 0;
}

function getFields(input: any, field: any) {
    const output = [];
    for (let i = 0; i < input.length; ++i)
        output.push(input[i][field]);
    return output;
}

//Flat tree
function flatTree(items: any, array: any): any {

    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        if (item?.children?.length > 0) {
            flatTree(item.children, array);
        }

        if (Number(item?.starred) === 1) {
            array.push(item);
        }
    }

    return { children: array };
}

function getPath(items: any, val: any): any {
    for (let i = 0; i < items?.length; i++) {
        const item = items[i];
        if (item.id !== val) {
            if (item.children) {
                const path = getPath(item.children, val);
                if (path) {
                    path.unshift({ 'id': item?.id, 'text': item?.text, 'children': item?.children });
                    return path;
                }
            }
        } else {
            return [{ 'id': item?.id, 'text': item?.text, 'children': item?.children }];
        }
    }
}

function getCurrentFolderPreviousPathIds(items: any, array: any): any {
    for (let i = 0; i < items?.length; i++) {
        const item = items[i];
        array.push(item?.id);
    }
    return array;
}

function getUniqueAttachments(items: any, removedItems: any) {
    const attachments: any = [];
    const ids: any = [];
    items.forEach((item: any) => {
        if (!in_array(item?.id, ids) && !in_array(`attachment-${item?.id}`, removedItems)) {
            attachments.push(item);
            ids.push(item?.id);
        }
    });
    return attachments;
}

export {
    ltrim,
    in_array,
    childrens,
    findById,
    getCurrentPageAllItems,
    keywordFilter,
    removeItemFromArray,
    addElementToArray,
    wordpressExtendMediaAttachmentsBrowser,
    generateRandomInteger,
    updateArrayValue,
    updateAttachmentValues,
    getGridData,
    reminderOfTotalByInterval,
    startsWith,
    removeItemFromArrayByKey,
    flatTree,
    getFields,
    getPath,
    getCurrentFolderPreviousPathIds,
    image_ext,
    compressed_ext,
    video_ext,
    getUniqueAttachments
};