import * as React from 'react';

function Youtube (url) {
  const youtube =  /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
  const match = url.match(youtube);
  return match[2]
}
function Vimeo (url) {
  const video =  /^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/;
  const match = url.match(video);
  return match[4]
}
function DailyMotion (url) {
  const video =  /^(?:(?:https?):)?(?:\/\/)?(?:www\.)?(?:(?:dailymotion\.com(?:\/embed)?\/video)|dai\.ly)\/([a-zA-Z0-9]+)(?:_[\w_-]+)?$/;
  const match = url.match(video);
  return match[1]
}
const Videopopup = ({ onClose, photo }) => {
  React.useEffect(() => {
    if (typeof window !== 'undefined') {
      document.getElementsByTagName('body')[0].classList.add('un-scroll');
      return () => {
        document.getElementsByTagName('body')[0].classList.remove('un-scroll');
      }
    }
  }, [])

  return (
    <div onClick={onClose} className="wrapper-popup">
      <div onClick={(e) => e.stopPropagation()} className="container-popup">
        {Number(photo.type) === 4 || Number(photo.type) === 5 && <div className="ebs-video-wrapper">
          <video controls playsInline  autoPlay src={photo.video_path && process.env.NEXT_APP_EVENTCENTER_URL + "/assets/videos/" + photo.video_path} width='100%' height="540px"></video>
        </div>}
        {Number(photo.type) === 3 && <div className="ebs-video-wrapper">
        <iframe width="560" height="315" src={`https://www.youtube.com/embed/${Youtube(photo.URL)}?controls=1&autoplay=1`} title="YouTube video player" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen autoPlay></iframe>
        </div>}
        {Number(photo.type) === 2 && <div className="ebs-video-wrapper">
        <iframe width="560" height="315" src={`https://player.vimeo.com/video/${Vimeo (photo.URL)}?h=0a7f520d09&title=0&byline=0&portrait=0&autoplay=1`} title="Vimeo Player" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen autoPlay></iframe>
        </div>}
        {Number(photo.type) === 1 && <div className="ebs-video-wrapper">
        <iframe width="560" height="315" src={`https://www.dailymotion.com/embed/video/${DailyMotion(photo.URL)}?autoplay=1`} title="Daily Motion" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen autoPlay></iframe>
        </div>}
      </div>
    </div>
  );
}
export default Videopopup;
