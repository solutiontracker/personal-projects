import React, { useEffect, useState, useRef } from 'react';
import { profileSelector, fetchInvoiceData } from 'store/Slices/myAccount/profileSlice';
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import { service } from 'services/service';
import { useRouter } from 'next/router';
import PageLoader from 'components/ui-components/PageLoader'

const MyBilling = () => {

  const { event } = useSelector(eventSelector);

  const dispatch = useDispatch();

  const router = useRouter();

  const [loading, setLoading] = useState(false);

  useEffect(() => {
    dispatch(fetchInvoiceData(event.id, event.url));
  }, []);

  const mounted = useRef(false);

  const { invoice, order_id, is_invoice_update } = useSelector(profileSelector);

  const cloneOrder = (id) => {
    setLoading(true);
    service.post(`${process.env.NEXT_APP_API_URL}/registration/event/${event.url}/registration/clone-order/${id}/eventsite`, {})
      .then(
        response => {
          if (mounted.current) {
            setLoading(false);
            if (response.success) {
              router.push(`/${event.url}/profile/update-billing/${response.data.id}`);
            }
          }
        },
        error => {
          setLoading(false);
        }
      );
  }

  useEffect(() => {
    mounted.current = true;
    return () => { mounted.current = false; };
  }, []);

  return (
    loading ? (
      <PageLoader />
    ) : (
      <div className="edgtf-container ebs-my-profile-area pb-5">
        <div className="edgtf-container-inner container">
          <div className="ebs-header text-center">
            <h2>{event.labels.MY_REGISTRATION_INVOICE}</h2>
          </div>
          {invoice && (
            <>
              {is_invoice_update ? (
                <div className="bottom-button">
                  <button
                    className="btn btn-save-next btn-loader"
                    onClick={() => {
                      cloneOrder(order_id);
                    }}
                  >
                    {event.labels.EVENTSITE_BILLING_EDIT_LABEL !== undefined ? event.labels.EVENTSITE_BILLING_EDIT_LABEL : 'Edit'}
                  </button>
                </div>
              ) : ''}
              <div dangerouslySetInnerHTML={{ __html: invoice }}>
              </div>
            </>
          )}
        </div>
      </div>
    )
  )
}

export default MyBilling;