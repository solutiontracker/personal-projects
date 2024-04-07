import React from 'react'
import { Box, Button, Container, HStack, Icon, IconButton, ScrollView, Select, Spacer, Text, VStack } from 'native-base'
import { Ionicons } from '@expo/vector-icons';
import { useState } from 'react';

const Itembox = () => {
  const [toggle, setToggle] = useState(false)
  return (
    <Container w="100%" maxW="100%">
      <Box px="4" py="3" borderBottomWidth="1px" borderColor="#EDEDED" bg="#fff" w="100%" rounded="0">
        <HStack mb="2" w="100%" space="3" alignItems="flex-start">
          <Text fontSize="xs" pt="1" lineHeight="xs" bold>The Session Q1</Text>
          <Spacer/>
          <Text bg="#F7F7F7" px="2" py="1" rounded="md" lineHeight="xs" fontSize="xs">1531</Text>
        
        </HStack>
        <HStack  space="2" alignItems="center">
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-calendar-outline"  />
            <Text color="primary.default" fontSize="10px">21 May 2021</Text>
          </HStack>
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-time-outline"  />
            <Text color="primary.default" fontSize="10px">11:00 am</Text>
          </HStack>
        </HStack>
      
      </Box>
      {toggle && <Box  bg="#FEEBED"  px="4" py="3" rounded="0" w="100%">
        <Text mb="2" fontSize="xs" lineHeight="xsm" bold>Which of these would be hardest to live without?</Text>
        <VStack w="100%" space="1" alignItems="flex-start">
          <HStack w="100%" space="0" alignItems="flex-start">
            <HStack  space="1" alignItems="center">
              <Text color="primary.default" fontSize="10px">This is the Option 1</Text>
              <Icon color="primary.500" size="sm" as={Ionicons} name="ios-checkmark-circle"  />
            </HStack>
            
            <Spacer />
            <Text fontSize="10px">70% | 512</Text>
          </HStack>
          <HStack w="100%" space="3" alignItems="flex-start">
            <Text color="primary.default" fontSize="10px">This is the Option 1</Text>
            <Spacer />
            <Text fontSize="10px">70% | 512</Text>
          </HStack>
          <HStack w="100%" space="3" alignItems="flex-start">
            <Text color="primary.default" fontSize="10px">This is the Option 1</Text>
            <Spacer />
            <Text fontSize="10px">70% | 512</Text>
          </HStack>
        </VStack>
        
      </Box>}
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
      
    </Container> 
  )
}

const ClosedQuestions = () => {
  return (
    <Container alignItems="flex-stat" justifyContent="flex-start" h="100%" w="100%" maxW="100%">
      <HStack w="100%" space="0" mb="3" alignItems="center">
        <Text fontSize="lg" bold>Closed</Text>
      </HStack>
      <Box bg="#F7F7F7" p="2" rounded="lg" w="100%" h="90%">
        <Box overflow="hidden" w="100%" h="100%" bg="#fff" p="0" rounded="lg" borderWidth="1" borderColor="#EDEDED">
          <ScrollView w="100%" h="100%">
            {[...Array(2)].map((item,k) =>
              <React.Fragment key={k}>
                <Itembox />
              </React.Fragment>
              
            )}
          </ScrollView>
          
        </Box>
        
      </Box>
      
    </Container>
    
  )
}

export default ClosedQuestions