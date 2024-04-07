import React from 'react'
import { Box, Button, Center, Container, HStack, Icon, IconButton, Input, Pressable, ScrollView, Select, Spacer, Stack, Switch, Text } from 'native-base'
import { Ionicons } from '@expo/vector-icons'
import { useState } from 'react';
import UseQaService from '@src/store/services/UseQaService';
import moment from 'moment';
import { useEffect } from 'react';
import { useMemo } from 'react';

const ItemBox = ({data, active}) => {
  const [toggle, setToggle] = React.useState(false);
  const { incomingtoreject, rejecttoincoming, comingtolive } = UseQaService()
  return (
    <Container bg="#fff" maxW="100%" p="0" w="100%">
      <Box w="100%" px="3" py="2" bg={active ? 'danger.100' : '#fff'}>
        <HStack mb="2" space="3" alignItems="center">
          <Center>
            <Text maxW="150" isTruncated fontSize="xs" bold>{data.attendee.first_name}</Text>
          </Center>
          <Spacer />
          {!active && <HStack space="1">
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="#bbb" size="md" as={Ionicons} name="ios-checkmark-circle-outline" />}
              onPress={()=>{
                comingtolive({id: data.id})
              }}
              
            />
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="#bbb" size="md" as={Ionicons} name="ios-close-circle-outline" />}
              onPress={()=>{
                incomingtoreject({id: data.id})
              }}
              
            />
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="#bbb" size="sm" as={Ionicons} name="ios-copy-outline" />}
              onPress={()=>{
                console.log('hello')
              }}
              
            />
            
            
          </HStack>}
          {active && <HStack space="1">
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="primary.default" size="md" as={Ionicons} name="reload" />}
              onPress={()=>{
                rejecttoincoming({id: data.id})
              }}
              
            />
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="primary.default" size="sm" as={Ionicons} name="ios-copy-outline" />}
              onPress={()=>{
                console.log('hello')
              }}
              
            />
            
            
          </HStack>}
        </HStack>
        <HStack mb="1" space="2" alignItems="center">
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-calendar-outline"  />
            <Text color="primary.default" fontSize="10px">{moment(new Date(data.created_at)).format('D MMMM, YYYY')}</Text>
          </HStack>
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-time-outline"  />
            <Text color="primary.default" fontSize="10px">{moment(new Date(data.created_at)).format('h:mm a')}</Text>
          </HStack>
        </HStack>
        {toggle && <React.Fragment>
          <Text mt="1" mb="2" color="primary.default" fontSize="xs">{data.info[0].value.replace(/<\/?[^>]+(>|$)/g, '')}</Text>
          <HStack mb="2" space="1" alignItems="center">
            <Text minW="75px" color="primary.default" fontSize="xs">Speakers :</Text>
            <Center alignItems="flex-start" w="calc(100% - 75px)">
              <Stack direction="row" flexWrap="wrap" space="2">
                <Button
                  size="sm"
                  px="3"
                  py="1"
                  bg="#F7F7F7"
                  _text={{color: 'primary.default'}}
                  variant="unstyled"
                  onPress={()=>{
                    console.log('hello')
                  }}
              
                >
                New speaker
                </Button>
                <Button
                  size="sm"
                  px="3"
                  py="1"
                  bg="#F7F7F7"
                  _text={{color: 'primary.default'}}
                  variant="unstyled"
                  onPress={()=>{
                    console.log('hello')
                  }}
              
                >
                sp 1
                </Button>
              
              </Stack>
            
            </Center>

          </HStack>
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

const ModerationView = () => {
  const { programs, innerProcessing, activemoderator } = UseQaService();
  const [data, setData] = useState(null);
  const [toggle, setToggle] = useState(false);
  useMemo(() => {
    if (toggle) {
      setData(programs.rejectingQA)
    } else {
      setData(programs.incomingQA)
    }
  }, [programs, innerProcessing])
  
  return (
    <>
      {data &&  <Container alignItems="flex-stat" justifyContent="flex-start" h="100%" w="100%" maxW="100%">
        <HStack mb="4" w="100%" space="3" alignItems="center">
          <HStack borderBottomWidth="1" borderColor="#EBEBEB" pb="2"  space="5" alignItems="center">
            <Pressable
              p="0"
              varient="unstyled"
              borderWidth="0"
              onPress={()=>{
                setToggle(false)
                setData(programs.incomingQA)
              }}
            >
              <Text color={!toggle ? '#000' : '#bbb'} fontSize="16px" bold>Incoming</Text>
              {!toggle &&  <Spacer h="2px" rounded="lg" w="100%" bg="#000" position="absolute" left="0" bottom="-10px" />}
            </Pressable>
            <Pressable
              p="0"
              varient="unstyled"
              borderWidth="0"
              onPress={()=>{
                setToggle(true)
                setData(programs.rejectingQA)
              }}
            >
              <Text color={toggle ? '#000' : '#bbb'} fontSize="16px" bold>Rejected</Text>
              {toggle && <Spacer h="2px" rounded="lg" w="100%" bg="#000" position="absolute" left="0" bottom="-10px" />}
            </Pressable>
          </HStack>
          <Spacer />
          <HStack  space="3" alignItems="center">
            <Text fontSize="sm">Moderation</Text>
            <Switch onValueChange={(val:any) => activemoderator({id: val})} isChecked={programs.setting.moderator === 1 ? true : false} size="sm" />
          </HStack>
        </HStack>
        <Box bg="#F7F7F7" p="2" rounded="lg" w="100%" h="90%">
          <Input fontSize={'xs'} mb="2" size="sm" bg="#fff" h="40px" InputLeftElement={<Icon ml="2" as={Ionicons} size="sm" name="ios-search-outline" />} placeholder="Search"  />
          
          <Box overflow="hidden" w="100%" h="90%" bg="#fff" p="0" rounded="lg" borderWidth="1" borderColor="#EDEDED">
            <ScrollView w="100%" h="100%">
              {data.length !== 0 && data.map((item,k) => 
                <React.Fragment key={k}>
                  <ItemBox active={toggle} data={item} />
                </React.Fragment>
              )}
              {data.length === 0 && <Text p="4" fontSize="xs">No Data Found</Text>}
            </ScrollView>
          </Box>
        </Box>
        
      </Container>}
    </>
    
  )
}

export default ModerationView