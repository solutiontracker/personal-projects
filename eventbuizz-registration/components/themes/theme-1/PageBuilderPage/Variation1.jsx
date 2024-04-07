import React from 'react'

const Variation1 = (props) => {
  const iframe = React.useRef();
  const [height, setHeight] = React.useState(0);
  const [Loaded, setLoaded] = React.useState(false);
  const Styles = `<style>
  * {
  margin: 0;
  padding: 0;
}
div {
  max-width: 100%;
}
*,
*::after,*::before {
  box-sizing: border-box;
}
body {
  font-family: 'Open Sans', sans-serif;
  font-size: 16px;
}
.cell {
  max-width: 1140px;
  margin: auto;
  padding: 0 15px;
}
.gjs-lory-frame img{
  width: 100%;
}
.accordion.ebs-accordion{background-color: transparent;}
img {
  max-width: 100%;
  height: auto;
}
.gp-hero {
  background-color: rgba(231, 231, 231, 0.4);
}

.gp-hero-container {
  padding-top: 60px;
  padding-bottom: 60px;
  max-width: 925px;
  text-align: center;
  margin-bottom: 0px
}
.gp-heading {
  font-size: 3rem;
  margin: 0 0 20px 0
}
.gp-text {
  font-size: 1.25rem;
  margin-bottom: 20px
}
.gp-btn {
  display: inline-block;
  padding: 0 10px
}
.gp-link {
  border: 1px solid #0b5ed7;
  font-size: 16px;
  background-color: #0b5ed7;
  display: inline-block;
  border-radius: 6px;
  color: #fff;
  padding: 12px 25px;
  text-decoration: none
}
.gp-secondry {
  border-color: #000;
  color: #000;
  background-color: transparent;
}
.gp-album {
  background-color: rgba(231, 231, 231, 0.4);
}
.gp-album-container {
  padding-left: 0;
  padding-right: 0;
  padding-top: 60px;
  padding-bottom: 60px;
}
.gp-album-container .gp-block-grid {
  padding: 0 0 0 0;
}
.gp-item-row,.gp-logo-row {
  display: flex;
  justify-content: flex-start;
  align-items: stretch;
  flex-wrap: wrap;
  word-wrap: break-word;
}
.gp-grid {
  padding: 0 15px;
  flex: 0 0 auto;
  width: 33.33%;
}
.gp-logo-grid {
  padding: 0 15px;
  flex: 0 0 auto;
  width: 20%;
}

@media (max-width: 768px) {
  .gp-logo-grid {
   width: 33.33%;
  }
  .gp-grid {
    width: 50%;
  }
 }
@media (max-width: 600px) {
  .gp-logo-grid {
   width: 50%;
  }
  .gp-grid {
    width: 100%;
  }
 }

.gp-figure {
  margin: 0;
  line-height: 0;
  text-align: center;
}
.gp-grid-wrapp {
  box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 1px solid rgba(0,0,0,.125);
  border-radius: .25rem;
  margin-bottom: 30px;
  overflow: hidden;
}
.gb-caption {
  padding: 1rem 1rem;
}
.gp-pricing-wrapp {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 1px solid rgba(0,0,0,.125);
  border-radius: .25rem;
  overflow: hidden;
  margin-bottom: 30px;
}
.gp-title {
  padding: 1rem 2rem;
  margin-bottom: 0;
  background-color: rgba(0,0,0,.03);
  border-bottom: 1px solid rgba(0,0,0,.125);
  text-align: center;
  font-size: 22px;
}
.gb-body {
  flex: 1 1 auto;
  padding: 2rem 1rem;
  text-align: center;
}
.gb-pricing-title {
  margin: 0 0 20px;
  font-weight: 400;
  font-size: 2rem;
}
.gb-light {
  color: #6c757d;
}
.gb-list {
  padding: 0;
  margin: 0 0 30px;
  list-style: none;
}
.gps-link {
  display: inline-block;
  padding: 3px;
  margin-right: 5px;
}
.op-icon {
  max-width: 44px;
  border-radius: 5px;
}
.gp-block-grid {
  padding: 0 15px;
  flex: 0 0 auto;
  width: 50%;
  display: flex;
  align-items: center;
}
.op-width-100 {
  width: 100%;
}
.textcenter {
  text-align: center;
}
.op-pb-20 {
  padding-bottom: 20px;
}
.gp-grid-box {
  padding: 0 15px;
  flex: 0 0 auto;
  width: 50%;
  display: flex;
}
.valign-middle {
  align-items: center;
}
@media (max-width: 600px) {
  .gp-block-grid,.gp-grid-box {
    width: 100%;
  }
}
.gp-footer-1 {
  background-color: #3C4342;
}
.gjs-lory-slides {
  width: 100%;
  padding: 0;
}

.twidth-100,.twidth-50,.twidth-33,.twidth-25,.twidth-20 {
  display: inline-block;
  width: 100%;
  background: rgba(0,0,0,.03);
  min-height: 150px;
  margin: 0;
  vertical-align: top;
}
.twidth-50 {
  width: 50%;
}
.twidth-33 {
  width: 33.33%;
}
.twidth-25 {
  width: 25%;
}
.twidth-20 {
  width: 20%;
}

.eb-dummy {
  display: flex;
  font-size: 60px;
  align-items: center;
  justify-content: center;
  height: 100%;
}
.gjs-lory-frame,[data-gjs-type='lory-slider'] {
  max-width: 100%;
}
.gp-footer-grid {
  padding: 0 15px;
  flex: 0 0 auto;
  width: 40%;
  display: flex;
  color: #fff; 
}
.gp-col-2 {
  width: 30%;
}
.gp-link-list {
  color: #fff;
  text-decoration: none;
}
.gp-left-footer {
  width: 65%;
  color: #000;
  align-items: center;
}
.gp-right-footer {
  width: 35%;
  align-items: center;
  justify-content: flex-end;
}
.op-bottom-link {
  text-decoration: none;
  color: #555;
  margin-left: 10px;
}
.ebs-separator {
  padding: 60px 15px;
  width: 100%;
}
.ebs-breakpage {
  height: 1px;
  width: 100%;
  background-color: rgba(0,0,0,.5);
}
@media (max-width: 600px) {
  .gp-footer-grid,.gp-left-footer,
  .gp-right-footer {
    width: 100%;
    justify-content: center;
  }
}
.ebs-container {
  max-width: 1140px;
  padding: 10px 0px;
  margin: auto;
  min-height: 75px;
}
.ebs-row {
  display: flex;
  justify-content: flex-start;
  align-items: stretch;
  flex-wrap: wrap;
  word-wrap: break-word; 
}
.ebs-grid {
  padding: 15px;
  flex: 0 0 auto;
  width: 100%;
  min-height: 55px;
}
.eb-1 {
  width: 8.3333333333%;
}
.eb-2 {
  width: 16.6666666667%;
}
.eb-3 {
  width: 25%;
}
.eb-4 {
  width: 33.3333333333%;
}
.eb-5 {
  width: 41.6666666667%;
}
.eb-6 {
  width: 50%;
}
.eb-7 {
  width: 58.3333333333%;
}
.eb-8 {
  width: 66.6666666667%;
}
.eb-9 {
  width: 75%;
}
.eb-10 {
  width: 83.3333333333%;
}
.eb-11 {
  width: 91.6666666667%;
}
.eb-12 {
  width: 100%;
}
@media (max-width: 600px) {
  .ebs-grid {
    width: 100%;
  }
}
  ${JSON.parse(props.data.css)}</style>`;
React.useEffect(() => {
  if (Loaded && iframe?.current !== null) {
  const observer = new ResizeObserver(() => {
    setHeight(iframe?.current !== null ? iframe.current.contentWindow.document.body.offsetHeight + 80 : height);
});
  observer.observe(iframe.current.contentWindow.document.body);
}
}, [Loaded])
  return (
    <React.Fragment>
      <div className="edgtf-container">
      {!Loaded && 
        <div style={{paddingTop: '30px',paddingBottom: '30px'}} className="d-flex justify-content-center"> 
          <div style={{width: '6rem', height: '6rem'}} className="spinner-border"> <span className="sr-only">Loading...</span></div>
        </div>}
        {props.data?.html  && <iframe
            ref={iframe}
            onLoad={() => {
              const obj = iframe.current;
              setHeight(obj.contentWindow.document.body.scrollHeight + 80);
              setLoaded(true)
            }}
            width="100%"
            height={height}
            title="test"
            itemProp="description"
            className="edgtf-post-excerpt"
            srcDoc={`<base target="_top">${Styles+JSON.parse(props.data.html)}</base>`}
          />}
        </div>
    </React.Fragment>
  )
}

export default Variation1