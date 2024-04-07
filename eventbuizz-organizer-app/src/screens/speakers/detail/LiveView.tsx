import React from 'react'
import { Box, Button, Center, Container, HStack, Icon, IconButton, Input, Pressable, ScrollView, Select, Spacer, Stack, Switch, Text } from 'native-base'
import { Ionicons } from '@expo/vector-icons'

const ItemBox = ({active}: any) => {
  const [toggle, setToggle] = React.useState(active || false);
  return (
    <Container bg="#fff" maxW="100%" p="0" w="100%">
      {active && 
        <HStack px="2" py="3" bg="green.100" w="100%" rounded="sm" alignItems="center">
          <Text fontSize="xs" bold>Active Question</Text>
          <Spacer />
          <HStack space="1" alignItems="center">
            <Text fontSize="xs" bold>00 : 04 : 31</Text>
            <IconButton
              p="0"
              variant="unstyled"
              icon={<Icon size="lg" as={Ionicons} name="ios-pause-circle-outline" color="danger.500" />}
              onPress={()=>{
                console.log('hello')
              }}
              
            />
            
            
          </HStack>
          
        </HStack>
        
      }
      <Box w="100%" px="3" py="2">
        <HStack mb="2" space="3" alignItems="center">
          <Center>
            <Text fontSize="xs" bold>Qna 1</Text>
          </Center>
          <Spacer />
          {!active &&  <HStack space="1">
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="primary.500" size="md" as={Ionicons} name="ios-play-circle" />}
              onPress={()=>{
                console.log('hello')
              }}
              
            />
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="#bbb" size="md" as={Ionicons} name="ios-close-circle-outline" />}
              onPress={()=>{
                console.log('hello')
              }}
              
            />
          </HStack>}
        </HStack>
        <HStack mb="1" space="2" alignItems="center">
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-calendar-outline"  />
            <Text color="primary.default" fontSize="10px">21 May 2021</Text>
          </HStack>
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-time-outline"  />
            <Text color="primary.default" fontSize="10px">11:00 am</Text>
          </HStack>
        </HStack>
        {toggle && <React.Fragment>
          <Text mt="1" mb="2" color="primary.default" fontSize="xs">In order to create and disseminate an effective press release.</Text>
        </React.Fragment>}
      </Box>
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
          <Text color="primary.default" fontSize="xs">{toggle ? 'Less' : 'More'}</Text>
          <Icon as={Ionicons} name={toggle ? 'chevron-up-outline' : 'chevron-down-outline'}  />
        </HStack>
       
      </Button>
    </Container>
    
  )
}

const LiveView = ({mode,toggleChange} :any) => {
  return (
    <Container alignItems="flex-stat" justifyContent="flex-start" h="100%" w="100%" maxW="100%">
      <HStack mb="4" w="100%" space="3" alignItems="center">
        <HStack pb="2" space="5" alignItems="center">
          <Text fontSize="lg" bold>Live</Text>
        </HStack>
        <Spacer />
        <HStack  space="3" alignItems="center">
          <Text fontSize="xs">Moderation</Text>
          <Switch onValueChange={(val:any) => toggleChange(val)} isChecked={mode} size="sm" />
        </HStack>
      </HStack>
      <Box bg="#F7F7F7" p="2" rounded="lg" w="100%" h="90%">
        <Input fontSize={'xs'} mb="2" size="sm" bg="#fff" h="40px" InputLeftElement={<Icon ml="2" as={Ionicons} size="sm" name="ios-search-outline" />} placeholder="Search"  />
        
        <Box overflow="hidden" w="100%" h="90%" bg="#fff" p="0" rounded="lg" borderWidth="1" borderColor="#EDEDED">
          <ScrollView w="100%" h="100%">
            <ItemBox active="true" />
            {[...Array(2)].map((item,k) => 
              <React.Fragment key={k}>
                <ItemBox />
              </React.Fragment>
            )}
          </ScrollView>
        </Box>
      </Box>
      
    </Container>
    
  )
}

export default LiveView