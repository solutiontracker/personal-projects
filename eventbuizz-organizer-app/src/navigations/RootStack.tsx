import * as React from 'react';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import Index from '@src/screens/login/Index';
import Authentication from '@src/screens/login/Authentication';
import AuthenticationCode from '@src/screens/login/AuthenticationCode';
import Dashboard from '@src/screens/dashboard/Dashboard';
import Sessions from '@src/screens/sessions/Sessions';
import QASessions from '@src/screens/qa/QASessions';
import SpeakersSession from '@src/screens/speakers/SpeakersSession';
import NewsSession from '@src/screens/news/NewsSession';
import UseAuthService from '@src/store/services/UseAuthService';



const Stack = createNativeStackNavigator();

const RootStack = () => {
  const {  isLoggedIn } = UseAuthService();

  return (
    <>

      <Stack.Navigator initialRouteName="qa">
        {/* {!isLoggedIn && <>
          <Stack.Screen options={{ headerShown: false }} name="login" component={Index} />
          <Stack.Screen options={{ headerShown: false }} name="auth" component={Authentication} />
          <Stack.Screen options={{ headerShown: false }} name="verify" component={AuthenticationCode} />
        </>} */}
        {!isLoggedIn && <>
          <Stack.Screen options={{ headerShown: false }} name="dashboard" component={Dashboard} />
          <Stack.Screen options={{ headerShown: false }} name="polls" component={Sessions} />
          <Stack.Screen options={{ headerShown: false }} name="qa" component={QASessions} />
          <Stack.Screen options={{ headerShown: false }} name="speakers" component={SpeakersSession} />
          <Stack.Screen options={{ headerShown: false }} name="news" component={NewsSession} />
        </>}

      </Stack.Navigator>


    </>
  );
};

export default RootStack;