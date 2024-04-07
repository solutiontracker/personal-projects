import React from 'react';
import { Ionicons } from '@expo/vector-icons'
import { Box, HStack, Icon, Pressable, ScrollView, Spacer, Switch, Text, VStack } from 'native-base';
import moment from 'moment';
import UsePollsService from '@src/store/services/UsePollsService';


const SessionsName = ({ programs }: any) => {
  const {program_id, programID , pollstatus} = UsePollsService();
  const handleEvent = (name: number) => {
    programID({id: name})
  }
  return (
    <ScrollView pb="3" pr="3" w="100%" h="100%">
      {programs && programs.data.map((item: {
        question_count: JSX.Element; id: number | null | undefined; program: { info: { value: any; }[]; created_at: string | number | Date; }; status: number; questions: any; active: number; 
},k: any) =>
        <Box  mb="2" key={item.id} w="100%" bg={item.id === program_id ? '#f1f1f1' :'#fff'} p="4">
          <VStack space="2">
            <HStack space="3" alignItems="center">
              <Pressable
                onPress={()=>{
                  handleEvent(item.id)
                }}
              ><Text fontSize="sm" bold>{item.program.info[0].value}</Text>
              </Pressable>
              <Spacer />
              <Switch onToggle={(event: any) => pollstatus({page: item.id, poll_status: event ? 1 : 0})} size="sm" onTrackColor="primary.100" onThumbColor="primary.500" defaultIsChecked={item.status ? true: false } />
            </HStack>
            <Pressable
              onPress={()=>{
                handleEvent(item.id)
              }}
            >
              <HStack mb="2" space="3" alignItems="center">
                <HStack space="1" alignItems="center">
                  <Icon as={Ionicons} name="ios-calendar-outline"  />
                  {/* <Text color="primary.default" fontSize="xs">{moment(new Date(item.program.created_at)).format('D MMMM, YYYY')}</Text> */}
                </HStack>
                <HStack space="1" alignItems="center">
                  <Icon as={Ionicons} name="ios-time-outline"  />
                  <Text color="primary.default" fontSize="xs">{moment(new Date(item.program.created_at)).format('h:mm a')}</Text>
                </HStack>
              </HStack>
              <HStack space="3" alignItems="center">
                {/* {item.question_count && <Text color="primary.default" fontSize="xs">No of Questions : {item.question_count}</Text>} */}
                <Spacer />
                {/* {item.active && <Text color="primary.default" fontSize="xs">active: {item.active}</Text>} */}
              </HStack>
            </Pressable>
          </VStack>
        </Box>
      )}
    </ScrollView>
    
  )
}

export default SessionsName