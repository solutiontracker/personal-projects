import React, { useEffect } from 'react';
import { Center, Container, HStack, Spinner } from 'native-base';
import { Dimensions } from 'react-native'
import UsePollsService from '@src/store/services/UsePollsService';
import ActiveQuestions from './ActiveQuestions';
import ClosedQuestions from './ClosedQuestions';
import HeaderScreen from '@src/screens/components/HeaderScreen';
import ListQuestions from './ListQuestions';

const Index = () => {
  const { questions, loadQuestionListing, questionprocessing, program_id, updatequestionprocessing } = UsePollsService();
  const heigth = Dimensions.get('window').height;
  const loadData = () => {
    loadQuestionListing({
      page: program_id,
    });
  }
  useEffect(() => {
    loadData();
  },[program_id, updatequestionprocessing]);
  return (
    <Container p="6" w="100%" h="100%" maxW="100%" >
      {questions.event && <HeaderScreen data={questions.event} />}
      {questionprocessing && <HStack w="100%" h={heigth - 400}  space={8} justifyContent="center"><Spinner /></HStack>}
      {!questionprocessing && <HStack w="100%" h={heigth - 200} space="3%" alignItems="center">
        <Center w="31.33%" h="100%">
          <ListQuestions data={questions.questions} />
        </Center>
        <Center w="31.33%" h="100%">
          <ActiveQuestions data={questions.active_questions} />
        </Center>
        <Center w="31.33%" h="100%">
          <ClosedQuestions />
        </Center>
      </HStack>}
			
    </Container>
    
  )
}

export default Index
