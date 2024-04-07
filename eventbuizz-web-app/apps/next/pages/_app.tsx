import { Provider } from 'application/provider/web'
import Head from 'next/head'
import React from 'react'
import type { SolitoAppProps } from 'solito'
import 'raf/polyfill'
import { Provider as ReduxProvider } from 'react-redux'
import { store } from 'application/store/Index'
import Master from 'application/screens/web/layouts/Master'
function MyApp({ Component, pageProps }: SolitoAppProps) {

  const env = {
    enviroment: process.env.NODE_ENV,
    api_base_url: process.env.APP_API_BASE_URL,
    msw_enabled: process.env.APP_MSW_ENABLED,
    eventcenter_base_url: process.env.APP_EVENTCENTER_BASE_URL,
    api_gateway_url: process.env.APP_API_GATEWAY_URL,
    app_server_enviornment: process.env.APP_SERVER_ENVIRONMENT,
    socket_connection_server: process.env.APP_SOCKET_SERVER,
  }

  const getLayout = Component.getLayout || ((page:any) => <Master>{page}</Master>)

  return (
    <>
      <Head>
        <title>Eventbuizz app</title>
        <meta
          name="description"
          content="Eventbuizz app"
        />
        <link rel="icon" href="/favicon.ico" />
      </Head>
      {typeof window !== "undefined" && (
        <ReduxProvider store={store}>
          <Provider env={env}>
            {getLayout(<Component {...pageProps} />)}
          </Provider>
        </ReduxProvider>
      )}
    </>
  )
}

export default MyApp
