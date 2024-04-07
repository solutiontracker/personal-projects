import { Center, Container, HStack } from 'native-base';
import React from 'react';
import { Dimensions } from 'react-native'
import LiveView from './LiveView';
import HeaderScreen from '@src/screens/components/HeaderScreen';
import ModerationView from './ModerationView';

const Index = ({ navigation }: any) => {
  const heigth = Dimensions.get('window').height;
  const [moderation, setModeration] = React.useState(true);
  const handleChange = (e) => {
    setModeration(e);
  }
  return (
    <Container p="6" w="100%" h="100%" maxW="100%" >
      <HeaderScreen />
      <HStack w="100%" h={heigth - 200} space="2%" alignItems="center">
        {moderation && <Center w="31.333%" h="100%">
          <ModerationView  hasSpeakers />
        </Center>}
        {moderation && <Center w="31.333%" h="100%">
          <ModerationView  />
        </Center>}
        <Center w={moderation ? '31.333%' : '100%'} h="100%">
          <LiveView mode={moderation} toggleChange={handleChange} />
        </Center>
      </HStack>
			
    </Container>
    
  )
}

export default Index
