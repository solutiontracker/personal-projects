import React from 'react'
import { Box, Container, HStack, Icon, IconButton, ScrollView, Spacer, Text } from 'native-base'
import { Ionicons } from '@expo/vector-icons'
import CreateQuestions from '@src/screens/components/CreateQuestions';
import { useState } from 'react';
import EditQuestion from '@src/screens/components/EditQuestion';

const ListQuestions = ({data}) => {
  const [editdata, setEditdata] = useState(null);
  const [modal, setmodal] = useState(false);
  return (
    <>
      <Container alignItems="flex-stat" justifyContent="flex-start" h="100%" w="100%" maxW="100%">
        <HStack w="100%" space="0" mb="3" alignItems="center">
          <Text fontSize="lg" bold>List</Text>
          <Spacer />
          <CreateQuestions title="Create new Question" />
        </HStack>
        <Box bg="#F7F7F7" p="2" rounded="lg" w="100%" h="90%">
          <Box overflow="hidden" w="100%" h="100%" bg="#fff" p="0" rounded="lg" borderWidth="1" borderColor="#EDEDED">
            <ScrollView w="100%" h="100%">
              {data && data.map((item,k) =>
                <Box px="3" py="4" borderBottomWidth="1px" borderColor="#EDEDED" key={item.id} bg="#fff" w="100%" rounded="0">
                  <HStack w="100%" space="3" alignItems="flex-start">
                    {item?.info[0]?.value && <Text fontSize="xs" lineHeight="xs" bold>{item.info[0].value}</Text>}
                    <Spacer />
                    <HStack minW="60px" space="1" alignItems="center" justifyContent="flex-end">
                      <IconButton
                        variant="unstyled"
                        size="xs"
                        p="0"
                        icon={<Icon size="sm" as={Ionicons} name="ios-create-outline" color="#BBB" />}
                        onPress={()=>{setEditdata(item);setmodal(true)}}
                      />
                      <IconButton
                        variant="unstyled"
                        size="xs"
                        p="0"
                        icon={<Icon size="xs" as={Ionicons} name="ios-copy-outline" color="#BBB" />}
                        onPress={()=>{
                          console.log('hello')
                        }}
                      />
                      <IconButton
                        variant="unstyled"
                        size="xs"
                        p="0"
                        icon={<Icon size="sm" as={Ionicons} name="ios-enter-outline" color="#BBB" />}
                        onPress={()=>{
                          console.log('hello')
                        }}
                      />
                    
                    </HStack>
                  </HStack>
                </Box>
              
              )}
            </ScrollView>
          
          </Box>
        
        </Box>
      
      </Container>
      {modal && editdata && <EditQuestion onPress={() => setmodal(false)} data={editdata} />}
    </>
    
  )
}

export default ListQuestions