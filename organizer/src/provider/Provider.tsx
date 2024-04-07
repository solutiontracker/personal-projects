import 'react-native-gesture-handler';
import React, { useState, useEffect } from 'react';
import { NativeBaseProvider, extendTheme, useColorMode  } from 'native-base';
import * as SplashScreen from 'expo-splash-screen';
import { LinearGradient } from 'expo-linear-gradient';
import { func } from '@src/styles';
import * as Font from 'expo-font';
import { NavigationProvider } from './navigation';



export function Provider({ children, env }: { children: React.ReactNode, env: any }) {
  
  const config = {
    dependencies: {
      'linear-gradient': LinearGradient
    }
  };
  const {colorMode} = useColorMode();
  console.log(colorMode);
  const [appIsReady, setAppIsReady] = useState(false);

  const theme = extendTheme({
    colors: {
      
      'primary': {
        '50': '#ffcea8',
        '100': '#febb88',
        '200': '#faa96b',
        '300': '#f5974f',
        '400': '#f3842f',
        '500': '#ec7a23',
        '600': '#e1701a',
        '700': '#ca681e',
        '800': '#b36021',
        '900': '#9e5823',
        'heading': '#231F20',
        'border' : '#bbb',
        'default' : '#676E73',
      }
      
    },
    components: {
      Input: {
        defaultProps: {
          fontSize: 'sm',
          height: '50px',
          bg: '#F7F7F7',
          rounded: '10px',
          borderColor: '#E1E8EF',
          _focus: {
            borderColor: '#E1E8EF',
          },
        },
        baseStyle: {
          _light: {
            placeholderTextColor: '#212121',
          },
          _dark: {
            placeholderTextColor: '#ffffff',
          },
        },
      }
    },
    fontConfig: {
      poppins: {
        400: {
          normal: 'poppins'
        },
        600: {
          normal: 'poppins-demi',
          fontWeight: 400,
        },
        700: {
          normal: 'poppins-demi',
          fontWeight: 400,
        },
      }
    },
    fonts: {
      heading: 'poppins',
      body: 'poppins',
      mono: 'poppins',
    },
  });

  useEffect(() => {
    async function prepare() {
      try {
        await SplashScreen.preventAutoHideAsync();
        func.loadAssetsAsync;
        await Font.loadAsync({
          'poppins': require('@src/assets/fonts/Poppins-Regular.ttf'),
          'poppins-demi': require('@src/assets/fonts/Poppins-SemiBold.ttf'),
          'poppins-bold': {
            uri: require('@src/assets/fonts/Poppins-SemiBold.ttf'),
            display: Font.FontDisplay.FALLBACK,
          },
        });
      } catch (e) {
        console.warn(e);
      } finally {
        setAppIsReady(true);
        await SplashScreen.hideAsync();
      }
    }
    void prepare();
  }, []);
  
  if (!appIsReady) {
    return null;
  }

  return (
    <NavigationProvider>
      <NativeBaseProvider config={config} theme={theme}>{children}</NativeBaseProvider>
    </NavigationProvider>
  );

}
