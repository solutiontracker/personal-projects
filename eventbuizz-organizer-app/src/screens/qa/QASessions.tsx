import React from 'react'
import { Dimensions } from 'react-native'
import { images } from '@src/styles';
import { Ionicons } from '@expo/vector-icons'
import { Box, Center, Container, HStack, Icon, IconButton, Image, Spacer, Spinner, Text } from 'native-base'
import QASessionsName from './QASessionsName';
import Index from './detail/Index';
import FooterScreen from '@src/screens/components/FooterScreen';
import UseQaService from '@src/store/services/UseQaService';

const QASessions = ({ navigation }: any) => {
  const heigth = Dimensions.get('window').height;
  const width = Dimensions.get('window').width;
  const { modules, loadModules, processing } = UseQaService();
  React.useMemo(() => {
    loadModules();
  }, []);
  return (
    <>
      {processing && 
      <HStack bg="#fff" w="100%" maxW="100%" h="100%"  px="8" py="8" mx="auto" space={2} justifyContent="center" alignItems="center">
        <Spinner size="lg" accessibilityLabel="Loading posts" />
        <Text color="primary.500" fontSize="xl">
          Loading
        </Text>
      </HStack>}
      {!processing && <Container bg="#fff" w="100%" maxW="100%" h="100%" alignItems="flex-start" justifyContent="flex-start">
        <HStack h={heigth - 80} w="100%" space="0" alignItems="center">
          <Center alignItems="flex-start" justifyContent="flex-start" bg="#f7f7f7" w="310px" h="100%">
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
                <Text fontSize="xl" bold>Q&A Sessions</Text>
              </HStack>
          
            </Box>
            {modules.qa_session_programs && <QASessionsName data={modules.qa_session_programs} />}
          </Center>
          <Center alignItems="flex-start" justifyContent="flex-start"  bg="#ffffff" w={width - 310} h="100%">
            <Index />
          </Center>
        </HStack>
        <FooterScreen navigation={navigation} />
      </Container>}
    </>
  )
}

export default QASessions;