/* eslint-disable @typescript-eslint/no-unsafe-return */
import React, { useEffect, useState } from 'react';
import DateTimePicker from '@react-native-community/datetimepicker';
import { Icon, IconButton, Modal, Button, Container, Box, Text, TextArea, HStack, Center, Spacer, Checkbox, Switch, VStack, Input } from 'native-base';
import { Ionicons } from '@expo/vector-icons'
import CustomSelect from './CustomSelect';
import UsePollsService from '@src/store/services/UsePollsService';
import SingleEdit from './questions/SingleEdit';

const question_type = [
  {id: 'single', name: 'User can select one answer'},
  {id: 'multiple', name: 'User can select multiple answers'},
  {id: 'open', name: 'User can type a response'},
  {id: 'number', name: 'User can type a number'},
  {id: 'date', name: 'User can select a date'},
  {id: 'date_time', name: 'User can select a date and time'},
  {id: 'dropdown', name: 'User can select a value from dropdown'},
  {id: 'matrix', name: 'User can select matrix'},
  {id: 'world_cloud', name: 'Use word cloud'},
]
const chart_type = [
  {id: 'pie', name: 'Pie chart'},
  {id: 'horizontal', name: 'Horizontal bar chart'},
  {id: 'vertical', name: 'Vertical bar chart'},
  {id: 'progress', name: 'Progress bar chart'},
]

function createArray (ara: any) {
  const _answer: { value: any; correct: any; }[] = [];
  ara.forEach((element: { correct: any; value: any; }, k: any) => {
    const _object = {
      id: element.id,
      value: element.value,
      correct: element.correct
    }
    _answer.push(_object);
  });
  return _answer;
}
const EditQuestion = ({data, onPress}: any) => {
  const [scheduleModal, setscheduleModal] = useState(false);
  const { updatequestion, program_id } = UsePollsService();
  const [state, setState] = useState({
    program: '',
    questiontype: data.question_type, 
    charttype: data.result_chart_type, 
    required_question: data.required_question,
    enable_comments: data.enable_comments,
    is_anonymous: data.is_anonymous,
    question: data.info[0].value,
    question_status: data.question_status,
    data_question: [],
    data_column: [],
    max_options: data.max_options,
    min_options: data.min_options,
  })
  const handleSelect = (name:any, value:any) => {
    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    setState((prevState: any) => ({ ...prevState, [name]: value }));
  }

  const getData = (item: any) => {
    setState((prevState: any) => ({ ...prevState, data_question: item }));
  }
  const getColumn = (item: any) => {
    setState((prevState: any) => ({ ...prevState, data_column: item }));
  }
  const handleSubmit = () => {
    if (state.questiontype === 'single' || state.questiontype === 'multiple' || state.questiontype === 'dropdown') {
      updatequestion({
        question_id: data.id,
        question_type: state.questiontype,
        question: state.question,
        result_chart_type: state.charttype,
        page: program_id,
        required_question: state.required_question,
        enable_comments: state.enable_comments,
        is_anonymous: state.is_anonymous,
        question_status: state.question_status,
        max_options: state.max_options,
        min_options: state.min_options,
        column: [],
        answer: createArray(state.data_question),
      });
    }
    if (state.questiontype === 'open' || state.questiontype === 'number'|| state.questiontype === 'date' || state.questiontype === 'date_time') {
      updatequestion({
        question_id: data.id,
        question_type: state.questiontype,
        question: state.question,
        result_chart_type: state.charttype,
        page: program_id,
        required_question: state.required_question,
        enable_comments: state.enable_comments,
        is_anonymous: state.is_anonymous,
        answer: [],
        max_options: state.max_options,
        min_options: state.min_options,
        question_status: state.question_status,
        column: []
      });
    }
    if (state.questiontype === 'matrix') {
      updatequestion({
        question_id: data.id,
        question_type: state.questiontype,
        question: state.question,
        result_chart_type: state.charttype,
        page: program_id,
        required_question: state.required_question,
        enable_comments: state.enable_comments,
        is_anonymous: state.is_anonymous,
        question_status: state.question_status,
        max_options: state.max_options,
        min_options: state.min_options,
        answer: createArray(state.data_question),
        column: createArray(state.data_column),
      });
    }
  }
  console.log(program_id)
  return (
    <React.Fragment>
      <Modal size="full" isOpen onClose={() => {}}>
        <Modal.Content maxW="660px">
          {<>
            <Modal.Header bg="primary.default" _text={{ color: '#fff' }}>
              Edit Question
            </Modal.Header>
            <Modal.Body borderWidth="0">
              <Container w="100%" maxW="100%">
                <Spacer h="3" />
                <HStack mb="4" w="100%" space="2%" alignItems="center">
                  <Center w="49%">
                    <CustomSelect
                      width="300px"
                      title="Question type"
                      name="questiontype"
                      value={state.questiontype}
                      onSelect={handleSelect}
                      required="true"
                      items={question_type}
                    />
                  </Center>
                  <Center w="49%">
                    <CustomSelect
                      width="300px"
                      title="Result Chart Type *"
                      name="charttype"
                      value={state.charttype}
                      required="false"
                      onSelect={handleSelect}
                      items={chart_type}
                    />
                  </Center>
                </HStack>
                <Box mb="2" px="4" py="2" rounded="lg" borderWidth="1" borderColor="#E0E0E0" w="100%">
                  <Text color="#bbb" fontSize="xs" bold>Question *</Text>
                  <TextArea value={state.question} onChange={(e: { target: { value: any; }; }) => handleSelect('question',e.target.value)}  h="40px" fontSize="xs" focusOutlineColor="transparent" _focus={{bg: 'transparent'}} p="0" borderWidth="0" placeholder="Here is the Question"  />
                </Box>
                {(state.questiontype === 'single' || state.questiontype === 'multiple' || state.questiontype === 'dropdown') && <SingleEdit answers={data.answer} type={false} item_data={getData} />}
                {state.questiontype === 'matrix' && 
                  <>
                    <SingleEdit answers={data.answer} item_data={getData} type={false} />
                    <SingleEdit answers={data.matrix} item_data={getColumn} type="column" />
                  </>
                }
                {state.questiontype === 'multiple' && 
                  <VStack mb="5" space="2">
                    <HStack  space="3" alignItems="center">
                      <Text  fontSize="xs">Minimum selectable options </Text>
                      <Input w='60%' rounded='6px' height={35}  onChange={(e: { target: { value: any; }; }) => handleSelect('min_options',e.target.value)} value={state.min_options} placeholder="Option"  />
                    </HStack>
                    <HStack  space="3" alignItems="center">
                      <Text  fontSize="xs">Maximum selectable options </Text>
                      <Input w='60%' rounded='6px' height={35}  onChange={(e: { target: { value: any; }; }) => handleSelect('max_options',e.target.value)} value={state.max_options} placeholder="option"  />
                    </HStack>
                      
                  </VStack>
                  
                }
                <Center mb="4" pb="3" borderBottomWidth="1" borderBottomColor="#E0E0E0" w="100%" maxW="100%" alignItems="flex-start">
                  <HStack space="4" w="100%">
                    <Checkbox onChange={(value) => handleSelect('required_question', value)} size="sm" _text={{ fontSize: 'xs', margin: 0 }} isChecked={state.required_question} value={state.required_question}>
                    Required question
                    </Checkbox>
                    <Checkbox isChecked={state.enable_comments} onChange={(value) => handleSelect('enable_comments', value)} size="sm" _text={{ fontSize: 'xs', margin: 0 }} value={state.enable_comments}>
                  Enable comments
                    </Checkbox>
                    <Checkbox isChecked={state.is_anonymous} onChange={(value) => handleSelect('is_anonymous', value)} size="sm" _text={{ fontSize: 'xs', margin: 0 }} value={state.is_anonymous}>
                  Anonymous
                    </Checkbox>
                  </HStack>
                </Center>
                <HStack space="3" alignItems="center">
                  <HStack space="3" alignItems="center">
                    <Switch isChecked={state.question_status} onToggle={(value: any) => handleSelect('question_status',value)} />
                    <Text fontSize="xs">Activate Question Now</Text>
                  </HStack>
                  <Button
                    _pressed={{_text: {color: '#fff'}}}
                    size="sm"
                    rounded="lg"
                    leftIcon={<Icon color="#231F20" as={Ionicons} name="ios-calendar-outline" />}
                    bg="#f7f7f7"
                    _text={{color: '#231F20'}}
                    variant="unstyled"
                    onPress={()=>{console.log('true')}}>
                    Schedule Question
                  </Button>
                </HStack>
              </Container>
            </Modal.Body>
            <Modal.Footer pt="0" borderColor={'transparent'}>
              <Button.Group size="md" space="2">
                <Button onPress={onPress} variant="unstyled">
                Cancel
                </Button>
                <Button onPress={handleSubmit} rounded="lg" minW="100px" _text={{ fontWeight: 'bold' }}>
                Save
                </Button>
              </Button.Group>
            </Modal.Footer>
          </>}
        </Modal.Content>
      </Modal>
      <Modal size="full" isOpen={scheduleModal} onClose={() => {}}>
        <Modal.Content maxW="660px">
          <Modal.Header bg="primary.default" _text={{ color: '#fff' }}>
            Schedule Polls
          </Modal.Header>
          <Modal.Body borderWidth="0">
            <DateTimePicker
              testID="dateTimePicker"
              display={true}
              placeholderText="select date"
            />
          </Modal.Body>
          <Modal.Footer pt="0" borderColor={'transparent'}>
            <Button.Group size="md" space="2">
              <Button onPress={onPress} variant="unstyled">
                Cancel
              </Button>
              <Button onPress={handleSubmit} rounded="lg" minW="100px" _text={{ fontWeight: 'bold' }}>
                Save
              </Button>
            </Button.Group>
          </Modal.Footer>
        </Modal.Content>
      </Modal>
    </React.Fragment>
  );
}

export default EditQuestion;