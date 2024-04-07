import React from 'react'
import { Dimensions } from 'react-native'
import { images } from '@src/styles';
import { Ionicons } from '@expo/vector-icons'
import { Box, Center, Container, HStack, Icon, IconButton, Image, Spacer, Text } from 'native-base'
import SpeakersSessionName from './SpeakersSessionName';
import Index from './detail/Index';
import FooterScreen from '@src/screens/components/FooterScreen';

const SpeakersSession = ({ navigation }: any) => {
  const heigth = Dimensions.get('window').height;
  const width = Dimensions.get('window').width;
  return (
    <Container bg="#fff" w="100%" maxW="100%" h="100%" alignItems="flex-start" justifyContent="flex-start">
      <HStack h={heigth - 80} w="100%" space="0" alignItems="center">
        <Center alignItems="flex-start" justifyContent="flex-start" bg="#f7f7f7" w="270px" h="100%">
          <Box h="165px" w="100%">
            <Image
              mx="6"
              my="6"
              source={images.Logosm}
              alt="Alternate Text"
              w="45px"
              h="70px"
          
            />
            <HStack w="100%" px="4" space="0" alignItems="center">
              <Text fontSize="xl" bold>Speaker List Sessions</Text>
            </HStack>
          
          </Box>
          <SpeakersSessionName />
        </Center>
        <Center alignItems="flex-start" justifyContent="flex-start"  bg="#ffffff" w={width - 270} h="100%">
          <Index />
        </Center>
      </HStack>
      <FooterScreen navigation={navigation} />
    </Container>
  )
}

export default SpeakersSession;