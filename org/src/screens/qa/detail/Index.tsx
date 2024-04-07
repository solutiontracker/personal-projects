import { Center, Container, HStack, Spinner } from 'native-base';
import React from 'react';
import { Dimensions } from 'react-native'
import LiveView from './LiveView';
import HeaderScreen from '@src/screens/components/HeaderScreen';
import ModerationView from './ModerationView';
import UseQaService from '@src/store/services/UseQaService';
import { useEffect } from 'react';
import CloneQuestion from '@src/screens/components/questions/CloneQuestion';

const Index = ({ navigation }: any) => {
  const heigth = Dimensions.get('window').height;
  const { modules, loadPrograms, activeId, innerProcessing, statusprocessing, programs, openpopup} = UseQaService();
  const loadData = () => {
    loadPrograms({id: activeId})
  }
  useEffect(() => {
    loadData()
  }, [activeId, statusprocessing]);
  return (
    <Container p="6" w="100%" h="100%" maxW="100%" >
      {modules?.event && <HeaderScreen data={modules?.event} />}
      {innerProcessing  &&  <HStack w="100%" h={heigth - 200}  space={8} justifyContent="center"><Spinner /></HStack>}
      {!innerProcessing &&  programs.length !== 0 &&  <HStack w="100%" h={heigth - 200} space="4%" alignItems="center">
        <Center w={programs.setting.moderator === 1 ? '48%' : '100%'} h="100%">
          <ModerationView  />
        </Center>
        {programs?.setting.moderator === 1 && <Center w="48%" h="100%">
          <LiveView  />
        </Center>}
      </HStack>}
      {openpopup?.status && <CloneQuestion />}
    </Container>
    
  )
}

export default Index
