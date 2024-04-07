import React from 'react'
import { Box, Button, Container, HStack, Icon, IconButton, ScrollView, Select, Spacer, Text, VStack } from 'native-base'
import { Ionicons } from '@expo/vector-icons';
import { useState } from 'react';
import moment from 'moment';
import QuestionType from './QuestionType';

const Itembox = ({data}: any) => {
  const [toggle, setToggle] = useState(false)
  return (
    <Container w="100%" maxW="100%">
      <Box px="3" py="3" borderBottomWidth="1px" borderColor="#EDEDED" bg="#fff" w="100%" rounded="0">
        <HStack w="100%" space="3" alignItems="flex-start">
          <Text fontSize="xs" pt="1" lineHeight="xs" bold>{data.info[0].value}</Text>
          <Spacer/>
          <Text bg="#F7F7F7" px="2" py="1" rounded="sm" lineHeight="xs" fontSize="xs">{data.id}</Text>
        
        </HStack>
      </Box>
      {data.question_type === 'single' && <>
        {toggle && <QuestionType data={data} />}
        <Button
          w="100%"
          p="0"
          py="1"
          borderBottomRightRadius="3"
          borderBottomLeftRadius="3"
          rounded="0"
          bg="#F7F7F7"
          _text={{fontSize: 'xs', color: 'primary.default'}}
          colorScheme="primary"
          onPress={()=>{
            setToggle(!toggle)
          }}
      
        >
          <HStack w="100%" space="2" alignItems="center">
            <Text fontSize="xs">{toggle ? 'Less' : 'More'}</Text>
            <Icon as={Ionicons} name={toggle ? 'chevron-up-outline' : 'chevron-down-outline'}  />
          </HStack>
       
        </Button>
      </>}
    </Container> 
  )
}

const ActiveQuestions = ({data}) => {
  return (
    <>
      {data && data && <Container alignItems="flex-stat" justifyContent="flex-start" h="100%" w="100%" maxW="100%">
        <HStack w="100%" space="0" mb="3" alignItems="center">
          <Text fontSize="lg" bold>Active</Text>
        </HStack>
        <Box bg="#F7F7F7" p="2" rounded="lg" w="100%" h="90%">
          <Box overflow="hidden" w="100%" h="100%" bg="#fff" p="0" rounded="lg" borderWidth="1" borderColor="#EDEDED">
            <ScrollView w="100%" h="100%">
              {data && data.map((item: any,k: number) =>
                <React.Fragment key={item.id}>
                  <Itembox data={item} />
                </React.Fragment>
              )}
            </ScrollView>
          </Box>
        </Box>
      </Container>}
    </>
    
  )
}

export default ActiveQuestions