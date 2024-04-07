import React from 'react'
import { Box, Button, Center, Container, HStack, Icon, IconButton, Input, Pressable, ScrollView, Select, Spacer, Stack, Switch, Text } from 'native-base'
import { Ionicons } from '@expo/vector-icons'
import IcomenuPlus from '@src/assets/icons/IcomenuPlus';
import Icobuilding from '@src/assets/icons/Icobuilding';
import Icousers from '@src/assets/icons/Icousers';

const ItemBox = ({speakers} : any) => {
  const [toggle, setToggle] = React.useState(false);
  console.log(speakers)
  return (
    <Container bg="#fff" maxW="100%" p="0" w="100%">
      <Box w="100%" px="3" py="3">
        <HStack mb="2" space="3" alignItems="center">
          <Center>
            <Text fontSize="sm" bold>Person 1</Text>
          </Center>
          <Spacer />
          <HStack space="1">
            {speakers && <IconButton
              variant="unstyled"
              p="0"
              icon={<IcomenuPlus color="#bbb"  />}
              onPress={()=>{
                console.log('hello')
              }}
            />}
            {!speakers && 
              <React.Fragment>
                <IconButton
                  variant="unstyled"
                  p="0"
                  icon={<Icon color="#bbb" size="md" as={Ionicons} name="ios-checkmark-circle-outline" />}
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
              </React.Fragment>
            }
          </HStack>
        </HStack>
        <HStack mb="1" space="2" alignItems="center">
          <HStack space="1" alignItems="center">
            <Icobuilding color="primary.default" />
            <Text color="primary.default" fontSize="10px">IT Tech Google</Text>
          </HStack>
          <HStack space="1" alignItems="center">
            <Icousers color="primary.default" />
            <Text color="primary.default" fontSize="10px">51423</Text>
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

const ModerationView = ({hasSpeakers}:any) => {
  return (
    <Container alignItems="flex-stat" justifyContent="flex-start" h="100%" w="100%" maxW="100%">
      <HStack mb="4" w="100%" space="3" alignItems="center">
        <Text fontSize="lg" bold>{hasSpeakers ? 'Speakers' : 'Pending'}</Text>
      </HStack>
      <Box bg="#F7F7F7" p="2" rounded="lg" w="100%" h="90%">
        <Input fontSize={'xs'} mb="2" size="sm" bg="#fff" h="40px" InputLeftElement={<Icon ml="2" as={Ionicons} size="sm" name="ios-search-outline" />} placeholder="Search"  />
        
        <Box overflow="hidden" w="100%" h="90%" bg="#fff" p="0" rounded="lg" borderWidth="1" borderColor="#EDEDED">
          <ScrollView w="100%" h="100%">
            {[...Array(5)].map((item,k) => 
              <React.Fragment key={k}>
                <ItemBox speakers={hasSpeakers} />
              </React.Fragment>
            )}
          </ScrollView>
        </Box>
      </Box>
      
    </Container>
    
  )
}

export default ModerationView