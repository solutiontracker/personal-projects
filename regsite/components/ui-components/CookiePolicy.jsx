import React, { useState } from 'react';
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector } from "react-redux";
import { getCookie, setCookie } from 'cookies-next';
import { useRouter } from 'next/router'
import ActiveLink from 'components/atoms/ActiveLink';
const CookiePolicy = () => {

  const { event } = useSelector(eventSelector);

  const router = useRouter();

  const serverCookie = getCookie(`cookie__${event.url}`);

  const actionCookie = getCookie(`action_cookie__${event.url}`);

  const [show, setShow] = useState(((serverCookie != '') && (actionCookie == 'cookie_action')) ? false : true);

  const [showAction, setShowAction] = useState(actionCookie == 'cookie_action' ? true : false);

  const [statisticsCheck, setStatisticsCheck] = useState(false);

  const [cookieStatus, setCookieStatus] = useState(serverCookie);

  return (
    <React.Fragment>
      {!showAction && <div className='site-blocker'></div>}
      <div className="ebs-cookie-policy-container">
        {!show && <div onClick={() => setShow(true)} className="ebs-btn-cookie">
          <svg width="30.937" height="30.937" viewBox="0 0 30.937 30.937">
            <g transform="translate(-1248.532 -1157)">
              <path d="M217.747,135.557a7.734,7.734,0,0,0,6.574-3.661,3.309,3.309,0,0,1,1.02-5.484,7.549,7.549,0,0,0-.757-2.18,3.3,3.3,0,0,1-4.537-3.787,7.731,7.731,0,1,0-2.3,15.112Zm.392-6.789a.647.647,0,0,1-.182.116.521.521,0,0,1-.42,0,.574.574,0,0,1-.182-.116.56.56,0,0,1,0-.785.574.574,0,0,1,.182-.116.553.553,0,0,1,.6.9Zm3.082-1.337a.562.562,0,0,1,.6-.116.646.646,0,0,1,.182.116.574.574,0,0,1,.116.182.572.572,0,0,1-.116.6.582.582,0,0,1-.392.16.726.726,0,0,1-.11-.011.353.353,0,0,1-.1-.033.432.432,0,0,1-.1-.05.972.972,0,0,1-.082-.066.56.56,0,0,1,0-.785Zm-.713,3.155a1.657,1.657,0,1,1-1.657,1.657A1.657,1.657,0,0,1,220.509,130.585Zm-3.315-8.839a2.21,2.21,0,1,1-2.21,2.21A2.21,2.21,0,0,1,217.194,121.746ZM215,132.132a.354.354,0,0,1,.033-.1.421.421,0,0,1,.05-.1l.066-.083.082-.066a.432.432,0,0,1,.1-.05.353.353,0,0,1,.1-.033.579.579,0,0,1,.5.149.905.905,0,0,1,.066.083.447.447,0,0,1,.05.1.324.324,0,0,1,.033.1.727.727,0,0,1,.012.11.547.547,0,0,1-.552.552.823.823,0,0,1-.11-.011.353.353,0,0,1-.1-.033.431.431,0,0,1-.1-.05.973.973,0,0,1-.082-.066.574.574,0,0,1-.116-.182.553.553,0,0,1-.044-.21A.859.859,0,0,1,215,132.132Zm-3.878-4.861a1.657,1.657,0,1,1,1.657,1.657A1.657,1.657,0,0,1,211.118,127.271Z" transform="translate(1046.253 1043.54)" fill="#1da1c1" />
              <path d="M251.1,240.552a.552.552,0,1,1-.552-.552.552.552,0,0,1,.552.552" transform="translate(1008.476 930.259)" fill="#1da1c1" />
              <path d="M516.887,456.889l.54-.54-1.62-.54.54,1.62Z" transform="translate(757.353 726.372)" fill="#1da1c1" />
              <path d="M250.5,460h-8.839a1.657,1.657,0,0,0,0,3.315H250.5a1.657,1.657,0,0,0,0-3.315Zm-.552,2.21H242.21a.552.552,0,1,1,0-1.1h7.734a.552.552,0,1,1,0,1.1Z" transform="translate(1017.923 722.412)" fill="#1da1c1" />
              <path d="M322.21,171.1a1.1,1.1,0,1,1-1.1-1.1,1.1,1.1,0,0,1,1.1,1.1" transform="translate(942.343 996.392)" fill="#1da1c1" />
              <path d="M71.657,106.517H99.279a1.657,1.657,0,0,0,1.657-1.657V80H70v24.86A1.657,1.657,0,0,0,71.657,106.517Zm18.231-1.1H81.049a2.762,2.762,0,1,1,0-5.524h8.839a2.762,2.762,0,0,1,0,5.524Zm7.921-4.479a.552.552,0,0,1-.147.517l-.782.782,1.954,1.953a.552.552,0,1,1-.781.781L96.1,103.013l-.781.781a.553.553,0,0,1-.914-.216l-1.172-3.516a.552.552,0,0,1,.7-.7l3.516,1.172a.552.552,0,0,1,.363.4ZM85.468,81.1a8.8,8.8,0,0,1,3.188.595l.008.006h.009a.526.526,0,0,1,.077.055.543.543,0,0,1,.1.066.527.527,0,0,1,.055.082.589.589,0,0,1,.06.092.63.63,0,0,1,.023.1.591.591,0,0,1,.02.1.561.561,0,0,1-.018.107.527.527,0,0,1-.017.1l-.006.008v.009a2.25,2.25,0,0,0-.182.883,2.213,2.213,0,0,0,3.441,1.838l.008,0,.007-.006a.486.486,0,0,1,.094-.035.541.541,0,0,1,.1-.039c.07,0,.141,0,.211.006a.5.5,0,0,1,.083.034.3.3,0,0,1,.178.118.49.49,0,0,1,.08.08.012.012,0,0,0,0,.009l.006.006a8.684,8.684,0,0,1,1.228,3.465v0a.372.372,0,0,0,.009.051.578.578,0,0,1-.006.082.543.543,0,0,1-.008.11.534.534,0,0,1-.038.1.514.514,0,0,1-.047.093.541.541,0,0,1-.071.076.568.568,0,0,1-.08.069l-.175.078a2.21,2.21,0,0,0-.743,3.983.543.543,0,0,1,.067.059l.017.014a.552.552,0,0,1,.106.155c0,.011.009.023.013.035a.546.546,0,0,1,.039.193.58.58,0,0,1-.04.2c0,.009-.006.018-.01.026a.352.352,0,0,1-.017.044,8.839,8.839,0,1,1-7.771-13.053Z" transform="translate(1178.532 1081.42)" fill="#1da1c1" />
              <path d="M100.937,1.657A1.657,1.657,0,0,0,99.279,0H71.657A1.657,1.657,0,0,0,70,1.657V3.315h30.937ZM72.762,2.21H72.21a.552.552,0,0,1,0-1.1h.552a.552.552,0,1,1,0,1.1Zm2.762,0h-.552a.552.552,0,1,1,0-1.1h.552a.552.552,0,0,1,0,1.1Zm2.762,0h-.552a.552.552,0,1,1,0-1.1h.552a.552.552,0,0,1,0,1.1Zm20.44,0H80.5a.552.552,0,1,1,0-1.1H98.727a.552.552,0,1,1,0,1.1Z" transform="translate(1178.532 1157)" fill="#1da1c1" />
              <path d="M391.1,330.552a.552.552,0,1,1-.552-.552.552.552,0,0,1,.552.552" transform="translate(876.21 845.231)" fill="#1da1c1" />
            </g>
          </svg>
        </div>}
        {show &&
          <>
            <div className="ebs-cookie-container">
              {showAction && <span onClick={() => setShow(false)} className="btn-close">
                <i className="material-icons">close</i>
              </span>}
              <h4>Cookies Policy</h4>
              <p>{event?.interface_labels?.cookie.COOKIES_PRAGHRAP}
                <ActiveLink className="ebs-logo" href={`/${event.url}/cookie-policy`}>
                  {event?.interface_labels?.cookie.COOKIES_MORE_INFO}
                </ActiveLink>
              </p>
              <div className="ebs-cookie-type">
                <label className="label-radio">
                  <input type="checkbox" name="cookie" disabled defaultChecked={(serverCookie === 'necessary' || serverCookie === "all")} />
                  <span>
                    {event?.interface_labels?.cookie.COOKIES_NECESSARY}
                  </span>
                </label>
                <label className="label-radio">
                  <input type="checkbox" name="cookie" defaultChecked={serverCookie === 'all'} onChange={(e) => { setStatisticsCheck(true); setCookieStatus(e.currentTarget.checked ? 'all' : 'necessary') }} />
                  <span>
                    {event?.interface_labels?.cookie.COOKIES_STATISTICS}
                  </span>
                </label>
              </div>
              <button className="btn" onClick={() => {
                setCookie(`cookie__${event.url}`, statisticsCheck == true ? cookieStatus : 'all', { maxAge: 30 * 24 * 60 * 60, domain: '.eventbuizz.com' })
                setCookie(`action_cookie__${event.url}`, 'cookie_action', { maxAge: 30 * 24 * 60 * 60, domain: '.eventbuizz.com' })
                setShow(false);
                router.reload(window.location.pathname)
              }}>
                {statisticsCheck == true ? 'Save changes' : event?.interface_labels?.cookie.COOKIES_ACCEPT}
              </button>
              <button className="btn bordered" onClick={() => {
                setCookie(`cookie__${event.url}`, 'necessary', { maxAge: 30 * 24 * 60 * 60, domain: '.eventbuizz.com' })
                setCookie(`action_cookie__${event.url}`, 'cookie_action', { maxAge: 30 * 24 * 60 * 60, domain: '.eventbuizz.com' })
                setShow(false);
                router.reload(window.location.pathname)
              }}>
                {event?.interface_labels?.cookie.COOKIES_REJECT_COOKIES}
              </button>
            </div>
          </>
        }
      </div>
    </React.Fragment>
  );
};

export default CookiePolicy;