/* eslint-disable @typescript-eslint/no-unsafe-return */
import React, { useEffect, useState } from 'react';
import DateTimePicker from '@react-native-community/datetimepicker';
import { Icon, IconButton, Modal, Button, Container, Box, Text, TextArea, HStack, Center, Spacer, Checkbox, Switch } from 'native-base';
import { Ionicons } from '@expo/vector-icons'
import CustomSelect from './CustomSelect';
import UsePollsService from '@src/store/services/UsePollsService';
import Single from './questions/Single';

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
const CreatePoll = ({title, type}: any) => {
  const [modalVisible, setModalVisible] = useState(false);
  const [scheduleModal, setscheduleModal] = useState(false);
  const { programs, loadprograms, createprocessing, createpoll, error } = UsePollsService();
  const [state, setState] = useState({
    program: '',
    questiontype: '', 
    charttype: 'pie', 
    required_question: false,
    enable_comments: false,
    is_anonymous: false,
    question: '',
    poll_status: false,
    data_question: [],
  })
  const handleSelect = (name:any, value:any) => {
    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    setState((prevState: any) => ({ ...prevState, [name]: value }));
  }

  const getData = (item: any) => {
    setState((prevState: any) => ({ ...prevState, data_question: item }));
  }
  const handleSubmit = () => {
    if (state.questiontype === 'single' || state.questiontype === 'multiple' || state.questiontype === 'dropdown') {
      const _answer: { value: any; correct: any; }[] = [];
      state.data_question.forEach((element: { correct: any; value: any; }, k: any) => {
        const _object = {
          value: element.value,
          correct: element.correct
        }
        _answer.push(_object);
      });
      createpoll({
        question_type: state.questiontype,
        question: state.question,
        result_chart_type: state.charttype,
        agenda_id: state.program,
        required_question: state.required_question,
        enable_comments: state.enable_comments,
        is_anonymous: state.is_anonymous,
        poll_status: state.poll_status,
        max_options: '',
        min_options: '',
        column: [],
        answer: _answer,
      });
    }
    if (state.questiontype === 'open' || state.questiontype === 'number'|| state.questiontype === 'date' || state.questiontype === 'date_time') {
      createpoll({
        question_type: state.questiontype,
        question: state.question,
        result_chart_type: state.charttype,
        agenda_id: state.program,
        required_question: state.required_question,
        enable_comments: state.enable_comments,
        is_anonymous: state.is_anonymous,
        answer: [],
        max_options: '',
        min_options: '',
        poll_status: state.poll_status,
        column: []
      });
    }
  }
  return (
    <React.Fragment>
      <IconButton
        variant="solid"
        p="0"
        size="sm"
        icon={<Icon size="md" as={Ionicons} name="add-outline" color="white" />}
        onPress={() => {
          loadprograms();
          setModalVisible(true);
        }}
      />
      <Modal size="full" isOpen={modalVisible} onClose={() => {}}>
        <Modal.Content maxW="660px">
          {!createprocessing && <>
            <Modal.Header bg="primary.default" _text={{ color: '#fff' }}>
              {title ? title : 'Create'}
            </Modal.Header>
            <Modal.Body borderWidth="0">
              {!createprocessing && error && <Text mb="3" fontSize="sm">{error}</Text>}
              <Container w="100%" maxW="100%">
                {type === 'create' && <CustomSelect
                  width="628px"
                  title="Session Poll"
                  name="program"
                  required="true"
                  value={state.program}
                  onSelect={handleSelect}
                  items={programs}
                />}
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
                {(state.questiontype === 'single' || state.questiontype === 'multiple' || state.questiontype === 'dropdown') && <Single item_data={getData} />}
                <Center mb="4" pb="3" borderBottomWidth="1" borderBottomColor="#E0E0E0" w="100%" maxW="100%" alignItems="flex-start">
                  <HStack space="4" w="100%">
                    <Checkbox onChange={(value) => handleSelect('required_question',value)} size="sm" _text={{fontSize: 'xs',margin: 0}}  value={state.required_question}>
                    Required question
                    </Checkbox>
                    <Checkbox value={state.enable_comments} onChange={(value) => handleSelect('enable_comments',value)} size="sm" _text={{fontSize: 'xs',margin: 0}}>
                  Enable comments
                    </Checkbox>
                    <Checkbox value={state.is_anonymous} onChange={(value) => handleSelect('is_anonymous',value)} size="sm" _text={{fontSize: 'xs',margin: 0}}>
                  Anonymous
                    </Checkbox>
                  </HStack>
                </Center>
                <HStack space="3" alignItems="center">
                  <HStack space="3" alignItems="center">
                    <Switch isChecked={state.poll_status} onToggle={(value: any) => handleSelect('poll_status',value)} />
                    <Text fontSize="xs">Activate Poll Now</Text>
                  </HStack>
                  <Button
                    _pressed={{_text: {color: '#fff'}}}
                    size="sm"
                    rounded="lg"
                    leftIcon={<Icon color="#231F20" as={Ionicons} name="ios-calendar-outline" />}
                    bg="#f7f7f7"
                    _text={{color: '#231F20'}}
                    variant="unstyled"
                    onPress={()=>{setscheduleModal(true)}}>
                    Schedule Question
                  </Button>
                </HStack>
              </Container>
            </Modal.Body>
            <Modal.Footer pt="0" borderColor={'transparent'}>
              <Button.Group size="md" space="2">
                <Button onPress={() => setModalVisible(false)} variant="unstyled">
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
              <Button onPress={() => setscheduleModal(false)} variant="unstyled">
                Cancel
              </Button>
              <Button onPress={handleSubmit} rounded="lg" minW="100px" _text={{ fontWeight: 'bold' }}>
                Save
              </Button>
            </Button.Group>
          </Modal.Footer>
          
          {createprocessing && 
            <Modal.Body borderWidth="0">
              <Text minH="400" fontSize="lg">Loading</Text>
            </Modal.Body>}
        </Modal.Content>
      </Modal>

    </React.Fragment>
  );
}

export default CreatePoll;