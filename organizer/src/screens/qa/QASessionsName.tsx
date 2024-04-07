import React from 'react';
import { Ionicons } from '@expo/vector-icons'
import { Box, HStack, Icon, Pressable, ScrollView, Spacer, Text, VStack } from 'native-base';
import UseQaService from '@src/store/services/UseQaService';

import moment from 'moment';


const QASessionsName = ({data, navigation }: any) => {
  const { activeId, getactiveprogram } = UseQaService();
  const handleClick = (item: number) => {
    getactiveprogram({id: item})
  }
  return (
    <ScrollView pb="3" pr="3" w="100%" h="100%">
      {data && data.map((item: { id: number; info: { value: any; }[]; start_date: string | number | Date; start_time: string; total_question_count: any; new_question_count: any; replied_question_count: any; },k: Key | null | undefined) =>
        <Pressable
          key={item.id}
          onPress={()=>{
            handleClick(item.id)
          }}
       
        >
          <Box  mb="2" key={k} w="100%" bg={item.id === activeId ? '#f1f1f1' : '#fff'} p="4">
            <VStack space="2">
              <HStack space="3" alignItems="center">
                <Text fontSize="sm" bold>{item.info[0].value}</Text>
              </HStack>
              <HStack  space="3" alignItems="center">
                <HStack space="1" alignItems="center">
                  <Icon as={Ionicons} name="ios-calendar-outline"  />
                  <Text color="primary.default" fontSize="xs">{moment(new Date(item.start_date)).format('D MMMM, YYYY')}</Text>
                </HStack>
                <HStack space="1" alignItems="center">
                  <Icon as={Ionicons} name="ios-time-outline"  />
                  <Text color="primary.default" fontSize="xs">{moment(`${item.start_date} ${item.start_time}`).format('h:mm a')}</Text>
                </HStack>
                {item.info[5].value && <HStack space="1" alignItems="center">
                  <Icon as={Ionicons} name="ios-location-outline"  />
                  <Text color="primary.default" fontSize="xs">{item.info[5].value}</Text>
                </HStack>}
              </HStack>
              <HStack space="3" alignItems="center">
                <Text color="primary.default" fontSize="xs">No of Questions : {item.total_question_count}</Text>
                <Spacer />
                <Text color="primary.default" fontSize="xs">New Questions : {item.new_question_count}</Text>
						
              </HStack>
              <HStack space="3" alignItems="center">
                <Text color="primary.default" fontSize="xs">Replied Questions : {item.replied_question_count}</Text>
              </HStack>
									
            </VStack>
					
          </Box>
        </Pressable>
       
				
      )}
    </ScrollView>
    
  )
}

export default QASessionsName