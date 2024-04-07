import React from 'react';
import { Ionicons } from '@expo/vector-icons'
import { Box, HStack, Icon, ScrollView, Spacer, Switch, Text, VStack } from 'native-base';
import moment from 'moment';

const data = [
  {
    id: '1',
    title: 'Mundtlig beretning v/ Ole Wehlast',
    datetime: '2021-03-11 11:00',
    questions: '3',
    active: '0'
  },
  {
    id: '2',
    title: 'Mandatudvalgets formand',
    datetime: '2021-03-11 11:00',
    questions: '3',
    active: '2'
  },
  {
    id: '3',
    title: 'Debat om skriftlig og mund tlig Ole Wehlast',
    datetime: '2021-03-11 15:00',
    questions: '3',
    active: '0'
  },
  {
    id: '4',
    title: 'Session Imp 3',
    datetime: '2021-03-11 11:00',
    questions: '3',
    active: '1'
  },
]

const QASessionsName = ({ navigation }: any) =>{
  return (
    <ScrollView pb="3" pr="3" w="100%" h="100%">
      {data && data.map((item,k) =>
        <Box  mb="2" key={k} w="100%" bg="#fff" p="4">
          <VStack space="2">
            <HStack space="3" alignItems="center">
              <Text fontSize="sm" bold>{item.title}</Text>
              <Spacer />
              <Switch size="sm" onTrackColor="primary.100" onThumbColor="primary.500" defaultIsChecked={item.active > '0' ? true: false } />
            </HStack>
            <HStack  space="3" alignItems="center">
              <HStack space="1" alignItems="center">
                <Icon as={Ionicons} name="ios-calendar-outline"  />
                <Text color="primary.default" fontSize="xs">{moment(new Date(item.datetime)).format('D MMMM, YYYY')}</Text>
              </HStack>
              <HStack space="1" alignItems="center">
                <Icon as={Ionicons} name="ios-time-outline"  />
                <Text color="primary.default" fontSize="xs">{moment(new Date(item.datetime)).format('h:mm a')}</Text>
              </HStack>
            </HStack>
            <HStack space="3" alignItems="center">
              <Text color="primary.default" fontSize="xs">Speaker List : {item.questions}</Text>
              <Spacer />
              {item.active > '0' && <Text color="primary.default" fontSize="xs">active: {item.active}</Text>}
							
            </HStack>
									
          </VStack>
					
        </Box>
				
      )}
    </ScrollView>
    
  )
}

export default QASessionsName