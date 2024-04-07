import { UseEventService } from 'application/store/services';
import UseAuthService from 'application/store/services/UseAuthService';
import { useRouter } from 'next/router';
import React from 'react'

const Index = () => {
  const { response,  } = UseAuthService();
  const { event } = UseEventService();
  const { push } = useRouter();

    const access_token_exists = Boolean(localStorage.getItem(`access_token`));
    React.useEffect(() => {
        if (access_token_exists === false) {
          push(`/${event.url}/auth/login`)
        }else{
            push(`/${event.url}/dashboard`)
        }
      }, [response])
  return null;
}

export default Index