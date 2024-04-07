/* eslint-disable @typescript-eslint/no-unsafe-assignment */
import 'react-native-gesture-handler';
import 'setimmediate'
import React from 'react';
import { StatusBar } from 'react-native';
import RootStack from '@src/navigations/RootStack';
import { Provider } from '@src/provider/Provider'
import { store } from '@src/store/Index'
import { Provider as ReduxProvider } from 'react-redux'
import { NODE_ENV, APP_API_BASE_URL, APP_MSW_ENABLED } from '@env';
const App = () => {

  const env = {
    enviroment: NODE_ENV,
    api_base_url: APP_API_BASE_URL,
    msw_enabled: APP_MSW_ENABLED
  }
  return (
    <ReduxProvider store={store}>
      {env && (
        <Provider env={env}>
          <StatusBar />
          <RootStack />
        </Provider>
      )}
    </ReduxProvider>
  );
};

export default App;
