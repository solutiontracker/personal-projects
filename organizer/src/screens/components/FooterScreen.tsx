import React from 'react';
import { useRoute } from '@react-navigation/native';
import { HStack,Button, Spacer } from 'native-base';
import IcoChart from '@src/assets/icons/IcoChart';
import IcoClipboard from '@src/assets/icons/IcoClipboard';
import IcoQuestions from '@src/assets/icons/IcoQuestions';
import IcoSpeaker from '@src/assets/icons/IcoSpeaker';
import IcoClipboardinfo from '@src/assets/icons/IcoClipboardinfo';
import IcoEvents from '@src/assets/icons/IcoEvents';

const FooterScreen = ({navigation}:any) => {
  const route = useRoute();
  return (
    <HStack mb="7" px="2" py="4" bg="primary.default" w="100%" space="3" h="80px" alignItems="center">
      <Button
        onPress={() => navigation.navigate('polls')}
        _text={{lineHeight: '22px', color: '#fff',fontSize: '16px'}}
        leftIcon={<IcoChart color="#fff" />} 
        variant="unstyled">
        Poll
      </Button>
      <Button
        onPress={() => navigation.navigate('polls')}
        _text={{lineHeight: '22px', color: '#fff',fontSize: '16px'}}
        leftIcon={<IcoClipboard color="#fff" />} 
        variant="unstyled">
        Survey
      </Button>
      <Button
        onPress={() => navigation.navigate('qa')}
        _text={{lineHeight: '22px', color: '#fff',fontSize: '16px'}}
        leftIcon={<IcoQuestions color="#fff"  />} 
        variant="unstyled">
        Q&A
      </Button>
      <Button
        onPress={() => navigation.navigate('speakers')}
        _text={{lineHeight: '22px', color: '#fff',fontSize: '16px'}}
        leftIcon={<IcoSpeaker color="#fff"  />} 
        variant="unstyled">
        Speaker List
      </Button>
      <Button
        onPress={() => navigation.navigate('news')}
        _text={{lineHeight: '22px', color: '#fff',fontSize: '16px'}}
        leftIcon={<IcoClipboardinfo color="#fff"  />} 
        variant="unstyled">
        News & updates
      </Button>
      <Spacer />
      <Button
        onPress={() => navigation.navigate('dashboard')}
        rounded="lg"
        _text={{lineHeight: '22px', color: '#fff',fontSize: '16px'}}
        leftIcon={<IcoEvents color="#fff"  />} 
        variant="outline">
        Events
      </Button>
      
    </HStack>
  )
}

export default FooterScreen;