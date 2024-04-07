import React from "react";
import { connect } from "react-redux";
import Image from 'next/image'

const CustomSection = ({ data }) => {
  const iframe = React.useRef();
  const [height, setHeight] = React.useState(0);
  const [Loading, setLoading] = React.useState(true);
  React.useEffect(() => {
    window.addEventListener("resize", handleResize);
    return () => {
      window.removeEventListener("resize", handleResize);
    }
  }, [])
  const handleResize = () => {
        window.resizedFinished = setTimeout(() => {
          const obj = iframe.current;
          setHeight(obj.contentWindow.document.body.scrollHeight);
        }, 100);
  }
  return (
    <React.Fragment>
      {/* dangerouslySetInnerHTML={{__html:data}} */}
      {data && <div className="ebs-default-padding clearfix">
        {Loading && 
        <div className="d-flex justify-content-center"> 
          <div style={{width: '6rem', height: '6rem'}} className="spinner-border"> <span className="sr-only">Loading...</span></div>
        </div>}
        <iframe
            ref={iframe}
            onLoad={() => {
              const obj = iframe.current;
              obj.contentWindow.document.body.style.fontFamily = '"Open Sans", sans-serif';
              obj.contentWindow.document.body.style.margin = '0';
              setTimeout(() => {
                setHeight(obj.contentWindow.document.body.scrollHeight);
                setLoading(false)
              }, 1000);
            }}
            width="100%"
            height={height+20}
            title="test"
            itemProp="description"
            srcDoc={`<style>*{padding: 0; margin: 0;}</style>`+data}
          />
      </div>}
    </React.Fragment>
  )
  // return (
  //   <div style={{ paddingTop: "80px" }} className="edgtf-container pb-5">
  //     <div className="edgtf-container-inner container">
  //       <div className="ebs-custom-section-html">
  //         <section>
  //           <figure className="image" style={{ float: "left" }}>
  //             <img
  //               alt=""
  //               src="https://via.placeholder.com/350.png"
  //               width="350"
  //               height="350"
  //             />
  //             <figcaption>Caption</figcaption>
  //           </figure>
  //           1 Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa
  //           vitae facere debitis pariatur eos nisi inventore quis modi, atque
  //           earum magnam dolor provident possimus cumque. Tenetur mollitia
  //           maiores natus eveniet fugit dolore quae culpa, commodi itaque? Ullam
  //           ad nemo quae quis a, voluptatibus neque sed quod? Cumque incidunt
  //           dolores velit? Lorem ipsum dolor sit amet consectetur adipisicing
  //           elit. Ipsa vitae facere debitis pariatur eos nisi inventore quis
  //           modi, atque earum magnam dolor provident possimus cumque. Tenetur
  //           mollitia maiores natus eveniet fugit dolore quae culpa, commodi
  //           itaque?
  //           <br />
  //           <br />
  //           Ullam ad nemo quae quis a, voluptatibus neque sed quod? Cumque
  //           incidunt dolores velit? Lorem ipsum dolor sit amet consectetur
  //           adipisicing elit. Ipsa vitae facere debitis pariatur eos nisi
  //           inventore quis modi, atque earum magnam dolor provident possimus
  //           cumque. Tenetur mollitia maiores natus eveniet fugit dolore quae
  //           culpa, commodi itaque? Ullam ad nemo quae quis a, voluptatibus neque
  //           sed quod? Cumque incidunt dolores velit?
  //         </section>
  //       </div>
  //     </div>
  //   </div>
  // );
};

function mapStateToProps(state) {
  const { event } = state;
  return {
    event,
  };
}

export default connect(mapStateToProps)(CustomSection);
