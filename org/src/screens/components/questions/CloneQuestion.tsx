import React, { useState, useEffect } from 'react'
import { Button, Checkbox, HStack, Input, Modal, Spinner, TextArea, VStack } from 'native-base';
import { QaActions } from '@src/store/slices/Qa.Slice';
import { useDispatch } from 'react-redux';
import UseQaService from '@src/store/services/UseQaService';

const CloneQuestion = () => {
  const dispatch = useDispatch();
  const { popupprocessing, getquestiondata, openpopup, clonequesiondata, clonequestion } = UseQaService();
  const [question, setquestion] = useState('');
  const [anonymous_user, setanonymous_user] = useState(0);

  useEffect(() => getquestiondata({ id: openpopup?.id }), []);
  useEffect(() => {
    if (clonequesiondata.success) {
      setquestion(clonequesiondata?.data.qa_data.info[0].value)
    }
  }, [clonequesiondata])
  const handlePress = () => {
    clonequestion({
      id: openpopup?.id,
      question: question,
      anonymous_user: anonymous_user
    })
  }
  return (
    <Modal size="full" isOpen={true} onClose={() => {}}>
      <Modal.Content minH="300px" maxW="660px">
        {popupprocessing  &&  <HStack w="100%" h="300px" space={8} justifyContent="center"><Spinner /></HStack>}
        {!popupprocessing && clonequesiondata.success && <>
          <Modal.Header bg="primary.default" _text={{ color: '#fff' }}>
					Clone Question
          </Modal.Header>
          <Modal.Body borderWidth="0">
            <VStack  space="2">
              <Input  isReadOnly value={clonequesiondata?.data.attendee.first_name}  />
              <TextArea onChangeText={(val: string) => setquestion(val)}  value={question}  />
              <Checkbox onChange={() => setanonymous_user(!anonymous_user)} size="sm" value={anonymous_user}>
              Anonymous
              </Checkbox>
            </VStack>
          </Modal.Body>
          <Modal.Footer pt="0" borderColor={'transparent'}>
            <Button.Group size="md" space="2">
              <Button onPress={() => dispatch(QaActions.openpopupAction({id: 0, status: false}))}  variant="unstyled">Cancel</Button>
              <Button onPress={handlePress} rounded="lg" minW="100px" _text={{ fontWeight: 'bold' }}>Save</Button>
            </Button.Group>
          </Modal.Footer>
        </>}
      </Modal.Content>
    </Modal>
  )
}

export default CloneQuestion